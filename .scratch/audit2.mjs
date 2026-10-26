import { readFileSync, readdirSync } from 'node:fs';
import { join } from 'node:path';

const walk = (d) => readdirSync(d, { withFileTypes: true }).flatMap((e) => (e.isDirectory() ? walk(join(d, e.name)) : [join(d, e.name)]));
const files = walk('resources/js');
const en = JSON.parse(readFileSync('resources/js/i18n/en.json', 'utf8'));
const keys = new Set(Object.keys(en));

// meta.js labels + hints are translation keys
const meta = readFileSync('resources/js/data/meta.js', 'utf8');
const metaKeys = [...meta.matchAll(/(?:label|hint):\s*'((?:[^'\\]|\\.)*)'/g)].map((m) => m[1]);
const missMeta = metaKeys.filter((k) => !keys.has(k));

// dynamic $t(var) usages -> print for manual check
const dyn = [];
for (const f of files.filter((f) => f.endsWith('.vue'))) {
    const s = readFileSync(f, 'utf8');
    for (const m of s.matchAll(/\$t\(\s*([a-zA-Z_$][\w.$]*)\s*\)/g)) dyn.push(`${f}  $t(${m[1]})`);
}

console.log('meta keys missing from en.json:', missMeta.length);
missMeta.forEach((k) => console.log('   ' + JSON.stringify(k)));
console.log('\ndynamic $t(var) usages:');
[...new Set(dyn)].forEach((x) => console.log('   ' + x));
console.log('MARKER-END');
