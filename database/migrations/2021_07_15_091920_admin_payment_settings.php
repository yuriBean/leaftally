<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AdminPaymentSettings extends Migration
{
    public function up()
    {
        Schema::create(
            'admin_payment_settings', function (Blueprint $table){
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
        Schema::dropIfExists('admin_payment_settings');
    }
}
