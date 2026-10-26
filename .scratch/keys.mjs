// Kumpulkan seluruh kunci katalog (teks Indonesia verbatim) dari resources/js.
import { readFileSync, writeFileSync } from 'node:fs';
import { readdir } from 'node:fs/promises';
import { join } from 'node:path';

const files = [];
const walk = async (d) => {
    for (const e of await readdir(d, { withFileTypes: true })) {
        const p = join(d, e.name);
        if (e.isDirectory()) await walk(p);
        else if (/\.(vue|js)$/.test(e.name)) files.push(p);
    }
};
await walk('resources/js');

const keys = new Set();
const add = (s) => {
    const v = s.replace(/\\'/g, "'").replace(/&quot;/g, '"').replace(/\s+/g, ' ').trim();
    if (v) keys.add(v);
};

for (const f of files) {
    const src = readFileSync(f, 'utf8');
    for (const m of src.matchAll(/\$t\(\s*'([^']*)'/g)) add(m[1]);
    for (const m of src.matchAll(/i18n-t keypath="([^"]*)"/g)) add(m[1]);
}

// label/hint di data/meta.js dirender sebagai $t(meta.label) — ikut jadi kunci.
for (const m of readFileSync('resources/js/data/meta.js', 'utf8').matchAll(/(?:label|hint): '([^']*)'/g)) add(m[1]);

const sorted = [...keys].sort((a, b) => a.localeCompare(b, 'id'));
writeFileSync('.scratch/keys.json', JSON.stringify(sorted, null, 2));
console.log(sorted.length);