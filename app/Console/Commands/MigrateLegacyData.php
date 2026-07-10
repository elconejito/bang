<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Connection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class MigrateLegacyData extends Command
{
    protected $signature = 'migrate:legacy
                            {--dry-run : Print row counts without writing to the new database}';

    protected $description = 'Migrate data from the legacy MySQL database into the current PostgreSQL schema';

    /** @var array<int, int> */
    private array $userMap = [];

    /** @var array<int, int> */
    private array $caliberTypeMap = [];

    /** @var array<int, int> */
    private array $caliberMap = [];

    /** @var array<int, int> */
    private array $purposeMap = [];

    /** @var array<int, int> */
    private array $storeMap = [];

    /** @var array<int, int> */
    private array $rangeMap = [];

    /** @var array<int, int> */
    private array $orderMap = [];

    /** @var array<int, int> */
    private array $firearmMap = [];

    /** @var array<int, int> */
    private array $ammunitionMap = [];

    /** @var array<int, int> */
    private array $magazineMap = [];

    /** @var array<int, int> */
    private array $trainingSessionMap = []; // legacy trip_id → new training_session id

    /** @var array<int, string> */
    private array $trainingSessionDateMap = []; // legacy trip_id → session_date string

    /** @var array<int, int> */
    private array $sessionLineMap = []; // legacy training_session id → new session_line id

    /** @var array<int, int> */
    private array $inventoryMap = [];

    /** @var array<int, int> */
    private array $pictureMap = [];

    private bool $dryRun = false;

    public function handle(): int
    {
        $this->dryRun = (bool) $this->option('dry-run');

        if ($this->dryRun) {
            $this->warn('DRY RUN — no data will be written.');
        }

        $this->verifyLegacyConnection();

        $this->migrateUsers();
        $this->migrateCaliberTypes();
        $this->migratePurposes();
        $this->migrateStores();
        $this->migrateRanges();
        $this->migrateCalibersAndCartridges();
        $this->migrateOrders();
        $this->migrateFirearms();
        $this->migrateAmmunition();
        $this->migrateMagazines();
        $this->migrateCaliberFirearmPivot();
        $this->migrateCaliberMagazinePivot();
        $this->migrateTrainingSessions();
        $this->migrateSessionLines();
        $this->migrateInventories();
        $this->recalculateAmmunitionInventory();
        $this->migratePictures();
        $this->migratePictureables();
        $this->migrateNotes();
        $this->migrateTargets();

        $this->newLine();
        $this->info('Migration complete.');

        return self::SUCCESS;
    }

    // -------------------------------------------------------------------------
    // Phase 1 — Users & reference lookups
    // -------------------------------------------------------------------------

    private function migrateUsers(): void
    {
        $this->info('Phase 1: users');
        $rows = $this->legacy()->table('users')->get();

        foreach ($rows as $row) {
            $existing = $this->new()->table('idam.users')->where('email', $row->email)->first();

            if ($existing) {
                $this->userMap[$row->id] = $existing->id;
                $this->line("  skip user {$row->email} (already exists → id {$existing->id})");

                continue;
            }

            if ($this->dryRun) {
                $this->userMap[$row->id] = $row->id;

                continue;
            }

            $newId = $this->new()->table('idam.users')->insertGetId([
                'name' => $row->name,
                'email' => $row->email,
                'password' => $row->password,
                'remember_token' => $row->remember_token,
                'created_at' => $row->created_at,
                'updated_at' => $row->updated_at,
            ]);

            $this->userMap[$row->id] = $newId;
        }

        $this->line('  users: '.count($this->userMap));
    }

    private function migrateCaliberTypes(): void
    {
        $this->info('Phase 1: caliber_types → reference.caliber_types');
        $rows = $this->legacy()->table('caliber_types')->get();

        foreach ($rows as $row) {
            if ($this->dryRun) {
                $this->caliberTypeMap[$row->id] = $row->id;

                continue;
            }

            $newId = $this->new()->table('reference.caliber_types')->insertGetId([
                'label' => $row->label,
                'user_id' => $this->mapUser($row->user_id ?? null),
                'created_at' => $row->created_at,
                'updated_at' => $row->updated_at,
            ]);

            $this->caliberTypeMap[$row->id] = $newId;
        }

        $this->line('  caliber_types: '.count($this->caliberTypeMap));
    }

    private function migratePurposes(): void
    {
        $this->info('Phase 1: purposes → reference.purposes');
        $rows = $this->legacy()->table('purposes')->get();

        foreach ($rows as $row) {
            if ($this->dryRun) {
                $this->purposeMap[$row->id] = $row->id;

                continue;
            }

            $newId = $this->new()->table('reference.purposes')->insertGetId([
                'label' => $row->label,
                'user_id' => $this->mapUser($row->user_id ?? null),
                'created_at' => $row->created_at,
                'updated_at' => $row->updated_at,
            ]);

            $this->purposeMap[$row->id] = $newId;
        }

        $this->line('  purposes: '.count($this->purposeMap));
    }

    // -------------------------------------------------------------------------
    // Phase 2 — Core lookups
    // -------------------------------------------------------------------------

    private function migrateStores(): void
    {
        $this->info('Phase 2: stores → cms.stores');
        $rows = $this->legacy()->table('stores')->get();

        foreach ($rows as $row) {
            if ($this->dryRun) {
                $this->storeMap[$row->id] = $row->id;

                continue;
            }

            $newId = $this->new()->table('cms.stores')->insertGetId([
                'label' => $row->label,
                'description' => null,
                'user_id' => $this->mapUser($row->user_id),
                'created_at' => $row->created_at,
                'updated_at' => $row->updated_at,
            ]);

            $this->storeMap[$row->id] = $newId;
        }

        $this->line('  stores: '.count($this->storeMap));
    }

    private function migrateRanges(): void
    {
        $this->info('Phase 2: ranges → cms.ranges');
        $rows = $this->legacy()->table('ranges')->get();

        foreach ($rows as $row) {
            if ($this->dryRun) {
                $this->rangeMap[$row->id] = $row->id;

                continue;
            }

            $newId = $this->new()->table('cms.ranges')->insertGetId([
                'label' => $row->label,
                'description' => null,
                'address' => null,
                'user_id' => $this->mapUser($row->user_id),
                'created_at' => $row->created_at,
                'updated_at' => $row->updated_at,
            ]);

            $this->rangeMap[$row->id] = $newId;
        }

        $this->line('  ranges: '.count($this->rangeMap));
    }

    private function migrateCalibersAndCartridges(): void
    {
        // The live DB never ran the calibers migration — cartridges is the real source.
        // Both ammunitions.caliber_id and bullets.cartridge_id point to cartridges.id.
        $this->info('Phase 2: cartridges → cms.calibers');
        $rows = $this->legacy()->table('cartridges')->get();

        if ($rows->isEmpty()) {
            $this->line('  calibers (from cartridges): 0');

            return;
        }

        // cms.calibers.caliber_type_id is NOT NULL; create a placeholder type for legacy data
        $firstUserId = $this->mapUser($rows->first()->user_id) ?? $rows->first()->user_id;
        $legacyTypeId = $this->new()->table('reference.caliber_types')->insertGetId([
            'label' => 'Legacy Import',
            'user_id' => $firstUserId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        foreach ($rows as $row) {
            if ($this->dryRun) {
                $this->caliberMap[$row->id] = $row->id;

                continue;
            }

            $newId = $this->new()->table('cms.calibers')->insertGetId([
                // size is the full designation (e.g. "9x19mm"); label is the short form (e.g. "9mm")
                'caliber' => $row->label,
                'label' => $row->size,
                'caliber_type_id' => $legacyTypeId,
                'user_id' => $this->mapUser($row->user_id),
                'created_at' => $row->created_at,
                'updated_at' => $row->updated_at,
            ]);

            $this->caliberMap[$row->id] = $newId;
        }

        $this->line('  calibers (from cartridges): '.count($this->caliberMap));
    }

    private function migrateOrders(): void
    {
        $this->info('Phase 2: orders → cms.orders');
        $rows = $this->legacy()->table('orders')->get();

        foreach ($rows as $row) {
            if ($this->dryRun) {
                $this->orderMap[$row->id] = $row->id;

                continue;
            }

            $newId = $this->new()->table('cms.orders')->insertGetId([
                'rounds' => $row->rounds,
                'total_cost' => $row->total_cost,
                'store_id' => isset($row->store_id) ? ($this->storeMap[$row->store_id] ?? null) : null,
                'order_ref' => null,
                'order_date' => $row->order_date,
                'user_id' => $this->mapUser($row->user_id),
                'created_at' => $row->created_at,
                'updated_at' => $row->updated_at,
            ]);

            $this->orderMap[$row->id] = $newId;
        }

        $this->line('  orders: '.count($this->orderMap));
    }

    // -------------------------------------------------------------------------
    // Phase 3 — Equipment
    // -------------------------------------------------------------------------

    private function migrateFirearms(): void
    {
        $this->info('Phase 3: firearms → cms.firearms');
        $rows = $this->legacy()->table('firearms')->get();

        foreach ($rows as $row) {
            if ($this->dryRun) {
                $this->firearmMap[$row->id] = $row->id;

                continue;
            }

            $newId = $this->new()->table('cms.firearms')->insertGetId([
                'label' => $row->label,
                'manufacturer' => $row->manufacturer,
                'model' => $row->model,
                'serial' => null,
                'location_id' => null,
                'purchase_date' => null,
                'purchase_price' => null,
                'purchase_store_id' => null,
                'user_id' => $this->mapUser($row->user_id),
                'created_at' => $row->created_at,
                'updated_at' => $row->updated_at,
            ]);

            $this->firearmMap[$row->id] = $newId;
        }

        $this->line('  firearms: '.count($this->firearmMap));
    }

    private function migrateAmmunition(): void
    {
        // Primary source: ammunitions table (has caliber_id, partially migrated)
        $this->info('Phase 3: ammunitions → cms.ammunition');
        $rows = $this->legacy()->table('ammunitions')->get();

        foreach ($rows as $row) {
            if ($this->dryRun) {
                $this->ammunitionMap[$row->id] = $row->id;

                continue;
            }

            $newId = $this->new()->table('cms.ammunition')->insertGetId([
                'manufacturer' => $row->manufacturer,
                'label' => $row->name, // legacy: name; new: label
                'weight' => $row->weight ?? null,
                'inventory' => $row->inventory ?? 0,
                'reorder_min' => null,
                'reorder_target' => null,
                'purpose_id' => isset($row->purpose_id) ? ($this->purposeMap[$row->purpose_id] ?? null) : null,
                'caliber_id' => $this->caliberMap[$row->caliber_id] ?? null,
                'bullet_type_id' => null,
                'ammunition_casing_id' => null,
                'ammunition_condition_id' => null,
                'primer_type_id' => null,
                'shot_material_id' => null,
                'shell_length_id' => null,
                'shell_type_id' => null,
                'user_id' => $this->mapUser($row->user_id),
                'created_at' => $row->created_at,
                'updated_at' => $row->updated_at,
            ]);

            $this->ammunitionMap[$row->id] = $newId;
        }

        // Supplement: bullets rows not present in ammunitions (IDs not already mapped)
        $migratedIds = array_keys($this->ammunitionMap);
        $supplement = $this->legacy()->table('bullets')
            ->when(! empty($migratedIds), fn ($q) => $q->whereNotIn('id', $migratedIds))
            ->get();

        foreach ($supplement as $row) {
            if ($this->dryRun) {
                $this->ammunitionMap[$row->id] = $row->id;

                continue;
            }

            $newId = $this->new()->table('cms.ammunition')->insertGetId([
                'manufacturer' => $row->manufacturer,
                'label' => $row->name,
                'weight' => $row->weight ?? null,
                'inventory' => $row->inventory ?? 0,
                'reorder_min' => null,
                'reorder_target' => null,
                'purpose_id' => isset($row->purpose_id) ? ($this->purposeMap[$row->purpose_id] ?? null) : null,
                'caliber_id' => $this->caliberMap[$row->cartridge_id] ?? null,
                'bullet_type_id' => null,
                'ammunition_casing_id' => null,
                'ammunition_condition_id' => null,
                'primer_type_id' => null,
                'shot_material_id' => null,
                'shell_length_id' => null,
                'shell_type_id' => null,
                'user_id' => $this->mapUser($row->user_id),
                'created_at' => $row->created_at,
                'updated_at' => $row->updated_at,
            ]);

            $this->ammunitionMap[$row->id] = $newId;
        }

        $this->line('  ammunition: '.count($this->ammunitionMap));
    }

    private function migrateMagazines(): void
    {
        $this->info('Phase 3: magazines → cms.magazines');
        $rows = $this->legacy()->table('magazines')->get();

        foreach ($rows as $row) {
            if ($this->dryRun) {
                $this->magazineMap[$row->id] = $row->id;

                continue;
            }

            $newId = $this->new()->table('cms.magazines')->insertGetId([
                'label' => $row->label,
                'manufacturer' => $row->manufacturer,
                'model_name' => $row->model_name,
                'capacity' => $row->capacity,
                'serial_number' => $row->serial_number ?? null,
                'id_marking' => $row->id_marking ?? null,
                'loaded_ammunition_id' => null,
                'loaded_rounds' => 0,
                'location_id' => null,
                'current_firearm_id' => null,
                'user_id' => $this->mapUser($row->user_id),
                'created_at' => $row->created_at,
                'updated_at' => $row->updated_at,
            ]);

            $this->magazineMap[$row->id] = $newId;
        }

        $this->line('  magazines: '.count($this->magazineMap));
    }

    // -------------------------------------------------------------------------
    // Phase 4 — Pivot tables
    // -------------------------------------------------------------------------

    private function migrateCaliberFirearmPivot(): void
    {
        $this->info('Phase 4: caliber_firearm → cms.caliber_firearm');
        $rows = $this->legacy()->table('caliber_firearm')->get();
        $count = 0;

        foreach ($rows as $row) {
            $caliberId = $this->caliberMap[$row->caliber_id] ?? null;
            $firearmId = $this->firearmMap[$row->firearm_id] ?? null;

            if (! $caliberId || ! $firearmId) {
                $this->warn("  skip caliber_firearm ({$row->caliber_id},{$row->firearm_id}) — unmapped FK");

                continue;
            }

            if (! $this->dryRun) {
                $this->new()->table('cms.caliber_firearm')->insert([
                    'caliber_id' => $caliberId,
                    'firearm_id' => $firearmId,
                ]);
            }

            $count++;
        }

        $this->line("  caliber_firearm: {$count}");
    }

    private function migrateCaliberMagazinePivot(): void
    {
        $this->info('Phase 4: caliber_magazine → cms.caliber_magazine');
        $rows = $this->legacy()->table('caliber_magazine')->get();
        $count = 0;

        foreach ($rows as $row) {
            $caliberId = $this->caliberMap[$row->caliber_id] ?? null;
            $magazineId = $this->magazineMap[$row->magazine_id] ?? null;

            if (! $caliberId || ! $magazineId) {
                $this->warn("  skip caliber_magazine ({$row->caliber_id},{$row->magazine_id}) — unmapped FK");

                continue;
            }

            if (! $this->dryRun) {
                $this->new()->table('cms.caliber_magazine')->insert([
                    'caliber_id' => $caliberId,
                    'magazine_id' => $magazineId,
                ]);
            }

            $count++;
        }

        $this->line("  caliber_magazine: {$count}");
    }

    // -------------------------------------------------------------------------
    // Phase 5 — Training sessions (structural reshape)
    // -------------------------------------------------------------------------

    private function migrateTrainingSessions(): void
    {
        $this->info('Phase 5: trips → cms.training_sessions');
        $rows = $this->legacy()->table('trips')->get();

        foreach ($rows as $row) {
            $rangeId = isset($row->range_id) ? ($this->rangeMap[$row->range_id] ?? null) : null;
            $date = Carbon::parse($row->trip_date);

            if ($this->dryRun) {
                $this->trainingSessionMap[$row->id] = $row->id;
                $this->trainingSessionDateMap[$row->id] = $row->trip_date;

                continue;
            }

            $newId = $this->new()->table('cms.training_sessions')->insertGetId([
                'label' => 'Range Trip – '.$date->format('M j, Y'),
                'description' => null,
                'session_date' => $row->trip_date,
                'location_id' => null,
                'range_id' => $rangeId,
                'user_id' => $this->mapUser($row->user_id),
                'created_at' => $row->created_at,
                'updated_at' => $row->updated_at,
            ]);

            $this->trainingSessionMap[$row->id] = $newId;
            $this->trainingSessionDateMap[$row->id] = $row->trip_date;
        }

        $this->line('  training_sessions (from trips): '.count($this->trainingSessionMap));
    }

    private function migrateSessionLines(): void
    {
        // The live DB has two tables: training_sessions (ammunition_id, 179 rows, partially
        // migrated) and shoots (bullet_id, 203 rows including 24 new rows not yet migrated).
        // We read both and de-duplicate by ID so no row is inserted twice.
        $this->info('Phase 5: training_sessions + shoots → cms.session_lines');
        $count = 0;
        $processedIds = [];

        // Primary: training_sessions (already uses ammunition_id)
        $rows = $this->legacy()->table('training_sessions')->get();

        foreach ($rows as $row) {
            $processedIds[] = $row->id;
            $trainingSessionId = $this->trainingSessionMap[$row->trip_id] ?? null;
            $firearmId = $this->firearmMap[$row->firearm_id] ?? null;
            $ammunitionId = $this->ammunitionMap[$row->ammunition_id] ?? null;

            if (! $trainingSessionId || ! $firearmId || ! $ammunitionId) {
                $this->warn("  skip session_line (training_sessions id {$row->id}) — unmapped FK (trip:{$row->trip_id}, firearm:{$row->firearm_id}, ammo:{$row->ammunition_id})");

                continue;
            }

            if ($this->dryRun) {
                $this->sessionLineMap[$row->id] = $row->id;
                $count++;

                continue;
            }

            $newId = $this->new()->table('cms.session_lines')->insertGetId([
                'training_session_id' => $trainingSessionId,
                'firearm_id' => $firearmId,
                'ammunition_id' => $ammunitionId,
                'suppressor_id' => null,
                'rounds' => $row->rounds,
                'deduct_ammo' => true,
                'add_firearm_count' => true,
                'add_suppressor_count' => false,
                'user_id' => $this->mapUser($row->user_id),
                'created_at' => $row->created_at,
                'updated_at' => $row->updated_at,
            ]);

            // Mirror what SessionLineController::createInventoryDeduction() does at runtime
            $this->insertInventoryDeduction(
                $newId,
                $ammunitionId,
                $row->rounds,
                $this->trainingSessionDateMap[$row->trip_id],
                $this->mapUser($row->user_id),
                $row->created_at,
            );

            $this->sessionLineMap[$row->id] = $newId;
            $count++;
        }

        // Supplement: shoots rows not yet migrated to training_sessions (still use bullet_id)
        $supplementRows = $this->legacy()->table('shoots')
            ->when(! empty($processedIds), fn ($q) => $q->whereNotIn('id', $processedIds))
            ->get();

        foreach ($supplementRows as $row) {
            $trainingSessionId = $this->trainingSessionMap[$row->trip_id] ?? null;
            $firearmId = $this->firearmMap[$row->firearm_id] ?? null;
            $ammunitionId = $this->ammunitionMap[$row->bullet_id] ?? null;

            if (! $trainingSessionId || ! $firearmId || ! $ammunitionId) {
                $this->warn("  skip session_line (shoots id {$row->id}) — unmapped FK (trip:{$row->trip_id}, firearm:{$row->firearm_id}, bullet:{$row->bullet_id})");

                continue;
            }

            if ($this->dryRun) {
                $this->sessionLineMap[$row->id] = $row->id;
                $count++;

                continue;
            }

            $newId = $this->new()->table('cms.session_lines')->insertGetId([
                'training_session_id' => $trainingSessionId,
                'firearm_id' => $firearmId,
                'ammunition_id' => $ammunitionId,
                'suppressor_id' => null,
                'rounds' => $row->rounds,
                'deduct_ammo' => true,
                'add_firearm_count' => true,
                'add_suppressor_count' => false,
                'user_id' => $this->mapUser($row->user_id),
                'created_at' => $row->created_at,
                'updated_at' => $row->updated_at,
            ]);

            $this->insertInventoryDeduction(
                $newId,
                $ammunitionId,
                $row->rounds,
                $this->trainingSessionDateMap[$row->trip_id],
                $this->mapUser($row->user_id),
                $row->created_at,
            );

            $this->sessionLineMap[$row->id] = $newId;
            $count++;
        }

        $this->line("  session_lines: {$count}");
    }

    // -------------------------------------------------------------------------
    // Phase 6 — Inventories
    // -------------------------------------------------------------------------

    private function migrateInventories(): void
    {
        $this->info('Phase 6: inventories → cms.inventories');
        $rows = $this->legacy()->table('inventories')->get();

        foreach ($rows as $row) {
            // Live DB still uses bullet_id (the rename migration was never run on this DB)
            $ammunitionId = $this->ammunitionMap[$row->bullet_id] ?? null;

            if (! $ammunitionId) {
                $this->warn("  skip inventory (legacy id {$row->id}) — unmapped bullet_id {$row->bullet_id}");

                continue;
            }

            if ($this->dryRun) {
                $this->inventoryMap[$row->id] = $row->id;

                continue;
            }

            $newId = $this->new()->table('cms.inventories')->insertGetId([
                'rounds' => $row->rounds,
                'inventory_date' => Carbon::parse($row->created_at)->toDateString(),
                'order_id' => isset($row->order_id) ? ($this->orderMap[$row->order_id] ?? null) : null,
                'cost' => $row->cost ?? 0,
                'training_session_id' => null,
                'session_line_id' => null,
                'firearm_id' => null,
                'ammunition_id' => $ammunitionId,
                'user_id' => $this->mapUser($row->user_id),
                'created_at' => $row->created_at,
                'updated_at' => $row->updated_at,
            ]);

            $this->inventoryMap[$row->id] = $newId;
        }

        $this->line('  inventories: '.count($this->inventoryMap));
    }

    // -------------------------------------------------------------------------
    // Phase 7 — Media, Notes, Targets
    // -------------------------------------------------------------------------

    private function migratePictures(): void
    {
        $this->info('Phase 7: pictures → cms.pictures');
        $rows = $this->legacy()->table('pictures')->get();

        foreach ($rows as $row) {
            if ($this->dryRun) {
                $this->pictureMap[$row->id] = $row->id;

                continue;
            }

            $newId = $this->new()->table('cms.pictures')->insertGetId([
                'name' => $row->name,
                'filename' => $row->filename,
                'user_id' => $this->mapUser($row->user_id),
                'created_at' => $row->created_at,
                'updated_at' => $row->updated_at,
            ]);

            $this->pictureMap[$row->id] = $newId;
        }

        $this->line('  pictures: '.count($this->pictureMap));
    }

    private function migratePictureables(): void
    {
        $this->info('Phase 7: pictureables → cms.pictureables');
        $rows = $this->legacy()->table('pictureables')->get();
        $count = 0;

        foreach ($rows as $row) {
            $pictureId = $this->pictureMap[$row->picture_id] ?? null;

            if (! $pictureId) {
                $this->warn("  skip pictureable (legacy id {$row->id}) — unmapped picture_id {$row->picture_id}");

                continue;
            }

            $newPictureableId = $this->remapMorphId($row->pictureable_type, $row->pictureable_id);

            if (! $newPictureableId) {
                $this->warn("  skip pictureable (legacy id {$row->id}) — unmapped {$row->pictureable_type}:{$row->pictureable_id}");

                continue;
            }

            if (! $this->dryRun) {
                $this->new()->table('cms.pictureables')->insert([
                    'picture_id' => $pictureId,
                    'pictureable_id' => $newPictureableId,
                    'pictureable_type' => $row->pictureable_type,
                    'is_primary' => false,
                    'order' => null,
                    'user_id' => null,
                    'created_at' => $row->created_at,
                    'updated_at' => $row->updated_at,
                ]);
            }

            $count++;
        }

        $this->line("  pictureables: {$count}");
    }

    private function migrateNotes(): void
    {
        $this->info('Phase 7: notes → cms.notes');
        $rows = $this->legacy()->table('notes')->get();
        $count = 0;

        foreach ($rows as $row) {
            $newNotableId = $this->remapMorphId($row->notable_type, $row->notable_id);

            if (! $newNotableId) {
                $this->warn("  skip note (legacy id {$row->id}) — unmapped {$row->notable_type}:{$row->notable_id}");

                continue;
            }

            if (! $this->dryRun) {
                $this->new()->table('cms.notes')->insert([
                    'user_id' => $this->mapUser($row->user_id),
                    'note' => $row->note,
                    'notable_id' => $newNotableId,
                    'notable_type' => $row->notable_type,
                    'created_at' => $row->created_at,
                    'updated_at' => $row->updated_at,
                ]);
            }

            $count++;
        }

        $this->line("  notes: {$count}");
    }

    private function migrateTargets(): void
    {
        $this->info('Phase 7: targets → cms.targets');
        $rows = $this->legacy()->table('targets')->get();
        $count = 0;

        foreach ($rows as $row) {
            $pictureId = isset($row->picture_id) ? ($this->pictureMap[$row->picture_id] ?? null) : null;

            if (! $this->dryRun) {
                $this->new()->table('cms.targets')->insert([
                    'label' => $row->label ?? null,
                    'distance' => $row->distance,
                    'group_size' => $row->group_size,
                    'picture_id' => $pictureId,
                    // bullet_id in targets references ammunitions in legacy
                    'bullet_id' => isset($row->bullet_id) ? ($this->ammunitionMap[$row->bullet_id] ?? null) : null,
                    'firearm_id' => isset($row->firearm_id) ? ($this->firearmMap[$row->firearm_id] ?? null) : null,
                    // shoot_id references legacy training_sessions (shoots); map to new session_lines
                    'shoot_id' => isset($row->shoot_id) ? ($this->sessionLineMap[$row->shoot_id] ?? null) : null,
                    // trip_id references legacy trips; map to new training_sessions
                    'trip_id' => isset($row->trip_id) ? ($this->trainingSessionMap[$row->trip_id] ?? null) : null,
                    'training_session_id' => null,
                    'user_id' => $this->mapUser($row->user_id),
                    'created_at' => $row->created_at,
                    'updated_at' => $row->updated_at,
                ]);
            }

            $count++;
        }

        $this->line("  targets: {$count}");
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function mapUser(?int $legacyUserId): ?int
    {
        if ($legacyUserId === null) {
            return null;
        }

        return $this->userMap[$legacyUserId] ?? null;
    }

    /**
     * Remap a polymorphic morph ID from the legacy ID space to the new ID space.
     * Model namespaces are identical between systems (App\Models\*).
     */
    private function remapMorphId(string $morphType, int $legacyId): ?int
    {
        return match ($morphType) {
            'App\\Models\\Ammunition', 'App\\Models\\Bullet' => $this->ammunitionMap[$legacyId] ?? null,
            'App\\Models\\Firearm' => $this->firearmMap[$legacyId] ?? null,
            'App\\Models\\Magazine' => $this->magazineMap[$legacyId] ?? null,
            'App\\Models\\Order' => $this->orderMap[$legacyId] ?? null,
            'App\\Models\\Range' => $this->rangeMap[$legacyId] ?? null,
            'App\\Models\\Store' => $this->storeMap[$legacyId] ?? null,
            'App\\Models\\TrainingSession' => $this->sessionLineMap[$legacyId] ?? null,
            default => null,
        };
    }

    private function insertInventoryDeduction(
        int $sessionLineId,
        int $ammunitionId,
        int $rounds,
        string $sessionDate,
        ?int $userId,
        string $createdAt,
    ): void {
        $this->new()->table('cms.inventories')->insert([
            'ammunition_id' => $ammunitionId,
            'rounds' => -$rounds,
            'inventory_date' => Carbon::parse($sessionDate)->toDateString(),
            'session_line_id' => $sessionLineId,
            'order_id' => null,
            'firearm_id' => null,
            'training_session_id' => null,
            'cost' => 0,
            'user_id' => $userId,
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ]);
    }

    private function recalculateAmmunitionInventory(): void
    {
        if ($this->dryRun) {
            return;
        }

        $this->info('Recalculating ammunition inventory totals');

        $ammoIds = array_values($this->ammunitionMap);

        foreach ($ammoIds as $ammoId) {
            $total = $this->new()->table('cms.inventories')
                ->where('ammunition_id', $ammoId)
                ->sum('rounds');

            $this->new()->table('cms.ammunition')
                ->where('id', $ammoId)
                ->update(['inventory' => $total]);
        }

        $this->line('  recalculated: '.count($ammoIds).' ammunition records');
    }

    private function verifyLegacyConnection(): void
    {
        try {
            $this->legacy()->getPdo();
            $this->info('Legacy database connection OK.');
        } catch (\Exception $e) {
            $this->error('Cannot connect to legacy database: '.$e->getMessage());
            exit(self::FAILURE);
        }
    }

    private function legacy(): Connection
    {
        return DB::connection('legacy');
    }

    private function new(): Connection
    {
        return DB::connection('pgsql');
    }
}
