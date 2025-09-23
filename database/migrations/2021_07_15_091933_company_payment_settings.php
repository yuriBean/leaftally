<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CompanyPaymentSettings extends Migration
{
    public function up()
    {
        Schema::create(
            'company_payment_settings', function (Blueprint $table){
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('value');
            $table->integer('created_by');
            $table->timestamps();
            $table->unique(
                [
                    'name',
                    'created_by',
                ]
            );
        }
        );
    }

    public function down()
    {
        Schema::dropIfExists('company_payment_settings');
    }
}
