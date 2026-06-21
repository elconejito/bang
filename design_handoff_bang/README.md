# Handoff: Bang — Firearms Inventory & Training Tracker

## Overview
Bang is a **personal** (non-commercial) web app for tracking firearms and related
equipment across four areas — **Firearms, Ammo, Accessories, Training** — plus a
shared **Gallery** for images. This package contains the complete high-fidelity
UI design for every major screen, plus the design system that defines the visual
language.

The guiding product principle: a firearm's own data rarely changes, so the app is
built around **logging activity against items** (range sessions, cleanings, mounts,
stock changes) and keeping running counts in sync automatically.

---

## About the Design Files
The files in this bundle are **design references created in HTML** — prototypes that
show the intended look, layout, and behavior. **They are not production code to copy
directly.**

The target codebase already exists: **Laravel + Vue 3 + Tailwind CSS** (see
`resources/front-end/` in the `bang` repo). The task is to **recreate these designs as
Vue single-file components using the codebase's existing patterns, router, and Tailwind
config** — not to drop the HTML in. Map the inline styles in these prototypes to Tailwind
utility classes (the values below are chosen to line up with a standard Tailwind scale),
and wire real data from the existing Laravel models (`Firearm`, `Ammunition`, `Accessory`,
`TrainingSession`, etc.).

### Why HTML / how to read it
Each `.dc.html` file is a self-contained prototype. Styling is done with **inline styles**
on purpose (so the prototype renders progressively) — when porting, convert these to
Tailwind classes. The `image-slot.js` and `support.js` files are prototype-runtime only and
**should not be ported** — replace `<image-slot>` with the codebase's real image
upload/display component.

---

## Fidelity
**High-fidelity (hifi).** These are pixel-level mockups with final colors, typography,
spacing, icons, and interaction affordances. Recreate the UI faithfully using the
codebase's Tailwind setup and component conventions. Exact tokens are listed under
**Design Tokens** below; per-screen specifics are under **Screens**.

The `*_Wireframes.dc.html` files (in `wireframes/`) are the earlier low-fidelity
explorations that led here — included for context on *why* layouts are the way they are.
Do not implement from those; implement from the hifi files.

---

## Design Tokens

### Color
| Token | Hex | Use |
|---|---|---|
| `brass/wash` | `#F4ECD6` | subtle accent fills, focus ring backdrop |
| `brass/border` | `#E3D3A3` | borders on brass elements |
| `brass/500` ★ | `#C2A14D` | **primary accent** — primary buttons, active nav underline, key data highlight, focus ring |
| `brass/hover` | `#A8842F` | primary button hover |
| `brass/text` | `#7D6320` | brass text on light bg (links, "Stock"/"Move" actions) — AA-safe |
| `ink/900` | `#1A1C1F` | primary text, app bar bg, dark buttons |
| `ink/700` | `#3A3E44` | secondary text |
| `ink/500` | `#5B6066` | body text muted |
| `muted` | `#8A9098` | tertiary text, eyebrow labels, icon strokes |
| `line` | `#E2E4E6` | hairline borders / dividers |
| `canvas` | `#ECEEF0` | page background |
| `surface` | `#FFFFFF` | card background |
| `success` | `#2F7D57` / bg `#E7F1EB` / border `#9CCBB1` | mounted / in-stock / "in gun" |
| `caution` (low) | `#B4452F` / bg `#F7E9E4` / border `#E0A999` | low-stock / remove / deduct |
| `special` (NFA) | `#6B5A8C` / bg `#EEE9F3` / border `#C3B6D6` | suppressed / NFA / adjustments |

Mag status dots: in-gun `#2F7D57`, loaded-spare `#C2A14D`, empty `1.5px` border `#B6BCC1`.

### Typography
Load via Google Fonts.
- **Display / headers** — `Schibsted Grotesk`, weights 500/600/700/800. Tight tracking on
  large sizes (`letter-spacing: -0.02em` at H1). Page H1 = 28–30px/700; section H2 = 22px/700;
  card title = 16–18px/600.
- **Body / UI** — `Hanken Grotesk`, weights 400/500/600/700. Body 15px/400; labels 14px/500;
  buttons 14–15px/600.
