<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class MainSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PlansTableSeeder::class,
            UsersTableSeeder::class,
            AiTemplateSeeder::class,
            ConstantsModuleSeeder::class,
            NotificationSeeder::class,
        ]);
    }
}
