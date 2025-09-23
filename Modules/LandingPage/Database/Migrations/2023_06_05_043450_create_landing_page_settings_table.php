<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up()
    {
        if(!Schema::hasTable('landing_page_settings')){
            Schema::create('landing_page_settings', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name')->unique();
                $table->longtext('value')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('landing_page_settings');
    }
};
