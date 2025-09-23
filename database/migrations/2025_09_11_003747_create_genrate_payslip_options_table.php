<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGenratePayslipOptionsTable extends Migration
{
    public function up()
    {
        Schema::create('genrate_payslip_options', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('genrate_payslip_options');
    }
}
