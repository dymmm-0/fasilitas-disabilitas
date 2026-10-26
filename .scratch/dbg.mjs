import { parse } from '@vue/compiler-sfc';
const src = `<template><p>Halo {{ meta.label }} dunia</p></template>`;
const { descriptor } = parse(src, { filename: 'x.vue' });
const walk = (n) => {
  if (n.type === 5) {
    console.log('node.loc', n.loc.start.offset, n.loc.end.offset, JSON.stringify(n.loc.source));
    console.log('content.loc', n.content.loc.start.offset, n.content.loc.end.offset, JSON.stringify(n.content.loc.source));
  }
  for (const c of n.children ?? []) walk(c);
};
walk(descriptor.template.ast);
