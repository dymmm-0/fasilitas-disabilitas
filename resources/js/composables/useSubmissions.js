import { request } from './useAuth';

/**
 * Halaman kontribusi: kirim pengajuan ke API. Multipart karena bukti foto ikut
 * diunggah; berkas disimpan server, bukan sekadar namanya. Pembacaan antrean ada
 * di `useVerification`, karena antrean memuat fasilitas dan pengajuan sekaligus.
 */
export function useSubmissions() {
    const submit = async (payload) => {
        const body = new FormData();

        body.append('type', payload.type);
        body.append('facility_name', payload.facilityName);
        body.append('category', payload.category);
        body.append('city', payload.city);
        body.append('submitted_by', payload.submittedBy);
        body.append('contact', payload.contact);
        body.append('summary', payload.summary ?? '');

        if (payload.type === 'usulan') {
            body.append('address', payload.payload.address);
            body.append('latitude', payload.payload.lat);
            body.append('longitude', payload.payload.lng);
            body.append('notes', payload.payload.notes ?? '');

            Object.entries(payload.payload.accessibility).forEach(([key, value]) => {
                body.append(`accessibility[${key}]`, value);
            });
        } else {
            body.append('facility_id', payload.facilityId);
            body.append('field', payload.payload.field);
            body.append('to', payload.payload.to);
            body.append('notes', payload.payload.notes ?? '');
        }

        if (payload.proof) body.append('proof', payload.proof);

        const data = await request('/api/submissions', { method: 'POST', body });

        return data.submission;
    };

    return { submit };
}
