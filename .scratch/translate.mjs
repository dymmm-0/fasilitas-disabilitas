/**
 * Isi resources/js/i18n/en.json dengan terjemahan otomatis dari MyMemory (gratis).
 * Kunci = teks Indonesia apa adanya; nilai = terjemahan Inggris.
 *
 * Pakai: node .scratch/translate.mjs
 */
import { readFileSync, writeFileSync, existsSync } from 'node:fs';

const KEYS = JSON.parse(readFileSync('.scratch/keys.json', 'utf8'));
const OUT = 'resources/js/i18n/en.json';
const API = 'https://api.mymemory.translated.net/get';
const SEP = ' ||| ';
const MAX_CHARS = 480;

const existing = existsSync(OUT) ? JSON.parse(readFileSync(OUT, 'utf8')) : {};

const chunks = [];
let current = [];
let size = 0;
for (const key of KEYS) {
    if (existing[key]) continue;
    if (key.length > MAX_CHARS) { chunks.push([key]); continue; } // terlalu panjang untuk satu query; lihat laporan
    if (size + key.length + SEP.length > MAX_CHARS && current.length) { chunks.push(current); current = []; size = 0; }
    current.push(key);
    size += key.length + SEP.length;
}
if (current.length) chunks.push(current);

const translated = { ...existing };
let failed = 0;

for (const [index, chunk] of chunks.entries()) {
    const url = `${API}?q=${encodeURIComponent(chunk.join(SEP))}&langpair=id|en`;
    try {
        const response = await fetch(url, { headers: { Accept: 'application/json' } });
        const data = await response.json();
        const text = data?.responseData?.translatedText ?? '';
        const parts = text.split(/\s*\|\|\|\s*/);

        if (parts.length !== chunk.length) {
            // Jawaban tidak sejajar: terjemahkan satu per satu agar tetap tersimpan.
            for (const key of chunk) {
                const single = await fetch(`${API}?q=${encodeURIComponent(key)}&langpair=id|en`).then((r) => r.json());
                const value = single?.responseData?.translatedText;
                if (value && !/MYMEMORY WARNING|QUERY LENGTH LIMIT/i.test(value)) translated[key] = value;
                else failed += 1;
                await new Promise((resolve) => setTimeout(resolve, 300));
            }
        } else {
            chunk.forEach((key, i) => {
                if (parts[i]) translated[key] = parts[i];
                else failed += 1;
            });
        }
    } catch {
        failed += chunk.length;
    }

    console.log(`${index + 1}/${chunks.length} (${Object.keys(translated).length} tersimpan, ${failed} gagal)`);
}

const sorted = Object.fromEntries(Object.keys(translated).sort((a, b) => a.localeCompare(b, 'id')).map((k) => [k, translated[k]]));
writeFileSync(OUT, `${JSON.stringify(sorted, null, 4)}\n`);
console.log(`selesai: ${Object.keys(sorted).length} kunci, ${failed} gagal`);