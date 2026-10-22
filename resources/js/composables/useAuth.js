import { ref } from 'vue';

/**
 * Sesi admin. Pengunjung (guest) tidak punya akun — kontribusi cukup mengisi
 * nama pada formulir, jadi tidak ada state pengguna untuk guest.
 */
const user = ref(null);

let loaded = null;

/**
 * Token CSRF dari cookie `XSRF-TOKEN` — selalu versi terbaru, termasuk setelah
 * login mengganti sesi. Meta tag akan basi karena SPA tidak memuat ulang halaman.
 */
const csrfToken = () => {
    const cookie = document.cookie.split('; ').find((row) => row.startsWith('XSRF-TOKEN='));

    return cookie ? decodeURIComponent(cookie.slice('XSRF-TOKEN='.length)) : '';
};

/** Fetch kecil: cookie sesi + token CSRF. */
export const request = async (path, options = {}) => {
    const response = await fetch(path, {
        credentials: 'same-origin',
        ...options,
        headers: {
            Accept: 'application/json',
            'X-XSRF-TOKEN': csrfToken(),
            ...(options.body && !(options.body instanceof FormData) ? { 'Content-Type': 'application/json' } : {}),
            ...options.headers,
        },
    });

    const data = response.status === 204 ? null : await response.json().catch(() => null);

    if (!response.ok) {
        const error = new Error(data?.message ?? 'Permintaan gagal.');
        error.status = response.status;
        error.errors = data?.errors ?? {};
        throw error;
    }

    return data;
};

/** Sekali per muat halaman; router guard membaca `user` setelah ini. */
const load = () =>
    (loaded ??= request('/api/me')
        .then((data) => (user.value = data?.user ?? null))
        .catch(() => null));

const login = async (email, password) => {
    const data = await request('/api/login', {
        method: 'POST',
        body: JSON.stringify({ email, password }),
    });

    user.value = data.user;
    loaded = Promise.resolve(user.value);

    return user.value;
};

const logout = async () => {
    await request('/api/logout', { method: 'POST' });

    user.value = null;
    loaded = Promise.resolve(null);
};

export function useAuth() {
    return { user, load, login, logout };
}