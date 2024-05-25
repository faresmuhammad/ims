<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sysAdmin = Role::create(['name' => 'System Admin']);
        $admin = Role::create(['name' => 'Admin']);
        $user = Role::create(['name' => 'User']);
        $permissions = [
            //categories
            'see all categories info' => [1, 2, 3],
            'add category' => [1, 2],
            'edit category' => [1, 2],
            'delete category' => [1, 2],
            //products
            'see all products' => [1, 2, 3],
            'add product' => [1, 2, 3],
            'delete product' => [1, 2, 3],
            'import products' => [1, 2, 3],
            'see product info' => [1, 2, 3],
            'edit product' => [1, 2, 3],
            'see product stocks' => [1, 2, 3],
            'edit stock' => [1, 2, 3],
            //suppliers
            'see all suppliers' => [1, 2, 3],
            'add supplier' => [1, 2, 3],
            'delete supplier' => [1, 2, 3],
            'edit supplier' => [1, 2, 3],
            //orders
            'make customer order' => [1, 2, 3],
            'make supplier order' => [1, 2, 3],
            'make return order' => [1, 2, 3],
            //settings
            'edit settings' => [1, 2],
            'add user' => [1, 2],
            'assign user permission' => [1, 2],
            'revoke user permission' => [1, 2],
            'delete user' => [1, 2],
            //statistics
            'see all statistics data' => [1, 2, 3],
            'filter statistics data' => [1, 2, 3],
            //shifts
            'start shift' => [1, 2, 3],
            'end shift' => [1, 2, 3],
            'set shift real amount' => [1, 2, 3],
            'see shift expected amount' => [1, 2],
            'see shift difference' => [1, 2],
            'edit shift date and time' => [1, 2],
        ];

        foreach ($permissions as $permission  => $roles){
            Permission::create(['name' => $permission]);
            foreach ($roles as $role){
                $role = Role::findById($role);
                $role->givePermissionTo($permission);
            }
        }
    }
}
