# Magazine Handling Implementation Plan

## Objective

Implement magazines as individually tracked firearm accessories while presenting similar magazines as groups. A magazine has independent compatibility, placement, and load state:

- **Compatibility:** a magazine may be compatible with many firearms.
- **Placement:** a magazine is either in one firearm, in one storage location, or unassigned.
- **Load:** a magazine may contain a specific ammunition load and a round count.

Loading or unloading a magazine does **not** change global ammunition inventory. Ammunition remains owned until it is fired, sold, disposed of, or adjusted through the ammunition ledger.

The Accessories page shows magazine groups only. The Magazine Groups page shows all groups, optionally filtered for a compatible firearm. Selecting a group opens a paginated, filterable, sortable table of its individual magazines.

## Product Decisions

### Status and placement

Do not use one persisted `status` value to represent both placement and load. Store the underlying facts and derive presentation state:

```text
current_firearm_id is present                       -> in_gun
current_firearm_id is absent and loaded_rounds > 0  -> loaded
otherwise                                           -> empty
```

Also expose `load_state` independently as `loaded` or `empty`. This permits an empty magazine to be physically inserted into a firearm while its primary display state remains `in_gun`.

### Group identity

A group is a read-model/UI aggregate, not a database entity. Its identity consists of:

```text
normalized manufacturer
+ normalized model name
+ capacity
+ sorted caliber IDs
```

Location, marking, load, current firearm, and compatible firearms do not split groups.

The API returns a URL-safe opaque encoded group key. Vue must never create, decode, or interpret this key. A server-side `MagazineGroupKey` value object owns canonicalization, encoding, decoding, validation, and application to an Eloquent query.

The key does not need encryption because the encoded attributes are not secret. “Opaque” means clients treat it as an uninterpreted identifier.

### Existing data

Existing magazine data does not need to be retained. During migration, existing rows may be normalized to:

```text
loaded_rounds = 0
loaded_ammunition_id = null
current_firearm_id = null
location_id = null
```

The obsolete persisted `status` column should be removed after all callers use derived state.

## Target User Experience

### Accessories to magazine group

```text
Accessories
  -> select a magazine group
  -> /magazines/groups/{opaqueGroupKey}
  -> see only individual magazines in that group
```

The Accessories response contains group summaries only; it does not include every individual magazine.

### Firearm to compatible magazines

```text
Firearm Detail
  -> Compatible Magazines
  -> /magazines?compatible_firearm_id={firearmId}
  -> see groups containing compatible magazines
  -> select a group
  -> /magazines/groups/{key}?compatible_firearm_id={firearmId}
```

The compatibility filter is applied before grouping. Group counts therefore describe the compatible subset. It remains active when entering the individual group page because otherwise the user could see same-group magazines that were not marked compatible with the originating firearm.

### Main Magazine navigation

```text
Magazines
  -> /magazines
  -> see all magazine groups
  -> select a group
  -> see its paginated individuals
```

### Individual group page

Display one magazine per row. Remove the collapsed-empty summary row.

| Marking | State | Loaded With | Rounds Loaded | Location | Action |
|---|---|---|---:|---|---|
| GL9-001 | In gun | Federal HST 124gr | 15 / 17 | Glock 19 | Edit |
| GL9-002 | Loaded | Federal HST 124gr | 17 / 17 | Bedside Safe | Edit |
| GL9-003 | Empty | — | 0 / 17 | Large Safe | Edit |

For an inserted magazine, the Location column displays its current firearm. A stored magazine displays its storage location. An unplaced magazine displays `Unassigned`.

Filters:

- State: All, In firearm, Loaded spare, Empty.
- Location: All locations, In a firearm, Unassigned, or a specific storage location.
- Marking search.

Sorts:

- Marking.
- State, using `In firearm -> Loaded spare -> Empty` domain order.
- Loaded With, using ammunition manufacturer and label.
- Location, using current firearm label, otherwise storage location label, otherwise Unassigned.

Default to marking ascending and 25 rows per page. Filtering and sorting occur before pagination. The table footer shows `Showing X-Y of Z`.

