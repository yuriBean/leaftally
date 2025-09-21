<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('bom_inputs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bom_id')->index();
            $table->unsignedBigInteger('product_id')->index();      // raw material
            $table->decimal('qty_per_batch', 15, 4);                 // quantity required per one "batch"
            $table->decimal('scrap_pct', 8, 2)->default(0);          // optional
            $table->timestamps();

            $table->foreign('bom_id')->references('id')->on('boms')->cascadeOnDelete();
        });
    }
    public function down(): void {
        Schema::dropIfExists('bom_inputs');
    }
};
