# Graph Report - D:\Laragon\www\fasilitas-disabilitas  (2026-10-21)

## Corpus Check
- 85 files · ~162,332 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 411 nodes · 540 edges · 47 communities (40 shown, 7 thin omitted)
- Extraction: 95% EXTRACTED · 5% INFERRED · 0% AMBIGUOUS · INFERRED: 28 edges (avg confidence: 0.74)
- Token cost: 21,000 input · 7,200 output

## Community Hubs (Navigation)
- Facility Catalog & Filters
- Composer Manifest
- Verification Review UI
- NPM Manifest
- Submission Queue & Toasts
- Composer Scripts
- Auth Scaffold (User Model)
- Architecture Rules (AGENTS.md)
- Map & Facility List
- Contribution Form
- Design System Rules
- Admin Panels (Categories & Users)
- Correction Form
- Laravel Feature Tests
- App Shell & Router
- Atelier Tide Prototype Sections
- Commands & Quality Gates
- Template Hero Anatomy
- Tide Hall Imagery
- Laravel Service Provider
- PHPUnit Unit Test
- Misc 24
- Misc 26
- Misc 27
- Misc 44
- Misc 45

## God Nodes (most connected - your core abstractions)
1. `useFacilities()` - 12 edges
2. `scripts` - 9 edges
3. `ATTRIBUTES` - 9 edges
4. `categoryLabel()` - 9 edges
5. `Atelier Tide Design Tokens (paper / ink / rust)` - 9 edges
6. `require-dev` - 8 edges
7. `setup` - 7 edges
8. `VERIFICATION` - 7 edges
9. `Atelier Tide Hero Thumbnail (architecture studio landing page screenshot)` - 7 edges
10. `User` - 6 edges

## Surprising Connections (you probably didn't know these)
- `Single Accent Hue Rule` --semantically_similar_to--> `Rust as Sole Accent, Kept Off Small Text on Ink`  [INFERRED] [semantically similar]
  TemplateDesignWeb/design_handoff_atelier_tide/README.md → AGENTS.md
- `robots.txt Allow-All (Laravel default)` --conceptually_related_to--> `AksesKita — Disability-Friendly Facility Mapping SPA`  [INFERRED]
  public/robots.txt → AGENTS.md
- `CLAUDE.md as Pointer to AGENTS.md` --references--> `AksesKita — Disability-Friendly Facility Mapping SPA`  [EXTRACTED]
  CLAUDE.md → AGENTS.md
- `Laravel Framework (default scaffold README)` --conceptually_related_to--> `AksesKita — Disability-Friendly Facility Mapping SPA`  [INFERRED]
  README.md → AGENTS.md
- `Design Tokens and Component Classes in resources/css/app.css` --implements--> `High-Fidelity Handoff: Recreate, Do Not Copy`  [INFERRED]
  AGENTS.md → TemplateDesignWeb/design_handoff_atelier_tide/README.md

## Import Cycles
- None detected.

## Hyperedges (group relationships)
- **Atelier Tide One-Page Composition** — templatedesignweb_design_handoff_atelier_tide_readme_top_bar, templatedesignweb_design_handoff_atelier_tide_readme_hero, templatedesignweb_design_handoff_atelier_tide_readme_filmstrip, templatedesignweb_design_handoff_atelier_tide_readme_philosophy, templatedesignweb_design_handoff_atelier_tide_readme_timeline_rail, templatedesignweb_design_handoff_atelier_tide_readme_contact_cta, templatedesignweb_design_handoff_atelier_tide_readme_site_footer [EXTRACTED 1.00]
- **Design Restraint System (one accent, zero radius, one shadow)** — templatedesignweb_design_handoff_atelier_tide_readme_design_tokens, templatedesignweb_design_handoff_atelier_tide_readme_one_accent_rule, templatedesignweb_design_handoff_atelier_tide_readme_zero_radii_rule, templatedesignweb_design_handoff_atelier_tide_readme_offset_shadow, agents_app_css_design_tokens [EXTRACTED 1.00]
- **Accessibility-First Rendering Requirements** — agents_status_pill_label_glyph, agents_card_list_keyboard_path, agents_rust_accent_contrast_rule, templatedesignweb_design_handoff_atelier_tide_readme_known_gaps [INFERRED 0.85]

