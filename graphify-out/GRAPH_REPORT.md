# Graph Report - .  (2026-10-22)

## Corpus Check
- Corpus is ~32,821 words - fits in a single context window. You may not need a graph.

## Summary
- 556 nodes · 894 edges · 51 communities (45 shown, 6 thin omitted)
- Extraction: 97% EXTRACTED · 3% INFERRED · 0% AMBIGUOUS · INFERRED: 28 edges (avg confidence: 0.69)
- Token cost: 5,800 input · 3,450 output

## Community Hubs (Navigation)
- Submission Decisions & Facility Creation
- Admin Category Management
- App Shell & Navigation
- HTTP Controllers & Auth
- Composer Autoload Manifest
- NPM Dependencies
- Models, Users & Seeders
- Composer Scripts
- Architecture Rules (AGENTS.md)
- Facility Card List
- Status & Accessibility Display
- Admin Facility Table
- Contribution Form
- Verification Queue Tests
- Completeness Meters
- Facility CSV Importer
- Verification Queue View
- Correction Form
- Filters & Facility Review
- Queue List & Status Meta
- Toast Notifications
- Service Provider Bootstrap
- Review Action Buttons
- Verification Decision Rules
- Example Unit Test
- Accessibility Checklist Form
- CLAUDE.md Pointer
- robots.txt Default

## God Nodes (most connected - your core abstractions)
1. `Facility` - 31 edges
2. `Submission` - 28 edges
3. `SubmissionTest` - 19 edges
4. `VerificationQueueTest` - 18 edges
5. `useFacilities()` - 14 edges
6. `TestCase` - 14 edges
7. `User` - 13 edges
8. `request()` - 12 edges
9. `AksesKita` - 12 edges
10. `ImportFacilities` - 11 edges

## Surprising Connections (you probably didn't know these)
- `pending Means Never Checked` --semantically_similar_to--> `belum Is Not tidak (unknown != no)`  [INFERRED] [semantically similar]
  AGENTS.md → README.md
- `AksesKita` --conceptually_related_to--> `SPA Fallback Route with /up Excluded`  [AMBIGUOUS]
  README.md → AGENTS.md
- `Only Verification Checks: PHPUnit, Pint, Vite Build` --semantically_similar_to--> `PHPUnit Test Suite (39 tests, 317 assertions)`  [INFERRED] [semantically similar]
  AGENTS.md → README.md
- `VerificationQueueTest` --references--> `User`  [EXTRACTED]
  tests/Feature/VerificationQueueTest.php → app/Models/User.php
- `Server-side Merged Verification Queue` --conceptually_related_to--> `VerificationQueueController`  [INFERRED]
  AGENTS.md → README.md

## Import Cycles
- None detected.

## Hyperedges (group relationships)
- **Merged Verification Queue and Decision Flow** — agents_verification_queue_merge, agents_facility_decision_scope, readme_verification_queue_controller, readme_facility_controller_verify, readme_submission_controller, readme_verification_events [INFERRED 0.85]
- **'Absence of Evidence Is Not Evidence of Absence' Principle** — agents_pending_means_never_checked, readme_belum_vs_tidak, readme_accessibility_indicators, readme_data_completeness [INFERRED 0.85]
- **Frontend Shared-State, Data Contract and Status Presentation** — agents_singleton_composables, agents_meta_js_sync_contract, agents_status_label_and_glyph_rule, readme_use_verification [INFERRED 0.75]

## Communities (51 total, 6 thin omitted)

### Community 0 - "Submission Decisions & Facility Creation"
Cohesion: 0.08
Nodes (10): SubmissionController, Facility, Submission, Illuminate\Foundation\Testing\RefreshDatabase, Illuminate\Foundation\Testing\TestCase, AksesKitaTest, ExampleTest, FacilityImportTest (+2 more)

### Community 1 - "Admin Category Management"
Cohesion: 0.06
Nodes (40): el, shown, categories, counts, { facilities }, newName, facilities, filters (+32 more)

### Community 2 - "App Shell & Navigation"
Cohesion: 0.07
Nodes (33): error, users, LINKS, onLogout(), open, route, router, { user, logout } (+25 more)

### Community 3 - "HTTP Controllers & Auth"
Cohesion: 0.09
Nodes (12): AuthController, Controller, FacilityController, VerificationQueueController, SubmissionProof, VerificationEvent, Illuminate\Database\Eloquent\Model, Illuminate\Database\Eloquent\Relations\BelongsTo (+4 more)

### Community 4 - "Composer Autoload Manifest"
Cohesion: 0.05
Nodes (40): pestphp/pest-plugin, php-http/discovery, autoload, autoload-dev, psr-4, psr-4, config, allow-plugins (+32 more)

### Community 5 - "NPM Dependencies"
Cohesion: 0.07
Nodes (29): concurrently, @laravel/multiplex, laravel-vite-plugin, leaflet, dependencies, leaflet, vue, vue-router (+21 more)

### Community 6 - "Models, Users & Seeders"
Cohesion: 0.11
Nodes (11): User, UserFactory, DatabaseSeeder, Illuminate\Database\Console\Seeds\WithoutModelEvents, Illuminate\Database\Eloquent\Factories\Factory, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Database\Seeder, Illuminate\Foundation\Auth\User (+3 more)

