import { createSSRApp } from 'vue';
import { renderToString } from 'vue/server-renderer';
import { createI18n } from 'vue-i18n';

const i18n = createI18n({
  legacy: false, locale: 'en', warnHtmlMessage: false,
  messages: { en: {
    hi: 'Find facilities <em>everyone can access.</em>',
    name: '{n} facilities',
    combo: 'Category {c}. Status {s}',
  } },
});
const App = { template: '<div><i18n-t keypath="hi" tag="h1" class="display-xl" />|<i18n-t keypath="combo" tag="span" scope="global"><template #c>Transport</template><template #s>Verified</template></i18n-t></div>' };
const app = createSSRApp(App); app.use(i18n);
console.log('OUT:', await renderToString(app));
console.log('param:', i18n.global.t('name', { n: 5 }));
console.log('raw-id-key:', i18n.global.t('Transportasi'));
