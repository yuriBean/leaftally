<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up()
    {
        if(!Schema::hasTable('join_us')){
            Schema::create('join_us', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('email')->unique();
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('join_us');
    }
};