## Communities (47 total, 7 thin omitted)

### Community 0 - "Facility Catalog & Filters"
Cohesion: 0.07
Nodes (35): el, shown, props, rows, stale(), known, low, percent (+27 more)

### Community 1 - "Composer Manifest"
Cohesion: 0.05
Nodes (40): pestphp/pest-plugin, php-http/discovery, autoload, autoload-dev, psr-4, psr-4, config, allow-plugins (+32 more)

### Community 2 - "Verification Review UI"
Cohesion: 0.09
Nodes (28): meta, props, SOURCES, emit, props, setValue(), decide(), decided (+20 more)

### Community 3 - "NPM Manifest"
Cohesion: 0.07
Nodes (29): concurrently, @laravel/multiplex, laravel-vite-plugin, leaflet, dependencies, leaflet, vue, vue-router (+21 more)

### Community 4 - "Submission Queue & Toasts"
Cohesion: 0.11
Nodes (22): { messages, dismiss }, counts, decide(), history, now(), queue, queueFilters, submissions (+14 more)

### Community 5 - "Composer Scripts"
Cohesion: 0.08
Nodes (26): scripts, dev, post-autoload-dump, post-create-project-cmd, post-root-package-install, post-update-cmd, pre-package-uninstall, setup (+18 more)

### Community 6 - "Auth Scaffold (User Model)"
Cohesion: 0.16
Nodes (10): User, UserFactory, DatabaseSeeder, Illuminate\Database\Console\Seeds\WithoutModelEvents, Illuminate\Database\Eloquent\Factories\Factory, Illuminate\Database\Eloquent\Factories\HasFactory, Illuminate\Database\Seeder, Illuminate\Foundation\Auth\User (+2 more)

### Community 7 - "Architecture Rules (AGENTS.md)"
Cohesion: 0.12
Nodes (17): AksesKita — Disability-Friendly Facility Mapping SPA, Card List as the Keyboard/Screen-Reader Path to the Map, Dummy-Data-Only Frontend Phase, Leaflet L.divIcon Square Markers (no image assets), meta.js as Single Source for Status Labels and Glyphs, Module-Level Singleton Composables, SPA Fallback {any} with /up Excluded, StatusPill: Label + Glyph, Never Colour Alone (+9 more)

### Community 8 - "Map & Facility List"
Cohesion: 0.18
Nodes (13): emit, props, verified, emit, container, drawPickMarker(), emit, label() (+5 more)

### Community 9 - "Contribution Form"
Cohesion: 0.18
Nodes (11): emit, errors, form, photo, preview, submit(), validate(), center (+3 more)

### Community 10 - "Design System Rules"
Cohesion: 0.21
Nodes (13): Design Tokens and Component Classes in resources/css/app.css, bunny() Font Download at Build Time, Rust as Sole Accent, Kept Off Small Text on Ink, TemplateDesignWeb as Read-Only Design Reference, Atelier Tide Single-File HTML Prototype, Atelier Tide (fictional architecture studio), Atelier Tide Design Tokens (paper / ink / rust), Syne / Inter / DM Mono Type Triad (+5 more)

### Community 11 - "Admin Panels (Categories & Users)"
Cohesion: 0.20
Nodes (6): categories, newName, users, ROLES, CATEGORY_SETTINGS, USERS

### Community 12 - "Correction Form"
Cohesion: 0.20
Nodes (10): currentValue, emit, errors, { facilities }, form, photo, props, selected (+2 more)

### Community 13 - "Laravel Feature Tests"
Cohesion: 0.22
Nodes (4): Illuminate\Foundation\Testing\TestCase, AksesKitaTest, ExampleTest, TestCase

