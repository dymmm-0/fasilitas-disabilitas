import { createI18n } from 'vue-i18n';
import en from './en.json';

// Kunci katalog = teks Indonesia apa adanya. Katalog `id` dibangun dari daftar
// kunci itu (kunci -> kunci) supaya interpolasi `{a}` tetap jalan tanpa berkas
// id.json yang harus disinkronkan tiap kali teks baru ditambahkan.
const id = Object.fromEntries(Object.keys(en).map((key) => [key, key]));

export const LOCALES = ['id', 'en'];
const STORAGE_KEY = 'akseskita:locale';

const stored = typeof localStorage === 'undefined' ? null : localStorage.getItem(STORAGE_KEY);
const locale = LOCALES.includes(stored) ? stored : 'en';

const i18n = createI18n({
    legacy: false,
    locale,
    fallbackLocale: 'id',
    messages: { id, en },
    // Kunci yang belum diterjemahkan jatuh ke katalog `id` (teks Indonesia asli);
    // jangan spam konsol karena itu perilaku normal.
    missingWarn: false,
    fallbackWarn: false,
    warnHtmlMessage: false,
});

/** Untuk kode di luar komponen (composables, router) yang tidak punya $t. */
export const t = (key, params) => i18n.global.t(key, params);

/** Ganti bahasa, ingat pilihan pengguna, dan samakan atribut lang dokumen. */
export const setLocale = (value) => {
    if (!LOCALES.includes(value)) return;

    i18n.global.locale.value = value;
    document.documentElement.lang = value;
    localStorage.setItem(STORAGE_KEY, value);
};

document.documentElement.lang = locale;

export default i18n;