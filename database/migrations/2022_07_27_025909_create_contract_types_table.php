<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContractTypesTable extends Migration
{
    public function up()
    {
        Schema::create(
            'contract_types', function (Blueprint $table){
            $table->id();
            $table->string('name');
            $table->integer('created_by');
            $table->timestamps();
        }
        );

    }

    public function down()
    {
        Schema::dropIfExists('contract_types');
    }
}
