<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
     public function run(): void
    {
        $permissions = [

            // products
            'product.create',
            'product.update',
            'product.delete',
            'product.view',

            // categories
            'category.create',
            'category.update',
            'category.delete',

            // orders
            'order.view',
            'order.update',

            // vendors
            'vendor.approve',
            'vendor.reject',

            // analytics
            'analytics.view',

        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission
            ]);
        }
    }
}
