# Graph Report - D:\Laragon\www\fasilitas-disabilitas  (2026-10-27)

## Corpus Check
- 68 files · ~90,134 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 775 nodes · 1312 edges · 65 communities (49 shown, 16 thin omitted)
- Extraction: 95% EXTRACTED · 5% INFERRED · 0% AMBIGUOUS · INFERRED: 63 edges (avg confidence: 0.77)
- Token cost: 1,450 input · 780 output

## Community Hubs (Navigation)
- Auth and Facility API Controllers
- Composer Autoload Config
- Admin and Form Vue Components
- Facility Model and Import Tests
- Core Database Migrations
- Architecture Decisions (AGENTS.md)
- Frontend Dependencies
- Design Handoff Docs
- Facility Map and Cards
- Public Header and Footer
- Facility Table and Filters
- Verification API Composable
- Verification Queue Tests
- App Bootstrap and i18n
- Status Pills and Accessibility Panel
- User Roles and Contribution Form
- Facilities Import Command
- Verification Models and Queue Controller
- Admin Dashboard View
- Config and User Factory
- Verification View and Stats
- Location Picker
- App Layout Shell
- Completeness Meter and Chart
- Submission Status Tests
- Eloquent Models and Queue Config
- Feature Test Shell
- Guest Submission Status View
- Submission Composable and Contribution View
- Login View
- Accessibility Attributes Tests
- Auth Feature Tests
- User Model and Auth Config
- Facility Review Panel
- Hero Photo: Wheelchair Path
- Toast Notifications
- Queue Submission Tests
- App Service Provider
- Review Actions
- Logging Config
- Accessibility Attributes Migration
- Database Seeder
- Hero Photo: Community Park Path
- Facility Detail View
- Design Handoff and Hero Slider
- Console Routes
- Accessibility Checklist
- Artisan Entrypoint
- CLAUDE.md Agent Pointer
- UploadedFile Binding
- DB Facade Binding
- Queue Facade Binding
- Storage Facade Binding
- Validation Rule Binding
- Robots Allow All

## God Nodes (most connected - your core abstractions)
1. `Facility` - 42 edges
2. `Submission` - 34 edges
3. `t()` - 26 edges
4. `SubmissionTest` - 21 edges
5. `VerificationQueueTest` - 19 edges
6. `FacilityImportTest` - 17 edges
7. `request()` - 15 edges
8. `SubmissionStatusTest` - 14 edges
9. `ImportFacilities` - 13 edges
10. `useFacilities()` - 13 edges

## Surprising Connections (you probably didn't know these)
- `Module-Level Singleton Composables` --semantically_similar_to--> `AksesKita README`  [INFERRED] [semantically similar]
  AGENTS.md → README.md
- `Client-Side Category Management` --semantically_similar_to--> `Frontend/Backend Constant Sync Contract`  [INFERRED] [semantically similar]
  README.md → AGENTS.md
- `Facility Verification Audit (2026-10-22)` --semantically_similar_to--> `Facility Verification Status Enum`  [INFERRED] [semantically similar]
  AGENTS.md → README.md
- `Rejected Facilities Excluded From Public Query` --semantically_similar_to--> `Facility Verification Status Enum`  [INFERRED] [semantically similar]
  AGENTS.md → README.md
- `GET /api/verification-queue Merged Queue` --semantically_similar_to--> `Merged Verification Queue`  [INFERRED] [semantically similar]
  AGENTS.md → README.md

## Import Cycles
- None detected.

## Hyperedges (group relationships)
- **Facility + Submission Verification Flow** — agents_verification_queue_api, agents_facility_decision_endpoint, agents_submission_decision_endpoint, readme_merged_verification_queue, readme_verification_events [INFERRED 0.85]
- **Citizen Submission Lifecycle (write, hold, proof, decide)** — agents_queue_submission_delay, agents_submission_decision_endpoint, agents_private_proof_storage, readme_queued_submission_delay, readme_submission_proofs [INFERRED 0.85]
- **Shared Accessibility Vocabulary Contract (frontend, backend, dataset)** — agents_meta_constant_sync, readme_accessibility_indicators, readme_unknown_is_not_no, readme_category_map, readme_facility_decision_scope [INFERRED 0.85]
- **Accessible-route hero scene: wheelchair user + level paved path + enclosed corridor** — public_images_hero_1_facility_wheelchair_user, public_images_hero_1_facility_paved_pathway, public_images_hero_1_facility_hedge_corridor [INFERRED 0.75]

## Communities (65 total, 16 thin omitted)

