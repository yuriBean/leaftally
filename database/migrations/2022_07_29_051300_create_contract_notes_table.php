<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContractNotesTable extends Migration
{
    public function up()
    {
        Schema::create('contract_notes', function (Blueprint $table) {
            $table->id();
            $table->Integer('contract_id');
            $table->longText('note');
            $table->string('type')->nullable();
            $table->integer('created_by');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('contract_notes');
    }
}
