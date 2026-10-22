import { ATTRIBUTES } from './meta';

/**
 * Turunan dari fasilitas yang dikirim `/api/facilities`. Isi fasilitas berasal dari
 * database (lihat `php artisan facilities:import`), bukan lagi data dummy di klien.
 */

/** Persentase atribut aksesibilitas yang sudah diketahui (0–100). */
export const completeness = (facility) => {
    const known = ATTRIBUTES.filter((a) => facility.accessibility[a.id] !== 'belum').length;

    return Math.round((known / ATTRIBUTES.length) * 100);
};

export const knownAttributeCount = (facility) =>
    ATTRIBUTES.filter((a) => facility.accessibility[a.id] !== 'belum').length;