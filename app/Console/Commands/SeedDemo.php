<?php

namespace App\Console\Commands;

use App\Actions\Users\ProvisionDefaultReferenceData;
use App\Models\User;
use Database\Seeders\FullInventorySeeder;
use Database\Seeders\SystemReferenceDataSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SeedDemo extends Command
{
    protected $signature = 'demo:seed {mode : user preserves data; empty and full rebuild the configured database}';

    protected $description = 'Prepare the local test account for UI review';

    public function handle(): int
    {
        if (! app()->environment('local', 'testing')) {
            $this->error('Demo data is available only in local and testing environments.');

            return self::FAILURE;
        }

        $mode = $this->argument('mode');
        if (! in_array($mode, ['user', 'empty', 'full'], true)) {
            $this->error('Choose user, empty, or full.');

            return self::INVALID;
        }

        foreach (['name', 'email', 'password'] as $field) {
            if (blank(config('app.test_user_'.$field))) {
                $this->error('Configure TEST_NAME, TEST_EMAIL and TEST_PASSWORD before seeding.');

                return self::FAILURE;
            }
        }

        if ($mode !== 'user') {
            $result = $this->call('migrate:fresh');
            if ($result !== self::SUCCESS) {
                return $result;
            }

            Storage::disk('demo-pictures')->deleteDirectory('samples');
        }

        return DB::transaction(function () use ($mode): int {
            $this->call('db:seed', ['--class' => SystemReferenceDataSeeder::class]);
            $this->call('db:seed', ['--class' => UserSeeder::class]);
            $user = User::where('email', config('app.test_user_email'))->firstOrFail();
            resolve(ProvisionDefaultReferenceData::class)->execute($user);

            if ($mode === 'full') {
                return $this->call('db:seed', ['--class' => FullInventorySeeder::class]);
            }

            return self::SUCCESS;
        });
    }
}
