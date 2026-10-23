<script setup>
import { computed } from 'vue';
import { useRoute } from 'vue-router';
import { useFacilities } from '../composables/useFacilities';
import { useAuth } from '../composables/useAuth';
import { categoryLabel, formatDate } from '../data/meta';
import AccessibilityPanel from '../components/facilities/AccessibilityPanel.vue';
import CompletenessMeter from '../components/facilities/CompletenessMeter.vue';
import FacilityMap from '../components/map/FacilityMap.vue';
import StatusPill from '../components/common/StatusPill.vue';

const route = useRoute();
const { byId, status } = useFacilities();
// Antrean verifikasi adalah halaman internal: tautannya hanya untuk petugas.
const { user } = useAuth();

const facility = computed(() => byId(route.params.id));
</script>

<template>
    <section v-if="facility" class="container section">
        <div class="stack">
            <nav class="breadcrumb" aria-label="Remah navigasi">
                <RouterLink to="/">Peta fasilitas</RouterLink>
                <span aria-hidden="true">/</span>
                <span>{{ categoryLabel(facility.category) }}</span>
                <span aria-hidden="true">/</span>
                <span>{{ facility.name }}</span>
            </nav>

            <div class="cluster cluster--between">
                <div class="stack--sm">
                    <p class="label">{{ categoryLabel(facility.category) }} · {{ facility.city }}</p>
                    <h1 class="display-lg">{{ facility.name }}</h1>
                    <p class="body-copy">{{ facility.address || 'Alamat jalan belum ada di sumber data.' }}</p>
                    <p class="meta">{{ [facility.village, facility.subdistrict, facility.city].filter(Boolean).join(' · ') }}</p>
                </div>
                <div class="stack--sm">
                    <StatusPill kind="verification" :value="facility.verification" />
                    <span class="meta">Diperbarui {{ formatDate(facility.updatedAt) }}</span>
                </div>
            </div>

            <div class="grid grid--sidebar">
                <div class="stack">
                    <FacilityMap
                        :facilities="[facility]"
                        :center="[facility.lat, facility.lng]"
                        :zoom="16"
                        :selected-id="facility.id"
                        :label="`Peta lokasi ${facility.name}`"
                    />

                    <div class="detail-media">Belum ada foto dokumentasi dari sumber data ini</div>

                    <div class="panel stack--sm">
                        <h2 class="display-sm">Kondisi aksesibilitas</h2>
                        <p class="help">Setiap atribut punya status tersedia, tidak tersedia, atau belum diketahui.</p>
                        <AccessibilityPanel :facility="facility" />
                    </div>

                    <div class="panel stack--sm">
                        <h2 class="display-sm">Catatan aksesibilitas</h2>
                        <p class="body-copy">{{ facility.notes || 'Belum ada catatan dari pemeriksaan.' }}</p>

                        <h3 class="meta">Bukti pemeriksaan</h3>
                        <ul v-if="facility.proofs.length" class="proof-list">
                            <li v-for="proof in facility.proofs" :key="proof.caption" class="proof">
                                <span class="proof__thumb" aria-hidden="true">
                                    {{ proof.type === 'foto' ? 'FOTO' : 'CATATAN' }}
                                </span>
                                <span>
                                    <b style="display: block; font-size: 14px">{{ proof.caption }}</b>
                                    <span class="meta">{{ formatDate(proof.date) }}</span>
                                </span>
                            </li>
                        </ul>
                        <p v-else class="help">Belum ada bukti foto yang dilampirkan untuk fasilitas ini.</p>
                    </div>
                </div>

                <aside class="stack">
                    <div class="panel stack--sm">
                        <h2 class="meta">Indikator kelengkapan data</h2>
                        <CompletenessMeter :facility="facility" />
                        <p class="help">
                            Semakin banyak atribut yang diketahui, semakin dapat diandalkan data ini saat merencanakan
                            perjalanan.
                        </p>
                    </div>

                    <div class="panel stack--sm">
                        <h2 class="meta">Informasi verifikasi</h2>
                        <div class="kv">
                            <div class="kv__row">
                                <span class="kv__label">Status</span>
                                <StatusPill kind="verification" :value="facility.verification" />
                                <span class="kv__note">{{ facility.id }}</span>
                            </div>
                            <div class="kv__row">
                                <span class="kv__label">Data dikumpulkan</span>
                                <span>{{ formatDate(facility.lastChecked) }}</span>
                                <span class="kv__note">{{ facility.id }}</span>
                            </div>
                            <div class="kv__row">
                                <span class="kv__label">Koordinat</span>
                                <span>{{ facility.lat }}, {{ facility.lng }}</span>
                                <span class="kv__note">{{ facility.city }}</span>
                            </div>
                            <div class="kv__row">
                                <span class="kv__label">Sumber data</span>
                                <span>{{ facility.contributor }}</span>
                                <a
                                    v-if="facility.source.url"
                                    class="kv__note"
                                    :href="facility.source.url"
                                    target="_blank"
                                    rel="noopener"
                                >{{ facility.source.ref }}</a>
                                <span v-else class="kv__note">{{ facility.source.ref || 'Tanpa ID sumber' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="panel stack--sm">
                        <h2 class="meta">Ada data yang keliru?</h2>
                        <p class="help">Laporkan koreksi agar verifikator dapat memperbarui catatan fasilitas ini.</p>
                        <RouterLink
                            class="btn btn--primary"
                            :to="{ path: '/kontribusi', query: { mode: 'koreksi', facility: facility.id } }"
                        >
                            Laporkan koreksi
                        </RouterLink>
                        <RouterLink v-if="user" class="btn" to="/verifikasi">Lihat antrean verifikasi</RouterLink>
                    </div>
                </aside>
            </div>
        </div>
    </section>

    <section v-else-if="status === 'memuat'" class="container section">
        <div class="empty-state" role="status">
            <span class="label">Memuat</span>
            <h1 class="display-md">Mengambil detail fasilitas...</h1>
        </div>
    </section>

    <section v-else class="container section">
        <div class="empty-state">
            <span class="label">Tidak ditemukan</span>
            <h1 class="display-md">Fasilitas tidak ditemukan.</h1>
            <p class="body-copy">Periksa kembali tautan, atau kembali ke peta untuk memilih fasilitas lain.</p>
            <RouterLink class="btn btn--primary" to="/">Kembali ke peta</RouterLink>
        </div>
    </section>
</template>