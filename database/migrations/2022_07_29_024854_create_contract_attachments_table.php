<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContractAttachmentsTable extends Migration
{
    public function up()
    {
        Schema::create('contract_attachments', function (Blueprint $table) {
            $table->id();
            $table->Integer('contract_id');
            $table->string('files');
            $table->string('type')->nullable();
            $table->integer('created_by');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('contract_attachments');
    }
}
