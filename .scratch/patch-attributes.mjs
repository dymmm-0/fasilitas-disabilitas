// Terjemahan Inggris untuk refactor 8 atribut aksesibilitas. Idempoten.
import { readFileSync, writeFileSync } from 'node:fs';

const OUT = 'resources/js/i18n/en.json';
const en = JSON.parse(readFileSync(OUT, 'utf8'));

// Kunci baru/berubah: label atribut, petunjuk indikator baru, dan prosa yang menyebut jumlah atribut.
const SET = {
    'Ramp / Jalur Landai': 'Accessible Ramp',
    'Toilet Aksesibel': 'Accessible Toilet',
    'Lift Aksesibel': 'Accessible Elevator',
    'Jalur Pejalan Kaki Aksesibel': 'Accessible Pedestrian Pathway',
    'Pintu Masuk Aksesibel': 'Accessible Entrance',
    'Guiding Block / Jalur Pemandu Tunanetra': 'Tactile Paving / Guidance Path',
    'Informasi dan Petunjuk Aksesibel': 'Accessible Information and Signage',
    'Parkir Khusus Disabilitas': 'Accessible Parking',
    'Ubin pemandu taktil yang mengarahkan tunanetra di area publik.': 'Tactile guiding tiles that direct visually impaired people in public areas.',
    'Papan informasi dengan huruf jelas, kontras, dan braille.': 'Information boards with clear, high-contrast lettering and braille.',
    'Ruang parkir khusus dengan akses datar ke pintu masuk.': 'Dedicated parking bay with level access to the entrance.',
    '{a} Periksa delapan atribut aksesibilitas, catatan pemeriksaan, bukti foto, dan tanggal pemeriksaan terakhir sebelum berangkat.':
        '{a} Check the eight accessibility attributes, inspection notes, photo evidence, and last inspection date before departing.',
    'AksesKita memetakan ramp, toilet aksesibel, lift, jalur pejalan kaki aksesibel, dan fasilitas pendukung lain di fasilitas publik — lalu memverifikasinya bersama masyarakat agar keputusan berkegiatan tidak lagi menebak-nebak.':
        'AksesKita maps ramps, accessible toilets, elevators, accessible pedestrian pathways, and other supporting facilities in public facilities — then verifies them together with the community so that activity decisions are no longer guesswork.',
};

// Kunci yatim: label/indikator lama dan prosa lama yang sudah tidak dipakai kode.
const REMOVE = [
    'Akses masuk',
    'Ramp / jalur landai',
    'Toilet aksesibel',
    'Lift',
    'Jalur kursi roda',
    '{a} Periksa lima atribut aksesibilitas, catatan pemeriksaan, bukti foto, dan tanggal pemeriksaan terakhir sebelum berangkat.',
    'AksesKita memetakan ramp, toilet aksesibel, lift, dan jalur kursi roda di fasilitas publik — lalu memverifikasinya bersama masyarakat agar keputusan berkegiatan tidak lagi menebak-nebak.',
];

const out = { ...en, ...SET };
for (const key of REMOVE) delete out[key];

const sorted = Object.fromEntries(Object.keys(out).sort((a, b) => a.localeCompare(b, 'id')).map((k) => [k, out[k]]));
writeFileSync(OUT, `${JSON.stringify(sorted, null, 4)}\n`);
console.log(`${Object.keys(en).length} -> ${Object.keys(sorted).length} kunci`);