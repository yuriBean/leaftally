<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeTaxColumnType extends Migration
{
    public function up()
    {
        Schema::table(
            'proposal_products', function (Blueprint $table){
            $table->string('tax', '50')->nullable()->change();
        }
        );
        Schema::table(
            'invoice_products', function (Blueprint $table){
            $table->string('tax', '50')->nullable()->change();
        }
        );
        Schema::table(
            'bill_products', function (Blueprint $table){
            $table->string('tax', '50')->nullable()->change();
        }
        );
    }

    public function down()
    {
        Schema::table(
            'proposal_products', function (Blueprint $table){
            $table->dropColumn('tax');
        }
        );
        Schema::table(
            'invoice_products', function (Blueprint $table){
            $table->dropColumn('tax');
        }
        );
        Schema::table(
            'bill_products', function (Blueprint $table){
            $table->dropColumn('tax');
        }
        );
    }
}