- **Data / numbers** — `JetBrains Mono`, weights 400/500/600. **All** counts, serials, dates,
  prices, $/rd, and eyebrow labels. Big stat numbers 22–30px/500; eyebrow labels 10–11px/500
  with `letter-spacing: 0.05–0.1em`, color `muted`, often UPPERCASE.

### Spacing — 8px grid
`4, 8, 12, 16, 24, 32, 48`. Card padding 14–16px; section gaps 16px; page gutters 32px.

### Shape & elevation
- **Border radius: `3px`** on essentially everything (cards, buttons, inputs, chips, badges,
  swatches, tiles). Pills/toggles/status-dots use `999px`. Modals `4px`.
- **Borders, not shadows, define structure** — `1px solid #E2E4E6` hairlines.
- **Card hover lift** (lists only): `border-color:#C2C6CA; box-shadow: 0 1px 2px rgba(20,22,26,0.05), 0 8px 20px rgba(20,22,26,0.07)`. Detail/dashboard cards do **not** lift.
- **Modal/overlay shadow**: `0 10px 30px rgba(20,22,26,0.22), 0 2px 8px rgba(20,22,26,0.12)`; scrim `rgba(20,22,26,0.46)`.
- **Focus ring** (active input): `1.5px solid #C2A14D` + `box-shadow: 0 0 0 3px #F4ECD6`.

