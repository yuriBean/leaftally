<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        \DB::table('plans')->where('price', '<=', 0)->delete();
        
        \DB::table('users')->where('plan', '<=', 0)->update(['plan' => null]);
        
        $existingPlanIds = \DB::table('plans')->pluck('id')->toArray();
        \DB::table('users')->whereNotIn('plan', $existingPlanIds)->whereNotNull('plan')->update(['plan' => null]);
    }

    public function down(): void
    {
    }
};
