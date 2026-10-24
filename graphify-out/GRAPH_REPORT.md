# Graph Report - D:\Laragon\www\fasilitas-disabilitas  (2026-10-24)

## Corpus Check
- Corpus is ~37,820 words - fits in a single context window. You may not need a graph.

## Summary
- 726 nodes · 1287 edges · 46 communities (37 shown, 9 thin omitted)
- Extraction: 98% EXTRACTED · 2% INFERRED · 0% AMBIGUOUS · INFERRED: 32 edges (avg confidence: 0.84)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `c1d56e3b`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- Verification API Controllers
- Facilities Import Command
- Facility Model and Feature Tests
- User Model and Seeders
- Submission Feature Tests
- App Service Provider
- Example Unit Test
- Verification Queue Tests
- Submission Model and Queue Job
- Verification API Composable
- Accessibility Panel and Status UI
- Admin Dashboard and Facility Table
- Contribution Form and Location Picker
- Completeness Meter and Chart
- Correction Form
- Public Header Navigation
- Login View
- Admin Header Navigation
- Contribution View Tabs
- Toast Notifications
- Facility Review Panel
- Review Actions
- Facility Composable and Detail View
- Accessibility Checklist
- Database Migrations
- Guest Submission Status View
- Category Manager Panel
- Console Routes
- Leaflet Map and Facility Cards
- Verification Queue UI
- User Roles Panel
- Atelier Tide Design Handoff
- Vue App Bootstrap
- Composer Autoload Config
- Artisan Entrypoint
- Logging Config
- Documented Verification Model
- NPM Frontend Dependencies
- CLAUDE.md Pointer
- Robots.txt

## God Nodes (most connected - your core abstractions)
1. `vue` - 32 edges
2. `Submission` - 30 edges
3. `Facility` - 21 edges
4. `SubmissionTest` - 21 edges
5. `VerificationQueueTest` - 19 edges
6. `TestCase` - 18 edges
7. `User` - 17 edges
8. `QueueSubmission` - 16 edges
9. `SubmissionStatusTest` - 14 edges
10. `request()` - 14 edges

## Surprising Connections (you probably didn't know these)
- `Auth Enforced by Middleware, Never by Frontend Alone` --semantically_similar_to--> `Vue Route Guard Is UX Only; Middleware Is the Real Gate`  [INFERRED] [semantically similar]
  AGENTS.md → README.md
- `Server-Enforced Verification Decision Rules` --semantically_similar_to--> `Facility Decision Writes Only Verification Columns`  [INFERRED] [semantically similar]
  README.md → AGENTS.md
- `User` --references--> `VerificationQueueTest`  [EXTRACTED]
  app/Models/User.php → tests/Feature/VerificationQueueTest.php
- `QueueSubmission` --references--> `{closure#1}()`  [EXTRACTED]
  app/Jobs/QueueSubmission.php → tests/Feature/QueueSubmissionTest.php
- `Merged Verification Queue (facilities + citizen submissions)` --conceptually_related_to--> `Verification Queue Buckets and Server-Side Counters`  [INFERRED]
  AGENTS.md → README.md

## Import Cycles
- None detected.

## Hyperedges (group relationships)
- **Absence of Evidence Is Not Evidence of Absence (indicator and verification levels)** — readme_belum_not_tidak, readme_data_completeness_percent, agents_no_verification_evidence, agents_facility_decision_scope [INFERRED 0.80]
- **Verification Decision Flow (queue buckets -> decision endpoint -> audit trail)** — agents_verification_queue, readme_verification_queue_buckets, app_http_controllers_verificationqueuecontroller_buckets, app_http_controllers_facilitycontroller_verify, readme_verification_events [INFERRED 0.85]
- **Shared Verification Vocabulary Kept in Sync Across Client and Server** — resources_js_data_meta, app_models_facility_needs_verification, app_http_controllers_verificationqueuecontroller_buckets, agents_status_meta_single_source, readme_indikator_aksesibilitas [INFERRED 0.90]

## Communities (46 total, 9 thin omitted)