Show Rounds Loaded in every row even though it does not need to be sortable initially.

### URL state

Persist meaningful list state in the URL:

```text
/magazines/groups/{key}
  ?compatible_firearm_id=42
  &state=loaded
  &location_id=7
  &sort=loaded_ammunition
  &page=2
  &per_page=25
```

Persist compatibility context, state, location, sort, page, and page size. Do not persist temporary UI state such as open menus or dialogs.

## Data Model

Add these fields to `cms.magazines`:

| Field | Type | Rules |
|---|---|---|
| `location_id` | nullable foreign ID | Null when inserted into a firearm |
| `current_firearm_id` | nullable foreign ID | Null when stored in a location |
| `loaded_rounds` | unsigned integer, default 0 | Between 0 and capacity |

Keep `loaded_ammunition_id`.

Rename the conceptual Eloquent relationship from `firearms()` to `compatibleFirearms()`. The existing `cms.firearm_magazine` pivot represents compatibility only. Add unique constraints to:

- `cms.firearm_magazine (firearm_id, magazine_id)`
- `cms.caliber_magazine (caliber_id, magazine_id)`

Add indexes for common group, filter, and join paths:

- Magazine owner plus manufacturer/model/capacity.
- Magazine owner plus location.
- Magazine owner plus current firearm.
- Loaded ammunition foreign key.

### Invariants

- `0 <= loaded_rounds <= capacity`.
- Positive loaded rounds require `loaded_ammunition_id`.
- Zero loaded rounds force `loaded_ammunition_id` to null.
- `current_firearm_id` and `location_id` cannot both be populated.
- The current firearm must be included in `compatibleFirearms`.
- Loaded ammunition must be caliber-compatible with the magazine.
- All referenced records must belong to the authenticated user.
- Magazine state changes never mutate ammunition inventory or create ammunition ledger entries.

## Backend Architecture

### Value object and queries

Recommended structure:

```text
app/Data/Magazines/MagazineGroupKey.php
app/Queries/Magazines/MagazineGroupQuery.php
app/Queries/Magazines/MagazinesInGroupQuery.php
app/Actions/Magazines/ChangeMagazineState.php
app/Actions/Magazines/CreateMagazineBatch.php
```

`MagazineGroupQuery` returns aggregate summaries and is shared by `MagazineGroupController` and `AccessoriesController`. It owns user scoping, compatibility filtering, group-level filters, aggregates, and group sorting.

`MagazinesInGroupQuery` decodes a group key, applies it to a user-scoped Magazine query, applies individual filters/sorts, eager-loads relations, and returns a paginator.

Controllers must not call other controllers. They depend on these shared query/action classes.

All state changes and multi-record writes run in database transactions. Use row locking when a concurrent change could violate placement or loaded-round invariants.

### API routes

Individual CRUD remains:

```http
GET    /api/magazines
POST   /api/magazines
GET    /api/magazines/{magazine}
PUT    /api/magazines/{magazine}
DELETE /api/magazines/{magazine}
PATCH  /api/magazines/{magazine}/state
```

Grouped API:

```http
GET /api/magazine-groups
GET /api/magazine-groups/{group}/magazines
```

The group-summary endpoint supports:

```text
filter[search]
filter[compatible_firearm_id]
filter[caliber_id]
sort=manufacturer|model_name|capacity|-total|-loaded_count
```

The individual endpoint supports:

```text
filter[compatible_firearm_id]
filter[state]
filter[location_id]
filter[search]
sort=id_marking|-id_marking
sort=state|-state
sort=loaded_ammunition|-loaded_ammunition
sort=location|-location
page=N
per_page=N
```

Clamp `per_page` to a reasonable maximum such as 100.

### Group summary response

