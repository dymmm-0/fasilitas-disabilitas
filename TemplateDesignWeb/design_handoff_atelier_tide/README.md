# Handoff: Atelier Tide — Studio Site (One-Page, Horizontal Filmstrip)

## Overview

A single-page marketing site for **Atelier Tide**, a fictional architecture practice working between Lisbon and Naples. The page presents the studio as an editorial object rather than a portfolio grid: a quiet warm-paper canvas, a large display headline, and a horizontally scrolling "filmstrip" of fourteen projects as the primary browsing gesture.

Structure, top to bottom:

1. **Fixed top bar** — wordmark, centre nav, language switch
2. **Hero** — display headline + tall portrait image with an offset rust shadow and an ink stamp badge
3. **Filmstrip** — horizontally scrollable project cards (scroll-snap), with prev/next controls
4. **Dashed rail divider**
5. **Philosophy split** — image left, editorial copy + numbered principles right
6. **Timeline** — full-bleed ink (near-black) band with eight milestone points on a dashed rail
7. **Closing CTA** — large statement + two buttons
8. **Footer** — four-column grid + legal strip

The page's core idea is direction: everything reads left-to-right, and the studio's work is *walked through sideways*. Preserve that.

---

## About the Design Files

The files in this bundle are **design references created in HTML** — a working prototype that shows the intended look, spacing and behaviour. **They are not production code to copy directly.**

The task is to **recreate these designs inside the target codebase's existing environment** (React, Vue, Svelte, SwiftUI, Flutter, native, etc.) using that codebase's established patterns, component primitives, styling approach and libraries. If no environment exists yet, choose the most appropriate framework for the project and implement the design there.

