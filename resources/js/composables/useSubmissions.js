import { computed, reactive, ref } from 'vue';
import { SUBMISSIONS, VERIFICATION_HISTORY, nextSubmissionId } from '../data/submissions';
import { SUBMISSION_STATUSES } from '../data/meta';
import { useFacilities } from './useFacilities';
import { useToast } from './useToast';

const submissions = ref([...SUBMISSIONS]);
const history = ref([...VERIFICATION_HISTORY]);

const queueFilters = reactive({ query: '', type: 'all', status: 'all' });

const queue = computed(() => {
    const q = queueFilters.query.trim().toLowerCase();

    return submissions.value.filter((s) => {
        if (queueFilters.type !== 'all' && s.type !== queueFilters.type) return false;
        if (queueFilters.status !== 'all' && s.status !== queueFilters.status) return false;
        if (!q) return true;

        return [s.id, s.facilityName, s.submittedBy, s.city].join(' ').toLowerCase().includes(q);
    });
});

const counts = computed(() => ({
    total: submissions.value.length,
    usulan: submissions.value.filter((s) => s.type === 'usulan').length,
    koreksi: submissions.value.filter((s) => s.type === 'koreksi').length,
    menunggu: submissions.value.filter((s) => s.status === 'menunggu').length,
    selesai: submissions.value.filter((s) => s.status === 'disetujui' || s.status === 'ditolak').length,
}));

const now = () => new Date().toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'short' });

/** Dipakai halaman kontribusi: pengajuan masuk antrean sebagai simulasi. */
const submit = (payload) => {
    const id = nextSubmissionId(submissions.value);

    submissions.value = [
        { ...payload, id, submittedAt: new Date().toISOString().slice(0, 10), status: 'menunggu' },
        ...submissions.value,
    ];

    return id;
};

const decide = (id, action, note = '') => {
    const submission = submissions.value.find((s) => s.id === id);
    if (!submission) return;

    submission.status = action;
    submission.decidedBy = 'Hendra Saputra';

    const facilities = useFacilities();
    const toast = useToast();

    if (action === 'disetujui') {
        if (submission.type === 'usulan') {
            const facilityId = facilities.addFacility({
                ...submission.payload,
                name: submission.facilityName,
                category: submission.category,
                city: submission.city,
                submittedBy: submission.submittedBy,
            });
            submission.facilityId = facilityId;
            toast.push('Pengajuan disetujui', `${submission.facilityName} ditambahkan ke peta sebagai ${facilityId}.`);
        } else if (facilities.applyCorrection(submission.facilityId, submission.payload.field, submission.payload.to)) {
            toast.push('Koreksi disetujui', `Data ${submission.facilityName} diperbarui.`);
        } else {
            // Antrean demonstrasi menyimpan id lama (fas-00x) yang tidak ada di data fasilitas.
            toast.push('Koreksi tidak diterapkan', `Fasilitas ${submission.facilityId} tidak ditemukan pada data fasilitas.`);
        }
    } else if (action === 'ditolak') {
        toast.push('Pengajuan ditolak', `${id} ditandai ditolak dan tidak masuk ke peta.`);
    } else {
        toast.push('Perbaikan diminta', `Kontributor ${submission.submittedBy} diminta melengkapi bukti.`);
    }

    history.value = [
        { id, action, by: 'Hendra Saputra', at: now(), note: note || SUBMISSION_STATUSES[action].label },
        ...history.value,
    ];
};

export function useSubmissions() {
    return { submissions, queue, counts, queueFilters, history, submit, decide, byId: (id) => submissions.value.find((s) => s.id === id) ?? null };
}