```json
{
  "data": [
    {
      "key": "opaque-url-safe-key",
      "manufacturer": "Glock",
      "model_name": "OEM",
      "capacity": 17,
      "calibers": [{ "id": 3, "label": "9mm" }],
      "summary": {
        "total": 100,
        "in_gun": 2,
        "loaded": 18,
        "empty": 80
      },
      "locations": [
        { "id": 4, "label": "Bedside Safe", "count": 3 },
        { "id": 7, "label": "Large Safe", "count": 95 }
      ]
    }
  ],
  "meta": {
    "groups": 1,
    "magazines": 100
  }
}
```

### Individual response

```json
{
  "data": [
    {
      "id": 21,
      "id_marking": "GL9-001",
      "display_status": "in_gun",
      "load_state": "loaded",
      "loaded_rounds": 15,
      "capacity": 17,
      "loaded_ammunition": {
        "id": 42,
        "manufacturer": "Federal",
        "label": "HST 124gr"
      },
      "current_firearm": {
        "id": 12,
        "label": "Glock 19"
      },
      "location": null
    }
  ],
  "group": {
    "key": "opaque-url-safe-key",
    "manufacturer": "Glock",
    "model_name": "OEM",
    "capacity": 17,
    "calibers": [{ "id": 3, "label": "9mm" }]
  },
  "context": {
    "compatible_firearm": {
      "id": 42,
      "label": "Glock 19"
    }
  },
  "meta": {
    "current_page": 1,
    "last_page": 4,
    "per_page": 25,
    "total": 100
  },
  "links": {}
}
```

## Front-End Architecture

Recommended pages/components:

```text
pages/magazines/MagazineGroupsIndex.vue
pages/magazines/MagazineGroupShow.vue
components/magazines/MagazineGroupCard.vue
components/magazines/MagazineGroupTable.vue
components/magazines/MagazineStateModal.vue
stores/magazineGroups.js
```

The Accessories page consumes compact group summaries. The Magazine Groups page consumes full group summaries. The individual page consumes only the selected group’s paginated rows.

The group page synchronizes filters, sort, and pagination with Vue Router query state. Any filter or sort change resets `page` to 1. Back/forward navigation must restore the table state without stale requests overwriting newer results.

State changes should be separate from static magazine editing:

- Static edit: manufacturer, model, capacity, marking, calibers, compatible firearms, notes, and photos.
- State action: load/unload, store at location, insert into firearm, remove from firearm, or move.

Include this note near load controls:

> Tracking magazine contents does not change ammunition inventory.

## Required Integration Points

- Accessories response and screen: grouped magazine summaries only.
- Firearm detail: compatible magazine count and link to filtered group browser.
- Firearm detail: identify the magazine currently inserted, if any.
- Location detail and transformer: include magazines stored at that location.
- Reference-location usage counts: include magazines.
- Magazine detail: show loaded ammunition, loaded rounds, effective placement, compatibility, and history.
- Batch creation: create several individual magazines with optional padded marking generation.

## Test Plan

### Backend PHPUnit coverage

- Create empty, loaded, partially loaded, full, stored, unassigned, and inserted magazines.
- Reject negative and over-capacity round counts.
- Reject positive rounds without ammunition.
- Clear ammunition when rounds become zero.
- Reject simultaneous firearm and location placement.
- Reject an incompatible current firearm.
- Reject caliber-incompatible ammunition.
- Reject cross-user firearm, location, ammunition, and compatibility IDs.
- Confirm loading/unloading does not modify ammunition inventory or its ledger.
- Encode/decode group keys canonically, including sorted caliber IDs.
- Reject malformed group keys.
- Return correct whole-group aggregates.
- Apply firearm compatibility before grouping.
- Preserve compatibility filtering on individual group queries.
- Apply state and location filters before pagination.
- Sort individuals correctly by marking, state, ammunition, and effective location.
- Prevent N+1 regressions for group and individual endpoints.
- Roll back invalid state and batch operations.
- Enforce unique compatibility pivot rows.

### Front-end coverage

