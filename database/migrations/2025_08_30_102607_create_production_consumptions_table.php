<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('production_consumptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('production_order_id')->index();
            $table->unsignedBigInteger('product_id')->index();      // raw material consumed
            $table->decimal('qty_required', 15, 4);
            $table->decimal('qty_issued', 15, 4)->default(0);       // deducted/held at start
            $table->decimal('unit_cost', 15, 4)->default(0);
            $table->decimal('total_cost', 15, 2)->default(0);
            $table->timestamps();

            $table->foreign('production_order_id')->references('id')->on('production_orders')->cascadeOnDelete();
        });
    }
    public function down(): void {
        Schema::dropIfExists('production_consumptions');
    }
};
