<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Remove free plans (price <= 0) from plans table
        \DB::table('plans')->where('price', '<=', 0)->delete();
        
        // Update users who were on free plans to have no plan (null)
        // This will require them to purchase a plan to continue using the system
        \DB::table('users')->where('plan', '<=', 0)->update(['plan' => null]);
        
        // Also handle any users who might have been assigned to the deleted free plan
        $existingPlanIds = \DB::table('plans')->pluck('id')->toArray();
        \DB::table('users')->whereNotIn('plan', $existingPlanIds)->whereNotNull('plan')->update(['plan' => null]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Note: This migration cannot be fully reversed as we've deleted data
        // In a rollback scenario, you would need to manually recreate free plans
        // and reassign users if needed
    }
};
