# Graph Report - D:/Laragon/www/fasilitas-disabilitas  (2026-10-24)

## Corpus Check
- Corpus is ~33,563 words - fits in a single context window. You may not need a graph.

## Summary
- 583 nodes · 956 edges · 55 communities (50 shown, 5 thin omitted)
- Extraction: 96% EXTRACTED · 4% INFERRED · 0% AMBIGUOUS · INFERRED: 35 edges (avg confidence: 0.71)
- Token cost: 0 input · 0 output

## Community Hubs (Navigation)
- Verification API Controllers
- Composables and Page Views
- Facility Model and Tests
- Composer Autoload Config
- Authentication and Queue Tests
- Documented Verification Model
- NPM Frontend Dependencies
- Composer Scripts
- Leaflet Map and Facility Cards
- Verification Queue UI
- Accessibility Panel and Status UI
- Admin Dashboard and Facility Table
- Contribution Form and Location Picker
- Completeness Meter and Chart
- Facilities Import Command
- Correction Form
- Auth and Submissions Composables
- Public Header Navigation
- Login View
- Facility Filters and Legend
- Admin Header Navigation
- Contribution View Tabs
- Router and Auth Guards
- User Factory
- Layout Shells
- Toast Notifications
- Facility Review Panel
- App Service Provider
- Review Actions
- Facility Detail View
- Example Unit Test
- Accessibility Checklist
- CLAUDE.md Pointer
- Robots.txt

## God Nodes (most connected - your core abstractions)
1. `Facility` - 31 edges
2. `Submission` - 28 edges
3. `SubmissionTest` - 19 edges
4. `VerificationQueueTest` - 18 edges
5. `useFacilities()` - 14 edges
6. `TestCase` - 14 edges
7. `User` - 13 edges
8. `request()` - 12 edges
9. `useAuth()` - 12 edges
10. `ImportFacilities` - 11 edges

## Surprising Connections (you probably didn't know these)
- `Facility Decision Writes Only Verification Columns` --semantically_similar_to--> `Server-Enforced Verification Decision Rules`  [INFERRED] [semantically similar]
  AGENTS.md → README.md
- `Auth Enforced by Middleware, Never by Frontend Alone` --semantically_similar_to--> `Vue Route Guard Is UX Only; Middleware Is the Real Gate`  [INFERRED] [semantically similar]
  AGENTS.md → README.md
- `Merged Verification Queue (facilities + citizen submissions)` --conceptually_related_to--> `Verification Queue Buckets and Server-Side Counters`  [INFERRED]
  AGENTS.md → README.md
- `meta.js as Single Source of Status Labels and Glyphs` --conceptually_related_to--> `Five Accessibility Indicators (ramp, toilet, lift, path, entrance)`  [INFERRED]
  AGENTS.md → README.md
- `Private Local Disk Storage for Submission Proofs` --conceptually_related_to--> `submission_proofs Table (one row per proof file)`  [INFERRED]
  AGENTS.md → README.md

## Import Cycles
- None detected.

## Hyperedges (group relationships)
- **Verification Decision Flow (queue buckets -> decision endpoint -> audit trail)** — agents_verification_queue, readme_verification_queue_buckets, app_http_controllers_verificationqueuecontroller_buckets, app_http_controllers_facilitycontroller_verify, readme_verification_events [INFERRED 0.85]
- **Shared Verification Vocabulary Kept in Sync Across Client and Server** — resources_js_data_meta, app_models_facility_needs_verification, app_http_controllers_verificationqueuecontroller_buckets, agents_status_meta_single_source, readme_indikator_aksesibilitas [INFERRED 0.90]
- **Absence of Evidence Is Not Evidence of Absence (indicator and verification levels)** — readme_belum_not_tidak, readme_data_completeness_percent, agents_no_verification_evidence, agents_facility_decision_scope [INFERRED 0.80]

## Communities (55 total, 5 thin omitted)

### Community 0 - "Verification API Controllers"
Cohesion: 0.08
Nodes (16): Public POST /api/submissions Lives in routes/web.php, AuthController, Controller, FacilityController, SubmissionController, VerificationQueueController, Submission, SubmissionProof (+8 more)

### Community 1 - "Composables and Page Views"
Cohesion: 0.06
Nodes (39): el, shown, categories, counts, { facilities }, newName, facilities, filters (+31 more)

### Community 2 - "Facility Model and Tests"
Cohesion: 0.10
Nodes (7): Facility, Illuminate\Foundation\Testing\TestCase, AksesKitaTest, ExampleTest, FacilityImportTest, SubmissionTest, TestCase

### Community 3 - "Composer Autoload Config"
Cohesion: 0.05
Nodes (40): pestphp/pest-plugin, php-http/discovery, autoload, autoload-dev, psr-4, psr-4, config, allow-plugins (+32 more)

### Community 4 - "Authentication and Queue Tests"
Cohesion: 0.11
Nodes (10): User, DatabaseSeeder, Illuminate\Database\Console\Seeds\WithoutModelEvents, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Database\Seeder, Illuminate\Foundation\Auth\User, Illuminate\Foundation\Testing\RefreshDatabase, Illuminate\Notifications\Notifiable (+2 more)

### Community 5 - "Documented Verification Model"
Cohesion: 0.08
Nodes (35): Two-Area Layout Split (AdminLayout vs PublicLayout), AksesKita (project overview, AGENTS.md), Build-Time Font Download via Bunny in vite.config.js, Facility Decision Writes Only Verification Columns, Leaflet divIcon Square Markers (no image assets), 2026-10-22 Audit: All 1,117 Facilities Are Genuinely Unverified, Private Local Disk Storage for Submission Proofs, Module-Level Singleton Composables for Cross-Page State (+27 more)

