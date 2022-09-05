<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [

            'View Role',
            'Create Role',
            'Edit Role',
            'Delete Role',

            'View User',
            'Create User',
            'Edit User',
            'Delete User',

        ];

        $data = [];

        foreach ($permissions as $permission) {
            $data[] = [
                'name' => strtolower(str_replace(" ", "_", $permission)),
                'title' => $permission,
                'model' => explode(" ", $permission)[1],
                'guard_name'=>'web'
            ];
        }


        Permission::insert($data);


        $admin_excluded_permissions = ['view_role', 'create_role', 'edit_role', 'delete_role'];

        $roles = ['Root Admin', 'General Admin', 'Admin'];

        foreach ($roles as  $role) {
            $new_role = Role::create(['name' => $role]);

            if ($role == 'Admin') {
                $new_role->syncPermissions(Permission::whereNotIn('name', $admin_excluded_permissions)->pluck('id', 'id'));
            } else {
                $new_role->syncPermissions(Permission::pluck('id', 'id')->all());
            }
        }
    }
}
