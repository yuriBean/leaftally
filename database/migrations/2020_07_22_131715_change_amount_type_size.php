<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeAmountTypeSize extends Migration
{
    public function up()
    {
        Schema::table(
            'goals', function (Blueprint $table){
            $table->decimal('amount', 15, 2)->default(0.00)->change();
        }
        );

        Schema::table(
            'revenues', function (Blueprint $table){
            $table->decimal('amount', 15, 2)->default(0.00)->change();
        }
        );

        Schema::table(
            'payments', function (Blueprint $table){
            $table->decimal('amount', 15, 2)->default(0.00)->change();
        }
        );
    }

    public function down()
    {
        Schema::table(
            'goals', function (Blueprint $table){
            $table->dropColumn('amount');
        }
        );

        Schema::table(
            'revenues', function (Blueprint $table){
            $table->dropColumn('amount');
        }
        );

        Schema::table(
            'payments', function (Blueprint $table){
            $table->dropColumn('amount');
        }
        );

    }
}
