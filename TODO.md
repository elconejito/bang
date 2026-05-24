# Bang — Development Backlog

This file tracks features that need to be built, completed, or cleaned up.
Add context/notes to each item before starting work on it.

---

## Features: Missing Frontend Pages (API controllers exist)

### Orders
- `app/Http/Controllers/API/OrderController.php` is implemented
- No Vue pages yet
- Needs: index, create, show pages + Pinia store + router entries

### Purchases
- `app/Http/Controllers/API/PurchaseController.php` is implemented
- No Vue pages yet
- Needs: index, create, show pages + Pinia store + router entries

### Shoot / Range / Target
- Controllers exist: `ShootController`, `RangeController`, `TargetController`
- No Vue pages yet
- These are related — a "shoot" takes place at a "range" and uses "targets"
- Needs design thought before building: how do shoots relate to training sessions?

### Photo Uploads
- `FirearmImages.vue` and `AmmunitionImages.vue` components exist
- Backend: `Picture` model and `MorphToMany` relationships are defined
- Unclear if any upload endpoint is wired up — needs investigation
- `intervention/image` package is installed for image processing

### Dashboard (`HomeDashboard.vue`)
- Currently a stub/empty page
- Needs design thought: what summary data should appear here?
- Candidate: ammo counts by caliber, recent training sessions, total inventory

---

## Phase Roadmap

- [x] Phase 1 — Config & dependency cleanup (Vite, laravel-vite-plugin, remove Vue CLI)
- [x] Phase 2 — Backend simplification (remove repository pattern, add spatie/laravel-query-builder)
- [x] Phase 3 — Auth cleanup (JWT review, Fortify, dead code removal)
- [x] Phase 4 — Vue 3 migration (Vue 3, Pinia, Vue Router 4, Composition API)
- [x] Phase 5 — Bootstrap → Tailwind CSS 4 migration + dead code cleanup
