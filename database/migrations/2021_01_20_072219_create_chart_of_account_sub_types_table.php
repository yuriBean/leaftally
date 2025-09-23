<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChartOfAccountSubTypesTable extends Migration
{
    public function up()
    {
        Schema::create(
            'chart_of_account_sub_types',
            function (Blueprint $table) {
                $table->id();
                $table->string('name')->default();
                $table->integer('type')->default(0);
                $table->timestamps();
            }
        );
    }

    public function down()
    {
        Schema::dropIfExists('chart_of_account_sub_types');
    }
}