### Community 0 - "Auth and Facility API Controllers"
Cohesion: 0.06
Nodes (26): AuthController, Controller, FacilityController, SubmissionController, VerificationQueueController, QueueSubmission, Submission, {closure#1}() (+18 more)

### Community 1 - "Composer Autoload Config"
Cohesion: 0.04
Nodes (47): pestphp/pest-plugin, php-http/discovery, autoload, autoload-dev, psr-4, psr-4, config, allow-plugins (+39 more)

### Community 2 - "Admin and Form Vue Components"
Cohesion: 0.05
Nodes (39): el, shown, categories, counts, { facilities }, newName, currentValue, emit (+31 more)

### Community 3 - "Facility Model and Import Tests"
Cohesion: 0.11
Nodes (4): Facility, FacilityImportTest, User, SubmissionTest

### Community 4 - "Core Database Migrations"
Cohesion: 0.09
Nodes (19): {closure#1}(), {closure#2}(), {closure#3}(), {closure#1}(), {closure#2}(), {closure#1}(), {closure#2}(), {closure#3}() (+11 more)

### Community 5 - "Architecture Decisions (AGENTS.md)"
Cohesion: 0.08
Nodes (35): AksesKita (Laravel 13 + Vue 3 SPA), Auth Middleware As The Real Access Gate, Module-Level Singleton Composables, GET /api/facilities Public Facilities Endpoint, POST /api/facilities/{id}/verification, route.meta.layout Admin/Public Split, Frontend/Backend Constant Sync Contract, Private Local Disk Proof Storage (+27 more)

### Community 6 - "Frontend Dependencies"
Cohesion: 0.06
Nodes (31): concurrently, @laravel/multiplex, laravel-vite-plugin, leaflet, dependencies, leaflet, vue, vue-i18n (+23 more)

### Community 7 - "Design Handoff Docs"
Cohesion: 0.06
Nodes (31): 1. Top Bar, 2. Hero, 3. Filmstrip, 4. Dashed Rail Divider, 5. Philosophy Split, 6. Timeline, 7. Closing CTA, 8. Footer (+23 more)

### Community 8 - "Facility Map and Cards"
Cohesion: 0.15
Nodes (23): emit, props, verified, applyMarkerElement(), container, createMarker(), drawPickerLocation(), drawPickMarker() (+15 more)

### Community 9 - "Public Header and Footer"
Cohesion: 0.16
Nodes (13): onLogout(), { user }, LINKS, onLogout(), open, route, router, { user, logout } (+5 more)

### Community 10 - "Facility Table and Filters"
Cohesion: 0.21
Nodes (12): stale(), { filters, attributes, results, hasFilters, toggleAttribute, resetFilters }, emit, CATEGORIES, daysSince(), formatDate(), NEEDS_VERIFICATION, needsVerification() (+4 more)

### Community 11 - "Verification API Composable"
Cohesion: 0.18
Nodes (16): csrfToken(), request(), decide(), filters, history, items, load(), loadError (+8 more)

### Community 13 - "App Bootstrap and i18n"
Cohesion: 0.17
Nodes (11): { locale }, LOCALES, onChange(), emit, props, i18n, id, LOCALES (+3 more)

### Community 14 - "Status Pills and Accessibility Panel"
Cohesion: 0.15
Nodes (13): meta, props, SOURCES, accessibility, actions, doneLabel, emit, isUsulan (+5 more)

### Community 15 - "User Roles and Contribution Form"
Cohesion: 0.17
Nodes (12): error, users, emit, errors, form, photo, preview, props (+4 more)

### Community 17 - "Verification Models and Queue Controller"
Cohesion: 0.23
Nodes (5): SubmissionProof, VerificationEvent, Illuminate\Database\Eloquent\Attributes\Fillable, Illuminate\Database\Eloquent\Model, Illuminate\Database\Eloquent\Relations\BelongsTo

### Community 18 - "Admin Dashboard View"
Cohesion: 0.14
Nodes (11): average, { facilities, results, status, loadError }, page, pageCount, pageNumbers, pageRows, pending, perPage (+3 more)

### Community 19 - "Config and User Factory"
Cohesion: 0.18
Nodes (6): UserFactory, Illuminate\Database\Eloquent\Factories\Factory, Illuminate\Support\Facades\Hash, Illuminate\Support\Str, Pdo\Mysql, static

### Community 20 - "Verification View and Stats"
Cohesion: 0.15
Nodes (11): QUEUE_BUCKETS, applyFilters(), busy, currentPage, decideError, FACILITY_ACTIONS, { items, stats, history, filters, meta, status, loadError, selected, load, decide, verify, select }, lastPage (+3 more)

### Community 21 - "Location Picker"
Cohesion: 0.21
Nodes (12): accuracy, center, DEFAULT_CENTER, emit, locating, locationError, locationErrorMessage(), locationStatus (+4 more)

### Community 22 - "App Layout Shell"
Cohesion: 0.17
Nodes (7): layout, route, LINKS, open, route, router, { user, logout }

### Community 23 - "Completeness Meter and Chart"
Cohesion: 0.24
Nodes (9): props, rows, known, low, percent, props, completeness(), knownAttributeCount() (+1 more)

### Community 26 - "Feature Test Shell"
Cohesion: 0.22
Nodes (4): Illuminate\Foundation\Testing\TestCase, AksesKitaTest, ExampleTest, TestCase

### Community 27 - "Guest Submission Status View"
Cohesion: 0.18
Nodes (9): email, error, lookup(), name, queued, route, searched, state (+1 more)

### Community 28 - "Submission Composable and Contribution View"
Cohesion: 0.22
Nodes (8): useSubmissions(), busy, result, route, { submit }, submitError, tab, { user }

### Community 29 - "Login View"
Cohesion: 0.20
Nodes (9): onSubmitted(), error, form, { login }, route, router, section, sending (+1 more)

### Community 30 - "Accessibility Attributes Tests"
Cohesion: 0.28
Nodes (3): PHPUnit\Framework\TestCase, AccessibilityAttributesTest, ExampleTest

### Community 32 - "User Model and Auth Config"
Cohesion: 0.36
Nodes (5): User, Illuminate\Database\Eloquent\Attributes\Hidden, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Foundation\Auth\User, Illuminate\Notifications\Notifiable

### Community 33 - "Facility Review Panel"
Cohesion: 0.25
Nodes (7): actions, doneLabel, emit, facility, props, { t }, verificationNote

### Community 34 - "Hero Photo: Wheelchair Path"
Cohesion: 0.33
Nodes (7): Mobility-access narrative: independent wheelchair travel along a clear, level route, Red brick building wall with drainpipe on the right side, Narrow corridor of tall green hedges and wooden slat fences, Homepage hero crossfade slide (1 of 2 in heroSlides, HomeView.vue), Street lamp post at the far end of the path, Flat paved pathway (hexagonal/diagonal pavers) between fences and hedges, Person in manual wheelchair, seen from behind

### Community 35 - "Toast Notifications"
Cohesion: 0.48
Nodes (5): { messages, dismiss }, dismiss(), messages, push(), useToast()

### Community 38 - "Review Actions"
Cohesion: 0.40
Nodes (5): decide(), disabled, emit, note, props

### Community 39 - "Logging Config"
Cohesion: 0.40
Nodes (4): Monolog\Handler\NullHandler, Monolog\Handler\StreamHandler, Monolog\Handler\SyslogUdpHandler, Monolog\Processor\PsrLogMessageProcessor

### Community 40 - "Accessibility Attributes Migration"
Cohesion: 0.60
Nodes (3): remapSubmissionPayloads(), remapValues(), up()

### Community 41 - "Database Seeder"
Cohesion: 0.60
Nodes (3): DatabaseSeeder, Illuminate\Database\Console\Seeds\WithoutModelEvents, Illuminate\Database\Seeder

### Community 42 - "Hero Photo: Community Park Path"
Cohesion: 0.50
Nodes (5): Wide Smooth Paved Park Path (Step-Free Route), Homepage Hero Crossfade Slide 2 ("Community"), Manual Wheelchairs with Water Bottle and Backpack Attachments, Tree-Lined Park Trail, Outdoor Recreation Setting, Two Wheelchair Users on a Park Path, Holding Hands

### Community 43 - "Facility Detail View"
Cohesion: 0.40
Nodes (4): { byId, status }, facility, route, { user }

### Community 44 - "Design Handoff and Hero Slider"
Cohesion: 0.67
Nodes (4): Design Tokens In resources/css/app.css, Homepage Hero Crossfade Photo Slider, Hero Photo Crossfade Slider, Undetermined License

### Community 46 - "Accessibility Checklist"
Cohesion: 0.67
Nodes (3): emit, props, setValue()

## Knowledge Gaps
- **250 isolated node(s):** `1. Top Bar`, `2. Hero`, `3. Filmstrip`, `4. Dashed Rail Divider`, `5. Philosophy Split` (+245 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **16 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `Facility` connect `Facility Model and Import Tests` to `Auth and Facility API Controllers`, `Verification Queue Tests`, `Facilities Import Command`, `Verification Models and Queue Controller`, `Submission Status Tests`, `Eloquent Models and Queue Config`?**
  _High betweenness centrality (0.028) - this node is a cross-community bridge._
- **Why does `t()` connect `User Roles and Contribution Form` to `Admin and Form Vue Components`, `Facility Map and Cards`, `Public Header and Footer`, `Facility Table and Filters`, `Verification API Composable`, `App Bootstrap and i18n`, `Admin Dashboard View`, `Verification View and Stats`, `Location Picker`, `Login View`?**
  _High betweenness centrality (0.021) - this node is a cross-community bridge._
- **Why does `Submission` connect `Auth and Facility API Controllers` to `Facility Model and Import Tests`, `Queue Submission Tests`, `Verification Queue Tests`, `Verification Models and Queue Controller`, `Submission Status Tests`, `Eloquent Models and Queue Config`?**
  _High betweenness centrality (0.019) - this node is a cross-community bridge._
- **Are the 16 inferred relationships involving `Facility` (e.g. with `.mapRow()` and `.store()`) actually correct?**
  _`Facility` has 16 INFERRED edges - model-reasoned connections that need verification._
- **Are the 9 inferred relationships involving `Submission` (e.g. with `.index()` and `.stats()`) actually correct?**
  _`Submission` has 9 INFERRED edges - model-reasoned connections that need verification._
- **What connects `1. Top Bar`, `2. Hero`, `3. Filmstrip` to the rest of the system?**
  _250 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `Auth and Facility API Controllers` be split into smaller, more focused modules?**
  _Cohesion score 0.05920745920745921 - nodes in this community are weakly interconnected._