// Cari teks yang belum dibungkus $t di template .vue (text node + atribut teks).
import { readFileSync, readdirSync } from 'node:fs';
import { join } from 'node:path';
const walk = (d) => readdirSync(d, { withFileTypes: true }).flatMap((e) => (e.isDirectory() ? walk(join(d, e.name)) : [join(d, e.name)]));
const files = walk('resources/js').filter((f) => f.endsWith('.vue'));
const ID = /\b(dan|yang|untuk|atau|tidak|sudah|akan|harus|dengan|pada|dari|oleh|belum|ini|itu|kota|nama|halaman|akun|pesan|memuat|gagal|masuk|keluar|tutup|buka|pilih|kirim|tambah|semua|perlu|periksa|keputusan|antrean|verifikasi|catatan|laporan|usulan|foto|peta|lokasi|menit|hari|berkas|wajib|tanpa)\b/i;
const out = [];
for (const f of files) {
    const src = readFileSync(f, 'utf8');
    // compileTemplate tidak memberi AST; parse manual via regex text node & atribut.
    const tpl = src.match(/<template>([\s\S]*)<\/template>/);
    if (!tpl) continue;
    const body = tpl[1];
    const hits = new Set();
    // text node: antara > dan <, tanpa {{ dan tanpa <
    for (const m of body.matchAll(/>([^<{}]*[A-Za-zÀ-ÿ][^<{}]*)</g)) {
        const text = m[1].trim();
        if (text.length < 4 || !ID.test(text)) continue;
        hits.add('text: ' + text);
    }
    // atribut teks statis (bukan :binding) yang mengandung kata Indonesia
    for (const m of body.matchAll(/(?<![:\w-])(placeholder|title|aria-label|alt)="([^"]*)"/g)) {
        const val = m[2].trim();
        if (!val || val.includes('{{') || !ID.test(val)) continue;
        hits.add(m[1] + ': ' + val);
    }
    if (hits.size) out.push('== ' + f + '\n' + [...hits].map((h) => '  ' + h).join('\n'));
}
console.log(out.join('\n') || 'semua teks template sudah dibungkus $t');