# AGENTS.md

AksesKita — Laravel 13 + Vue 3 SPA for mapping/verifying disability-friendly public facilities. Facilities are real: 1.117 rows imported from the Medan OSM dataset into the `facilities` table and served by `GET /api/facilities`. Submissions, verification queue, and category management are still client-side demo data; admin login is real (session auth).

## Commands

- `composer run dev` — runs `artisan serve` (:8000), `queue:listen`, and Vite (HMR) together. Use this instead of starting the server and Vite separately.
- `npm run build` — required after any `resources/js` or `resources/css` change if you are serving assets without Vite HMR.
- `php artisan test` — PHPUnit 12. Single test: `php artisan test --filter=AksesKitaTest` or `php artisan test tests/Feature/AksesKitaTest.php`.
- `vendor/bin/pint` — format PHP; add `--test` to check only. No `pint.json`, so the default Laravel preset applies.
- `composer run setup` — one-time install; it runs `migrate --force`, so the `fasilitas_disabilitas` MySQL database must exist first.

## Frontend

- Vue 3 + `<script setup>` SPA mounted on `resources/views/app.blade.php` (`#app`). Pages in `resources/js/views/`, routes in `resources/js/router/index.js` (history mode), reusable UI in `resources/js/components/{layout,common,map,facilities,forms,verification,dashboard}/`.
- Cross-page state lives in `resources/js/composables/` and is module-level (singleton) on purpose — `useFacilities` feeds the map, card list, and admin dashboard from one array; `useSubmissions` mutates it when a submission is approved.
- Data files live in `resources/js/data/`: `meta.js` (status labels/glyphs — the single source, add statuses there, not inline in components), `submissions.js` (demo verification queue), and `facilities.js` (completeness helpers only). Facility data itself comes from `/api/facilities`, never from a client-side array.
- Status must always render label **and** glyph (`StatusPill`), never colour alone. `--rust` is the only accent colour; keep it off small text on `--ink` backgrounds (contrast).
- Leaflet markers are `L.divIcon` squares (no marker image assets). OSM tiles need internet access. The card list is the keyboard/screen-reader path for the map.
- Design tokens and component classes live in `resources/css/app.css` (translated from `TemplateDesignWeb/design_handoff_atelier_tide/`). Tailwind 4 is still wired up, but the UI is class-based CSS — there is no `tailwind.config.js`.
- Fonts (Syne, Inter, DM Mono) are downloaded at build time by `bunny()` in `vite.config.js` and injected through `{{ Vite::fonts() }}` in the Blade shell — a build without network fails.
- `TemplateDesignWeb/` is the original design reference. Do not delete or edit it; adapt from it.

## Gotchas

- `routes/web.php` is a SPA fallback (`/{any}`) with `/up` excluded so the Laravel health check keeps working. Keep that exclusion when editing routes.
- `.env.example` targets MySQL `fasilitas_disabilitas` (Laragon, root/no password), and the local `.env` currently uses MySQL, but `phpunit.xml` forces SQLite `:memory:`. Keep migrations portable and never assume tests touch local data.
- `.npmrc` sets `ignore-scripts=true`, so dependency install scripts do not run.
- No CI, static analysis, or JS test runner. `php artisan test`, `vendor/bin/pint --test`, and a successful `npm run build` are the only checks.
