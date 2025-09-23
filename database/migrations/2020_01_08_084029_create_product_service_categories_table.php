<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductServiceCategoriesTable extends Migration
{
    public function up()
    {
        Schema::create('product_service_categories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('type')->default(0);
            $table->string('color')->default('#fc544b');
            $table->integer('created_by')->default('0');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_service_categories');
    }
}
