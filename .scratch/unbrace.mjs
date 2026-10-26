// Buang artefak `{{ {{ ... }} }}` hasil codemod lama; cukup sekali pakai.
import { readFileSync, writeFileSync } from 'node:fs';
import { readdir } from 'node:fs/promises';
import { join } from 'node:path';

const files = [];
const walk = async (dir) => {
    for (const e of await readdir(dir, { withFileTypes: true })) {
        const p = join(dir, e.name);
        if (e.isDirectory()) await walk(p);
        else if (e.name.endsWith('.vue')) files.push(p);
    }
};
await walk('resources/js');
for (const f of files) {
    const src = readFileSync(f, 'utf8');
    const fixed = src.replace(/\{\{ \{\{ (.+?) \}\} \}\}/g, '{{ $1 }}');
    if (fixed !== src) { writeFileSync(f, fixed); console.log('fixed', f); }
}
