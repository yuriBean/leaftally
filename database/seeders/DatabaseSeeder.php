<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Utility;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Request;

class DatabaseSeeder extends Seeder
{

    public function run()
    {
        $this->call(NotificationSeeder::class);
        
        Artisan::call('module:migrate LandingPage');
        Artisan::call('module:seed LandingPage');

        if((Request::hasMacro('route') && Request::route()) && \Request::route()->getName()!='LaravelUpdater::database')
        {
            $this->call(PlansTableSeeder::class);
            $this->call(UsersTableSeeder::class);
            $this->call(AiTemplateSeeder::class);

        }else{
            Utility::languagecreate();

        }

    }
}
