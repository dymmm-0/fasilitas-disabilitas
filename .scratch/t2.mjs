import { createSSRApp, h } from 'vue';
import { renderToString } from 'vue/server-renderer';
import { createI18n } from 'vue-i18n';
const i18n = createI18n({ legacy:false, locale:'en', warnHtmlMessage:false, messages:{ en:{
  head: 'Find facilities {accent}',
  accent: 'everyone can access.',
  step: '{lead} Name, address and photos are sent through the form.',
  lead: 'Residents propose.',
}}});
const App = { template: `
<h1 class="display-xl"><i18n-t keypath="head" tag="span"><template #accent><em>{{ $t('accent') }}</em></template></i18n-t></h1>
<span><i18n-t keypath="step" tag="span"><template #lead><b>{{ $t('lead') }}</b></template></i18n-t></span>` };
const app = createSSRApp(App); app.use(i18n);
console.log(await renderToString(app));
