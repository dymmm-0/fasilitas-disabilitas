import { readFileSync } from 'node:fs';
import { readdir } from 'node:fs/promises';
import { join } from 'node:path';
import { parse } from '@vue/compiler-sfc';
const files = [];
const walk = async (d) => { for (const e of await readdir(d, { withFileTypes: true })) { const p = join(d, e.name); if (e.isDirectory()) await walk(p); else files.push(p); } };
await walk('resources/js');
const ID = /\b(dan|yang|untuk|atau|tidak|sudah|akan|harus|dengan|pada|dari|oleh|wajib|belum|ini|itu|kota|nama|halaman|akun|data|pesan|memuat|gagal|masuk|keluar|tutup|buka|pilih|kirim|tambah|semua|perlu|cek|periksa|tersimpan|keputusan|disetujui|ditolak|antrean|verifikasi|pemeriksaan|catatan|laporan|usulan|biaya|waktu|berkas|foto|peta|titik|lokasi|menit|jam|hari)\b/i;
const out = [];
for (const f of files) {
    const src = readFileSync(f, 'utf8');
    const { descriptor } = parse(src, { filename: 'x.vue' });
    const hits = [];
    for (const b of [descriptor.script, descriptor.scriptSetup].filter(Boolean)) {
        for (const m of b.content.matchAll(/(['"`])((?:(?!\1)[^\\n])*)\1/g)) {
            const s = m[2];
            if (s.length < 5 || s.includes('t(') || !ID.test(s)) continue;
            if (/^[a-z][\w-]*$/.test(s)) continue;
            hits.push(s);
        }
    }
    if (hits.length) out.push(`== ${f}\n` + [...new Set(hits)].map((s) => '  ' + s).join('\n'));
}
console.log(out.join('\n'));
