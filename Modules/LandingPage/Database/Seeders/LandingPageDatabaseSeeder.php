<?php

namespace Modules\LandingPage\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Nwidart\Modules\Facades\Module;
use Modules\LandingPage\Entities\LandingPageSetting;

class LandingPageDatabaseSeeder extends Seeder
{
    public function run()
    {
        Model::unguard();

        $this->call(LandingPageDataTableSeeder::class);

    }
}