### Community 14 - "App Shell & Router"
Cohesion: 0.20
Nodes (5): LINKS, open, route, router, routes

### Community 15 - "Atelier Tide Prototype Sections"
Cohesion: 0.24
Nodes (10): IntersectionObserver Reveal Script, Strip Prev/Next scrollBy(±500) Handlers, Filmstrip / ProjectStrip (horizontal scroll-snap gallery), Hero (display headline + offset-shadow portrait), Build Checklist / Known Gaps, Philosophy Split (image + numbered principles), Single 880px Breakpoint, Scroll Reveal (.reveal + one-shot IntersectionObserver) (+2 more)

### Community 16 - "Commands & Quality Gates"
Cohesion: 0.25
Nodes (8): php artisan test (PHPUnit 12), composer run dev (serve + queue:listen + Vite), composer run setup (one-time install with migrate --force), MySQL in .env vs SQLite :memory: in phpunit.xml, npm run build, .npmrc ignore-scripts=true, vendor/bin/pint (PHP formatting), Only Three Quality Gates (no CI, no static analysis)

### Community 17 - "Template Hero Anatomy"
Cohesion: 0.43
Nodes (8): Atelier Tide Hero Thumbnail (architecture studio landing page screenshot), Brutalist Concrete Colonnade Photo with Dust and Side Light, Editorial Split Hero Layout (oversized headline left, full-bleed photo right), Italic Display Accent Type in Rust ("sideways."), Minimal Uppercase Top Navigation with Language Switcher (EN / IT / ES), Rust Accent on Warm Off-White Palette (ink black type, #A85A32-ish accent), "SCROLL RIGHT" Boxed Directional CTA, "14 PROJECTS / SINCE 2014" Dark Stat Overlay Card

### Community 18 - "Tide Hall Imagery"
Cohesion: 0.48
Nodes (7): Abstract Bronze Maquette on Plinth, Tide Hall Photo (brutalist concrete interior), Repeating Concrete Column Grid and Coffered Ceiling, Glass-Balustrade Mezzanine Walkway, Warm Sun Patches and Atmospheric Haze, One-Point Perspective Toward Hazed Vanishing Point, Lone Walking Figure as Scale Reference

## Ambiguous Edges - Review These
- `Warm Sun Patches and Atmospheric Haze` → `Glass-Balustrade Mezzanine Walkway`  [AMBIGUOUS]
  TemplateDesignWeb/assets/img/15-tide-hall.jpg · relation: semantically_similar_to

## Knowledge Gaps
- **157 isolated node(s):** `Controller`, `$schema`, `name`, `type`, `description` (+152 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **7 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **What is the exact relationship between `Warm Sun Patches and Atmospheric Haze` and `Glass-Balustrade Mezzanine Walkway`?**
  _Edge tagged AMBIGUOUS (relation: semantically_similar_to) - confidence is low._
- **Why does `scripts` connect `Composer Scripts` to `Composer Manifest`?**
  _High betweenness centrality (0.015) - this node is a cross-community bridge._
- **Why does `useFacilities()` connect `Facility Catalog & Filters` to `Verification Review UI`, `Correction Form`, `Submission Queue & Toasts`?**
  _High betweenness centrality (0.011) - this node is a cross-community bridge._
- **Are the 4 inferred relationships involving `useFacilities()` (e.g. with `addFacility()` and `applyCorrection()`) actually correct?**
  _`useFacilities()` has 4 INFERRED edges - model-reasoned connections that need verification._
- **What connects `Controller`, `$schema`, `name` to the rest of the system?**
  _157 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `Facility Catalog & Filters` be split into smaller, more focused modules?**
  _Cohesion score 0.06767676767676768 - nodes in this community are weakly interconnected._
- **Should `Composer Manifest` be split into smaller, more focused modules?**
  _Cohesion score 0.04878048780487805 - nodes in this community are weakly interconnected._