### Community 0 - "Verification API Controllers"
Cohesion: 0.06
Nodes (28): AuthController, Controller, FacilityController, SubmissionController, VerificationQueueController, {closure#3}(), {closure#2}(), {closure#3}() (+20 more)

### Community 14 - "Facilities Import Command"
Cohesion: 0.23
Nodes (4): ImportFacilities, {closure#1}(), Illuminate\Console\Command, Illuminate\Support\Facades\DB

### Community 2 - "Facility Model and Feature Tests"
Cohesion: 0.07
Nodes (11): Facility, AksesKitaTest, AuthTest, ExampleTest, FacilityImportTest, SubmissionStatusTest, TestCase, Illuminate\Foundation\Testing\RefreshDatabase (+3 more)

### Community 23 - "User Model and Seeders"
Cohesion: 0.10
Nodes (14): User, UserFactory, DatabaseSeeder, Illuminate\Database\Console\Seeds\WithoutModelEvents, Illuminate\Database\Eloquent\Attributes\Hidden, Illuminate\Database\Eloquent\Factories\Factory, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Database\Seeder (+6 more)

### Community 7 - "Submission Model and Queue Job"
Cohesion: 0.07
Nodes (19): QueueSubmission, Submission, SubmissionProof, VerificationEvent, QueueSubmissionTest, {closure#1}(), {closure#4}(), {closure#1}() (+11 more)

### Community 1 - "Verification API Composable"
Cohesion: 0.19
Nodes (15): csrfToken(), request(), decide(), load(), useVerification(), verify(), filters, history (+7 more)

### Community 10 - "Accessibility Panel and Status UI"
Cohesion: 0.14
Nodes (17): attributeLabel(), meta, props, SOURCES, { filters, attributes, results, hasFilters, toggleAttribute, resetFilters }, accessibility, actions, doneLabel (+9 more)

### Community 11 - "Admin Dashboard and Facility Table"
Cohesion: 0.16
Nodes (16): stale(), loadTablePage(), daysSince(), needsVerification(), changePerPage(), goToPage(), STALE_DAYS, currentPage (+8 more)

### Community 12 - "Contribution Form and Location Picker"
Cohesion: 0.11
Nodes (20): submit(), validate(), locationErrorMessage(), pick(), updateManualCoordinate(), useMyLocation(), emit, errors (+12 more)

### Community 13 - "Completeness Meter and Chart"
Cohesion: 0.21
Nodes (12): completeness(), knownAttributeCount(), categoryLabel(), props, rows, known, low, percent (+4 more)

### Community 15 - "Correction Form"
Cohesion: 0.20
Nodes (10): submit(), validate(), currentValue, emit, errors, { facilities }, form, photo (+2 more)

### Community 17 - "Public Header Navigation"
Cohesion: 0.17
Nodes (12): onLogout(), load(), login(), logout(), useAuth(), { user }, LINKS, open (+4 more)

### Community 18 - "Login View"
Cohesion: 0.20
Nodes (9): onSubmitted(), submit(), error, form, { login }, route, router, section (+1 more)

### Community 20 - "Admin Header Navigation"
Cohesion: 0.22
Nodes (6): onLogout(), LINKS, open, route, router, { user, logout }

### Community 21 - "Contribution View Tabs"
Cohesion: 0.20
Nodes (9): useSubmissions(), busy, result, route, { submit }, submitError, tab, { user } (+1 more)

### Community 25 - "Toast Notifications"
Cohesion: 0.48
Nodes (5): dismiss(), push(), useToast(), { messages, dismiss }, messages

### Community 26 - "Facility Review Panel"
Cohesion: 0.18
Nodes (10): formatDate(), emit, props, verified, actions, doneLabel, emit, facility (+2 more)

### Community 28 - "Review Actions"
Cohesion: 0.40
Nodes (5): decide(), disabled, emit, note, props

### Community 29 - "Facility Composable and Detail View"
Cohesion: 0.15
Nodes (15): load(), resetFilters(), toggleAttribute(), useFacilities(), facilities, filters, hasFilters, loadError (+7 more)

### Community 31 - "Accessibility Checklist"
Cohesion: 0.67
Nodes (3): setValue(), emit, props

### Community 32 - "Database Migrations"
Cohesion: 0.08
Nodes (21): {closure#1}(), {closure#2}(), {closure#3}(), {closure#1}(), {closure#2}(), {closure#1}(), {closure#2}(), {closure#3}() (+13 more)

### Community 33 - "Guest Submission Status View"
Cohesion: 0.18
Nodes (9): lookup(), email, error, name, queued, route, searched, state (+1 more)

### Community 34 - "Category Manager Panel"
Cohesion: 0.33
Nodes (4): categories, counts, { facilities }, newName

### Community 8 - "Leaflet Map and Facility Cards"
Cohesion: 0.10
Nodes (21): drawPickerLocation(), drawPickMarker(), facilityLabel(), fitPickerView(), pinIcon(), render(), categoryGlyph(), leaflet@^1.9.4 (+13 more)

### Community 9 - "Verification Queue UI"
Cohesion: 0.12
Nodes (14): statusMeta(), onDecide(), emit, QUEUE_BUCKETS, QUEUE_TYPES, busy, currentPage, decideError (+6 more)

### Community 19 - "Atelier Tide Design Handoff"
Cohesion: 0.06
Nodes (31): 1. Top Bar, 2. Hero, 3. Filmstrip, 4. Dashed Rail Divider, 5. Philosophy Split, 6. Timeline, 7. Closing CTA, 8. Footer (+23 more)

### Community 22 - "Vue App Bootstrap"
Cohesion: 0.17
Nodes (9): dependencies, vue@^3.5.44, vue-router@^5.4.0, layout, route, router, routes, vue (+1 more)

### Community 3 - "Composer Autoload Config"
Cohesion: 0.04
Nodes (47): pestphp/pest-plugin, php-http/discovery, autoload, autoload-dev, psr-4, psr-4, config, allow-plugins (+39 more)

### Community 45 - "Logging Config"
Cohesion: 0.40
Nodes (4): Monolog\Handler\NullHandler, Monolog\Handler\StreamHandler, Monolog\Handler\SyslogUdpHandler, Monolog\Processor\PsrLogMessageProcessor

### Community 5 - "Documented Verification Model"
Cohesion: 0.08
Nodes (34): FacilityController::verify, VerificationQueueController::BUCKETS, Facility::CATEGORY_MAP, Facility::NEEDS_VERIFICATION, resources/css/app.css (design tokens and component classes), Two-Area Layout Split (AdminLayout vs PublicLayout), Build-Time Font Download via Bunny in vite.config.js, Leaflet divIcon Square Markers (no image assets) (+26 more)

### Community 6 - "NPM Frontend Dependencies"
Cohesion: 0.12
Nodes (22): devDependencies, concurrently@^10.0.3, laravel-vite-plugin@^3.1, tailwindcss@^4.0.0, @tailwindcss/vite@^4.0.0, vite@^8.0.0, @vitejs/plugin-vue@^6.0.10, optionalDependencies (+14 more)

## Knowledge Gaps
- **212 isolated node(s):** `filters`, `history`, `items`, `loadError`, `meta` (+207 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **9 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `Five Accessibility Indicators (ramp, toilet, lift, path, entrance)` connect `Documented Verification Model` to `Accessibility Panel and Status UI`?**
  _High betweenness centrality (0.216) - this node is a cross-community bridge._
- **Why does `Facility` connect `Facility Model and Feature Tests` to `Verification API Controllers`, `Verification Queue Tests`, `Documented Verification Model`, `Submission Model and Queue Job`, `Facilities Import Command`, `Submission Feature Tests`?**
  _High betweenness centrality (0.167) - this node is a cross-community bridge._
- **Why does `vue` connect `Vue App Bootstrap` to `Verification API Composable`, `NPM Frontend Dependencies`, `Leaflet Map and Facility Cards`, `Verification Queue UI`, `Accessibility Panel and Status UI`, `Admin Dashboard and Facility Table`, `Contribution Form and Location Picker`, `Completeness Meter and Chart`, `Correction Form`, `User Roles Panel`, `Public Header Navigation`, `Login View`, `Admin Header Navigation`, `Contribution View Tabs`, `Toast Notifications`, `Facility Review Panel`, `Review Actions`, `Facility Composable and Detail View`, `Guest Submission Status View`, `Category Manager Panel`?**
  _High betweenness centrality (0.113) - this node is a cross-community bridge._
- **What connects `filters`, `history`, `items` to the rest of the system?**
  _212 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `Verification API Controllers` be split into smaller, more focused modules?**
  _Cohesion score 0.061016949152542375 - nodes in this community are weakly interconnected._
- **Should `Facility Model and Feature Tests` be split into smaller, more focused modules?**
  _Cohesion score 0.07102040816326531 - nodes in this community are weakly interconnected._
- **Should `User Model and Seeders` be split into smaller, more focused modules?**
  _Cohesion score 0.10153846153846154 - nodes in this community are weakly interconnected._