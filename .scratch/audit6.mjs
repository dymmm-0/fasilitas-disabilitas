// Cari komponen yang memakai t(...) di <script> tapi tidak mengimpor/mendefinisikan t.
import { readFileSync, readdirSync } from 'node:fs';
import { join } from 'node:path';

const walk = (d) => readdirSync(d, { withFileTypes: true }).flatMap((e) => (e.isDirectory() ? walk(join(d, e.name)) : [join(d, e.name)]));
const files = walk('resources/js').filter((f) => f.endsWith('.vue'));
for (const f of files) {
    const s = readFileSync(f, 'utf8');
    const script = (s.match(/<script setup[^>]*>([\s\S]*?)<\/script>/) || [])[1] ?? '';
    const uses = [...script.matchAll(/(?<![\w.$])t\(/g)].length;
    if (!uses) continue;
    const defined = /\bimport\b[^\n]*\bt\b[^\n]*from|\bconst\s+t\s*=|\buseI18n\(/.test(script) || /\$t|i18n/.test(script);
    if (!defined) console.log('UNDEFINED t(): ' + f + '  (uses: ' + uses + ')');
}