### Icons
**Lucide** (https://lucide.dev), ISC-licensed, used as **inline SVG** (never an icon font),
`stroke-width: 1.75–2`, `currentColor`. The codebase should add `lucide-vue-next` (or
equivalent) rather than hand-pasting SVGs. Icons used: crosshair, plus, search, filter,
chevron-down/right/up, pencil (edit), trash, check, alert-triangle, package (ammo),
calendar, replace/move (suppressor & mounts), camera, lightbulb (light), scope/target
(optic), battery, brush-cleaning (clean), history, map-pin (location), home (firearm),
star (primary photo), upload, link (shared photo), lock (NFA), columns (mag).

---

## Global Layout (every screen)

**App bar** — sticky, 56px tall, `#1A1C1F` bg. Left: reticle logo mark (28px circle, brass
center dot, 4 tick marks) + "Bang" wordmark (Schibsted 800, white). Center: nav —
`Firearms · Ammo · Accessories · Training`; active item white with a `2px #C2A14D`
bottom border, others `#CFD3D7` → white on hover. Right: 26px avatar circle (`#3A3E44`,
mono initial) + name + chevron (account menu).

**Page container** — centered, `max-width` varies by screen (640–1280px), `padding: 24px 32px 64px`.

**Breadcrumb** — 13px `muted`, home icon + chevron separators, last crumb `#3A3E44`/500.

**Page header** — H1 + optional mono count chip + right-aligned action buttons.

Primary button = brass (`#C2A14D` bg, `#1A1C1F` text, `1px #B08A2E` border, hover `#B8902F`).
Secondary = white bg, `1px #C2C6CA` border, hover `#F5F6F7`. Dark = `#1A1C1F`/white.

---

## Screens

> Files live in `screens/`. Each is a full page unless noted as a modal/dialog.

### FIREARMS

**1. Firearms List** — `Firearms List.dc.html`
- Purpose: see the collection; jump to a gun or quick-log a session.
- Layout: page header (H1 "Firearms" + "N owned" chip + grid/table toggle + "Add Firearm");
  filter toolbar (search + Caliber / Type / Storage dropdowns); responsive **card grid**
  (3-up at desktop) OR a dense **table** via the toggle.
- Card: photo header (4:3-ish), name (Schibsted 600, `#3F5D82`→ use brass-link `#7D6320` in
  final system), make·model sub, caliber chip(s), "STORED · <location>" line, and a footer
  with big mono **rounds-fired** count + a **"＋ Log"** button (jumps to Log Session
  pre-filled with this gun). Card body click → detail.
- Sorts: **alpha by name (default), rounds fired, alpha by make, alpha by storage location.**
- No lift on hover was explicitly requested for these cards — keep them static. (Other lists
  may lift; Firearms list does not.)

**2. Firearm Detail** — `Firearm Detail.dc.html` (dashboard)
- Two columns: **344px rail + fluid main**.
- Left rail: photo + 4-up gallery strip (opens Gallery); **spec card** with **Rounds Fired
  anchored at top** (large mono) then caliber, serial, purchased + price, storage; **Accessories
  card** — "Mounted now" lists the optic/light/suppressor actually on the gun (icon + name; NFA
  tag on suppressor), then link rows **"Compatible magazines (6 →)"** and **"Holsters (3 →)"**
  to filtered lists.
- Right: **Activity feed** — type-colored timeline (RANGE=brass, CLEAN=neutral, MOUNT=special/violet),
  each entry has title (flex:1), rounds/load/location detail, and right-aligned mono date.
  Header has a **Type filter** + **reverse-sort** + **"＋ Log ▾"**. "View all N entries" footer.

**3. Firearm Add / Edit** — `Firearm Add-Edit.dc.html`
- Purpose: rarely opened; create/edit the static facts only.
- Centered 640px single-column form card. Fields: Nickname (shown focused), Manufacturer
  (select) + Model, **Calibers** (multi-chip, "a firearm can accept more than one"), Serial #
  (mono) + Storage (select w/ pin), Purchase date (calendar) + Price ($+mono).
- Footer: brass **Save firearm** + Cancel + quiet "Add photos after saving" hint. **No photos
  field** — photos/accessories/logs are added from the detail page after save. Same layout
  serves Edit (pre-filled).

### AMMO

**4. Ammo List** — `Ammo List.dc.html`
- Grouped **by caliber** (9mm / 5.56 / 12 GA sections, each with a rule + "N ON HAND · M LOADS"
  + a "K LOW" marker). Page header has "N LOADS · X RNDS" + terracotta "K LOW" badge; toolbar
  has search, Purpose filter, **Low-stock-only** toggle (terracotta), On-hand sort.
- Load card: brand, load name (Schibsted), purpose chip + **$/rd** (mono), footer with big mono
  **rounds-on-hand** + inline **Stock** button. **Low-stock cards flip to terracotta**: amber
  border, **LOW** badge, red count, "ON HAND · MIN n". Each section ends with a dashed "add a load" tile.

**5. Ammo Detail** — `Ammo Detail.dc.html` (data-rich dashboard)
- Left rail (344px): photo; **On-hand card** — big count, a **reorder progress bar** (fill toward
  Target with a terracotta Reorder/min marker line), avg $/rd, value on hand; **Specs**
  (purpose, bullet, case, primer, condition); **Used by** firearm links.
- Right: two **12-month trend charts** side by side — **On hand** + **Cost/rd** — current month
  bar in brass, others gray. (Use the codebase's chart lib; bars here are placeholder `<div>`s.)
  Then the **Inventory & usage ledger** with a running **Balance** column: **BUY** (brass) /
  **FIRED** (neutral, links to session) / **ADJUST** (violet), green/red change values, Type
  filter + reverse-sort. NOTE: a dedicated app-wide usage/cost-over-time section was requested
  separately and is not yet designed.

**6. Add Stock** — `Ammo Add Stock.dc.html` (modal over Ammo Detail)
- 484px modal. **Purchase | Adjustment ±** mode toggle. Quick path: Rounds added (focused) +
  Date. Expandable **Purchase details** (optional): Cost with **$ total / $ per rd** switch +
  live "= $0.30/round" readout, Store/FFL (from Locations), Order #. Footer: brass "Add N rounds"
  + Cancel + a **"850 → 1,350"** before/after preview.
- There is also a **generic add/remove inventory** path (the Adjustment ± mode) for changes that
  aren't purchases or range use.

### ACCESSORIES

**7. Accessories List** — `Accessories List.dc.html`
- Grouped **by category**: Suppressors, Magazines, Optics, Lights, Misc. Each card shows
  category-specific facts:
  - **Suppressor**: NFA badge + serial, caliber, mount; status **ON · <firearm>**.
  - **Optic**: type (red dot/variable) + battery; ON · firearm.
  - **Light**: lumens + battery; ON or **OFF · Unmounted**.
  - **Misc** (slings/holsters/stocks): relevant tag; sling ON, holster shows **FITS · <gun>**.
- **Status model (important):** **ON** (green, single-mounted right now → action **Move**) ·
  **OFF** (→ **Mount**) · **FITS** (gray, many-compatible like mags/holsters → **Edit**
  compatibility).
- Magazines render as **grouped cards** here (see #9) with a "Manage individually →" link.

**8. Accessory Detail** — `Accessory Detail.dc.html` (suppressor example)
- Left rail: photo; green **MOUNTED ON** card (current firearm + since-date); **Rounds through
  can** (suppressors keep their own round count for maintenance) + last-cleaned-at; **Specs**;
  a dedicated violet **NFA record** card (serial, tax stamp Approved, Form 4 date, trust).
- Right: **History** feed mixing RANGE (rounds added), CLEAN, MOUNT (moves between guns), NFA.
- Non-suppressor accessories use the same layout, lighter: optics/lights swap NFA card for
  battery info; round-count card optional; mags/holsters use FITS not a single mount.

**9. Magazines** — `Magazines.dc.html` (grouped → individual)
- The key requirement: mags are tracked **individually** but shown **grouped by make + capacity**
  so N near-identical mags collapse into one card.
- Group card: make/caliber/capacity, a proportional **status bar** (in-gun green / loaded brass /
  empty gray) + counts, where-they-live summary, and **Expand**.
- Expanded group → per-mag **table** keyed by **marking**: columns Marking · State (dot) ·
  Loaded with (ammo) · Where (firearm/location) · edit. **In-gun** mags show the firearm;
  **empties collapse** into one expandable "GL9-06…10" row. "Add another <type>" footer.
- A firearm's "Compatible magazines" link lands here pre-filtered to that gun.

### TRAINING

**10. Training Sessions** — `Training Sessions.dc.html`
- Stat strip (sessions / rounds / ammo cost / last session, current year). Toolbar: search +
  Firearm / Location / Year filters. Sessions grouped under **month labels**.
- Session card: left **date rail** (DOW / DD / MON), label (Schibsted), location (pin),
  right-aligned mono **rounds** + **targets** counts; **guns-used chips** with per-gun round
  split; violet **SUPPRESSED** tag when a can was on. "— targets" when none saved.

**11. Session Detail** — `Session Detail.dc.html`
- Left rail: totals grid (rounds/firearms/ammo cost/targets); **"Applied to your data"** audit
  card — the visible effects of the session: **ammo deducted** (−), **firearm counts** (+),
  **suppressor counts** (+, NFA-tagged); Notes.
- Right: **Shooting lines** — one block per firearm (gun + SUPPRESSED tag + round count + the
  ammo line w/ rounds & cost); **Targets** gallery (image slots; added here, not at log time).

**12. Log Session** — `Log Session.dc.html` (the heart of the app)
- 760px form. Session meta: label (focused), date, location.
- Repeatable **firearm lines** ("Add another firearm"). Each line: firearm select + remove;
  Rounds + Ammo-used picker (shows remaining); then an **"Apply to inventory"** block with the
  **three toggles**:
  1. **Deduct from ammo inventory** — *default ON*; shows "−250 from <load> → N left".
  2. **Add to <firearm>'s round count** — *default ON*; shows "3,250 → 3,500".
  3. **Add to suppressor's round count** — *default ON if a suppressor is mounted, else OFF.*
     - If mounted: shows the can (NFA tag, "mounted") + **Change** (swap which can was used).
     - If not mounted but toggled on: lets you **pick a suppressor for this session only** (shown
       "this session" + amber note "Won't change its mounted status") and **+ Add new suppressor**
       (log a can not yet in inventory).
- Sticky footer: running totals (rounds / firearms / −ammo) + Cancel + **Save session**.
- **Targets are intentionally NOT in this form** — they're added later from the Session detail,
  so an image-upload hiccup never blocks logging.

### SHARED

**13. Gallery** — `Gallery.dc.html`
- Shared image manager (works for firearms, accessories, and session targets — **images live in
  one shared Library**, never duplicated).
- Manager: header with **Add from Library** + **Upload**; grid of droppable tiles; first tile is
  the **PRIMARY** cover (brass border + star); each tile has set-primary (star) + detach (trash);
  a tile attached elsewhere shows an **"Also on N"** badge; dashed Upload tile at the end.
  Removing detaches from this item but keeps it in the Library.
- **Add-from-Library modal**: searchable grid of all library photos, circular select checks,
  **ATTACHED** state on already-attached photos (dimmed), footer "Attach N photos".

**14. Design System** — `Bang Design System.dc.html`
- The living reference: brand mark, full color palette, type scale, icon set, spacing/shape,
  and component examples (buttons, inputs, chips, status badges, toggles, nav, cards, ledger
  table). Use this as the source of truth for tokens.

---

## Interactions & Behavior
- **Nav**: top-level routes Firearms/Ammo/Accessories/Training; **Locations** (ranges + stores/FFLs)
  was decided to live under Locations and/or the account menu, not the primary nav.
- **Quick-log** from a Firearm card "＋ Log" opens Log Session pre-filled with that gun (and a
  compact variant exists as a dialog).
- **List card hover**: lift (see tokens) on Ammo/Accessories/Magazines/Training cards; **Firearms
  list cards do NOT lift** (explicit request).
- **Activity/history/ledger feeds**: filter by type + reverse chronological sort.
- **Toggles** (Log Session): green/brass pill on, gray off; each toggle's caption updates with a
  live before→after preview. The suppressor toggle's default depends on whether a can is mounted.
- **Add Stock**: mode toggle swaps fields; cost unit toggle recomputes $/rd; before→after preview.
- **Magazines**: expand/collapse groups; empties row expands; loading a mag or racking it into a
  gun is a **state change on one individual mag** (state + loaded-ammo + which-gun).
- **Gallery**: drag to reorder; star sets cover; Upload (drop/browse) and Add-from-Library
  (multi-select existing) both attach; detach ≠ delete from Library.
- **Focus**: brass ring (see tokens). **Low-stock**: terracotta card treatment + badge.

## State Management
Per the data model (existing Laravel models), key state:
- **Firearm**: make, model, nickname, calibers[], serial, purchase date/price, storage location,
  derived rounds-fired (sum of session lines), mounted accessories[], photos[].
- **Ammunition (load)**: brand, name, caliber, purpose, bullet/case/primer/condition, on-hand
  (derived from ledger), reorder min + target, cost history; ledger entries (BUY/FIRED/ADJUST,
  change, running balance, optional store/order).
- **Accessory**: category (suppressor/magazine/optic/light/misc) + category-specific fields;
  mount state (mounted-on firearm | unmounted | fits-compat list); suppressors carry NFA record
  + own round count; magazines are individual units (marking, state, loaded-ammo, location/gun).
- **TrainingSession**: label, date, location, lines[] (firearm + ammo + rounds + optional
  suppressor-for-session + 3 apply flags), notes, targets[] (gallery images).
- **Image/Library**: shared images, attachable to many owners; per-owner primary + order.
- Logging a session **transactionally** applies its line flags: decrement ammo on-hand, increment
  firearm round count, increment suppressor round count.

## Assets
- **Fonts**: Schibsted Grotesk, Hanken Grotesk, JetBrains Mono (Google Fonts).
- **Icons**: Lucide (ISC). Add `lucide-vue-next` to the codebase.
- **Images**: none shipped — `<image-slot>` in the prototypes is a placeholder for the
  codebase's real upload/display component. The reticle logo is built from CSS/divs; reproduce
  as an inline SVG component.
- **No third-party brand assets** are used.

## Files
- `screens/` — the 13 hi-fi screens + `Bang Design System.dc.html` (open any in a browser).
- `wireframes/` — earlier low-fi explorations (context only; do not implement from these).
- `runtime/` — `support.js` + `image-slot.js`, **prototype-only**, do not port.

To preview a file: open the `.dc.html` in a browser (it loads `support.js` relatively, so keep
the folder structure intact).
