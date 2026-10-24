import { createRouter, createWebHistory } from 'vue-router';
import { useAuth } from '../composables/useAuth';

const routes = [
    // Area pengunjung: layout publik (default) untuk semua route di bawah ini.
    {
        path: '/',
        name: 'home',
        component: () => import('../views/HomeView.vue'),
        meta: { title: 'Peta fasilitas' },
    },
    {
        path: '/fasilitas/:id',
        name: 'facility',
        component: () => import('../views/FacilityDetailView.vue'),
        meta: { title: 'Detail fasilitas' },
    },
    {
        path: '/kontribusi',
        name: 'contribution',
        component: () => import('../views/ContributionView.vue'),
        meta: { title: 'Kontribusi masyarakat' },
    },
    {
        path: '/status',
        name: 'status',
        component: () => import('../views/StatusView.vue'),
        meta: { title: 'Status pengajuan' },
    },
    // Area admin. `layout: 'admin'` memilih AdminLayout di App.vue; penegakan
    // aksesnya tetap di middleware `auth` routes/web.php (guard ini hanya UX).
    {
        path: '/verifikasi',
        name: 'verification',
        component: () => import('../views/VerificationView.vue'),
        meta: { title: 'Dashboard verifikasi', requiresAdmin: true, layout: 'admin' },
    },
    {
        path: '/admin',
        name: 'admin',
        component: () => import('../views/AdminDashboardView.vue'),
        meta: { title: 'Dashboard administrasi', requiresAdmin: true, layout: 'admin' },
    },
    {
        path: '/masuk',
        name: 'login',
        component: () => import('../views/LoginView.vue'),
        meta: { title: 'Masuk admin' },
    },
    {
        path: '/tentang',
        name: 'about',
        component: () => import('../views/AboutView.vue'),
        meta: { title: 'Tentang dan panduan' },
    },
    {
        path: '/:pathMatch(.*)*',
        name: 'not-found',
        component: () => import('../views/NotFoundView.vue'),
        meta: { title: 'Halaman tidak ditemukan' },
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
    scrollBehavior(to, from, savedPosition) {
        // `top` memberi offset untuk header yang menempel (sticky).
        if (to.hash) return { el: to.hash, top: 84 };

        return savedPosition ?? { top: 0 };
    },
});

// Guard ini hanya UX; penegakan sebenarnya ada di middleware `auth` (routes/web.php).
router.beforeEach((to) => {
    const { user } = useAuth();

    if (to.meta.requiresAdmin && !user.value) {
        return { name: 'login', query: { redirect: to.fullPath } };
    }

    if (to.name === 'login' && user.value) return { name: 'admin' };

    return true;
});

router.afterEach((to) => {
    document.title = to.meta.title ? `${to.meta.title} — AksesKita` : 'AksesKita';
});

export default router;