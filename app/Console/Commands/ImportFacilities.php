<?php

namespace App\Console\Commands;

use App\Models\Facility;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ImportFacilities extends Command
{
    protected $signature = 'facilities:import
        {file=Dataset/akseskita_facilities_medan.csv : CSV dataset, relatif root project atau absolut}
        {--dry-run : Validasi dan laporkan saja, tanpa menulis ke database}';

    protected $description = 'Impor dataset fasilitas AksesKita ke tabel facilities (idempoten, tidak menghapus data lama)';

    /** Kolom dataset yang wajib ada sebelum impor dijalankan. */
    private const REQUIRED_COLUMNS = [
        'facility_id', 'facility_name', 'category', 'category_detail', 'address',
        'village_kelurahan', 'subdistrict_kecamatan', 'district_city', 'province', 'postal_code',
        'latitude', 'longitude', 'osm_type', 'osm_id', 'source_name', 'source_url', 'collected_at',
        'ramp_status', 'ramp_details', 'accessible_toilet_status', 'accessible_toilet_details',
        'elevator_status', 'elevator_details', 'wheelchair_path_status', 'wheelchair_path_details',
        'entrance_access_status', 'entrance_width_cm', 'entrance_width_measured', 'entrance_details',
        'verification_status', 'verified_at', 'verification_method', 'evidence_url',
        'data_completeness_percent', 'notes',
    ];

    private const AVAILABILITY = ['yes', 'no', 'unknown'];

    private const VERIFICATION = ['pending', 'verified', 'needs_repair', 'rejected'];

    private const STATUS_COLUMNS = [
        'ramp_status', 'accessible_toilet_status', 'elevator_status',
        'wheelchair_path_status', 'entrance_access_status',
    ];

    private const CHUNK = 200;

    public function handle(): int
    {
        $path = $this->resolvePath($this->argument('file'));

        if (! is_file($path)) {
            $this->error("File dataset tidak ditemukan: {$path}");

            return self::FAILURE;
        }

        $header = null;
        $records = [];
        $errors = [];

        foreach ($this->lines($path) as $number => $line) {
            if ($header === null) {
                if ($number !== 1) {
                    continue;
                }
                $header = $this->readHeader($line);
                $missing = array_diff(self::REQUIRED_COLUMNS, $header);

                if ($missing) {
                    $this->error('Kolom dataset tidak lengkap, impor dibatalkan: '.implode(', ', $missing));

                    return self::FAILURE;
                }

                continue;
            }

            if (trim($line) === '') {
                continue;
            }

            $columns = $this->splitLine($line);

            if (count($columns) !== count($header)) {
                $errors[] = [$number, '', count($columns).' kolom (harus '.count($header).'), baris gagal diurai'];

                continue;
            }

            $row = array_combine($header, $columns);
            [$attributes, $error] = $this->mapRow($row);

            if ($error !== null) {
                $errors[] = [$number, $row['facility_id'], $error];

                continue;
            }

            $records[] = [$number, $attributes];
        }

        if ($records === [] && $errors === []) {
            $this->error('Dataset tidak berisi baris data.');

            return self::FAILURE;
        }

        $result = $this->store($records, (bool) $this->option('dry-run'));

        $this->table(
            ['Baris dibaca', 'Dibuat', 'Diperbarui', 'Dilewati', 'Gagal'],
            [[count($records) + count($errors), $result['created'], $result['updated'], $result['skipped'], count($errors)]],
        );

        if ($errors) {
            $this->warn('Baris gagal divalidasi:');
            foreach (array_slice($errors, 0, 20) as [$number, $id, $reason]) {
                $this->line("  baris {$number} {$id} — {$reason}");
            }
            if (count($errors) > 20) {
                $this->line('  ... '.(count($errors) - 20).' baris lainnya ada di laporan.');
            }
        }

        $this->report($errors);

        if ($this->option('dry-run')) {
            $this->info('Mode dry-run: tidak ada perubahan pada database.');
        }

        return $errors === [] ? self::SUCCESS : self::FAILURE;
    }

    private function resolvePath(string $file): string
    {
        return str_starts_with($file, DIRECTORY_SEPARATOR) || preg_match('/^[A-Za-z]:[\\\\\/]/', $file)
            ? $file
            : base_path($file);
    }

    /** @return iterable<int, string> baris dengan nomor baris 1-based */
    private function lines(string $path): iterable
    {
        $raw = str_replace(["\r\n", "\r"], "\n", (string) file_get_contents($path));
        $lines = explode("\n", $raw);

        if (end($lines) === '') {
            array_pop($lines);
        }

        foreach ($lines as $index => $line) {
            yield $index + 1 => $line;
        }
    }

    private function readHeader(string $line): array
    {
        $header = array_map('trim', $this->splitLine($line));

        return str_replace(';', '', $header);
    }

    /**
     * Dataset punya tiga cacat format: ekor `;;;`, seluruh baris terbungkus tanda kutip,
     * dan tanda kutip prosa yang tidak di-escape. Perbaikan di bawah deterministik dan
     * hasilnya tetap divalidasi jumlah kolom + koordinat sebelum dipakai.
     */
    private function splitLine(string $line): array
    {
        $line = rtrim(rtrim(rtrim($line), ';'));

        if (preg_match('/^"AKS-\d{4},/', $line)) {
            $line = substr($line, 1);
        }

        $line = str_replace('""', '"', $line);

        $clean = '';
        $length = strlen($line);
        $previous = '';

        for ($i = 0; $i < $length; $i++) {
            $character = $line[$i];

            if ($character === '"' && $i !== 0 && $previous !== ',' && $i !== $length - 1 && $line[$i + 1] !== ',') {
                continue; // tanda kutip prosa, bukan pembuka/penutup field
            }

            $clean .= $character;
            $previous = $character;
        }

        return array_map(fn ($value) => $this->text($value), str_getcsv($clean, ',', '"', '\\'));
    }

    private function text(?string $value): string
    {
        return trim(preg_replace('/\s+/u', ' ', (string) $value) ?? '');
    }

    /**
     * @return array{0: array<string, mixed>|null, 1: string|null} atribut atau alasan kegagalan
     */
    private function mapRow(array $row): array
    {
        $id = $row['facility_id'];

        if (! preg_match('/^[A-Z]{2,5}-\d{1,6}$/', $id)) {
            return [null, 'facility_id tidak valid'];
        }

        if ($row['facility_name'] === '') {
            return [null, 'facility_name kosong'];
        }

        if (! isset(Facility::CATEGORY_MAP[$row['category']])) {
            return [null, "kategori tidak dikenal: {$row['category']}"];
        }

        if (! is_numeric($row['latitude']) || ! is_numeric($row['longitude'])) {
            return [null, 'koordinat bukan angka'];
        }

        $latitude = (float) $row['latitude'];
        $longitude = (float) $row['longitude'];

        if ($latitude < -90 || $latitude > 90) {
            return [null, "latitude di luar rentang: {$latitude}"];
        }

        if ($longitude < -180 || $longitude > 180) {
            return [null, "longitude di luar rentang: {$longitude}"];
        }

        if (abs($latitude) > 10 && abs($longitude) < 10) {
            return [null, 'latitude dan longitude tampak tertukar'];
        }

        foreach (self::STATUS_COLUMNS as $column) {
            if (! in_array($row[$column], self::AVAILABILITY, true)) {
                return [null, "{$column} tidak dikenal: {$row[$column]}"];
            }
        }

        if (! in_array($row['verification_status'], self::VERIFICATION, true)) {
            return [null, "verification_status tidak dikenal: {$row['verification_status']}"];
        }

        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $row['collected_at']) || strtotime($row['collected_at']) === false) {
            return [null, "collected_at tidak valid: {$row['collected_at']}"];
        }

        $verified = $row['verification_status'] === 'verified'
            && preg_match('/^\d{4}-\d{2}-\d{2}/', $row['verified_at']);

        return [[
            'external_id' => $id,
            'name' => $row['facility_name'],
            'category' => Facility::CATEGORY_MAP[$row['category']],
            'source_category' => $row['category'],
            'source_category_detail' => $row['category_detail'] ?: null,
            'address' => $row['address'] ?: null,
            'village' => $row['village_kelurahan'] ?: null,
            'subdistrict' => $row['subdistrict_kecamatan'] ?: null,
            'city' => $row['district_city'],
            'province' => $row['province'] ?: null,
            'postal_code' => $row['postal_code'] ?: null,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'ramp_status' => $row['ramp_status'],
            'ramp_details' => $row['ramp_details'] ?: null,
            'toilet_status' => $row['accessible_toilet_status'],
            'toilet_details' => $row['accessible_toilet_details'] ?: null,
            'lift_status' => $row['elevator_status'],
            'lift_details' => $row['elevator_details'] ?: null,
            'path_status' => $row['wheelchair_path_status'],
            'path_details' => $row['wheelchair_path_details'] ?: null,
            'entrance_status' => $row['entrance_access_status'],
            'entrance_details' => $row['entrance_details'] ?: null,
            'entrance_width_cm' => is_numeric($row['entrance_width_cm']) ? (int) $row['entrance_width_cm'] : null,
            'entrance_width_measured' => $row['entrance_width_measured'] === 'yes',
            'verification_status' => $row['verification_status'],
            'verified_at' => $verified ? $row['verified_at'] : null,
            'verification_method' => $verified ? ($row['verification_method'] ?: null) : null,
            'evidence_url' => $verified ? ($row['evidence_url'] ?: null) : null,
            'source_name' => $row['source_name'] ?: null,
            'source_url' => $row['source_url'] ?: null,
            'source_ref_type' => $row['osm_type'] ?: null,
            'source_ref_id' => $row['osm_id'] ?: null,
            'collected_at' => $row['collected_at'],
            'data_completeness_percent' => is_numeric($row['data_completeness_percent'])
                ? (int) $row['data_completeness_percent']
                : 0,
            'notes' => $row['notes'] ?: null,
        ], null];
    }

    /**
     * @param  array<int, array{0: int, 1: array<string, mixed>}>  $records
     * @return array{created: int, updated: int, skipped: int}
     */
    private function store(array $records, bool $dryRun): array
    {
        $counts = ['created' => 0, 'updated' => 0, 'skipped' => 0];

        foreach (array_chunk($records, self::CHUNK) as $chunk) {
            if ($dryRun) {
                continue;
            }

            DB::transaction(function () use ($chunk, &$counts) {
                foreach ($chunk as [, $attributes]) {
                    $existing = Facility::firstWhere('external_id', $attributes['external_id']);

                    // Data yang sudah diverifikasi manusia tidak ditimpa impor.
                    if ($existing && $existing->verification_status !== 'pending') {
                        $counts['skipped']++;

                        continue;
                    }

                    if (! $existing) {
                        Facility::create($attributes);
                        $counts['created']++;

                        continue;
                    }

                    $existing->fill($attributes);

                    if ($existing->isDirty()) {
                        $existing->save();
                        $counts['updated']++;

                        continue;
                    }

                    $counts['skipped']++;
                }
            });
        }

        return $counts;
    }

    /** @param array<int, array{0: int, 1: string, 2: string}> $errors */
    private function report(array $errors): void
    {
        $name = 'import-reports/facilities-'.now()->format('Ymd-His').'.csv';
        $rows = "baris,facility_id,alasan\n";

        foreach ($errors as [$number, $id, $reason]) {
            $rows .= sprintf("%d,%s,\"%s\"\n", $number, $id, str_replace('"', '""', $reason));
        }

        Storage::disk('local')->put($name, $rows);
        $this->info('Laporan validasi: '.Storage::disk('local')->path($name));
    }
}
