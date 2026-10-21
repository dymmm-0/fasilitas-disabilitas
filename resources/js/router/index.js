import { createRouter, createWebHistory } from 'vue-router';

const routes = [
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
        path: '/verifikasi',
        name: 'verification',
        component: () => import('../views/VerificationView.vue'),
        meta: { title: 'Dashboard verifikasi' },
    },
    {
        path: '/admin',
        name: 'admin',
        component: () => import('../views/AdminDashboardView.vue'),
        meta: { title: 'Dashboard administrasi' },
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
        return savedPosition ?? { top: 0 };
    },
});

router.afterEach((to) => {
    document.title = to.meta.title ? `${to.meta.title} — AksesKita` : 'AksesKita';
});

export default router;