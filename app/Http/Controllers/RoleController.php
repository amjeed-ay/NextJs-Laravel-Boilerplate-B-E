<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoleRequest;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:create_role', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit_role', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete_role', ['only' => ['destroy']]);
    }

    public function index()
    {
        $data = auth()->user()->isSuperAdmin ? Role::all() : Role::whereNotIn('name',['Superadmin'])->get();

        return $data;
    }

    public function permissions()
    {

    //   Group permissions by model

       $permissions = Permission::all('id', 'name', 'title', 'model')->groupBy('model');
       $grouped = [];
       foreach ($permissions as $key => $value) {
            $grouped[] = [
                'title' => $key,
                'id' => $key,
                'model' => $key,
                'children' => $value,
            ];
       }
        return $grouped;
    }

    public function store(RoleRequest $request)
    {
        $role = Role::create(['name' => $request->name, 'guard_name' => 'web']);
        $role->syncPermissions($request->permissions);

        return response()->noContent();
    }

    public function show(Role $role)
    {
        $permissions = [];
        foreach ($role->permissions as  $value) {
            $permissions[] = $value->id;
        }

        $response = [
            'id' => $role->id,
            'name' => $role->name,
            'permissions' => $permissions,
        ];

        return $response;
    }

    public function update(RoleRequest $request, Role $role)
    {
        $role->update(['name' => $request->name]);
        $role->syncPermissions($request->permissions);

        return response()->noContent();
    }

    public function destroy(Role $role)
    {
        $role->delete();

        return response()->noContent();
    }
}
