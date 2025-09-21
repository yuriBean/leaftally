<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('plans', function (Blueprint $table) {
      $table->json('features')->nullable()->after('storage_limit');

        // use ->json('features') if your DB supports JSON types
    });
}

public function down()
{
    Schema::table('plans', function (Blueprint $table) {
        $table->dropColumn('features');
    });
}
};
