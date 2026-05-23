# Bang — Development Backlog

This file tracks features that need to be built, completed, or cleaned up.
Add context/notes to each item before starting work on it.

---

## Features: Missing Frontend Pages (API controllers exist)

### Orders
- `app/Http/Controllers/API/OrderController.php` is implemented
- No Vue pages yet
- Needs: index, create, show pages + Vuex module + router entries

### Purchases
- `app/Http/Controllers/API/PurchaseController.php` is implemented
- No Vue pages yet
- Needs: index, create, show pages + Vuex module + router entries

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

## Features: Auth

### User Registration
- The Vue page (`Register.vue`) exists
- Disabled in `config/fortify.php` — one-line config change to re-enable
- Decide: open registration or invite-only?

### Forgot Username
- `ForgotUsername.vue` page exists but has no backend endpoint
- Either build the backend or remove the page

---

## Cleanup: Dead Code

### Auth0 / Callback
- `resources/front-end/src/plugins/auth/auth0.js` — Auth0 SPA plugin (commented out in `main.js`)
- `resources/front-end/src/pages/auth/Callback.vue` — Auth0 callback page
- `@auth0/auth0-spa-js` already removed from `package.json`
- Remove these files when confirmed Auth0 is not being pursued

### Legacy Blade Views
- `resources/views/` still contains the old server-rendered Blade templates from master
  (ammunition, calibers, firearms, inventories, magazines, orders, purposes, ranges,
  shoots, stores, targets, trips, etc.)
- These are dead code — the app is now a full SPA served only via `app.blade.php`
- Safe to delete the entire directory except `app.blade.php`, `errors/`, and `vendor/`

---

## Phase Roadmap

- [x] Phase 1 — Config & dependency cleanup (Vite, laravel-vite-plugin, remove Vue CLI)
- [ ] Phase 2 — Backend simplification (remove repository pattern, add spatie/laravel-query-builder)
- [ ] Phase 3 — Auth cleanup (JWT review, Fortify scope)
- [ ] Phase 4 — Vue 3 migration (Vue 3, Pinia, Vue Router 4, Vitest)
- [ ] Phase 5 — Bootstrap → Tailwind CSS 4 migration (done alongside Vue 3 component rewrite)
