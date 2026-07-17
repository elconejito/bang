# Bang — Development Backlog

This file tracks features that need to be built, completed, or cleaned up.
Add context/notes to each item before starting work on it.

---

## Deferred Technical Items (added 2026-06-26)

Raised during the design-handoff alignment work; deferred intentionally.

### Future scaling
- [ ] **Aggregate endpoint for AmmoShow stats/charts.** The "On hand · 12 mo" /
  "Cost / rd · 12 mo" charts and avg-cost/value stats are computed client-side
  from a full-history fetch (`loadStats()` in `AmmoShow.vue`) capped at
  `per_page: 200` by `InventoryController`. Add a server-side aggregate endpoint
  so the client never needs the full ledger. The paginated ledger table itself
  is already correct.
- [ ] **Move merged feeds to DB-level queries at scale.** Both
  `FirearmActivityController::index` and the accessory history feed
  (`AccessoryEventControllerBase::listEvents` + `SuppressorEventController::rangeEntries`)
  build the entire merged feed in memory before filtering/sorting/paginating.
  Fine now; at very large history move to a UNION query or a dedicated
  activity/events table.

### Legacy domain cleanup
- [x] **Remove superseded shoot API scaffolding.** Training Sessions own shooting,
  ranges, shooting lines, targets, and ammunition deductions. Remove the unrouted
  `ShootController` and any associated obsolete policy or model relationships. Keep
  the active Order domain and `OrderController`; orders now provide the purchase
  workflow and inventory uses them for store, cost, and order-reference metadata.
- [x] **Remove the unused Purchase domain.** `Purchase`, `PurchaseController`, and
  `PurchasePolicy` only reference one another and have no routes or active workflow;
  remove them after confirming no retained migration data needs conversion.
- [x] **Remove duplicate reference-domain scaffolding.** `CartridgeController`,
  `StoreCartridgeRequest`, and the old `API\PurposeController` are unrouted. Current
  ammunition and Manage Lists workflows use `AmmunitionController`, `CaliberController`,
  and `API\Reference\PurposeController` instead. Remove the obsolete classes and their
  policy registrations after checking whether the `cartridges` table contains data that
  needs migration.

### Product cleanup
- [x] **Audit and consolidate entity-note support.** Notes are an active visible workflow
  across firearms, ammunition, magazines, accessories, locations, ranges, stores,
  training sessions, and orders. Confirm each supported notable type can list and
  persist notes through the generic note routes, then remove any superseded nested
  routes, empty controller methods, or obsolete model relationships.

### UI polish
- [ ] **Nav font weights** slightly off versus the design comps; deferred for a
  later refinement pass.
