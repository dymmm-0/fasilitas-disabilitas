/**
 * Codemod tahap 2: bungkus string Indonesia di dalam <script setup> dengan t(...)
 * dari resources/js/i18n. Hanya menyasar kalimat (≥2 kata Indonesia atau diawali
 * huruf besar), bukan path impor, kelas CSS, atau nilai internal.
 *
 * Pakai: node .scratch/script-codemod.mjs [--dry] [file.vue ...]
 */
import { readFileSync, writeFileSync } from 'node:fs';
import { readdir } from 'node:fs/promises';
import { relative, join, dirname } from 'node:path';
import { pathToFileURL } from 'node:url';
import { parse } from '@vue/compiler-sfc';

const ID_WORDS = /\b(dan|yang|untuk|atau|tidak|sudah|akan|harus|dengan|pada|dari|wajib|belum|ini|itu|kota|nama|halaman|akun|data|pesan|memuat|gagal|salah|masuk|keluar|tutup|buka|pilih|kirim|tambah|semua|perlu|izin|peta|titik|manual|lokasi|koneksi|aman|sesi|periksa|pastikan|benar|ditemukan|tersedia|diambil)\b/i;
const SKIP = new Set(['memuat', 'siap', 'gagal', 'idle', 'queued', 'perlu-perbaikan', 'id-ID', 'en-GB']);

const looksIndonesian = (s) => {
    if (s.length < 6 || SKIP.has(s)) return false;
    if (!/[A-Za-z]/.test(s)) return false;
    if (/^[\w./:@-]+$/.test(s)) return false; // path, kelas, id, surel
    if (s.includes('${') || s.includes('$t(') || s.includes('t(')) return false;
    if (/^https?:/.test(s)) return false;
    if (!ID_WORDS.test(s)) return false;
    return /^[A-Z]/.test(s) || /\s/.test(s);
};

export function processScript(source, file) {
    const { descriptor } = parse(source, { filename: 'x.vue' });
    const block = descriptor.scriptSetup ?? descriptor.script;
    if (!block) return source;

    const start = block.loc.start.offset;
    const end = block.loc.end.offset;
    let body = source.slice(start, end);
    let hits = 0;

    body = body.replace(/'([^'\n]*)'/g, (m, inner) => {
        if (!looksIndonesian(inner)) return m;
        hits += 1;
        return `t('${inner}')`;
    });

    // Hindari menggandakan `t(t(...))` bila terjadi dua kali jalan.
    if (!hits) return source;

    const rel = relative(dirname(file), 'resources/js/i18n').replace(/\\/g, '/');
    body = body.replace(/^(import .*?;\n)/m, `$1import { t } from '${rel.startsWith('.') ? rel : `./${rel}`}';\n`);

    return source.slice(0, start) + body + source.slice(end);
}

async function listVue(dir) {
    const out = [];
    for (const entry of await readdir(dir, { withFileTypes: true })) {
        const full = join(dir, entry.name);
        if (entry.isDirectory()) out.push(...(await listVue(full)));
        else if (entry.name.endsWith('.vue')) out.push(full);
    }
    return out;
}

if (import.meta.url === pathToFileURL(process.argv[1] ?? '').href) {
    const args = process.argv.slice(2);
    const dry = args.includes('--dry');
    const files = args.filter((a) => !a.startsWith('--'));
    const targets = files.length ? files : await listVue('resources/js');

    for (const file of targets) {
        const source = readFileSync(file, 'utf8');
        const code = processScript(source, file);
        if (code === source) continue;
        if (dry) console.log(`\n===== ${file} =====\n${code.slice(0, 400)}`);
        else {
            writeFileSync(file, code);
            console.log(`updated ${file}`);
        }
    }
}