<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeSettingsValueType extends Migration
{
    public function up()
    {
        Schema::table(
            'settings', function (Blueprint $table){
            $table->text('value')->nullable()->change();;
        }
        );
    }

    public function down()
    {
        Schema::table(
            'settings', function (Blueprint $table){
            $table->dropColumn('value');
        }
        );
    }
}
