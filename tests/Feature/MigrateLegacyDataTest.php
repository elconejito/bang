<?php

namespace Tests\Feature;

use Illuminate\Database\Connection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Tests for the migrate:legacy Artisan command.
 *
 * These tests use an in-memory SQLite database as a stand-in for the legacy MySQL
 * connection so they can run without a real legacy DB. The "legacy" connection is
 * swapped at test time via config().
 *
 * The schema mirrors the actual live DB state: partially migrated, so it has
 * both `cartridges` (the real caliber source) and the empty `calibers` table,
 * plus both `shoots` (bullet_id) and `training_sessions` (ammunition_id),
 * and `inventories` still using `bullet_id`.
 */
class MigrateLegacyDataTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.connections.legacy' => [
                'driver' => 'sqlite',
                'database' => ':memory:',
                'prefix' => '',
                'foreign_key_constraints' => false,
            ],
        ]);

        DB::purge('legacy');
        $this->seedLegacySchema();
    }

    // -------------------------------------------------------------------------
    // Happy-path tests
    // -------------------------------------------------------------------------

    public function test_users_are_matched_by_email_and_not_duplicated(): void
    {
        $legacy = DB::connection('legacy');

        $legacy->table('users')->insert([
            'id' => 1, 'name' => 'Harvey', 'email' => 'harvey@example.com',
            'password' => bcrypt('secret'), 'remember_token' => null,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        DB::table('idam.users')->insert([
            'name' => 'Harvey', 'email' => 'harvey@example.com',
            'password' => bcrypt('secret'), 'created_at' => now(), 'updated_at' => now(),
        ]);

        $before = DB::table('idam.users')->count();

        $this->artisan('migrate:legacy')->assertSuccessful();

        $this->assertSame($before, DB::table('idam.users')->count());
    }

    public function test_cartridges_are_migrated_to_cms_calibers(): void
    {
        $legacy = DB::connection('legacy');
        $userId = $this->insertLegacyUser($legacy);

        $legacy->table('cartridges')->insert([
            'id' => 1, 'size' => '9x19mm', 'label' => '9mm',
            'user_id' => $userId, 'created_at' => now(), 'updated_at' => now(),
        ]);

        $this->artisan('migrate:legacy')->assertSuccessful();

        $this->assertSame(1, DB::table('cms.calibers')->count());

        $caliber = DB::table('cms.calibers')->first();
        $this->assertSame('9mm', $caliber->caliber);    // cartridges.label → caliber
        $this->assertSame('9x19mm', $caliber->label);   // cartridges.size → label
        $this->assertNotNull($caliber->caliber_type_id); // placeholder "Legacy Import" type created
    }

    public function test_purposes_are_migrated_to_reference_schema(): void
    {
        $legacy = DB::connection('legacy');
        $userId = $this->insertLegacyUser($legacy);

        $legacy->table('purposes')->insert([
            'id' => 1, 'label' => 'Self Defense', 'user_id' => $userId,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $this->artisan('migrate:legacy')->assertSuccessful();

        $this->assertSame(1, DB::table('reference.purposes')->count());
        $this->assertSame('Self Defense', DB::table('reference.purposes')->first()->label);
    }

    public function test_firearms_are_migrated_with_nullable_new_fields(): void
    {
        $legacy = DB::connection('legacy');
        $userId = $this->insertLegacyUser($legacy);

        $legacy->table('firearms')->insert([
            'id' => 1, 'label' => 'Primary', 'manufacturer' => 'Glock', 'model' => 'G19',
            'user_id' => $userId, 'created_at' => now(), 'updated_at' => now(),
        ]);

        $this->artisan('migrate:legacy')->assertSuccessful();

        $firearm = DB::table('cms.firearms')->first();
        $this->assertSame('Glock', $firearm->manufacturer);
        $this->assertNull($firearm->serial);
        $this->assertNull($firearm->purchase_price);
    }

    public function test_ammunition_name_is_mapped_to_label(): void
    {
        $legacy = DB::connection('legacy');
        $userId = $this->insertLegacyUser($legacy);

        $legacy->table('cartridges')->insert([
            'id' => 1, 'size' => '9x19mm', 'label' => '9mm',
            'user_id' => $userId, 'created_at' => now(), 'updated_at' => now(),
        ]);

        $legacy->table('ammunitions')->insert([
            'id' => 1, 'manufacturer' => 'Federal', 'name' => 'HST 124gr',
            'weight' => 124, 'inventory' => 50, 'caliber_id' => 1,
            'purpose_id' => null, 'user_id' => $userId,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $this->artisan('migrate:legacy')->assertSuccessful();

        $ammo = DB::table('cms.ammunition')->first();
        $this->assertSame('HST 124gr', $ammo->label);
        $this->assertSame('Federal', $ammo->manufacturer);
        $this->assertNull($ammo->bullet_type_id);
    }

    public function test_bullets_not_in_ammunitions_are_also_migrated(): void
    {
        $legacy = DB::connection('legacy');
        $userId = $this->insertLegacyUser($legacy);

        $legacy->table('cartridges')->insert([
            'id' => 1, 'size' => '9x19mm', 'label' => '9mm',
            'user_id' => $userId, 'created_at' => now(), 'updated_at' => now(),
        ]);

        // Row in ammunitions (id=1) and an extra only in bullets (id=2)
        $legacy->table('ammunitions')->insert([
            'id' => 1, 'manufacturer' => 'Federal', 'name' => 'HST', 'weight' => 124,
            'inventory' => 0, 'caliber_id' => 1, 'purpose_id' => null,
            'user_id' => $userId, 'created_at' => now(), 'updated_at' => now(),
        ]);
        $legacy->table('bullets')->insert([
            'id' => 2, 'manufacturer' => 'Hornady', 'name' => 'XTP', 'weight' => 115,
            'inventory' => 0, 'cartridge_id' => 1, 'purpose_id' => null,
            'user_id' => $userId, 'created_at' => now(), 'updated_at' => now(),
        ]);

        $this->artisan('migrate:legacy')->assertSuccessful();

        $this->assertSame(2, DB::table('cms.ammunition')->count());
    }

    public function test_magazines_get_empty_status_and_null_loaded_ammunition(): void
    {
        $legacy = DB::connection('legacy');
        $userId = $this->insertLegacyUser($legacy);

        $legacy->table('magazines')->insert([
            'id' => 1, 'label' => 'Mag 1', 'manufacturer' => 'Magpul',
            'model_name' => 'PMAG 30', 'capacity' => 30,
            'serial_number' => null, 'id_marking' => null,
            'user_id' => $userId, 'created_at' => now(), 'updated_at' => now(),
        ]);

        $this->artisan('migrate:legacy')->assertSuccessful();

        $mag = DB::table('cms.magazines')->first();
        $this->assertSame('empty', $mag->status);
        $this->assertNull($mag->loaded_ammunition_id);
    }

    public function test_trips_become_training_sessions_with_generated_label(): void
    {
        $legacy = DB::connection('legacy');
        $userId = $this->insertLegacyUser($legacy);

        $legacy->table('ranges')->insert([
            'id' => 1, 'label' => 'Local Range',
            'user_id' => $userId, 'created_at' => now(), 'updated_at' => now(),
        ]);

        $legacy->table('trips')->insert([
            'id' => 1, 'trip_date' => '2023-06-15', 'range_id' => 1,
            'user_id' => $userId, 'created_at' => now(), 'updated_at' => now(),
        ]);

        $this->artisan('migrate:legacy')->assertSuccessful();

        $session = DB::table('cms.training_sessions')->first();
        $this->assertSame('Range Trip – Jun 15, 2023', $session->label);
        $this->assertSame('2023-06-15', $session->session_date);
        $this->assertNotNull($session->range_id);
    }

    public function test_training_sessions_rows_become_session_lines(): void
    {
        $legacy = DB::connection('legacy');
        $userId = $this->insertLegacyUser($legacy);
        $this->insertMinimalCaliberChain($legacy, $userId);

        $legacy->table('trips')->insert([
            'id' => 1, 'trip_date' => '2023-06-15', 'range_id' => null,
            'user_id' => $userId, 'created_at' => now(), 'updated_at' => now(),
        ]);

        // training_sessions already has ammunition_id
        $legacy->table('training_sessions')->insert([
            'id' => 1, 'trip_id' => 1, 'rounds' => 100,
            'firearm_id' => 1, 'ammunition_id' => 1,
            'user_id' => $userId, 'created_at' => now(), 'updated_at' => now(),
        ]);

        $this->artisan('migrate:legacy')->assertSuccessful();

        $this->assertSame(1, DB::table('cms.session_lines')->count());
        $line = DB::table('cms.session_lines')->first();
        $this->assertSame(100, $line->rounds);
        $this->assertTrue((bool) $line->deduct_ammo);
        $this->assertTrue((bool) $line->add_firearm_count);

        // A corresponding negative inventory deduction row must exist
        $deduction = DB::table('cms.inventories')->where('rounds', '<', 0)->first();
        $this->assertNotNull($deduction);
        $this->assertSame(-100, $deduction->rounds);
        $this->assertSame($line->id, $deduction->session_line_id);
    }

    public function test_shoots_rows_not_in_training_sessions_also_become_session_lines(): void
    {
        $legacy = DB::connection('legacy');
        $userId = $this->insertLegacyUser($legacy);
        $this->insertMinimalCaliberChain($legacy, $userId);

        $legacy->table('trips')->insert([
            'id' => 1, 'trip_date' => '2023-06-15', 'range_id' => null,
            'user_id' => $userId, 'created_at' => now(), 'updated_at' => now(),
        ]);

        // Row 1 already in training_sessions, row 2 only in shoots (uses bullet_id)
        $legacy->table('training_sessions')->insert([
            'id' => 1, 'trip_id' => 1, 'rounds' => 50,
            'firearm_id' => 1, 'ammunition_id' => 1,
            'user_id' => $userId, 'created_at' => now(), 'updated_at' => now(),
        ]);
        $legacy->table('shoots')->insert([
            'id' => 2, 'trip_id' => 1, 'rounds' => 75,
            'firearm_id' => 1, 'bullet_id' => 1,
            'user_id' => $userId, 'created_at' => now(), 'updated_at' => now(),
        ]);

        $this->artisan('migrate:legacy')->assertSuccessful();

        $this->assertSame(2, DB::table('cms.session_lines')->count());
    }

    public function test_ammunition_inventory_is_recalculated_as_purchases_minus_fired(): void
    {
        $legacy = DB::connection('legacy');
        $userId = $this->insertLegacyUser($legacy);
        $this->insertMinimalCaliberChain($legacy, $userId);

        $legacy->table('trips')->insert([
            'id' => 1, 'trip_date' => '2023-06-15', 'range_id' => null,
            'user_id' => $userId, 'created_at' => now(), 'updated_at' => now(),
        ]);

        // 500 rounds purchased
        $legacy->table('inventories')->insert([
            'id' => 1, 'boxes' => 5, 'rounds_per_box' => 100, 'rounds' => 500,
            'cost_per_box' => 25.00, 'cost' => 125.00,
            'order_id' => null, 'bullet_id' => 1,
            'user_id' => $userId, 'created_at' => now(), 'updated_at' => now(),
        ]);

        // 100 rounds fired across two sessions
        $legacy->table('training_sessions')->insert([
            'id' => 1, 'trip_id' => 1, 'rounds' => 60,
            'firearm_id' => 1, 'ammunition_id' => 1,
            'user_id' => $userId, 'created_at' => now(), 'updated_at' => now(),
        ]);
        $legacy->table('shoots')->insert([
            'id' => 2, 'trip_id' => 1, 'rounds' => 40,
            'firearm_id' => 1, 'bullet_id' => 1,
            'user_id' => $userId, 'created_at' => now(), 'updated_at' => now(),
        ]);

        $this->artisan('migrate:legacy')->assertSuccessful();

        // inventories table: +500 purchase, -60 deduction, -40 deduction = 400
        $ammo = DB::table('cms.ammunition')->first();
        $this->assertSame(400, $ammo->inventory);

        $totalInInventoryRows = DB::table('cms.inventories')->sum('rounds');
        $this->assertSame(400, (int) $totalInInventoryRows);
    }

    public function test_inventories_use_bullet_id_column_and_created_at_as_inventory_date(): void
    {
        $legacy = DB::connection('legacy');
        $userId = $this->insertLegacyUser($legacy);
        $this->insertMinimalCaliberChain($legacy, $userId);

        $createdAt = '2022-03-10 14:00:00';

        // inventories still uses bullet_id (rename migration never ran on live DB)
        $legacy->table('inventories')->insert([
            'id' => 1, 'boxes' => 2, 'rounds_per_box' => 50, 'rounds' => 100,
            'cost_per_box' => 25.00, 'cost' => 50.00,
            'order_id' => null, 'bullet_id' => 1,
            'user_id' => $userId,
            'created_at' => $createdAt, 'updated_at' => $createdAt,
        ]);

        $this->artisan('migrate:legacy')->assertSuccessful();

        $inv = DB::table('cms.inventories')->first();
        $this->assertSame('2022-03-10', $inv->inventory_date);
        $this->assertSame(100, $inv->rounds);
    }

    public function test_orders_are_migrated_with_null_order_ref(): void
    {
        $legacy = DB::connection('legacy');
        $userId = $this->insertLegacyUser($legacy);

        $legacy->table('stores')->insert([
            'id' => 1, 'label' => 'Ammoseek', 'user_id' => $userId,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $legacy->table('orders')->insert([
            'id' => 1, 'rounds' => 500, 'total_cost' => 200.00,
            'store_id' => 1, 'order_date' => '2022-01-01',
            'user_id' => $userId, 'created_at' => now(), 'updated_at' => now(),
        ]);

        $this->artisan('migrate:legacy')->assertSuccessful();

        $order = DB::table('cms.orders')->first();
        $this->assertSame(500, $order->rounds);
        $this->assertNull($order->order_ref);
        $this->assertNotNull($order->store_id);
    }

    public function test_dry_run_writes_nothing(): void
    {
        $legacy = DB::connection('legacy');
        $userId = $this->insertLegacyUser($legacy);

        $legacy->table('ranges')->insert([
            'id' => 1, 'label' => 'Test Range',
            'user_id' => $userId, 'created_at' => now(), 'updated_at' => now(),
        ]);

        $this->artisan('migrate:legacy', ['--dry-run' => true])->assertSuccessful();

        $this->assertSame(0, DB::table('cms.ranges')->count());
    }

    public function test_session_line_skipped_when_trip_not_found(): void
    {
        $legacy = DB::connection('legacy');
        $userId = $this->insertLegacyUser($legacy);
        $this->insertMinimalCaliberChain($legacy, $userId);

        // References trip_id 99 which doesn't exist
        $legacy->table('training_sessions')->insert([
            'id' => 1, 'trip_id' => 99, 'rounds' => 50,
            'firearm_id' => 1, 'ammunition_id' => 1,
            'user_id' => $userId, 'created_at' => now(), 'updated_at' => now(),
        ]);

        $this->artisan('migrate:legacy')->assertSuccessful();

        $this->assertSame(0, DB::table('cms.session_lines')->count());
    }

    // -------------------------------------------------------------------------
    // Legacy schema helpers
    // -------------------------------------------------------------------------

    private function seedLegacySchema(): void
    {
        $db = DB::connection('legacy');

        $db->statement('CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT, email TEXT UNIQUE, password TEXT,
            remember_token TEXT, created_at TEXT, updated_at TEXT
        )');
        $db->statement('CREATE TABLE IF NOT EXISTS caliber_types (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            label TEXT, user_id INTEGER, created_at TEXT, updated_at TEXT
        )');
        $db->statement('CREATE TABLE IF NOT EXISTS calibers (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            label TEXT, short_label TEXT, caliber_type_id INTEGER, user_id INTEGER,
            created_at TEXT, updated_at TEXT
        )');
        // The real caliber source in the live DB (calibers table is empty)
        $db->statement('CREATE TABLE IF NOT EXISTS cartridges (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            size TEXT, label TEXT,
            user_id INTEGER, created_at TEXT, updated_at TEXT
        )');
        $db->statement('CREATE TABLE IF NOT EXISTS purposes (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            label TEXT, user_id INTEGER, created_at TEXT, updated_at TEXT
        )');
        $db->statement('CREATE TABLE IF NOT EXISTS stores (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            label TEXT, user_id INTEGER, created_at TEXT, updated_at TEXT
        )');
        $db->statement('CREATE TABLE IF NOT EXISTS ranges (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            label TEXT, user_id INTEGER, created_at TEXT, updated_at TEXT
        )');
        $db->statement('CREATE TABLE IF NOT EXISTS orders (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            rounds INTEGER, total_cost REAL, store_id INTEGER,
            order_date TEXT, user_id INTEGER, created_at TEXT, updated_at TEXT
        )');
        $db->statement('CREATE TABLE IF NOT EXISTS firearms (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            label TEXT, manufacturer TEXT, model TEXT,
            user_id INTEGER, created_at TEXT, updated_at TEXT
        )');
        // Partially migrated ammo table (caliber_id references cartridges.id)
        $db->statement('CREATE TABLE IF NOT EXISTS ammunitions (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            manufacturer TEXT, name TEXT, weight INTEGER, inventory INTEGER,
            purpose_id INTEGER, caliber_id INTEGER,
            user_id INTEGER, created_at TEXT, updated_at TEXT
        )');
        // Original ammo table (cartridge_id references cartridges.id)
        $db->statement('CREATE TABLE IF NOT EXISTS bullets (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            manufacturer TEXT, name TEXT, weight INTEGER, inventory INTEGER,
            purpose_id INTEGER, cartridge_id INTEGER,
            user_id INTEGER, created_at TEXT, updated_at TEXT
        )');
        $db->statement('CREATE TABLE IF NOT EXISTS magazines (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            label TEXT, manufacturer TEXT, model_name TEXT, capacity INTEGER,
            serial_number TEXT, id_marking TEXT,
            user_id INTEGER, created_at TEXT, updated_at TEXT
        )');
        $db->statement('CREATE TABLE IF NOT EXISTS caliber_firearm (
            caliber_id INTEGER, firearm_id INTEGER
        )');
        $db->statement('CREATE TABLE IF NOT EXISTS caliber_magazine (
            caliber_id INTEGER, magazine_id INTEGER
        )');
        $db->statement('CREATE TABLE IF NOT EXISTS trips (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            trip_date TEXT, range_id INTEGER,
            user_id INTEGER, created_at TEXT, updated_at TEXT
        )');
        // Partially migrated shoot table (uses ammunition_id)
        $db->statement('CREATE TABLE IF NOT EXISTS training_sessions (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            trip_id INTEGER, rounds INTEGER, firearm_id INTEGER, ammunition_id INTEGER,
            user_id INTEGER, created_at TEXT, updated_at TEXT
        )');
        // Original shoot table still receiving new rows (uses bullet_id)
        $db->statement('CREATE TABLE IF NOT EXISTS shoots (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            trip_id INTEGER, rounds INTEGER, firearm_id INTEGER, bullet_id INTEGER,
            user_id INTEGER, created_at TEXT, updated_at TEXT
        )');
        // inventories still uses bullet_id (rename never ran on live DB)
        $db->statement('CREATE TABLE IF NOT EXISTS inventories (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            boxes INTEGER, rounds_per_box INTEGER, rounds INTEGER,
            cost_per_box REAL, cost REAL, order_id INTEGER, bullet_id INTEGER,
            user_id INTEGER, created_at TEXT, updated_at TEXT
        )');
        $db->statement('CREATE TABLE IF NOT EXISTS pictures (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT, filename TEXT,
            user_id INTEGER, created_at TEXT, updated_at TEXT
        )');
        $db->statement('CREATE TABLE IF NOT EXISTS pictureables (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            picture_id INTEGER, pictureable_id INTEGER, pictureable_type TEXT,
            created_at TEXT, updated_at TEXT
        )');
        $db->statement('CREATE TABLE IF NOT EXISTS notes (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER, note TEXT, notable_id INTEGER, notable_type TEXT,
            created_at TEXT, updated_at TEXT
        )');
        $db->statement('CREATE TABLE IF NOT EXISTS targets (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            label TEXT, distance REAL, group_size REAL, picture_id INTEGER,
            bullet_id INTEGER, firearm_id INTEGER, shoot_id INTEGER, trip_id INTEGER,
            user_id INTEGER, created_at TEXT, updated_at TEXT
        )');
    }

    private function insertLegacyUser(Connection $db): int
    {
        return $db->table('users')->insertGetId([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('secret'),
            'remember_token' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Insert the minimal cartridge → firearm + ammunition chain needed by tests
     * that exercise session_lines or inventories.
     */
    private function insertMinimalCaliberChain(Connection $db, int $userId): void
    {
        $db->table('cartridges')->insert([
            'id' => 1, 'size' => '9x19mm', 'label' => '9mm',
            'user_id' => $userId, 'created_at' => now(), 'updated_at' => now(),
        ]);
        $db->table('firearms')->insert([
            'id' => 1, 'label' => 'Carry Gun', 'manufacturer' => 'Glock', 'model' => 'G19',
            'user_id' => $userId, 'created_at' => now(), 'updated_at' => now(),
        ]);
        $db->table('ammunitions')->insert([
            'id' => 1, 'manufacturer' => 'Federal', 'name' => 'HST', 'weight' => 124,
            'inventory' => 0, 'caliber_id' => 1, 'purpose_id' => null,
            'user_id' => $userId, 'created_at' => now(), 'updated_at' => now(),
        ]);
    }
}
