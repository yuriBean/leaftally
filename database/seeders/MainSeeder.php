<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class MainSeeder extends Seeder
{
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
