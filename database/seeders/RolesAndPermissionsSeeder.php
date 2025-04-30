<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Illuminate\Cache\CacheManager::class]->forget('spatie.permission.cache');

        // Create permissions
        $permissions = [
            // User permissions
            ['name' => 'view-users', 'display_name' => 'View Users', 'description' => 'Can view users'],
            ['name' => 'create-users', 'display_name' => 'Create Users', 'description' => 'Can create users'],
            ['name' => 'edit-users', 'display_name' => 'Edit Users', 'description' => 'Can edit users'],
            ['name' => 'delete-users', 'display_name' => 'Delete Users', 'description' => 'Can delete users'],

            // Category permissions
            ['name' => 'view-categories', 'display_name' => 'View Categories', 'description' => 'Can view categories'],
            ['name' => 'create-categories', 'display_name' => 'Create Categories', 'description' => 'Can create categories'],
            ['name' => 'edit-categories', 'display_name' => 'Edit Categories', 'description' => 'Can edit categories'],
            ['name' => 'delete-categories', 'display_name' => 'Delete Categories', 'description' => 'Can delete categories'],

            // Product permissions
            ['name' => 'view-products', 'display_name' => 'View Products', 'description' => 'Can view products'],
            ['name' => 'create-products', 'display_name' => 'Create Products', 'description' => 'Can create products'],
            ['name' => 'edit-products', 'display_name' => 'Edit Products', 'description' => 'Can edit products'],
            ['name' => 'delete-products', 'display_name' => 'Delete Products', 'description' => 'Can delete products'],

            // Supplier permissions
            ['name' => 'view-suppliers', 'display_name' => 'View Suppliers', 'description' => 'Can view suppliers'],
            ['name' => 'create-suppliers', 'display_name' => 'Create Suppliers', 'description' => 'Can create suppliers'],
            ['name' => 'edit-suppliers', 'display_name' => 'Edit Suppliers', 'description' => 'Can edit suppliers'],
            ['name' => 'delete-suppliers', 'display_name' => 'Delete Suppliers', 'description' => 'Can delete suppliers'],

            // Inventory permissions
            ['name' => 'view-inventory', 'display_name' => 'View Inventory', 'description' => 'Can view inventory'],
            ['name' => 'adjust-inventory', 'display_name' => 'Adjust Inventory', 'description' => 'Can adjust inventory levels'],

            // Purchase Order permissions
            ['name' => 'view-purchase-orders', 'display_name' => 'View Purchase Orders', 'description' => 'Can view purchase orders'],
            ['name' => 'create-purchase-orders', 'display_name' => 'Create Purchase Orders', 'description' => 'Can create purchase orders'],
            ['name' => 'edit-purchase-orders', 'display_name' => 'Edit Purchase Orders', 'description' => 'Can edit purchase orders'],
            ['name' => 'delete-purchase-orders', 'display_name' => 'Delete Purchase Orders', 'description' => 'Can delete purchase orders'],
            ['name' => 'receive-purchase-orders', 'display_name' => 'Receive Purchase Orders', 'description' => 'Can receive purchase orders'],

            // Order permissions
            ['name' => 'view-orders', 'display_name' => 'View Orders', 'description' => 'Can view orders'],
            ['name' => 'create-orders', 'display_name' => 'Create Orders', 'description' => 'Can create orders'],
            ['name' => 'edit-orders', 'display_name' => 'Edit Orders', 'description' => 'Can edit orders'],
            ['name' => 'delete-orders', 'display_name' => 'Delete Orders', 'description' => 'Can delete orders'],
            ['name' => 'process-orders', 'display_name' => 'Process Orders', 'description' => 'Can process orders'],

            // Report permissions
            ['name' => 'view-reports', 'display_name' => 'View Reports', 'description' => 'Can view reports'],
            ['name' => 'export-reports', 'display_name' => 'Export Reports', 'description' => 'Can export reports'],
        ];

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }

        // Create roles and assign permissions
        // Admin role
        $adminRole = Role::create([
            'name' => 'admin',
            'display_name' => 'Administrator',
            'description' => 'Administrator with full access',
        ]);

        // Assign all permissions to admin
        $adminRole->permissions()->attach(Permission::all());

        // Manager role
        $managerRole = Role::create([
            'name' => 'manager',
            'display_name' => 'Manager',
            'description' => 'Manager with limited access',
        ]);

        // Assign specific permissions to manager
        $managerPermissions = Permission::whereIn('name', [
            'view-users',
            'view-categories', 'create-categories', 'edit-categories',
            'view-products', 'create-products', 'edit-products',
            'view-suppliers', 'create-suppliers', 'edit-suppliers',
            'view-inventory', 'adjust-inventory',
            'view-purchase-orders', 'create-purchase-orders', 'edit-purchase-orders', 'receive-purchase-orders',
            'view-orders', 'create-orders', 'edit-orders', 'process-orders',
            'view-reports', 'export-reports',
        ])->get();

        $managerRole->permissions()->attach($managerPermissions);

        // Staff role
        $staffRole = Role::create([
            'name' => 'staff',
            'display_name' => 'Staff',
            'description' => 'Regular staff member',
        ]);

        // Assign specific permissions to staff
        $staffPermissions = Permission::whereIn('name', [
            'view-categories',
            'view-products',
            'view-suppliers',
            'view-inventory',
            'view-purchase-orders',
            'view-orders', 'create-orders', 'process-orders',
            'view-reports',
        ])->get();

        $staffRole->permissions()->attach($staffPermissions);

        // Create admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'is_admin' => true,
        ]);

        // Assign admin role to admin user
        $admin->roles()->attach($adminRole);

        // Create manager user
        $manager = User::create([
            'name' => 'Manager User',
            'email' => 'manager@example.com',
            'password' => Hash::make('password'),
            'is_admin' => false,
        ]);

        // Assign manager role to manager user
        $manager->roles()->attach($managerRole);

        // Create staff user
        $staff = User::create([
            'name' => 'Staff User',
            'email' => 'staff@example.com',
            'password' => Hash::make('password'),
            'is_admin' => false,
        ]);

        // Assign staff role to staff user
        $staff->roles()->attach($staffRole);
    }
}
