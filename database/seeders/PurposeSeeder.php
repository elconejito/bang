<?php
namespace Database\Seeders;

use App\Models\Reference\Purpose;
use App\Models\User;
use Illuminate\Database\Seeder;

class PurposeSeeder extends Seeder
{
    private $purposes = [
        [
            "label" => "Range/Training",
        ],
        [
            "label" => "Home/Self Defense",
        ],
        [
            "label" => "Match/Competition",
        ],
        [
            "label" => "Hunting",
        ],
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info("Starting Purpose seeder");

        $user = User::where("email", config('app.test_user_email'))->first();

        collect($this->purposes)->each(function ($purpose) use($user) {
            $purpose["user_id"] = $user->id;
            Purpose::create($purpose);
        });

        $this->command->info("Finished Purpose seeder");
    }
}