What to carry over: the visual system (tokens below), the layout composition, the copy, and the interaction behaviour. What *not* to carry over: the single-file `<style>` block, the global CSS selectors, the inline `background-image` URLs, and the Google Fonts `<link>` (self-host or use the codebase's font pipeline instead).

---

## Fidelity

**High-fidelity (hifi).** The prototype uses final colors, typography, spacing, image treatments and hover states. Recreate the UI as close to pixel-accurate as the codebase's own components and design-system conventions allow.

Where the codebase already has an equivalent primitive (a button, a card, a nav bar), prefer that primitive and apply the tokens from this document rather than importing the exact CSS. Where it doesn't exist, build it to the specs here.

Two content placeholders are intentional in the prototype — see [Assets](#assets).

---

## Screens / Views

There is one view: `/` (the home page). Below, each region is documented as a discrete section with exact measurements.

Global assumptions for all measurements:

- The page uses a **max content width of 1480px**, centred (`margin: 0 auto`), with side padding of **32px** on desktop and **18px** below the 880px breakpoint.
- Base `body` font: **Inter 400**, **15–18px** depending on section, `line-height: 1.5`, color `#1f1c17`, background `#f5f1ea`.
- All interactive elements use `transition` on color/background/border/transform only. No scale transforms, no shadows animating.

---

### 1. Top Bar

**Name:** Fixed header / `TopBar`
**Purpose:** Persistent identity and navigation. The only element that does not scroll away.

**Layout**
- `position: fixed; top: 0; left: 0; right: 0; z-index: 50`
- `display: grid; grid-template-columns: auto 1fr auto; align-items: center`
- Padding: **18px 28px**
- Background: `rgba(245, 241, 234, 0.92)` with `backdrop-filter: blur(10px)`
- Bottom border: `1px solid rgba(31, 28, 23, 0.1)`
- Height is content-driven: roughly **22px glyph + 36px padding = ~58px**. Content sections account for this with `padding-top: 120px` on the hero.

**Components**

*Wordmark* — left column
- Text: `Atelier` + `Tide` (the second word is an `<em>`, italic)
- Font: **Syne 700**, **22px**, `letter-spacing: -0.01em`
- Color: `#1f1c17`; the `<em>` is `#a8542a` (rust), italic
- Links to `#` (top of page)

*Nav* — centre column, `justify-self: center`
- Links: **Projects** (`#strip`), **Atelier** (`#philosophy`), **Process** (`#philosophy`), **Journal** (`#`), **Contact** (`#cta`)
- Font: **DM Mono 400**, **12px**, `letter-spacing: 0.06em`, `text-transform: uppercase`
- Color: `#65604f`; gap **24px**
- Hover: color `#a8542a`, `transition: 0.2s`
- No active/current-page styling in the prototype

*Language switch* — right column
- Text: `EN · IT · ES` (single static string, not interactive in the prototype)
- Font: **DM Mono 400**, **11px**, `letter-spacing: 0.18em`, uppercase, color `#65604f`

**Responsive (< 880px):** `grid-template-columns: 1fr auto` (wordmark + language), padding drops to `14px 18px`, and the **nav is hidden entirely** (`display: none`). See [Interactions](#interactions--behaviour) for the mobile menu caveat.

---

### 2. Hero

**Name:** Hero / `Hero`
**Purpose:** Set the tone and route the user to the filmstrip with a single "scroll right" affordance.

**Layout**
- `padding: 120px 32px 60px`; `min-height: 90vh`
- `display: grid; grid-template-columns: 1.3fr 1fr; align-items: center; gap: 60px`
- `max-width: 1480px; margin: 0 auto`

**Left column**

*Eyebrow label*
- Text: `An architecture practice / Est. 2014`
- Font: **DM Mono 400**, **12px**, `letter-spacing: 0.18em`, uppercase, color `#a8542a`
- Margin-bottom **24px**; `display: flex; align-items: center; gap: 14px`
- `::before` pseudo-element: **48px × 1px** rust rule (`#a8542a`) preceding the text

*Headline*
- Text: `A studio, walked through ` + `sideways.` (the final word italic, rust)
- Font: **Syne 500**, `font-size: clamp(60px, 8.5vw, 128px)`, `line-height: 0.96`, `letter-spacing: -0.025em`
- `margin-bottom: 24px`; `text-wrap: balance`
- At 1480px viewport width the effective size is ~126px; at 880px it is ~75px

*Body paragraph*
- Text: "Atelier Tide builds slowly, between Lisbon and Naples, on commissions of which we accept four per year. This site is, deliberately, a hallway — scroll right to walk it."
- Font: **Inter 400**, **18px**, `line-height: 1.55`, color `#3a342a`
- `max-width: 520px`; `margin-bottom: 32px`; `text-wrap: pretty`

*"Scroll right" affordance* (renders as a bordered inline link, not a real button)
- Text: `scroll right →` followed by a separate span containing `↳`
- The `↳` uses **Syne 700**, **18px**
- The rest uses **DM Mono 400**, **12px**, `letter-spacing: 0.12em`, uppercase
- Box: `display: inline-flex; align-items: center; gap: 14px; padding: 14px 22px; border: 1px solid #1f1c17`
- Default: transparent background, ink text
- Hover: background `#1f1c17`, text `#f5f1ea`, `transition: 0.3s`
- Href: `#strip` (smooth-scrolls to the filmstrip)

**Right column**
- Wrapped in a `.reveal` element (see [Animations](#animations--transitions)) — it fades and rises in on load when scrolled into view
- `position: relative`

*Portrait image*
- `aspect-ratio: 3/4`; `background-image: url(assets/img/15-tide-hall.jpg)`; `background-size: cover; background-position: center`
- Offset shadow: `box-shadow: 24px 24px 0 #a8542a` — a hard rust block, bottom-right, no blur. This is a signature treatment; keep the 24px offset and zero blur.
- No border radius anywhere.

*Stamp badge*
- `position: absolute; top: -20px; right: -20px`
- Background `#1f1c17`, text `#f5f1ea`, padding **14px 18px**
- Two lines: `14 projects` (DM Mono 400, 11px, `letter-spacing: 0.18em`, uppercase) and `since 2014` inside a `<b>` (**Syne 700, 28px**, `letter-spacing: -0.02em`, `display: block`, paper color)
- `line-height: 1.4`

**Responsive (< 880px):** single column, `padding: 100px 18px 60px`, `gap: 40px`; the image becomes `aspect-ratio: 4/5`.

---

### 3. Filmstrip

**Name:** Filmstrip / `ProjectStrip`
**Purpose:** The primary browsing surface. Users move horizontally through projects; the strip is the page's "sidetrack".

**Section layout**
- `padding: 60px 0 100px`; `border-top: 1px solid rgba(31, 28, 23, 0.15)`
- Full-bleed: the section itself is not width-capped, but the header is.

**Header**
- `padding: 0 32px 36px`; `display: grid; grid-template-columns: 1fr auto; align-items: end`
- `max-width: 1480px; margin: 0 auto`
- Heading: `The ` + `filmstrip` (italic rust) + `<br>` + `— fourteen projects in twelve years.`
  - **Syne 500**, **64px**, `line-height: 1`, `letter-spacing: -0.025em`, `max-width: 780px`, `text-wrap: balance`
- Controls: `display: flex; gap: 8px`, two buttons (`←`, `→`) with `aria-label="Scroll left"` / `"Scroll right"`
  - **44px × 44px**, `border: 1px solid #1f1c17`, background `#f5f1ea`, **Syne 700 18px**
  - Hover: background `#1f1c17`, color `#f5f1ea`, `transition: 0.25s`
  - Hit target is 44px — keep it at or above 44px on touch.

**Scroller**
- `display: flex; gap: 24px; padding: 0 32px 24px`
- `overflow-x: auto`; `scroll-snap-type: x mandatory`; `scroll-padding-left: 32px`
- `scrollbar-width: thin; scrollbar-color: #a8542a transparent`
- WebKit: `::-webkit-scrollbar { height: 8px }`, thumb `#a8542a` with `border-radius: 99px`, track `rgba(31, 28, 23, 0.06)`
- The 8px scrollbar sits inside the 24px bottom padding.

**Project card** (`article.frame`)
- `flex: 0 0 460px` (fixed card width — do not let cards flex); `scroll-snap-align: start`
- `display: grid; grid-template-rows: auto 1fr`; background `#f5f1ea`
- Border: `1px solid rgba(31, 28, 23, 0.15)`; **no border radius**
- `transition: 0.4s`; hover: `transform: translateY(-6px)`, border-color `#1f1c17`

**Card image block** (top grid row)
- `aspect-ratio: 4/3`; `background-size: cover; background-position: center`
- Overlay: `linear-gradient(180deg, transparent 60%, rgba(0, 0, 0, 0.4))` via `::after`
- Year badge: `position: absolute; top: 18px; left: 18px`; background `#f5f1ea`; text `#1f1c17`; padding **6px 12px**; **DM Mono 500, 11px**, `letter-spacing: 0.18em`. Text format: `2014 / 01`.

**Card body**
- Padding **28px 24px**
- Meta row: `display: flex; justify-content: space-between`; **DM Mono 400, 11px**, `letter-spacing: 0.06em`, uppercase, color `#65604f`, margin-bottom **10px**. Left = discipline (`Cultural · Civic`), right = city (`Lisbon, PT`).
- Title: **Syne 700, 28px**, `line-height: 1.05`, `letter-spacing: -0.015em`, margin-bottom **10px**, `text-wrap: balance`; the italic `<em>` inside is `#a8542a` at weight **500**
- Description: **Inter 400, 14px**, `line-height: 1.55`, color `#3a342a`, margin-bottom **14px**
- Specs row: `display: grid; grid-template-columns: 1fr 1fr; gap: 6px`; **DM Mono 400, 11px**, color `#65604f`, uppercase, `letter-spacing: 0.06em`; `margin-top: 18px; padding-top: 14px; border-top: 1px dashed rgba(31, 28, 23, 0.2)`
  - Each cell is a `<span>` label (e.g. `Programme`) with a `<b>` value beneath (`display: block`; **Inter 500, 13px**; color `#1f1c17`; `letter-spacing: 0`; `text-transform: none`; `margin-top: 2px`)

**Card content (6 cards, in order).** Each card's `yr` badge is the project index within the studio's 14 total, not the card index.

| # | Year badge | Meta left | Meta right | Title | Description | Specs (label / value) |
|---|---|---|---|---|---|---|
| 1 | `2014 / 01` | Cultural · Civic | Lisbon, PT | Casa do *Eco* | A 240 m² civic chamber for an old olive press in Alfama, rebuilt as a small chamber-music hall. Our first built project. | Programme / 240 m² · Civic — Completion / March 2014 |
| 2 | `2016 / 03` | Exhibition · Pavilion | Venice, IT | Pavilion *delle Maree* | Our entry for the Venice Architecture Biennale — a five-room timber pavilion sited on the Arsenale that filled and emptied with the lagoon's twelve-hour cycle. | Programme / 180 m² · Exhibition — Open / Jun–Nov 2016 |
| 3 | `2019 / 06` | Residence · Single-family | Sintra, PT | Casa *Onda* | A 320 m² private house drawn around a single olive tree the client refused to remove. The plan, in a manner of speaking, drew itself. | Programme / 320 m² · Private — Completion / October 2019 |
| 4 | `2022 / 10` | Education · Refurbishment | Naples, IT | Scuola *San Paolo* | The refurbishment of a 1950s school in Quartieri Spagnoli, restored to its original 1953 daylighting drawings. Wallpaper House of the Year, 2023. | Programme / 1,820 m² · Education — Completion / Sept 2022 |
| 5 | `2024 / 13` | Office · Adaptive reuse | Porto, PT | Sede *do Cinque* | The headquarters of Aperture° Type Foundry — a former tannery, opened up around a single 14-metre concrete table. | Programme / 820 m² · Office — Completion / April 2024 |
| 6 | `2026 / 14` | Cultural · Library · ongoing | Mendrisio, CH | Library, *Mendrisio* | A public library for the Canton of Ticino, currently in construction. The drawings have been on display in Mendrisio since June. | Programme / 1,420 m² · Civic — Completion / Q4 2027 |

**Card imagery — important.** Cards 1 and 2 use the two real photographs. Cards 3–6 use **CSS gradient placeholders** in the prototype because no photography was available:

- Card 3: `linear-gradient(135deg, #928466, #5a4f3a)`
- Card 4: `linear-gradient(180deg, #3a342a, #1f1c17)`
- Card 5: `linear-gradient(135deg, #a8542a, #65604f)`
- Card 6: `linear-gradient(135deg, #f5f1ea, #928466)`

Replace these with real project photography at 4:3. Keep the same crop behaviour (`background-size: cover`) and keep the dark gradient overlay only where the image needs it for the year badge to read — note the badge is paper-on-paper on light imagery (card 6), which currently has low contrast and should be resolved when real images land.

**Responsive (< 880px):** header becomes one column with `gap: 18px`; heading drops to **42px**; scroller padding `0 18px 24px`; cards become `flex-basis: 88vw`.

---

### 4. Dashed Rail Divider

**Name:** `StripLine`
**Purpose:** A hairline "fishline" that closes the filmstrip metaphor before the philosophy section.

- `height: 1px`
- Background: `repeating-linear-gradient(90deg, #a8542a 0 8px, transparent 8px 14px)`
- `margin: 0 32px 60px`; `max-width: 1480px` with `margin-left/right: auto`
- `::before` and `::after` render the character `●` in rust, **14px**, vertically centred, at the far left and far right ends
- Mobile: `margin: 0 18px 60px`

---

### 5. Philosophy Split

**Name:** Philosophy / `Philosophy`
**Purpose:** Explain the practice's constraints and working method — the "why we're exclusive" argument.

**Layout**
- `padding: 140px 32px`; `border-top: 1px solid rgba(31, 28, 23, 0.15)`
- `display: grid; grid-template-columns: 1fr 1fr; gap: 80px; align-items: center`
- `max-width: 1480px; margin: 0 auto`

**Left: image**
- `aspect-ratio: 5/4`; `background-image: url(assets/img/15-tide-gallery.jpg)`; cover/center
- No border, no radius, no shadow (deliberate contrast with the hero's offset shadow)
- Wrapped in `.reveal`

**Right: copy**
- *Label*: same recipe as the hero eyebrow (`How we work`, DM Mono 12px, `0.18em`, uppercase, rust) but the rule is drawn as an inline `<span>` 48px × 1px rather than a `::before` — visible in the prototype as a slightly lighter 1px line. Standardise this to one component during implementation.
- *Heading*: `Four projects a year. ` + `One studio, two cities.` (italic rust)
  - **Syne 500, 60px**, `line-height: 1`, `letter-spacing: -0.025em`, margin-bottom **24px**, `text-wrap: balance`
- *Lede*: "A practice run from a former boatyard in Lisbon and a small apartment above a bakery in Naples, by eight architects who insist on detailing every corner themselves."
  - **Syne 500 italic, 24px**, `line-height: 1.35`, `letter-spacing: -0.005em`, color `#1f1c17`, margin-bottom **24px**
- *Body*: "We accept commissions on a rolling basis through May 31 each year — four per cycle, by partner preference. We do not enter open competitions. We do not, with apologies, do "concept" work."
  - **Inter 400, 17px**, `line-height: 1.65`, color `#3a342a`, `max-width: 520px`, margin-bottom **18px**

**Principles list**
- Container: `margin-top: 36px; padding-top: 24px; border-top: 1px solid #1f1c17`
- Each row: `display: grid; grid-template-columns: 60px 1fr`; `padding: 16px 0`; `border-bottom: 1px solid rgba(31, 28, 23, 0.15)`; text **15px**, `line-height: 1.5`; `align-items: start`
- Numeral: **Syne 700, 22px**, `#a8542a`, `line-height: 1`, `letter-spacing: -0.01em` — lowercase roman: `i.` `ii.` `iii.` `iv.`
- Text color `#3a342a`

| # | Text |
|---|---|
| i. | One partner leads each project from brief to last screw. |
| ii. | We build, when possible, within 200 km of the site. |
| iii. | Two materials per project. Three is a compromise. |
| iv. | We don't publish renderings — only photographs, of the built thing. |

- Right column wrapped in `.reveal`

**Responsive (< 880px):** single column, `gap: 40px`, side padding `18px`, heading **42px**.

---

### 6. Timeline

**Name:** Timeline / `TimelineRail`
**Purpose:** Studio milestones, 2014 → 2026. The one inverted (dark) section on the page — it separates the two light halves of the page and adds rhythm before the CTA.

**Layout**
- `padding: 80px 32px`; `border-top: 1px solid rgba(31, 28, 23, 0.15)`
- Background `#1f1c17`; text `#f5f1ea`; `overflow-x: auto`

**Heading**
- `A studio, walked ` + `year by year.` (italic rust)
- **Syne 500, 64px**, `line-height: 1`, `letter-spacing: -0.025em`, `margin-bottom: 48px`
- `max-width: 1480px` with auto side margins (aligned to the page grid)

**Rail**
- `max-width: 1480px; margin: 0 auto`
- `display: grid; grid-template-columns: repeat(8, 1fr); gap: 18px; position: relative`
- Connecting line: `::before` at `top: 14px`, `left: 0; right: 0`, `height: 1px`, same dashed rust pattern as section 4 (`repeating-linear-gradient(90deg, #a8542a 0 8px, transparent 8px 14px)`)

**Milestone** (`div.tn`)
- `padding-top: 36px`; **13px**, `line-height: 1.5`; `position: relative`
- Dot: `::before` at `top: 9px; left: 0`, **12px × 12px**, `#a8542a`, `border-radius: 50%` (the only circular element in the design)
- Year: **Syne 700, 24px**, paper color, margin-bottom **4px**, `letter-spacing: -0.01em`; an `<em>` inside is italic rust
- Description: `rgba(245, 241, 234, 0.7)`, **13px**

| Year | Description |
|---|---|
| 2014 | Founded · Lisbon |
| 2016 | Venice Biennale, Pavilion delle Maree |
| 2018 | Mies van der Rohe Award · Emerging |
| 2019 | Casa Onda · House of the Year, AJ |
| 2021 | Naples studio opens |
| 2022 | Scuola San Paolo · Wallpaper HOY '23 |
| 2024 | Sede do Cinque · DAM Prize shortlist |
| 2026 | *Library, Mendrisio* under construction *(italic rust on the project name)* |

**Responsive (< 880px):** `padding: 60px 18px`; heading **36px**; rail becomes `grid-template-columns: 1fr 1fr`. The connecting rail line becomes misleading in a 2-column layout — either hide `::before` at this breakpoint or switch to a vertical rail. Recommend a vertical rail (line on the left, dots aligned to it) for the mobile treatment.

---

### 7. Closing CTA

**Name:** CTA / `ContactCTA`
**Purpose:** Single conversion point — start a brief, or subscribe.

- `padding: 160px 32px`; `text-align: center`; `border-top: 1px solid rgba(31, 28, 23, 0.15)`
- `max-width: 1100px; margin: 0 auto`
- Heading: `Briefs for 2027 ` + `open in May.` (italic rust)
  - **Syne 500**, `clamp(56px, 8vw, 108px)`, `line-height: 0.96`, `letter-spacing: -0.03em`, margin-bottom **36px**, `text-wrap: balance`
- Button row: `display: inline-flex; gap: 14px; flex-wrap: wrap; justify-content: center`

*Primary button* — text `Send a brief →`
- Padding **16px 28px**; background `#1f1c17`; color `#f5f1ea`; `border: 1px solid #1f1c17`
- **DM Mono 400, 13px**, `letter-spacing: 0.1em`, uppercase; `transition: 0.3s`
- Hover: background `#a8542a`, border-color `#a8542a` (text stays paper)
- Href `#` — should become the contact/brief route

*Secondary button* — text `Subscribe to the journal`
- Padding **16px 24px**; transparent background; `border: 1px solid #1f1c17`; color `#1f1c17`
- Same type spec as primary; `transition: 0.3s`
- Hover: background `#1f1c17`, color `#f5f1ea`
- Href `#`

Both buttons are rectangular with zero radius. Their heights differ by 4px because of the differing horizontal padding only — heights match (13px type + 32px padding + 2px border ≈ 47px).

---

### 8. Footer

**Name:** `SiteFooter`

- `padding: 60px 32px 30px`; `border-top: 1px solid #1f1c17` (solid ink, heavier than every other divider); base **13px**

**Grid**
- `display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 32px`
- `max-width: 1480px; margin: 0 auto`

*Lead column*
- Wordmark `Atelier Tide` at **32px** (same Syne 700 styling as the header, `<em>` italic rust), `display: block`, margin-bottom **14px**
- Paragraph: "An architecture studio based in Lisbon & Naples. Founded 2014. Eight architects. Four projects a year." — `line-height: 1.55`, `max-width: 300px`, `#3a342a`

*Three link columns* — headings `Projects`, `Atelier`, `Reach`
- Heading: **DM Mono 500, 11px**, `letter-spacing: 0.18em`, uppercase, `#65604f`, margin-bottom **14px**
- Links: `display: block; padding: 4px 0`, color `#3a342a`, `transition: 0.2s`, hover `#a8542a`

| Projects | Atelier | Reach |
|---|---|---|
| Built 2014–24 | About | studio@ateliertide.example *(mailto)* |
| Ongoing | Process | Lisbon · +351 22 0000 |
| Publications | Journal | Naples · +39 081 0000 |
| Press | Lectures | |

**Legal strip**
- `max-width: 1480px; margin: 48px auto 0; padding-top: 20px`
- `border-top: 1px solid rgba(31, 28, 23, 0.15)`
- `display: flex; justify-content: space-between; flex-wrap: wrap; gap: 8px`
- **DM Mono 400, 11px**, `letter-spacing: 0.06em`, color `#65604f`
- Left: `© Atelier Tide, Lda · 2014–2026 · All rights reserved`
- Right: `Privacy · Imprint · Reg. Lda 514 220 220`

**Responsive (< 880px):** `grid-template-columns: 1fr 1fr`, `gap: 24px`; side padding `18px`.

---

## Interactions & Behaviour

### Navigation

- The header links are in-page anchors. Clicking **Projects** or the hero's *scroll right* link scrolls to `#strip`; **Atelier** and **Process** both go to `#philosophy`; **Contact** goes to `#cta`.
- Global `html { scroll-behavior: smooth }` provides the smooth scroll. The fixed header is ~58px tall and no `scroll-margin-top` is set — **implementation should add `scroll-margin-top: 80px`** to the anchor targets so headings aren't hidden behind the bar.
- There is no mobile menu. Below 880px the nav simply disappears. **This is a known gap** — implement a mobile nav (sheet or full-screen overlay) using the codebase's existing pattern.

### Filmstrip scrolling

- The prev/next buttons call `scrollBy({ left: ±500, behavior: 'smooth' })` on the scroller element. 500px is roughly one card (460px) plus its gap (24px) — the intent is "advance one card".
- Recommendation for implementation: use the actual card width + gap computed from the DOM rather than the hard-coded 500, so it stays correct at the 88vw mobile card width. Or use `scrollIntoView`-equivalent APIs — but **not** `Element.scrollIntoView`, which can move the whole document.
- Native horizontal scroll, trackpad swipe, and touch drag all work by default. `scroll-snap-type: x mandatory` gives the card-by-card feel; keep it.
- The controls do not disable at the ends of the strip (no "at start / at end" state). This is intentional in the prototype; if implementing end-detection, also add the `disabled` visual (reduced opacity, `cursor: not-allowed`) to match the button style.

### Hover states

| Element | Default | Hover |
|---|---|---|
| Nav links | `#65604f` | `#a8542a` (0.2s) |
| Hero scroll link | 1px ink border, paper bg | ink bg, paper text (0.3s) |
| Strip buttons | 1px ink border, paper bg | ink bg, paper text (0.25s) |
| Project card | `translateY(0)` | `translateY(-6px)` + ink border (0.4s) |
| Primary CTA | ink bg | rust bg (0.3s) |
| Secondary CTA | outline | ink bg, paper text (0.3s) |
| Footer links | `#3a342a` | `#a8542a` (0.2s) |

There are **no `:focus` / `:focus-visible` styles** in the prototype. Implementation should add a visible focus ring for keyboard users on every link and button — suggest `outline: 2px solid #a8542a; outline-offset: 3px` to stay in palette.

### Scroll reveal

- Elements with class `.reveal` start at `opacity: 0; transform: translateY(28px)` and transition to `opacity: 1; transform: none` over **0.8s ease** when they enter the viewport.
- Trigger: `IntersectionObserver` with `threshold: 0.1`; the class `.in` is added once and never removed (one-shot reveal, no reverse animation on scroll-up).
- Applied to: hero image block, philosophy image, philosophy copy. That's it — the filmstrip, timeline, CTA and footer are always visible.
- **Accessibility requirement:** honour `prefers-reduced-motion: reduce` by skipping the transform (or both properties) and rendering the content immediately. The prototype does not do this.
- Implementation note: with a JS framework, prefer a small `Reveal` wrapper component or the codebase's existing scroll-animation utility over a raw observer per element.

### Loading / error / validation states

None exist in this design. There are no forms, no async data, and no loading skeletons. If the project list becomes CMS-driven, add: a skeleton card state matching the card dimensions (460×~640 at desktop) and an empty state for the strip.

### Responsive summary

Single breakpoint at **880px**. It is the only breakpoint in the design.

| Region | Desktop | < 880px |
|---|---|---|
| Side padding | 32px | 18px |
| Header | 3 columns, nav visible | 2 columns, nav hidden |
| Hero | 1.3fr / 1fr, image 3:4 | stacked, image 4:5 |
| Hero h1 | clamp → ~126px @1480 | ~75px @880 |
| Strip heading | 64px | 42px |
| Cards | 460px fixed | 88vw |
| Philosophy | 2 columns, 80px gap | stacked, 40px gap |
| Philosophy h3 | 60px | 42px |
| Timeline | 8 columns | 2 columns |
| Timeline heading | 64px | 36px |
| Footer | 2fr 1fr 1fr 1fr | 1fr 1fr |

Tablet widths between 880px and ~1100px use the desktop layout as-is; the filmstrip is unaffected (it's always horizontal), but the hero headline and the 8-column timeline get tight — verify at 900–1024px and consider an intermediate breakpoint for the timeline (4 columns) if it crowds.

---

## State Management

The prototype is effectively stateless. The state to model in a real app:

| State | Type | Notes |
|---|---|---|
| `projects` | array of project objects | Drives the filmstrip cards. Six in the design; the copy claims fourteen. Fields: `id`, `year` (4-digit), `index` (card's ordinal within the fourteen, 1-based), `disciplines` (string, `·`-joined), `location` (city + country code), `title` (with an optional italic fragment), `description`, `mediaType` (`photo` / `gradient`), `imageUrl` / `gradient`, `specs` (array of `{ label, value }`) — or explicit `programme` / `status` fields. |
| `milestones` | array of `{ year, label, emphasis? }` | Drives the timeline. `emphasis` marks the italic-rust partial in 2026. |
| `principles` | array of `{ numeral, text }` | Four items, numerals `i.`–`iv.` |
| `isMobileNavOpen` | boolean | Not in the design — needed once the mobile nav is built. |
| `revealedIds` | set / per-element boolean | If the reveal is implemented in app state rather than CSS classes. |

**Data fetching:** none required. All copy is static and small enough to inline; if a CMS drives the projects, the fetch happens at page level and the strip renders from that array. No pagination, no infinite scroll — the strip shows the full set.

**Content consistency to resolve before build:** the page says "fourteen projects in twelve years," the hero badge says "14 projects since 2014," and the strip shows six cards. The six cards are numbered 01, 03, 06, 10, 13, 14, which intentionally implies the others exist but aren't shown. Decide whether the real site shows all fourteen (then the strip needs a scrollbar affordance or a "see all" link) or keeps the curated six (then consider a caption noting "selected projects").

---

## Design Tokens

### Colors

| Token | Hex | Role |
|---|---|---|
| `--paper` | `#f5f1ea` | Page background, card background, text on ink |
| `--ink` | `#1f1c17` | Primary text, borders, timeline background, primary button |
| `--rust` | `#a8542a` | Sole accent: italics, rules, dots, shadows, hovers |
| `--warm` | `#928466` | Warm stone — appears only in placeholder gradients |
| `--ink-muted` | `#65604f` | Meta text, nav, labels, legal |
| `--body` | `#3a342a` | Body copy (softer than ink) |

Supporting alphas (all on ink `#1f1c17`):
- `rgba(31, 28, 23, 0.1)` — header bottom border
- `rgba(31, 28, 23, 0.15)` — section top borders, card border, principle dividers, footer legal divider
- `rgba(31, 28, 23, 0.2)` — card specs dashed divider
- `rgba(31, 28, 23, 0.06)` — scrollbar track
- `rgba(245, 241, 234, 0.92)` — header background
- `rgba(245, 241, 234, 0.7)` — timeline descriptions
- `rgba(0, 0, 0, 0.4)` — card image overlay terminus

Gradient stops used as image placeholders: `#928466`, `#5a4f3a`, `#3a342a`, `#1f1c17`, `#a8542a`, `#65604f`, `#f5f1ea`.

There is exactly **one** accent hue. Do not introduce a second accent when building this out; the restraint is the design.

### Typography

| Role | Family | Weights loaded | Usage |
|---|---|---|---|
| Display | **Syne** | 500, 700, 800 | Headlines, card titles, wordmark, years, numerals. 500 for headings, 700 for titles/wordmark/years. 800 is loaded but unused. |
| Body | **Inter** | 400, 500 | Paragraphs, spec values, base text |
| Utility | **DM Mono** | 400, 500 | Eyebrows, meta, labels, buttons, nav, legal |

Fallback stacks as written: `'Inter', sans-serif` — the codebase should supply its own fallback chain (including a metric-compatible fallback to control layout shift).

Type scale in use:

| Size | Weight | Where |
|---|---|---|
| `clamp(60px, 8.5vw, 128px)` / 0.96 | Syne 500 | Hero h1 |
| `clamp(56px, 8vw, 108px)` / 0.96 | Syne 500 | CTA h2 |
| 64px / 1.0 | Syne 500 | Filmstrip + timeline headings |
| 60px / 1.0 | Syne 500 | Philosophy h3 |
| 42px | Syne 500 | Filmstrip + philosophy h3 (mobile) |
| 36px | Syne 500 | Timeline heading (mobile) |
| 32px | Syne 700 | Footer wordmark |
| 28px / 1.05 | Syne 700 | Card titles |
| 28px | Syne 700 | Hero stamp value |
| 24px / 1.35 | Syne 500 italic | Philosophy lede |
| 24px | Syne 700 | Timeline year |
| 22px / 1.0 | Syne 700 | Principle numerals |
| 22px | Syne 700 | Header wordmark |
| 18px / 1.55 | Inter 400 | Hero paragraph |
| 18px | Syne 700 | `↳` glyph |
| 17px / 1.65 | Inter 400 | Philosophy body |
| 15px / 1.5 | Inter 400 | Principle text |
| 14px / 1.55 | Inter 400 | Card description |
| 13px | Inter 500 | Card spec value |
| 13px / 1.5 | Inter/DM Mono | Timeline description, footer base, CTA buttons |
| 12px | DM Mono 400 | Eyebrows, nav, hero scroll link |
| 11px | DM Mono 400/500 | Meta, year badge, language switch, footer headings, legal |

Letter-spacing conventions: display headlines `-0.025em` to `-0.03em`; Syne titles `-0.015em`; uppercase mono labels `0.06em`–`0.18em` (11px labels take the widest tracking); body `0`.

### Spacing scale

The page uses an informal 4px-based scale. Values actually in use: **2, 4, 6, 8, 10, 12, 14, 16, 18, 20, 22, 24, 28, 30, 32, 36, 40, 48, 60, 80, 100, 120, 140, 160** px. Map to the codebase's nearest token rather than introducing new steps.

Section rhythm (vertical padding):

| Section | Padding |
|---|---|
| Hero | `120px 32px 60px` |
| Filmstrip | `60px 0 100px` |
| Divider | `0 32px 60px` |
| Philosophy | `140px 32px` |
| Timeline | `80px 32px` |
| CTA | `160px 32px` |
| Footer | `60px 32px 30px` |

### Radii

**Zero everywhere**, with a single exception: the filmstrip scrollbar thumb (`border-radius: 99px`) and the timeline dots (`border-radius: 50%`). Cards, buttons, images, badges, stamps and inputs are all hard-edged. Do not add radius during implementation.

### Shadows

One shadow in the entire design:

`box-shadow: 24px 24px 0 #a8542a` — hero image only. Hard offset, no blur, no spread, no alpha.

Everything else separates elements with 1px borders and flat background shifts.

### Motion

| Property | Duration | Easing |
|---|---|---|
| Nav / footer link color | 0.2s | default (ease) |
| Strip buttons | 0.25s | default |
| Buttons (all) | 0.3s | default |
| Card hover | 0.4s | default |
| Scroll reveal | 0.8s | `ease` |
| Smooth scroll | native `scroll-behavior: smooth` | UA |

No springs, no stagger, no parallax. The design should feel slow and deliberate.

---

## Assets

| File | Dimensions / size | Used in | Notes |
|---|---|---|---|
| `assets/img/15-tide-hall.jpg` | **1914 × 822, ~1.9 MB** (PNG data, `.jpg` extension) | Hero portrait (3:4 crop), filmstrip card 1 image (4:3 crop) | Raw concrete interior with column rhythm and light shafts. Note it is a wide landscape image (~2.3:1) used in a portrait frame — the 3:4 hero crop discards roughly 70% of the width, keeping only the centre. If this asset is replaced, supply a portrait crop. |
| `assets/img/15-tide-gallery.jpg` | **1536 × 1024, ~2.3 MB** (PNG data, `.jpg` extension) | Philosophy split image (5:4) | Gallery wall with framed drawings on board-marked concrete. |
| *(none)* | — | Filmstrip cards 3–6 | CSS gradient placeholders. **Need real project photography** at 4:3, one per project. |
| *(none)* | — | Favicon, OG image | Not defined. Needs a favicon and social card. |

Fonts are loaded from Google Fonts: `Syne:wght@500;700;800`, `Inter:wght@400;500`, `DM+Mono:wght@400;500`. For handoff, self-host them (or use the codebase's font pipeline) — do not ship the `<link>` tag.

No icon library is used. The only glyphs are typographic: `→ ← ↳ ●`.

---

## Files in This Bundle

| File | What it is |
|---|---|
| `design_handoff_atelier_tide/index.html` | The complete prototype — markup, all CSS in one `<style>` block, and ~8 lines of JS for the strip controls and the reveal observer. This is the visual source of truth. |
| `design_handoff_atelier_tide/tokens.css` | The design tokens extracted into CSS custom properties, for direct import or translation into the codebase's token system. |
| `design_handoff_atelier_tide/README.md` | This document. |

The images live in the project at `assets/img/` and are referenced from the prototype by relative path. They are not duplicated in this folder because of their size — copy them from the project root when building.

---

## Build Checklist / Known Gaps

Ordered roughly by importance. Items 1–4 must be resolved; 5–8 are recommended.

1. **Mobile navigation** — the nav disappears below 880px with no replacement. Build one.
2. **Keyboard focus styles** — none exist. Add a visible focus ring in rust on all interactive elements.
3. **`prefers-reduced-motion`** — the reveal animation and smooth scrolling both need a reduced-motion path.
4. **Anchor offset** — add `scroll-margin-top` (~80px) to `#strip`, `#philosophy`, `#cta` so the fixed header doesn't cover section headings.
5. **Project imagery** — cards 3–6 are gradients; year-badge contrast on light imagery (card 6) needs fixing when photos land.
6. **Timeline on mobile** — the horizontal rail line is wrong in a 2-column grid; switch to a vertical rail.
7. **Strip end states** — controls don't disable at either end; card-step is hard-coded to 500px.
8. **Content reconciliation** — "fourteen projects" vs. six cards; decide the real model. Also add a favicon and OG image.
