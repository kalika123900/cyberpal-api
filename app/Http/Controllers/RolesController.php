<?php

namespace App\Http\Controllers\dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
// use App\Http\Requests\StorePermissionRequest;
// use App\Http\Requests\StoreRoleRequest;
use App\User;

class RolesController extends Controller
{
    // - Get All Roles & Permissions
    public function fetchAllRoles () {
        try {
            $roles = $this->getAllRoles();
            $permissions = $this->getAllPermissions();
            
            return new ReviewsResource([
                'roles' => $roles,
                'permissions' => $permissions
            ]);
        } catch (\Exception $e) {
            return new ReviewsResource([
                'error' => $e
            ]);
        }
    }
    
    // - Create New Role.
    public function createNewRole (Request $request) {
        try {
            $role = Role::create([
                'name' => $request->name
            ]);
            $role->syncPermissions($request->permissions);
                        
            return new ReviewsResource([
                'status' => 200,
                'permission' => $role
            ]);
        } catch (\Exception $e) {
            return new ReviewsResource([
                'error' => $e
            ]); 
        }
    }

    // - Create New Permission.
    public function createNewPermission (Request $request) {
        try {
            $permission = Permission::create([
                'name' => $request->name
            ]);
            
            return new ReviewsResource([
                'status' => 200,
                'permission' => $permission
            ]);
        } catch (\Exception $e) {
            return new ReviewsResource([
                'error' => $e
            ]); 
        }
    }

        // - Return all Roles
    public function getAllRoles() {
        $roles = Role::all();
        return $roles;
    }
     
    // - Give User a Role.
    public function giveUserARole(Request $request) {
        // - Validations [*]
        $user = User::where('email', $request->email)->first();
        $user->assignRole($request->role);
        return $user;
    }

    // - Remove Role from User.
    public function removeUserRole(Request $request) {
        // - Validations [*]
        $user = User::where('email', $request->email)->first();
        $user->removeRole($request->role);
        return $user;
    }

    // - Return Users with a Role.
    public function getUsersWithRole(Request $request) {
        // - Validations [*] 
        $users = User::role($request->name)->get();
        return $users;
    }

    // - Give Permissions to Roles.
    public function givePermissionsToRole(Request $request) {
        // - Validations [*]         
        $role = Role::findByName($request->role);
        $role->givePermissionTo($request->permissions);
        return $role;
    }
    
    // - Revoke Permission to Roles.
    public function revokePermissionToRoles(Request $request) {
        // - Validations [*]         
        $role = Role::findByName($request->role);
        $role->revokePermissionTo($request->permission);
        return $role;
    }

    // - Sync Permissions to Roles.
    public function changePermissionsToRoles(Request $request) {
        // - Validations [*]         
        $role = Role::findByName($request->role);
        $role->syncPermissions($request->permissions); // - permissions should be array
        return $role;
    }

     // - Return all Permissions
    public function getAllPermissions() {
        $permissions = Permission::all();
        return $permissions;
    }

    // - Return users with permissions.
    public function getUsersWithPermissions(Request $request) {
        // - Validations [*] 
        $users = User::permission($request->name)->get();
        return $users;
    }

    // - Revoke Permission to User.
    public function revokePermissionToUser(Request $request) {
        // - Validations [*]         
        $user = User::where('email', $request->email)->first();
        $user->revokePermissionTo($request->permission);
        return $user;
    }

    // // - Sync Permissions to User.
    // public function changePermissionsToRoles(Request $request) {
    //     // - Validations [*]         
    //     $user = User::where('email', $request->role)->first();
    //     $user->syncPermissions($request->permissions); // - permissions should be array
    //     return $user;
    // }
}
