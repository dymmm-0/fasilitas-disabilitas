// Laporan lengkap kunci terjemahan yang belum ada di en.json.
import { readFileSync, readdirSync, writeFileSync } from 'node:fs';
import { join, extname } from 'node:path';

const walk = (d) => readdirSync(d, { withFileTypes: true }).flatMap((e) => (e.isDirectory() ? walk(join(d, e.name)) : [join(d, e.name)]));
const files = walk('resources/js').filter((f) => ['.js', '.vue'].includes(extname(f)) && !f.includes('i18n'));
const en = JSON.parse(readFileSync('resources/js/i18n/en.json', 'utf8'));
const keys = new Set(Object.keys(en));
const miss = new Map();
const add = (k, f) => { if (k && !keys.has(k)) miss.set(k, f); };

for (const f of files) {
    const s = readFileSync(f, 'utf8');
    for (const m of s.matchAll(/(?<![\w.$])t\(\s*'((?:[^'\\]|\\.)*)'/g)) add(m[1], f);
    for (const m of s.matchAll(/(?<![\w.$])t\(\s*"((?:[^"\\]|\\.)*)"/g)) add(m[1], f);
    for (const m of s.matchAll(/i18n-t\s+keypath="((?:[^"\\]|\\.)*)"/g)) add(m[1], f);
    // label/hint/title yang dipakai lewat variabel ke $t(...)
    for (const m of s.matchAll(/\b(?:label|hint|title|text)\s*:\s*'((?:[^'\\]|\\.)*)'/g)) if (/[a-zA-Z]/.test(m[1])) add(m[1], f);
    for (const m of s.matchAll(/\b(?:label|hint|title|text)\s*:\s*"((?:[^"\\]|\\.)*)"/g)) if (/[a-zA-Z]/.test(m[1])) add(m[1], f);
}
const list = [...miss].sort((a, b) => a[0].localeCompare(b[0], 'id'));
console.log(list.length + ' kunci belum diterjemahkan:');
for (const [k, f] of list) console.log(JSON.stringify(k) + '  <- ' + f.replace(/\\/g, '/'));
writeFileSync('resources/js/i18n/_missing.json', JSON.stringify(Object.fromEntries(list.map(([k]) => [k, ''])), null, 4) + '\n');