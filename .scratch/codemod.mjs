/**
 * Codemod: bungkus teks Indonesia di template Vue dengan $t(...) agar bisa
 * diterjemahkan katalog i18n. Kunci katalog = teks Indonesia itu sendiri, jadi
 * tidak ada penamaan kunci manual dan id.json selalu sinkron dengan sumbernya.
 *
 * Pakai: node .scratch/codemod.mjs [--dry] [file.vue ...]
 */
import { readFileSync, writeFileSync } from 'node:fs';
import { readdir } from 'node:fs/promises';
import { join } from 'node:path';
import { pathToFileURL } from 'node:url';
import { parse } from '@vue/compiler-sfc';

const INLINE = new Set(['em', 'b', 'strong', 'i']);
const ATTRS = ['aria-label', 'placeholder', 'title', 'alt'];
const EXACT_SKIP = new Set([
    'Akses', 'Kita', 'OpenStreetMap', 'Admin', 'Dashboard', 'Latitude', 'Longitude',
    'Fundev', 'SDG', 'JPG', 'PNG', 'WebP', 'MB', 'HTTPS', 'GPS', 'OSM', 'Medan',
]);

const norm = (s) => s.replace(/\s+/g, ' ').trim();

function translatable(text) {
    // Placeholder diabaikan: "{a} · {b}" bukan teks yang perlu diterjemahkan.
    const t = norm(text).replace(/\{[a-z0-9]+\}/gi, ' ').replace(/\s+/g, ' ').trim();
    if (!t) return false;
    if (EXACT_SKIP.has(t)) return false;
    if (/^(https?:|\/\/|\/|mailto:|[\w.+-]+@)/.test(t)) return false;
    if (!/[A-Za-z]/.test(t)) return false;
    if ((t.match(/[A-Za-z]/g) ?? []).length < 3) return false;
    if (/^[a-z][a-z0-9_\-.]*$/.test(t)) return false; // token internal: kelas, nilai enum
    return true;
}

