<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('production_outputs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('production_order_id')->index();
            $table->unsignedBigInteger('product_id')->index();      // finished product produced
            $table->decimal('qty_planned', 15, 4)->default(0);
            $table->decimal('qty_good',   15, 4)->default(0);
            $table->decimal('qty_scrap',  15, 4)->default(0);
            $table->decimal('cost_allocated', 15, 2)->default(0);
            $table->timestamps();

            $table->foreign('production_order_id')->references('id')->on('production_orders')->cascadeOnDelete();
        });
    }
    public function down(): void {
        Schema::dropIfExists('production_outputs');
    }
};
