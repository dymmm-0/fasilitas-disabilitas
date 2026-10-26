// Semua literal yang diteruskan ke t()/$t() di .vue/.js harus ada di en.json.
import { readFileSync, readdirSync } from 'node:fs';
import { join, extname } from 'node:path';

const walk = (d) => readdirSync(d, { withFileTypes: true }).flatMap((e) => (e.isDirectory() ? walk(join(d, e.name)) : [join(d, e.name)]));
const files = walk('resources/js').filter((f) => ['.js', '.vue'].includes(extname(f)) && !f.includes('i18n'));
const en = JSON.parse(readFileSync('resources/js/i18n/en.json', 'utf8'));
const keys = new Set(Object.keys(en));

const missing = new Map();
for (const f of files) {
    const s = readFileSync(f, 'utf8');
    for (const m of s.matchAll(/(?<![\w.$])t\(\s*'((?:[^'\\]|\\.)*)'/g)) if (m[1] && !keys.has(m[1])) missing.set(m[1], f);
    for (const m of s.matchAll(/(?<![\w.$])t\(\s*"((?:[^"\\]|\\.)*)"/g)) if (m[1] && !keys.has(m[1])) missing.set(m[1], f);
    for (const m of s.matchAll(/i18n-t\s+keypath="((?:[^"\\]|\\.)*)"/g)) if (m[1] && !keys.has(m[1])) missing.set(m[1], f);
}
console.log('misused/missing keys:', missing.size);
for (const [k, f] of missing) console.log('   ' + JSON.stringify(k) + '  <- ' + f);