const jsStr = (text, inAttr) => {
    let s = norm(text).replace(/\\/g, '\\\\').replace(/'/g, "\\'");
    if (inAttr) s = s.replace(/"/g, '&quot;');
    return `'${s}'`;
};

const tCall = (text, inAttr = false) => `$t(${jsStr(text, inAttr)})`;

/** `kode ${a} lalu ${b}` -> $t('kode {0} lalu {1}', [a, b]) */
function templateLiteralToCall(raw) {
    const parts = [];
    const params = [];
    let current = '';
    for (let i = 0; i < raw.length; i += 1) {
        if (raw[i] === '$' && raw[i + 1] === '{') {
            parts.push(current);
            current = '';
            i += 2;
            let expr = '';
            let depth = 1;
            while (i < raw.length) {
                if (raw[i] === '{') depth += 1;
                if (raw[i] === '}') {
                    depth -= 1;
                    if (depth === 0) break;
                }
                expr += raw[i];
                i += 1;
            }
            params.push(expr.trim());
            parts.push(`{${params.length - 1}}`);
            continue;
        }
        current += raw[i];
    }
    parts.push(current);

    const message = parts.join('').replace(/\s+/g, ' ').trim();
    if (!translatable(message)) return null;
    return `$t(${jsStr(message)}, [${params.join(', ')}])`;
}

/** Label dari `meta.js` yang dirender: ubah menjadi $t(...) supaya ikut diterjemahkan. */
function wrapDynamicLabels(src) {
    return src
        .replace(/(?<!\$t\()(\b[A-Z][A-Z0-9_]*\[[^\]]*\]\.(?:label|hint)\b)/g, (m) => `$t(${m})`)
        .replace(/(?<![\w.$])([A-Z][\w]*\.[a-z][\w]*\.(?:label|hint)\b)/g, (m) => `$t(${m})`)
        .replace(/(?<!\$t\()(\b[a-z][\w]*\.[a-z][\w]*\.(?:label|hint)\b)/g, (m) => `$t(${m})`)
        .replace(/(?<![\w.$])([a-z][\w]*\.(?:label|hint)\b)/g, (m) => `$t(${m})`)
        .replace(/(?<!\$t\()(\bstatusMeta\([^()]*\)\.(?:label|hint)\b)/g, (m) => `$t(${m})`);
}

const closeQuote = (src, start, quote) => {
    for (let i = start + 1; i < src.length; i += 1) {
        if (src[i] === '\\') i += 1;
        else if (src[i] === quote) return i;
    }
    return src.length - 1;
};

/** Lewati satu panggilan $t(...) yang sudah ada (beserta argumennya). */
const skipCall = (src, start) => {
    let depth = 0;
    for (let i = start + 2; i < src.length; i += 1) {
        const ch = src[i];
        if (ch === "'" || ch === '"' || ch === '`') i = closeQuote(src, i, ch);
        else if (ch === '(' || ch === '[') depth += 1;
        else if (ch === ')' || ch === ']') {
            depth -= 1;
            if (depth === 0) return i + 1;
        }
    }
    return src.length;
};

/** Ganti literal teks Indonesia di dalam sebuah ekspresi dengan $t(...). */
function rewriteExprLiterals(src) {
    let out = '';
    let i = 0;
    while (i < src.length) {
        if (src.startsWith('$t(', i)) {
            const end = skipCall(src, i);
            out += src.slice(i, end);
            i = end;
            continue;
        }
        const ch = src[i];
        if (ch === "'" || ch === '"') {
            const end = closeQuote(src, i, ch);
            const inner = src.slice(i + 1, end);
            out += translatable(inner) ? tCall(inner) : src.slice(i, end + 1);
            i = end + 1;
            continue;
        }
        if (ch === '`') {
            const end = closeQuote(src, i, '`');
            const call = templateLiteralToCall(src.slice(i + 1, end));
            out += call ?? src.slice(i, end + 1);
            i = end + 1;
            continue;
        }
        out += ch;
        i += 1;
    }
    return out;
}

function transformExpr(raw) {
    const e = raw.trim();
    if (!e || e.includes('$t(')) return null;

    const quoted = e.match(/^'([^'\\]*)'$/) ?? e.match(/^"([^"\\]*)"$/);
    if (quoted) return translatable(quoted[1]) ? tCall(quoted[1]) : null;

    if (e.startsWith('`') && e.endsWith('`') && closeQuote(e, 0, '`') === e.length - 1) {
        return templateLiteralToCall(e.slice(1, -1));
    }

    const out = rewriteExprLiterals(wrapDynamicLabels(e));
    return out === e ? null : out;
}

/** Elemen campuran (teks + <em>/<b> atau interpolasi) -> <i18n-t> dengan slot. */
function mixedTransform(node) {
    const kids = node.children.filter((c) => !(c.type === 2 && !c.content.trim()));
    if (kids.length < 2) return null;

    const pieces = [];
    const slots = [];
    for (const child of kids) {
        if (child.type === 3) return null;
        if (child.type === 2) {
            const text = child.content.replace(/\s+/g, ' ');
            if (EXACT_SKIP.has(norm(text))) return null; // mis. brand "Akses" + <em>Kita</em>
            pieces.push(text);
            continue;
        }
        if (child.type === 5) {
            const slot = String.fromCharCode(97 + slots.length);
            slots.push({ name: slot, node: child });
            pieces.push(`{${slot}}`);
            continue;
        }
        if (
            child.type === 1
            && INLINE.has(child.tag)
            && child.children.length === 1
            && child.children[0].type === 2
            && !EXACT_SKIP.has(norm(child.children[0].content))
        ) {
            const slot = String.fromCharCode(97 + slots.length);
            slots.push({ name: slot, node: child });
            pieces.push(`{${slot}}`);
            continue;
        }
        return null;
    }

    if (!slots.length) return null;

    const keypath = norm(pieces.join(''));
    if (!translatable(keypath)) return null;

    const slotTpl = slots
        .map(({ name, node: child }) => {
            if (child.type === 5) {
                const out = transformExpr(child.content.content);
                return `<template #${name}>{{ ${out ?? child.content.content.trim()} }}</template>`;
            }
            const html = child.loc.source.replace(
                />([^<]*)</,
                (m, text) => (translatable(text) ? `>{{ ${tCall(text)} }}<` : m),
            );
            return `<template #${name}>${html.trim()}</template>`;
        })
        .join('');

    return {
        start: kids[0].loc.start.offset,
        end: kids[kids.length - 1].loc.end.offset,
        text: `<i18n-t keypath=${JSON.stringify(keypath)} tag="span" scope="global">${slotTpl}</i18n-t>`,
        keypath,
    };
}

function collectKeys(text, keys) {
    for (const m of text.matchAll(/\$t\(\s*'((?:[^'\\]|\\.)*)'/g)) keys.add(m[1].replace(/\\'/g, "'"));
}

function walk(node, edits, keys) {
    if (!node || typeof node !== 'object') return;

    if (node.type === 1) {
        if (node.tag === 'i18n-t' || node.tag === 'script' || node.tag === 'style') return;

        const mixed = mixedTransform(node);
        if (mixed) {
            edits.push(mixed);
            keys.add(mixed.keypath);
            collectKeys(mixed.text, keys);
            return;
        }

        for (const prop of node.props ?? []) {
            if (prop.type === 6 && ATTRS.includes(prop.name) && prop.value && translatable(prop.value.content)) {
                edits.push({
                    start: prop.loc.start.offset,
                    end: prop.loc.end.offset,
                    text: `:${prop.name}="${tCall(prop.value.content, true)}"`,
                });
                keys.add(norm(prop.value.content));
            }
            if (prop.type === 7 && prop.exp?.type === 4) {
                const e = prop.exp.content.trim();
                const tpl = e.startsWith('`') && e.endsWith('`') && closeQuote(e, 0, '`') === e.length - 1;
                const call = tpl ? templateLiteralToCall(e.slice(1, -1)) : null;
                if (call) {
                    edits.push({ start: prop.exp.loc.start.offset, end: prop.exp.loc.end.offset, text: call });
                    collectKeys(call, keys);
                }
            }
        }
    }

    if (node.type === 2 && translatable(node.content)) {
        const text = norm(node.content);
        edits.push({ start: node.loc.start.offset, end: node.loc.end.offset, text: `{{ ${tCall(text)} }}` });
        keys.add(text);
        return;
    }

    if (node.type === 5) {
        const out = transformExpr(node.content.content);
        if (out) {
            edits.push({ start: node.loc.start.offset, end: node.loc.end.offset, text: `{{ ${out} }}` });
            collectKeys(out, keys);
        }
        return;
    }

    for (const child of node.children ?? []) walk(child, edits, keys);
}

export function processFile(source) {
    const { descriptor, errors } = parse(source, { filename: 'x.vue' });
    if (errors.length || !descriptor.template?.ast) return { code: source, keys: new Set() };

    const edits = [];
    const keys = new Set();
    walk(descriptor.template.ast, edits, keys);

    const merged = edits
        .sort((a, b) => b.start - a.start)
        .filter((edit, index, arr) => index === 0 || edit.end <= arr[index - 1].start);

    let code = source;
    for (const edit of merged) code = code.slice(0, edit.start) + edit.text + code.slice(edit.end);
    return { code, keys };
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
    const allKeys = new Set();

    for (const file of targets) {
        const source = readFileSync(file, 'utf8');
        const { code, keys } = processFile(source);
        keys.forEach((k) => allKeys.add(k));
        if (code === source) continue;
        if (dry) console.log(`\n===== ${file} =====\n${code}`);
        else {
            writeFileSync(file, code);
            console.log(`updated ${file}`);
        }
    }
    console.log(`\nkeys: ${allKeys.size}`);
}