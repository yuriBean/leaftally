<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class ConstantsModuleSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $guard = 'web';

        $modules = [
            'department',
            'designation',
            'branch',
            'constant bank',

            'bom',
            'production',
            'employee',
            'set salary',
            'pay slip',
            'loan option',
            'allowance option',
            'payslip type',
            'document type',
            'allowance',
            'commission',
            'loan',
            'saturation deduction',
            'other payment',
            'overtime',
            'deduction option'
        ];

        $actions = ['manage', 'create', 'edit', 'delete'];

        $oldPermissionNames = [];
        foreach ($modules as $m) {
            foreach ($actions as $a) {
                $oldPermissionNames[] = "{$a} {$m}";
            }
        }

        Permission::whereIn('name', $oldPermissionNames)
            ->where('guard_name', $guard)
            ->delete();

        $sampleRows = [
            'departments'       => ['Operations', 'Sales', 'Finance', 'Human Resources', 'IT'],
            'designations'      => ['Manager', 'Senior Associate', 'Associate', 'Intern'],
            'branches'          => ['Head Office', 'Lagos', 'Abuja', 'Port Harcourt'],
            'banks'             => ['Access Bank', 'First Bank', 'GTBank', 'UBA', 'Zenith Bank'],

            'allowance_types'   => ['Housing Allowance', 'Transport Allowance', 'Meal Allowance'],
            'deduction_types'   => ['Tax Deduction', 'Pension', 'Health Insurance'],
            'bonus_types'       => ['Performance Bonus', 'Annual Bonus', 'Referral Bonus'],
        ];

        foreach ($sampleRows as $table => $names) {
            if (Schema::hasTable($table)) {
                DB::table($table)->whereIn('name', $names)->delete();
            }
        }

        $createdPermissions = [];
        foreach ($modules as $m) {
            foreach ($actions as $a) {
                $createdPermissions[] = Permission::firstOrCreate([
                    'name'       => "{$a} {$m}",
                    'guard_name' => $guard,
                ]);
            }
        }

        $roles = Role::whereIn('name', ['super admin', 'company', 'accountant'])->get();
        foreach ($roles as $role) {
            foreach ($createdPermissions as $perm) {
                $role->givePermissionTo($perm);
            }
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
