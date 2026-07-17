<?php

namespace Database\Seeders;

use App\Actions\Users\ProvisionDefaultReferenceData;
use App\Models\User;
use Illuminate\Database\Seeder;

class DefaultReferenceDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(ProvisionDefaultReferenceData $provisionDefaultReferenceData): void
    {
        $user = User::query()
            ->where('email', config('app.test_user_email'))
            ->firstOrFail();

        $provisionDefaultReferenceData->execute($user);
    }
}
