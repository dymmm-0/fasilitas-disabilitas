import { readFileSync, readdirSync } from 'node:fs';
import { join } from 'node:path';

const walk = (d) => readdirSync(d, { withFileTypes: true }).flatMap((e) => (e.isDirectory() ? walk(join(d, e.name)) : [join(d, e.name)]));
const files = walk('resources/js').filter((f) => f.endsWith('.vue'));
const en = JSON.parse(readFileSync('resources/js/i18n/en.json', 'utf8'));
const keys = new Set(Object.keys(en));

// 1. components not using i18n at all
const noI18n = files.filter((f) => !/\$\bt\(|\$t\(|i18n-t|useI18n|\bt\(/.test(readFileSync(f, 'utf8')));

// 2. $t keys used in code but missing from en.json
const missing = new Map();
const reSingle = /\$t\(\s*'((?:[^'\\]|\\.)*)'|\$t\(\s*"((?:[^"\\]|\\.)*)"/g;
const reI18nT = /keypath="((?:[^"\\]|\\.)*)"/g;
for (const f of files) {
    const s = readFileSync(f, 'utf8');
    for (const m of s.matchAll(reSingle)) {
        const k = m[1] ?? m[2];
        if (k && !keys.has(k)) missing.set(k, f);
    }
    for (const m of s.matchAll(reI18nT)) {
        const k = m[1];
        if (k && !keys.has(k)) missing.set(k, f);
    }
}

console.log('== 350 keys, ' + keys.size + ' in en.json');
console.log('== files WITHOUT i18n (' + noI18n.length + ')');
noI18n.forEach((f) => console.log('   ' + f));
console.log('== $t keys MISSING from en.json (' + missing.size + ')');
[...missing].slice(0, 60).forEach(([k, f]) => console.log('   ' + JSON.stringify(k) + '  <- ' + f));