- Accessories group click opens the selected group only.
- Firearm compatible-magazine link opens the filtered group browser.
- Selecting a filtered group preserves `compatible_firearm_id`.
- Individual rows show marking, state, loaded ammunition, rounds loaded, and effective location.
- State/location filters update the URL and reset pagination.
- Sort and page state restore correctly on browser navigation.
- Empty magazines render individually; no collapsed-empty row remains.
- Mobile presentation remains usable without losing row data.
- State modal submits correct payloads and displays validation errors.

## Delivery Sequence

### Milestone 1: Domain foundation

1. Schema migrations and existing-row normalization.
2. Model relationships, casts, derived state, and scopes.
3. Group-key value object.
4. Domain invariant tests.

Acceptance gate: model and value-object tests pass; no magazine code depends on persisted `status` semantics.

### Milestone 2: State API

1. State Form Request and user-scoped validation.
2. Transactional `ChangeMagazineState` action.
3. State route/controller/resource changes.
4. API feature tests, including unchanged ammunition inventory.

Acceptance gate: every valid transition succeeds, every invalid combination fails atomically, and ammunition inventory remains unchanged.

### Milestone 3: Grouped API

1. `MagazineGroupQuery`.
2. `MagazinesInGroupQuery`.
3. Group controller and routes.
4. Aggregate/filter/sort/pagination tests.
5. Integrate compact summaries into `AccessoriesController`.

Acceptance gate: Accessories fetches no individual rows; group counts and paginated individual queries are correct under compatibility filters.

### Milestone 4: Group browser UI

1. Magazine group store and routes.
2. Group-index page and cards.
3. Accessories group navigation.
4. Firearm compatible-magazine navigation.
5. URL-filter context and breadcrumbs.

Acceptance gate: both navigation paths land on the expected group scope and browser back/forward works.

### Milestone 5: Individual management UI

1. Paginated individual table.
2. State/location filters.
3. Marking/state/ammunition/location sorts.
4. Rounds-loaded display.
5. State-change modal and API integration.
6. Responsive treatment.

Acceptance gate: 100 same-group magazines remain manageable without a long page and all row state is visible.

### Milestone 6: Cross-screen integration and batch creation

1. Firearm current/compatible magazine presentation.
2. Location contents and counts.
3. Magazine detail/history updates.
4. Batch creation backend and UI.
5. Remove obsolete `status` column.

Acceptance gate: all related screens agree about placement, compatibility, grouping, and loaded state.

## Sol-Orchestrated Agent Plan

This plan is suitable for Sol as the orchestrator/manager and Luna or Terra as implementation agents. Sol should retain responsibility for architecture, sequencing, integration review, and final verification. Implementation agents receive bounded work packets and should not independently redefine API contracts or group semantics.

### Agent roles

**Sol — orchestrator and integrator**

- Own this plan and resolve domain questions.
- Inspect current conventions and run required Laravel Boost documentation searches before code changes.
- Assign work packets with explicit files and acceptance criteria.
- Prevent overlapping edits between agents.
- Review migrations, API contracts, query plans, and security boundaries.
- Integrate work in dependency order.
- Run cross-milestone tests and final formatting/build checks.

**Luna — backend implementation**

- Best fit for migrations, models, value objects, actions, requests, controllers, resources/transformers, queries, policies, factories, and PHPUnit tests.
- Should work one backend packet at a time and return changed-file lists, decisions, test commands, and results.

**Terra — front-end implementation**

- Best fit for Vue routes, Pinia stores, group cards, tables, filters, pagination, URL synchronization, responsive behavior, and front-end tests.
- May begin UI scaffolding against the approved JSON fixtures after Sol freezes the API response contract, but must not invent backend response fields.

If only Luna or only Terra is available, either can execute all packets sequentially. The separation is about bounded ownership rather than model capability.

### Work packets

#### Packet B1 — schema and model foundation

Owner: Luna  
Depends on: none  
Files: migrations, `Magazine`, related models, factory, focused model tests  
Deliverable: new fields/constraints/indexes, normalized existing rows, relationships, casts, derived status/load state  
Must not: remove `status` until callers have migrated

#### Packet B2 — group key and query contract

