<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBankAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'bank_accounts',
            function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('holder_name');
                $table->string('bank_name');
                $table->string('account_number');
                $table->decimal('opening_balance', 15, 2)->default('0.0');
                $table->string('contact_number')->nullable();
                $table->text('bank_address')->nullable();
                $table->integer('created_by')->default('0');
                $table->timestamps();
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bank_accounts');
    }
}