### Community 7 - "Composer Scripts"
Cohesion: 0.08
Nodes (26): scripts, dev, post-autoload-dump, post-create-project-cmd, post-root-package-install, post-update-cmd, pre-package-uninstall, setup (+18 more)

### Community 8 - "Architecture Rules (AGENTS.md)"
Cohesion: 0.09
Nodes (23): Leaflet divIcon Category Squares, meta.js / Backend Constant Sync Contract, pending Means Never Checked, Module-level Singleton Composables, SPA Fallback Route with /up Excluded, Status Renders Label and Glyph, Never Colour Alone, Only Verification Checks: PHPUnit, Pint, Vite Build, Server-side Merged Verification Queue (+15 more)

### Community 9 - "Facility Card List"
Cohesion: 0.15
Nodes (16): emit, props, verified, emit, props, shown, visible, container (+8 more)

### Community 10 - "Status & Accessibility Display"
Cohesion: 0.14
Nodes (14): meta, props, SOURCES, accessibility, actions, doneLabel, emit, isUsulan (+6 more)

### Community 11 - "Admin Facility Table"
Cohesion: 0.16
Nodes (13): stale(), daysSince(), STALE_DAYS, average, currentPage, { facilities, tablePage, loadTablePage }, lastPage, pageNumbers (+5 more)

### Community 12 - "Contribution Form"
Cohesion: 0.16
Nodes (12): emit, errors, form, photo, preview, props, submit(), validate() (+4 more)

### Community 14 - "Completeness Meters"
Cohesion: 0.21
Nodes (10): props, rows, known, low, percent, props, completeness(), knownAttributeCount() (+2 more)

### Community 16 - "Verification Queue View"
Cohesion: 0.17
Nodes (10): QUEUE_BUCKETS, busy, currentPage, decideError, FACILITY_ACTIONS, { items, stats, history, filters, meta, status, loadError, selected, load, decide, verify, select }, lastPage, pageNumbers (+2 more)

### Community 17 - "Correction Form"
Cohesion: 0.20
Nodes (10): currentValue, emit, errors, { facilities }, form, photo, props, selected (+2 more)

### Community 18 - "Filters & Facility Review"
Cohesion: 0.20
Nodes (8): { filters, attributes, results, hasFilters, toggleAttribute, resetFilters }, actions, doneLabel, emit, facility, props, verificationNote, VERIFICATION

### Community 19 - "Queue List & Status Meta"
Cohesion: 0.39
Nodes (6): emit, formatDate(), NEEDS_VERIFICATION, needsVerification(), QUEUE_TYPES, statusMeta()

### Community 20 - "Toast Notifications"
Cohesion: 0.48
Nodes (5): { messages, dismiss }, dismiss(), messages, push(), useToast()

### Community 22 - "Review Action Buttons"
Cohesion: 0.40
Nodes (5): decide(), disabled, emit, note, props

### Community 23 - "Verification Decision Rules"
Cohesion: 0.40
Nodes (5): Facility Decision Writes Verification Columns Only, Private Storage for Submission Proofs, FacilityController::verify, SubmissionController, verification_events Audit Trail

### Community 25 - "Accessibility Checklist Form"
Cohesion: 0.67
Nodes (3): emit, props, setValue()

## Ambiguous Edges - Review These
- `SPA Fallback Route with /up Excluded` → `AksesKita`  [AMBIGUOUS]
  README.md · relation: conceptually_related_to

## Knowledge Gaps
- **191 isolated node(s):** `$schema`, `name`, `type`, `description`, `laravel` (+186 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **6 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **What is the exact relationship between `SPA Fallback Route with /up Excluded` and `AksesKita`?**
  _Edge tagged AMBIGUOUS (relation: conceptually_related_to) - confidence is low._
- **Why does `Facility` connect `Submission Decisions & Facility Creation` to `HTTP Controllers & Auth`, `Verification Queue Tests`, `Models, Users & Seeders`, `Facility CSV Importer`?**
  _High betweenness centrality (0.029) - this node is a cross-community bridge._
- **Why does `Submission` connect `Submission Decisions & Facility Creation` to `HTTP Controllers & Auth`, `Verification Queue Tests`?**
  _High betweenness centrality (0.016) - this node is a cross-community bridge._
- **Why does `TestCase` connect `Submission Decisions & Facility Creation` to `Verification Queue Tests`, `Models, Users & Seeders`?**
  _High betweenness centrality (0.013) - this node is a cross-community bridge._
- **Are the 6 inferred relationships involving `Facility` (e.g. with `.store()` and `.index()`) actually correct?**
  _`Facility` has 6 INFERRED edges - model-reasoned connections that need verification._
- **Are the 4 inferred relationships involving `useFacilities()` (e.g. with `load()` and `loadTablePage()`) actually correct?**
  _`useFacilities()` has 4 INFERRED edges - model-reasoned connections that need verification._
- **What connects `$schema`, `name`, `type` to the rest of the system?**
  _191 weakly-connected nodes found - possible documentation gaps or missing edges._