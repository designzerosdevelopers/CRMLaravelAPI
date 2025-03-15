<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

use Illuminate\Support\Facades\Log;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource: Select a user.
     */
    public function userSelect(Request $request)
    {
        if (auth()->user()->hasRole('super-admin')) {
            // Also fetch the 'name' column
            $users = User::select('id as user_id', 'name', 'email')->get();
        } else {
            // Retrieve employees with 'employee' role and specific creator_id
            // and include 'users.name' & 'users.email' in the selection
            $users = User::role('employee')
                ->join('employees', 'users.id', '=', 'employees.user_id')
                ->where('creator_id', auth()->user()->id)
                ->select(
                    'users.id as user_id',
                    'users.name',
                    'users.email',
                    'employees.*' // includes all employee columns
                )
                ->get();
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data'    => $users,
            ], 200);
        }
        return view('pages.controlpanel.permission.selectUser', compact('users'));
    }

    /**
     * Display a listing of the resource: Select a role.
     */
    public function roleSelect(Request $request)
    {
        if (auth()->user()->hasRole('super-admin')) {
            $roles = Role::all();
        } else {
            $roles = Role::where('name', 'employee')->get();
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data'    => $roles,
            ], 200);
        }
        return view('pages.controlpanel.permission.selectRole', compact('roles'));
    }

    /**
     * Show the form for setting a user's permissions.
     */
    public function userPermission(Request $request)
    {
        log::info('received data' ,$request->all());
        // Check if neither user_id nor user_email is provided.
        if (empty($request->input('user_id')) && empty($request->input('user_email'))) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }
            return redirect()->back()->with('error', 'User not found');
        }

        // Try to find the user by ID if provided, otherwise by email.
        $user = !empty($request->input('user_id'))
            ? User::find($request->input('user_id'))
            : User::where('email', $request->input('user_email'))->first();

        if (!$user) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }
            return redirect()->back()->with('error', 'User not found');
        }

        $allpermission = Permission::all();
        $permissions = $user->permissions;

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data'    => [
                    'user'         => $user,
                    'permissions'  => $permissions,
                    'allpermission'=> $allpermission,
                ]
            ], 200);
        }
        return view('pages.controlpanel.permission.userPermission', compact('user', 'permissions', 'allpermission'));
    }


    /**
     * Show the form for setting a role's permissions.
     */
    public function rolePermission(Request $request)
    {
        $allpermission = Permission::all();
        $role = Role::find($request->role_id);
        $permissions = $role ? $role->permissions : null;

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data'    => [
                    'role'         => $role,
                    'permissions'  => $permissions,
                    'allpermission'=> $allpermission,
                ]
            ], 200);
        }
        return view('pages.controlpanel.permission.rolePermission', compact('role', 'permissions', 'allpermission'));
    }

    /**
     * Update a user's permissions.
     */
    public function userPermissionSet(Request $request, $id)
    {
        // dd($request->all());
        log::info('setting permission' , $request->all());
        $request->validate([
            'user_permissions' => 'array',
        ]);

        $user = User::find($id);
        log::info('setting permission for user' ,['user' => $user]);

        if (!$user) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }
            return redirect()->back()->with('error', 'User not found');
        }

        $user->permissions()->sync($request->user_permissions);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Permissions updated successfully'
            ], 200);
        }
        return redirect()->route('user-select')->with('success', 'Permissions updated successfully');
    }

    /**
     * Update a role's permissions.
     */
    public function rolePermissionSet(Request $request, $id)
    {
        $request->validate([
            'role_permissions' => 'array',
        ]);

        $role = Role::find($id);
        if (!$role) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Role not found'
                ], 404);
            }
            return redirect()->back()->with('error', 'Role not found');
        }

        $role->permissions()->sync($request->role_permissions);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Permissions updated successfully'
            ], 200);
        }
        return redirect()->route('role-select')->with('success', 'Permissions updated successfully');
    }

    /**
     * Update a user's permission by adding or removing a single permission.
     */
    public function update(Request $request)
    {
        $request->validate([
            'user_id'    => 'required',
            'permission' => 'required',
            'action'     => 'required', // 'add' or 'remove'
        ]);

        $user = User::find($request->input('user_id'));
        if (!$user) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found.'
                ], 404);
            }
            return redirect()->route('permissions.index')->with('error', 'User not found.');
        }

        $permissionName = $request->input('permission');
        $permission = Permission::where('name', $permissionName)->first();

        if (!$permission) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Permission not found.'
                ], 404);
            }
            return redirect()->route('permissions.index')->with('error', 'Permission not found.');
        }

        if ($request->input('action') == 'add') {
            $user->givePermissionTo($permission);
        } elseif ($request->input('action') == 'remove') {
            $user->revokePermissionTo($permission);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Permissions updated successfully.'
            ], 200);
        }
        return redirect()->route('permission.index')->with('success', 'Permissions updated successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Not implemented
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Not implemented
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Not implemented
    }
}
