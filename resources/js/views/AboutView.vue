<template>
    <section class="container section">
        <div class="stack">
            <div class="section__head" style="max-width: none">
                <p class="label">Tentang AksesKita</p>
                <h1 class="display-lg">Fasilitas publik seharusnya <em>dapat digunakan semua orang.</em></h1>
                <p class="body-copy">
                    AksesKita adalah platform pemetaan dan verifikasi fasilitas publik ramah disabilitas. Kami
                    mengumpulkan informasi yang jarang terdokumentasi — kemiringan ramp, lebar pintu, keberadaan lift,
                    dan toilet aksesibel — lalu memverifikasinya bersama masyarakat.
                </p>
            </div>

            <div class="grid grid--2">
                <div class="panel stack--sm">
                    <h2 class="display-sm">Masalah yang dijawab</h2>
                    <p class="body-copy">
                        Informasi aksesibilitas sering hanya diketahui setelah tiba di lokasi. Akibatnya banyak orang
                        dengan disabilitas membatalkan rencana, atau terpaksa meminta bantuan orang lain untuk hal
                        sederhana seperti masuk ke gedung layanan publik.
                    </p>
                </div>

                <div class="panel stack--sm">
                    <h2 class="display-sm">Tujuan platform</h2>
                    <ul class="steps">
                        <li class="step">
                            <span class="step__num" aria-hidden="true">✓</span>
                            <span>Menyediakan peta fasilitas publik dengan status aksesibilitas yang jelas.</span>
                        </li>
                        <li class="step">
                            <span class="step__num" aria-hidden="true">✓</span>
                            <span>Membuka jalur kontribusi warga dengan bukti foto dan koordinat.</span>
                        </li>
                        <li class="step">
                            <span class="step__num" aria-hidden="true">✓</span>
                            <span>Menjaga kepercayaan data lewat verifikasi, riwayat, dan indikator kelengkapan.</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="panel stack--sm">
                <h2 class="display-lg">Panduan penggunaan</h2>
                <ol class="steps">
                    <li class="step">
                        <span class="step__num">i.</span>
                        <span>
                            <b>Cari dan saring di peta.</b> Gunakan kata kunci, kategori, status verifikasi, dan
                            atribut aksesibilitas untuk mempersempit hasil.
                        </span>
                        <span class="step__time">Untuk pengguna</span>
                    </li>
                    <li class="step">
                        <span class="step__num">ii.</span>
                        <span>
                            <b>Buka detail fasilitas.</b> Periksa lima atribut aksesibilitas, catatan pemeriksaan,
                            bukti foto, dan tanggal pemeriksaan terakhir sebelum berangkat.
                        </span>
                        <span class="step__time">Untuk pengguna</span>
                    </li>
                    <li class="step">
                        <span class="step__num">iii.</span>
                        <span>
                            <b>Usulkan fasilitas atau laporkan koreksi.</b> Lengkapi nama, alamat, koordinat lewat peta,
                            checklist aksesibilitas, dan foto bila ada.
                        </span>
                        <span class="step__time">Untuk pengunjung</span>
                    </li>
                    <li class="step">
                        <span class="step__num">iv.</span>
                        <span>
                            <b>Tinjau di dashboard verifikasi.</b> Admin menyetujui, menolak, atau meminta
                            perbaikan; keputusan tercatat pada riwayat.
                        </span>
                        <span class="step__time">Untuk admin</span>
                    </li>
                </ol>
            </div>

            <div class="grid grid--2">
                <div class="panel stack--sm">
                    <h2 class="display-sm">Mekanisme kontribusi</h2>
                    <p class="body-copy">
                        Kontribusi tidak memerlukan perangkat khusus: cukup satu foto dan catatan singkat. Status
                        aksesibilitas yang belum pasti ditandai "belum diketahui" alih-alih diasumsikan tersedia, agar
                        pengguna tahu batas pengetahuan datanya.
                    </p>
                </div>

                <div class="panel stack--sm">
                    <h2 class="display-sm">Mekanisme verifikasi</h2>
                    <p class="body-copy">
                        Setiap pengajuan melewati pemeriksaan bukti dan koordinat. Fasilitas yang disetujui tampil di
                        peta dengan status terverifikasi, sedangkan yang meragukan ditandai perlu perbaikan atau
                        ditolak dengan alasan yang tersimpan di riwayat.
                    </p>
                </div>
            </div>

            <div class="panel panel--dark stack">
                <p class="label">Kontribusi pada SDG</p>
                <h2 class="display-lg">Selaras dengan <em>SDG 10 dan SDG 11.</em></h2>
                <div class="grid grid--2">
                    <div>
                        <h3 class="display-sm">SDG 10 — Berkurangnya kesenjangan</h3>
                        <p style="color: rgba(245, 241, 234, 0.7); line-height: 1.65">
                            Data aksesibilitas yang terbuka mengurangi hambatan struktural bagi penyandang disabilitas
                            untuk mengakses layanan publik, pendidikan, dan pekerjaan.
                        </p>
                    </div>
                    <div>
                        <h3 class="display-sm">SDG 11 — Kota dan permukiman inklusif</h3>
                        <p style="color: rgba(245, 241, 234, 0.7); line-height: 1.65">
                            Peta aksesibilitas membantu pemerintah daerah memprioritaskan perbaikan fasilitas dan
                            merencanakan ruang publik yang dapat dijangkau semua warga.
                        </p>
                    </div>
                </div>
            </div>

            <div class="notice">
                <b>Catatan sumber data</b>
                <span>
                    Daftar fasilitas berasal dari impor dataset OpenStreetMap Kota Medan ke basis data AksesKita.
                    Status aksesibilitasnya belum diverifikasi lapangan: sebagian besar masih "belum diketahui".
                    Pengguna, pengajuan, dan verifikasi masih berupa data dummy demonstrasi tanpa autentikasi.
                </span>
            </div>

            <div class="inline-actions">
                <RouterLink class="btn btn--primary" to="/">Jelajahi peta fasilitas</RouterLink>
                <RouterLink class="btn" to="/kontribusi">Kirim kontribusi</RouterLink>
            </div>
        </div>
    </section>
</template>