<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function userSelect()
    {
        if (auth()->user()->hasRole('super-admin')) {
            $users = User::get(['id as user_id', 'email']);

            return view('pages.controlpanel.permission.selectUser', compact('users'));
        } else {
            // Retrieve employees with 'employee' role and specific creator_id
            $users = User::role('employee')
                ->join('employees', 'users.id', '=', 'employees.user_id')
                ->where('creator_id', auth()->user()->id)
                ->get(['users.*', 'employees.*']);


            return view('pages.controlpanel.permission.selectUser', compact('users'));
        }
    }

    public function roleSelect()
    {

        if (auth()->user()->hasRole('super-admin')) {
            $roles = Role::all();
            return view('pages.controlpanel.permission.selectRole', compact('roles'));
        } else {

            $roles = Role::where('name', 'employee')->get();

            return view('pages.controlpanel.permission.selectRole', compact('roles'));
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function userPermission(Request $request)
    {

        if (empty($request->input('user_id') || empty($request->input('user_name')))) {
            return redirect()->back()->with('error', 'User not found');
        }

        $user = $request->user_id ? User::find($request->user_id) : User::where('email', $request->user_email)->first();


        if (!$user) {

            return redirect()->back()->with('error', 'User not found');
        }

        $allpermission = Permission::all();
        $user = User::find($user->id);
        $permissions = $user->permissions;

        return view('pages.controlpanel.permission.userPermission', compact('user', 'permissions', 'allpermission'));
    }

    public function rolePermission(Request $request)
    {

        $allpermission = Permission::all();
        $role = Role::find($request->role_id);
        $permissions = $role->permissions;

        return view('pages.controlpanel.permission.rolePermission', compact('role', 'permissions', 'allpermission'));
    }



    public function userPermissionSet(Request $request, $id)
    {

        $request->validate([
            'user_permissions' => 'array',
        ]);

        $user = User::find($id);

        $user->permissions()->sync($request->user_permissions);

        return redirect()->route('user-select')
            ->with('success', 'Permissions updated successfully');
    }

    public function rolePermissionSet(Request $request, $id)
    {

        $request->validate([
            'role_permissions' => 'array',
        ]);

        $role = Role::find($id);

        $role->permissions()->sync($request->role_permissions);

        return redirect()->route('role-select')
            ->with('success', 'Permissions updated successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {

        // Validate the form input
        $request->validate([
            'user_id' => 'required',
            'permission' => 'required',
            'action' => 'required', // 'add' or 'remove'
        ]);


        $user = User::find($request->input('user_id'));

        if (!$user) {
            return redirect()->route('permissions.index')->with('error', 'User not found.');
        }

        $permissionName = $request->input('permission');

        $permission = Permission::where('name', $permissionName)->first();

        if (!$permission) {
            // Handle the case where the permission doesn't exist
            return redirect()->route('permissions.index')->with('error', 'Permission not found.');
        }

        if ($request->input('action') == 'add') {
            $user->givePermissionTo($permission);
        } elseif ($request->input('action') == 'remove') {
            $user->revokePermissionTo($permission);
        }

        return redirect()->route('permission.index')->with('success', 'Permissions updated successfully.');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
