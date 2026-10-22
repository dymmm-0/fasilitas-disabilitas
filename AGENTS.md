# AGENTS.md

AksesKita — Laravel 13 + Vue 3 SPA for mapping/verifying disability-friendly public facilities. Facilities are real: 1.117 rows imported from the Medan OSM dataset into the `facilities` table and served by `GET /api/facilities`. The verification queue at `GET /api/verification-queue` merges facilities that still need checking (`verification_status` = `pending`/`needs_repair`) with citizen submissions, so dataset rows and community reports are decided from the same page. Submissions, verification decisions, decision history, and photo proofs are stored in the `submissions`, `verification_events`, and `submission_proofs` tables (`SubmissionController`, `VerificationQueueController`, `FacilityController::verify`, session auth). Category management in the admin dashboard is still client-side counts.

## Commands

- `composer run dev` — runs `artisan serve` (:8000), `queue:listen`, and Vite (HMR) together. Use this instead of starting the server and Vite separately.
- `npm run build` — required after any `resources/js` or `resources/css` change if you are serving assets without Vite HMR.
- `php artisan test` — PHPUnit 12. Single test: `php artisan test --filter=AksesKitaTest` or `php artisan test tests/Feature/AksesKitaTest.php`.
- `vendor/bin/pint` — format PHP; add `--test` to check only. No `pint.json`, so the default Laravel preset applies.
- `composer run setup` — one-time install; it runs `migrate --force`, so the `fasilitas_disabilitas` MySQL database must exist first.

## Frontend

- Vue 3 + `<script setup>` SPA mounted on `resources/views/app.blade.php` (`#app`). Pages in `resources/js/views/`, routes in `resources/js/router/index.js` (history mode), reusable UI in `resources/js/components/{layout,common,map,facilities,forms,verification,dashboard}/`.
- Cross-page state lives in `resources/js/composables/` and is module-level (singleton) on purpose — `useFacilities` feeds the map, card list, and admin dashboard from one array; `useVerification` loads the merged queue/statistics/history from `GET /api/verification-queue` and decides both submissions and facilities; `useSubmissions` only writes new submissions (`POST /api/submissions`).
- Data files live in `resources/js/data/`: `meta.js` (status labels/glyphs — the single source, add statuses there, not inline in components) and `facilities.js` (completeness helpers only). Facility data itself comes from `/api/facilities`, never from a client-side array; the queue comes from `/api/verification-queue`, never from a hardcoded array. `QUEUE_BUCKETS`/`QUEUE_TYPES`/`NEEDS_VERIFICATION` in `meta.js` must stay in sync with `VerificationQueueController::BUCKETS`, `submissions.type`, and `Facility::NEEDS_VERIFICATION`.
- Submissions are written by `POST /api/submissions` (multipart: `proof` is a `File`, optional). Only `VerificationView` calls `useVerification.load()` on mount, because the queue requires a session.
- Facility and submission decisions are separate endpoints (`POST /api/facilities/{id}/verification`, `POST /api/submissions/{id}/decision`). Facility decisions write `verification_status`/`verified_at`/`verified_by`/`verification_method`/`verification_note` only — never the five accessibility indicator columns, and never a mass status update.
- Status must always render label **and** glyph (`StatusPill`), never colour alone. `--rust` is the only accent colour; keep it off small text on `--ink` backgrounds (contrast).
- Leaflet markers are `L.divIcon` squares (no marker image assets). OSM tiles need internet access. The card list is the keyboard/screen-reader path for the map.
- Design tokens and component classes live in `resources/css/app.css` (translated from `TemplateDesignWeb/design_handoff_atelier_tide/`). Tailwind 4 is still wired up, but the UI is class-based CSS — there is no `tailwind.config.js`.
- Fonts (Syne, Inter, DM Mono) are downloaded at build time by `bunny()` in `vite.config.js` and injected through `{{ Vite::fonts() }}` in the Blade shell — a build without network fails.
- `TemplateDesignWeb/` is the original design reference. Do not delete or edit it; adapt from it.

## Gotchas

- `routes/web.php` is a SPA fallback (`/{any}`) with `/up` excluded so the Laravel health check keeps working. Keep that exclusion when editing routes.
- `.env.example` targets MySQL `fasilitas_disabilitas` (Laragon, root/no password), and the local `.env` currently uses MySQL, but `phpunit.xml` forces SQLite `:memory:`. Keep migrations portable and never assume tests touch local data.
- `.npmrc` sets `ignore-scripts=true`, so dependency install scripts do not run.
- Submissions: `POST /api/submissions` is public (`throttle:10,1`) and lives in `routes/web.php` so guest browsers still get the session + CSRF cookie; the queue, the decision endpoints, and the proof route are behind `auth`. Proof files sit on the private `local` disk (`storage/app/private/submission-proofs`), never under `public/`.
- Facility verification: the audit on 2026-10-22 found all 1.117 rows `pending` with `verified_at`/`verification_method`/`evidence_url` empty, so `pending` genuinely means "never checked". Do not add a migration that mass-updates those statuses, and do not treat name/category/address completeness as evidence of verification.
- `verification_events` holds one row per decision for either subject (`submission_id` or `facility_id`, both nullable); the old table name was `submission_events`.
- No CI, static analysis, or JS test runner. `php artisan test`, `vendor/bin/pint --test`, and a successful `npm run build` are the only checks.
