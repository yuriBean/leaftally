<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = [
            'bank_accounts',
        ];

        foreach ($tables as $name) {
            if (Schema::hasTable($name) && !Schema::hasColumn($name, 'deleted_at')) {
                Schema::table($name, function (Blueprint $table) {
                    // place after updated_at if present
                    $table->softDeletes()->after('updated_at');
                });
            }
        }
    }

    public function down(): void
    {
        $tables = [
            'bank_accounts',
        ];

        foreach ($tables as $name) {
            if (Schema::hasTable($name) && Schema::hasColumn($name, 'deleted_at')) {
                Schema::table($name, function (Blueprint $table) {
                    // drop the soft deletes column
                    $table->dropSoftDeletes();
                });
            }
        }
    }
};
