<template>
    <section class="container section">
        <div class="empty-state">
            <span class="label">404</span>
            <h1 class="display-md">Halaman ini tidak ada di AksesKita.</h1>
            <p class="body-copy">Mungkin tautannya salah, atau halaman sudah dipindahkan.</p>
            <div class="inline-actions">
                <RouterLink class="btn btn--primary" to="/">Kembali ke peta</RouterLink>
                <RouterLink class="btn" to="/tentang">Baca panduan</RouterLink>
            </div>
        </div>
    </section>
</template>