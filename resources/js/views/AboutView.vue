<template>
    <section class="container section">
        <div class="stack">
            <div class="section__head" style="max-width: none">
                <p class="label">{{ $t('Tentang AksesKita') }}</p>
                <h1 class="display-lg"><i18n-t keypath="Fasilitas publik seharusnya {a}" tag="span" scope="global"><template #a><em>{{ $t('dapat digunakan semua orang.') }}</em></template></i18n-t></h1>
                <p class="body-copy">{{ $t('AksesKita adalah platform pemetaan dan verifikasi fasilitas publik ramah disabilitas. Kami mengumpulkan informasi yang jarang terdokumentasi — kemiringan ramp, lebar pintu, keberadaan lift, dan toilet aksesibel — lalu memverifikasinya bersama masyarakat.') }}</p>
            </div>

            <div class="grid grid--2">
                <div class="panel stack--sm">
                    <h2 class="display-sm">{{ $t('Masalah yang dijawab') }}</h2>
                    <p class="body-copy">{{ $t('Informasi aksesibilitas sering hanya diketahui setelah tiba di lokasi. Akibatnya banyak orang dengan disabilitas membatalkan rencana, atau terpaksa meminta bantuan orang lain untuk hal sederhana seperti masuk ke gedung layanan publik.') }}</p>
                </div>

                <div class="panel stack--sm">
                    <h2 class="display-sm">{{ $t('Tujuan platform') }}</h2>
                    <ul class="steps">
                        <li class="step">
                            <span class="step__num" aria-hidden="true">✓</span>
                            <span>{{ $t('Menyediakan peta fasilitas publik dengan status aksesibilitas yang jelas.') }}</span>
                        </li>
                        <li class="step">
                            <span class="step__num" aria-hidden="true">✓</span>
                            <span>{{ $t('Membuka jalur kontribusi warga dengan bukti foto dan koordinat.') }}</span>
                        </li>
                        <li class="step">
                            <span class="step__num" aria-hidden="true">✓</span>
                            <span>{{ $t('Menjaga kepercayaan data lewat verifikasi, riwayat, dan indikator kelengkapan.') }}</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="panel stack--sm">
                <h2 class="display-lg">{{ $t('Panduan penggunaan') }}</h2>
                <ol class="steps">
                    <li class="step">
                        <span class="step__num">i.</span>
                        <span>
                            <i18n-t keypath="{a} Gunakan kata kunci, kategori, status verifikasi, dan atribut aksesibilitas untuk mempersempit hasil." tag="span" scope="global"><template #a><b>{{ $t('Cari dan saring di peta.') }}</b></template></i18n-t></span>
                        <span class="step__time">{{ $t('Untuk pengguna') }}</span>
                    </li>
                    <li class="step">
                        <span class="step__num">ii.</span>
                        <span>
                            <i18n-t keypath="{a} Periksa delapan atribut aksesibilitas, catatan pemeriksaan, bukti foto, dan tanggal pemeriksaan terakhir sebelum berangkat." tag="span" scope="global"><template #a><b>{{ $t('Buka detail fasilitas.') }}</b></template></i18n-t></span>
                        <span class="step__time">{{ $t('Untuk pengguna') }}</span>
                    </li>
                    <li class="step">
                        <span class="step__num">iii.</span>
                        <span>
                            <i18n-t keypath="{a} Lengkapi nama, alamat, koordinat lewat peta, checklist aksesibilitas, dan foto bila ada." tag="span" scope="global"><template #a><b>{{ $t('Usulkan fasilitas atau laporkan koreksi.') }}</b></template></i18n-t></span>
                        <span class="step__time">{{ $t('Untuk pengunjung') }}</span>
                    </li>
                    <li class="step">
                        <span class="step__num">iv.</span>
                        <span>
                            <i18n-t keypath="{a} Admin menyetujui, menolak, atau meminta perbaikan; keputusan tercatat pada riwayat." tag="span" scope="global"><template #a><b>{{ $t('Tinjau di dashboard verifikasi.') }}</b></template></i18n-t></span>
                        <span class="step__time">{{ $t('Untuk admin') }}</span>
                    </li>
                </ol>
            </div>

            <div class="grid grid--2">
                <div class="panel stack--sm">
                    <h2 class="display-sm">{{ $t('Mekanisme kontribusi') }}</h2>
                    <p class="body-copy">{{ $t('Kontribusi tidak memerlukan perangkat khusus: cukup satu foto dan catatan singkat. Status aksesibilitas yang belum pasti ditandai "belum diketahui" alih-alih diasumsikan tersedia, agar pengguna tahu batas pengetahuan datanya.') }}</p>
                </div>

                <div class="panel stack--sm">
                    <h2 class="display-sm">{{ $t('Mekanisme verifikasi') }}</h2>
                    <p class="body-copy">{{ $t('Setiap pengajuan melewati pemeriksaan bukti dan koordinat. Fasilitas yang disetujui tampil di peta dengan status terverifikasi, sedangkan yang meragukan ditandai perlu perbaikan atau ditolak dengan alasan yang tersimpan di riwayat.') }}</p>
                </div>
            </div>

            <div class="panel panel--dark stack">
                <p class="label">{{ $t('Kontribusi pada SDG') }}</p>
                <h2 class="display-lg"><i18n-t keypath="Selaras dengan {a}" tag="span" scope="global"><template #a><em>{{ $t('SDG 10 dan SDG 11.') }}</em></template></i18n-t></h2>
                <div class="grid grid--2">
                    <div>
                        <h3 class="display-sm">{{ $t('SDG 10 — Berkurangnya kesenjangan') }}</h3>
                        <p style="color: rgba(245, 241, 234, 0.7); line-height: 1.65">{{ $t('Data aksesibilitas yang terbuka mengurangi hambatan struktural bagi penyandang disabilitas untuk mengakses layanan publik, pendidikan, dan pekerjaan.') }}</p>
                    </div>
                    <div>
                        <h3 class="display-sm">{{ $t('SDG 11 — Kota dan permukiman inklusif') }}</h3>
                        <p style="color: rgba(245, 241, 234, 0.7); line-height: 1.65">{{ $t('Peta aksesibilitas membantu pemerintah daerah memprioritaskan perbaikan fasilitas dan merencanakan ruang publik yang dapat dijangkau semua warga.') }}</p>
                    </div>
                </div>
            </div>

            <div class="notice">
                <b>{{ $t('Catatan sumber data') }}</b>
                <span>{{ $t('Daftar fasilitas berasal dari impor dataset OpenStreetMap Kota Medan ke basis data AksesKita — 1.117 fasilitas, semuanya masih berstatus menunggu verifikasi. Karena itu status aksesibilitasnya belum diperiksa lapangan: sebagian besar masih "belum diketahui", hanya sedikit yang punya catatan pendukung. Akun admin sudah asli dan dilindungi login; antrean pengajuan, keputusan verifikasi, riwayat, dan bukti pendukung tersimpan di basis data AksesKita. Pengelolaan kategori masih berupa data di sisi klien karena kategori mengikuti kolom fasilitas hasil impor.') }}</span>
            </div>

            <div class="inline-actions">
                <RouterLink class="btn btn--primary" to="/">{{ $t('Jelajahi peta fasilitas') }}</RouterLink>
                <RouterLink class="btn" to="/kontribusi">{{ $t('Kirim kontribusi') }}</RouterLink>
            </div>
        </div>
    </section>
</template>