<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('boms', function (Blueprint $table) {
            $table->id();
            $table->string('code')->index();                   // e.g., BOM-0001
            $table->string('name');
            $table->decimal('yield_pct', 8, 2)->default(100);  // overall yield if you want to use it
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->index();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('boms');
    }
};
