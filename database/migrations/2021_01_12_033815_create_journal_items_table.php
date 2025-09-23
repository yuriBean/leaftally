<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJournalItemsTable extends Migration
{
    public function up()
    {
        Schema::create(
            'journal_items', function (Blueprint $table){
            $table->id();
            $table->integer('journal')->default(0);
            $table->integer('account')->default(0);
            $table->text('description')->nullable();
            $table->float('debit')->default(0.0);
            $table->float('credit')->default(0.0);
            $table->timestamps();
        }
        );
    }

    public function down()
    {
        Schema::dropIfExists('journal_items');
    }
}
