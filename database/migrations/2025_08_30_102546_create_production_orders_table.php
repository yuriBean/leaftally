<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('production_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bom_id')->index();
            $table->string('code')->index();
            $table->tinyInteger('status')->default(0);
            $table->decimal('multiplier', 15, 4)->default(1);
            $table->date('planned_date')->nullable();
            $table->decimal('manufacturing_cost', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->unsignedBigInteger('created_by')->index();
            $table->timestamps();

            $table->foreign('bom_id')->references('id')->on('boms')->cascadeOnDelete();
        });
    }
    public function down(): void {
        Schema::dropIfExists('production_orders');
    }
};