Owner: Luna  
Depends on: B1  
Files: magazine data/value object, query classes, focused unit/feature tests  
Deliverable: opaque key plus reusable grouped and individual query foundations  
Must not: add controller-specific response formatting to query classes

#### Packet B3 — state mutations

Owner: Luna  
Depends on: B1  
Files: action, Form Request, controller route/method, resource/transformer, feature tests  
Deliverable: transactional state endpoint enforcing all invariants  
Must prove: ammunition inventory and ledger are unchanged

Packets B2 and B3 may run in parallel only if Sol assigns non-overlapping files and freezes shared `Magazine` changes after B1.

#### Packet B4 — grouped endpoints

Owner: Luna  
Depends on: B2 and B3 response vocabulary  
Files: group controller, routes, resources/transformers, query extensions, endpoint tests  
Deliverable: summary and paginated-individual endpoints with filtering and sorting

#### Packet B5 — Accessories and related backend integration

Owner: Luna  
Depends on: B4  
Files: `AccessoriesController`, firearm/location transformers and tests  
Deliverable: compact groups on Accessories plus firearm/location integration

#### Packet F1 — group browser shell

Owner: Terra  
Depends on: Sol-approved B4 JSON contract; may use fixtures before B4 completes  
Files: Vue routes, Pinia group store, group-index page, group cards, component tests  
Deliverable: all-groups and firearm-filtered group browsing

#### Packet F2 — paginated individual table

Owner: Terra  
Depends on: F1 and approved individual endpoint contract  
Files: group-show page, table, filter/sort controls, URL-state composable/tests  
Deliverable: one row per magazine, pagination, requested filters/sorts, rounds-loaded display

#### Packet F3 — state workflow

Owner: Terra  
Depends on: B3 and F2  
Files: state modal/components, stores, page integration, tests  
Deliverable: load/unload/store/insert/remove/move interactions

#### Packet F4 — navigation and cross-screen integration

Owner: Terra  
Depends on: B5, F1, and F2  
Files: Accessories, firearm detail, location detail, breadcrumbs/routes, tests  
Deliverable: correct destination and preserved context from every entry point

#### Packet X1 — batch creation and cleanup

Owner: Luna for backend, Terra for UI; Sol integrates  
Depends on: B4, F2, and F3  
Deliverable: batch creation, padded markings, obsolete status removal, final regression coverage

### Handoff format

Every implementation agent should return:

```text
Packet:
Status: complete | blocked
Files changed:
Behavior implemented:
API/schema decisions made:
Tests run and results:
Formatting/build checks:
Known risks or follow-ups:
```

An agent must stop and ask Sol before:

- Changing the agreed group identity.
- Adding a persistent magazine-group table.
- Changing URL or JSON contracts.
- Coupling magazine loading to ammunition inventory.
- Editing files assigned to another active packet.
- Adding dependencies.

### Integration gates for Sol

After each backend packet:

```text
php artisan test --compact <focused test file or filter>
vendor/bin/pint --dirty --format agent
```

After each front-end packet, run the repository’s focused front-end test command plus lint/format checks for changed files. After the final integration:

1. Run all magazine/accessory/firearm/location-focused PHPUnit tests.
2. Run the relevant front-end tests.
3. Run Pint on dirty PHP files.
4. Run ESLint and Prettier checks for changed front-end files.
5. Run the production front-end build.
6. Perform browser verification of the three navigation flows and a 100-row group fixture.
7. Offer to run the complete PHPUnit suite.

## Definition of Done

- Magazine compatibility, physical placement, and load state are independent and unambiguous.
- Magazine loading never changes ammunition inventory.
- Accessories displays compact groups without fetching individual rows.
- The firearm compatibility path shows filtered groups and preserves that context into the selected group.
- A selected group displays individually paginated magazines.
- Individual rows show marking, state, loaded ammunition, rounds loaded, and effective location.
- State and location filters plus all four requested sorts work before pagination.
- URL state supports refresh and browser back/forward behavior.
- Cross-user references and invalid state combinations are rejected.
- Relevant backend and front-end tests pass, formatting is clean, and the production front-end build succeeds.
