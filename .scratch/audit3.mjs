import { readFileSync, readdirSync } from 'node:fs';
import { join, extname } from 'node:path';

const walk = (d) => readdirSync(d, { withFileTypes: true }).flatMap((e) => (e.isDirectory() ? walk(join(d, e.name)) : [join(d, e.name)]));
const files = walk('resources/js').filter((f) => ['.js', '.vue'].includes(extname(f)));
const en = JSON.parse(readFileSync('resources/js/i18n/en.json', 'utf8'));
const keys = new Set(Object.keys(en));

// Any quoted string passed to $t via a variable must be a `label`/`hint`/`title`
// property somewhere. Collect every such literal in the codebase.
const candidates = new Map();
for (const f of files) {
    const s = readFileSync(f, 'utf8');
    for (const m of s.matchAll(/\b(?:label|hint|title|text)\s*:\s*'((?:[^'\\]|\\.)*)'/g)) candidates.set(m[1], f);
    for (const m of s.matchAll(/\b(?:label|hint|title|text)\s*:\s*"((?:[^"\\]|\\.)*)"/g)) candidates.set(m[1], f);
}
const missing = [...candidates].filter(([k]) => !keys.has(k) && /[a-zA-Z]/.test(k));
console.log('candidate dynamic labels:', candidates.size, '| missing from en.json:', missing.length);
missing.forEach(([k, f]) => console.log('   ' + JSON.stringify(k) + '  <- ' + f));