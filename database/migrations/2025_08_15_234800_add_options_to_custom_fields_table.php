<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('custom_fields', function (Blueprint $table) {
            $table->text('options')->nullable()->after('module');
        });
    }

    public function down(): void
    {
        Schema::table('custom_fields', function (Blueprint $table) {
            $table->dropColumn('options');
        });
    }
};
