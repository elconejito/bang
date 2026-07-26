<?php

namespace Database\Seeders;

use App\Actions\Users\ProvisionDefaultReferenceData;
use App\Models\User;
use Illuminate\Database\Seeder;

class DefaultReferenceDataSeeder extends Seeder
{
    public function run(ProvisionDefaultReferenceData $provisionDefaultReferenceData): void
    {
        User::query()
            ->lazyById()
            ->each(fn (User $user) => $provisionDefaultReferenceData->execute($user));
    }
}
