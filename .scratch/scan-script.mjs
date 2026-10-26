import { readFileSync } from 'node:fs';
import { readdir } from 'node:fs/promises';
import { join } from 'node:path';
import { parse } from '@vue/compiler-sfc';

const files = [];
const walk = async (d) => { for (const e of await readdir(d, { withFileTypes: true })) { const p = join(d, e.name); if (e.isDirectory()) await walk(p); else if (e.name.endsWith('.vue')) files.push(p); } };
await walk('resources/js');
const ID = /\b(dan|yang|untuk|atau|tidak|sudah|akan|harus|dengan|pada|di|ke|dari|wajib|belum|ini|itu|kota|nama|halaman|akun|data|pesan|memuat|gagal|salah|masuk|keluar|tutup|buka|pilih|kirim|tambah|semua|perlu)\b/i;
const out = [];
for (const f of files) {
    const { descriptor } = parse(readFileSync(f, 'utf8'), { filename: 'x.vue' });
    const lines = [];
    for (const b of [descriptor.script, descriptor.scriptSetup].filter(Boolean)) {
        for (const m of b.content.matchAll(/'([^'\n]*)'/g)) {
            const s = m[1];
            if (s.length < 6 || s.includes('$t(') || s.includes('/') && !s.includes(' ')) continue;
            if (!ID.test(s)) continue;
            lines.push(s);
        }
    }
    if (lines.length) out.push(`== ${f}\n` + [...new Set(lines)].map((s) => `  ${s}`).join('\n'));
}
console.log(out.join('\n\n'));
