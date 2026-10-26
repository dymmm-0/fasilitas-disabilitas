<template>
    <section class="container section">
        <div class="empty-state">
            <span class="label">404</span>
            <h1 class="display-md">{{ $t('Halaman ini tidak ada di AksesKita.') }}</h1>
            <p class="body-copy">{{ $t('Mungkin tautannya salah, atau halaman sudah dipindahkan.') }}</p>
            <div class="inline-actions">
                <RouterLink class="btn btn--primary" to="/">{{ $t('Kembali ke peta') }}</RouterLink>
                <RouterLink class="btn" to="/tentang">{{ $t('Baca panduan') }}</RouterLink>
            </div>
        </div>
    </section>
</template>