### Community 6 - "NPM Frontend Dependencies"
Cohesion: 0.07
Nodes (29): concurrently, @laravel/multiplex, laravel-vite-plugin, leaflet, dependencies, leaflet, vue, vue-router (+21 more)

### Community 7 - "Composer Scripts"
Cohesion: 0.08
Nodes (26): scripts, dev, post-autoload-dump, post-create-project-cmd, post-root-package-install, post-update-cmd, pre-package-uninstall, setup (+18 more)

### Community 8 - "Leaflet Map and Facility Cards"
Cohesion: 0.15
Nodes (16): emit, props, verified, emit, props, shown, visible, container (+8 more)

### Community 9 - "Verification Queue UI"
Cohesion: 0.13
Nodes (14): emit, formatDate(), QUEUE_BUCKETS, QUEUE_TYPES, statusMeta(), busy, currentPage, decideError (+6 more)

### Community 10 - "Accessibility Panel and Status UI"
Cohesion: 0.16
Nodes (12): meta, props, SOURCES, accessibility, actions, doneLabel, emit, isUsulan (+4 more)

### Community 11 - "Admin Dashboard and Facility Table"
Cohesion: 0.16
Nodes (13): stale(), daysSince(), STALE_DAYS, average, currentPage, { facilities, tablePage, loadTablePage }, lastPage, pageNumbers (+5 more)

### Community 12 - "Contribution Form and Location Picker"
Cohesion: 0.16
Nodes (12): emit, errors, form, photo, preview, props, submit(), validate() (+4 more)

### Community 13 - "Completeness Meter and Chart"
Cohesion: 0.21
Nodes (10): props, rows, known, low, percent, props, completeness(), knownAttributeCount() (+2 more)

### Community 15 - "Correction Form"
Cohesion: 0.20
Nodes (10): currentValue, emit, errors, { facilities }, form, photo, props, selected (+2 more)

### Community 16 - "Auth and Submissions Composables"
Cohesion: 0.29
Nodes (8): error, users, csrfToken(), load(), login(), request(), user, useSubmissions()

### Community 17 - "Public Header Navigation"
Cohesion: 0.20
Nodes (8): onLogout(), LINKS, onLogout(), open, route, router, { user, logout }, logout()

### Community 18 - "Login View"
Cohesion: 0.20
Nodes (9): onSubmitted(), error, form, { login }, route, router, section, sending (+1 more)

### Community 19 - "Facility Filters and Legend"
Cohesion: 0.39
Nodes (5): { filters, attributes, results, hasFilters, toggleAttribute, resetFilters }, CATEGORIES, NEEDS_VERIFICATION, needsVerification(), VERIFICATION

### Community 20 - "Admin Header Navigation"
Cohesion: 0.25
Nodes (5): LINKS, open, route, router, { user, logout }

### Community 21 - "Contribution View Tabs"
Cohesion: 0.25
Nodes (7): busy, result, route, { submit }, submitError, tab, { user }

### Community 22 - "Router and Auth Guards"
Cohesion: 0.38
Nodes (5): Auth Enforced by Middleware, Never by Frontend Alone, Vue Route Guard Is UX Only; Middleware Is the Real Gate, useAuth(), router, routes

### Community 23 - "User Factory"
Cohesion: 0.38
Nodes (3): UserFactory, Illuminate\Database\Eloquent\Factories\Factory, static

### Community 24 - "Layout Shells"
Cohesion: 0.29
Nodes (3): layout, route, { user }

### Community 25 - "Toast Notifications"
Cohesion: 0.48
Nodes (5): { messages, dismiss }, dismiss(), messages, push(), useToast()

### Community 26 - "Facility Review Panel"
Cohesion: 0.29
Nodes (6): actions, doneLabel, emit, facility, props, verificationNote

### Community 28 - "Review Actions"
Cohesion: 0.40
Nodes (5): decide(), disabled, emit, note, props

### Community 29 - "Facility Detail View"
Cohesion: 0.40
Nodes (4): { byId, status }, facility, route, { user }

### Community 31 - "Accessibility Checklist"
Cohesion: 0.67
Nodes (3): emit, props, setValue()

## Knowledge Gaps
- **198 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+193 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **5 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `Five Accessibility Indicators (ramp, toilet, lift, path, entrance)` connect `Documented Verification Model` to `Facility Model and Tests`, `Facility Filters and Legend`?**
  _High betweenness centrality (0.231) - this node is a cross-community bridge._
- **Why does `Facility` connect `Facility Model and Tests` to `Verification API Controllers`, `Authentication and Queue Tests`, `Facilities Import Command`, `User Factory`?**
  _High betweenness centrality (0.123) - this node is a cross-community bridge._
- **Are the 6 inferred relationships involving `Facility` (e.g. with `.store()` and `.index()`) actually correct?**
  _`Facility` has 6 INFERRED edges - model-reasoned connections that need verification._
- **Are the 4 inferred relationships involving `useFacilities()` (e.g. with `load()` and `loadTablePage()`) actually correct?**
  _`useFacilities()` has 4 INFERRED edges - model-reasoned connections that need verification._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _198 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `Verification API Controllers` be split into smaller, more focused modules?**
  _Cohesion score 0.07946127946127945 - nodes in this community are weakly interconnected._
- **Should `Composables and Page Views` be split into smaller, more focused modules?**
  _Cohesion score 0.0595959595959596 - nodes in this community are weakly interconnected._