<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = Role::findByName('super-admin');
        $vendor = Role::findByName('vendor');
        $customer = Role::findByName('customer');

        // admin gets everything
        $admin->givePermissionTo([
            'product.create',
            'product.update',
            'product.delete',
            'product.view',
            'category.create',
            'category.update',
            'category.delete',
            'order.view',
            'order.update',
            'vendor.approve',
            'vendor.reject',
            'analytics.view',
        ]);

        // vendor permissions
        $vendor->givePermissionTo([
            'product.create',
            'product.update',
            'product.view',
            'order.view',
        ]);

        // customer permissions
        $customer->givePermissionTo([
            'product.view',
        ]);
    }
}
