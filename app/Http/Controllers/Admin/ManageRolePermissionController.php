<?php

namespace App\Http\Controllers\Admin;

use App\Helper\Reply;
use App\Http\Requests\StoreRole;
use App\Http\Requests\StoreUserRole;
use App\Module;
use App\Permission;
use App\PermissionRole;
use App\Role;
use App\RoleUser;
use App\User;
use Illuminate\Http\Request;
use DB;

class ManageRolePermissionController extends AdminBaseController {

    public function __construct() {
        parent::__construct();
        $this->pageTitle = __('Roles & Permissions');
        $this->pageIcon = 'ti-lock';
    }

    public function index() {
        abort_if(!$this->user->hasrole('superadmin'), 403);
        $this->roles = Role::where('id', '>', 1)->get();
        $this->totalPermissions = Permission::count();
        $this->modules = Module::where('is_superadmin', '<', 1)->get();
        return view('admin.role-permission.index', $this->data);
    }

    public function store(Request $request) {
        abort_if(!$this->user->hasrole('superadmin'), 403);
        $roleId = $request->roleId;
        $permissionId = $request->permissionId;

        if ($request->assignPermission == 'yes') {
            PermissionRole::firstOrCreate([
                'permission_id' => $permissionId,
                'role_id' => $roleId
            ]);
        } else {
            /* if ($permissionId == 25){
              $ids = Permission::where("module_id",">","100")->get();
              PermissionRole::where('role_id', $roleId)->whereIn('permission_id', $ids)->delete();
              } */
            PermissionRole::where('role_id', $roleId)->where('permission_id', $permissionId)->delete();
        }

        return Reply::dataOnly(['status' => 'success']);
    }

    public function assignAllPermission(Request $request) {
        abort_if(!$this->user->hasrole('superadmin'), 403);
        DB::beginTransaction();

        try {
            $roleId = $request->roleId;
            $group = $request->group;

            $role = Role::findOrFail($roleId);

            if ($group == 'admin_roles') {
                $permissions = Permission::where("module_id", "<", 100);
                $role->permissions()->whereHas('permission', function($query) {
                    $query->where('permissions.module_id', "<", 100);
                })->delete();
            } else if ($group == 'order_roles'){ // orders roles
                $permissions = Permission::where("module_id", ">=", 100)->where("module_id", "<", 999);
                $role->permissions()->whereHas('permission', function($query) {
                    $query->where('permissions.module_id', ">=", 100)->where("module_id", "<", 999);
                })->delete();
            } else if ($group == 'ticket_roles'){ // tickets roles
                $permissions = Permission::where("module_id", ">=", 1000)->where("module_id", ">", 100);
                $role->permissions()->whereHas('permission', function($query) {
                    $query->where('permissions.module_id', ">=", 1000)->where("module_id", ">", 100);
                })->delete();
            }
            if ($request->status == 'true') {
                $role->attachPermissions($permissions->get());
            } else {
                $role->detachPermissions($permissions->get());
            }

            DB::commit();
            return Reply::dataOnly(['status' => 'success']);
        } catch (Exception $e) {
            DB::rollback();
            return Reply::error($e->getMessage(), $e->getCode());
        }
    }

    public function removeAllPermission(Request $request) {
        abort_if(!$this->user->hasrole('superadmin'), 403);
        $roleId = $request->roleId;

        $role = Role::findOrFail($roleId);
        $group = $request->group;
        return Reply::success($group);
        if ($group == 'admin_roles')
            $role->perms()->where("module_id", "<", 100)->sync([]);
        else if ($group == 'orders_roles')// orders roles
            $role->perms()->where("module_id", ">=", 100)->where("module_id", "<", 999)->sync([]);
        else if ($group == 'ticket_roles')// tickets roles
            $role->perms()->where("module_id", ">=", 1000)->where("module_id", ">", 100)->sync([]);

        return Reply::dataOnly(['status' => 'success']);
    }

    public function showMembers($id) {
        abort_if(!$this->user->hasrole('superadmin'), 403);
        $this->role = Role::find($id);
        $this->employees = User::doesntHave('role', 'and', function ($query) use ($id) {
                    $query->where('role_user.role_id', $id);
                })
                ->join('role_user', 'role_user.user_id', '=', 'users.id')
                ->join('roles', 'roles.id', '=', 'role_user.role_id')
                ->select('users.id', 'users.name', 'users.email', 'users.created_at')
                ->distinct('users.id')
                ->get();

        return view('admin.role-permission.members', $this->data);
    }

    public function storeRole(StoreRole $request) {
        abort_if(!$this->user->hasrole('superadmin'), 403);
        $roleUser = new Role();
        $roleUser->name = $request->name;
        $roleUser->display_name = ucwords($request->name);
        $roleUser->save();
        return Reply::success(__('messages.roleCreated'));
    }

    public function update(StoreRole $request, $id) {
        abort_if(!$this->user->hasrole('superadmin'), 403);
        $roleUser = Role::findOrFail($id);
        $roleUser->name = $request->name;
        $roleUser->display_name = ucwords($request->name);
        $roleUser->save();
        return Reply::success(__('messages.roleUpdated'));
    }

    public function assignRole(StoreUserRole $request) {
        abort_if(!$this->user->hasrole('superadmin'), 403);
        foreach ($request->user_id as $user) {
            $roleUser = new RoleUser();
            $roleUser->user_id = $user;
            $roleUser->role_id = $request->role_id;
            $roleUser->save();
        }
        return Reply::success(__('messages.roleAssigned'));
    }

    public function addPermessions() {
        foreach (\App\Module::where('is_superadmin', '=', 0)->where('id', '>=', 100)->get() as $m) {
            $p = explode(",", $m->permissions_name);
            for ($i = 0; $i < count($p); $i++) {
                $per = new \App\Permission;
                $per->name = $p[$i] . '_' . strtolower(str_replace(' ', '_', $m->module_name));
                $per->module_id = $m->id;
                $per->save();
            }
        }
    }

    public function detachRole(Request $request) {
        abort_if(!$this->user->hasrole('superadmin'), 403);
        $user = User::find($request->userId);
        $user->detachRole($request->roleId);
        return Reply::dataOnly(['status' => 'success']);
    }

    public function deleteRole(Request $request) {
        abort_if(!$this->user->hasrole('superadmin'), 403);
        Role::whereId($request->roleId)->delete();
        return Reply::dataOnly(['status' => 'success']);
    }

    public function create() {
        abort_if(!$this->user->hasrole('superadmin'), 403);
        $this->roles = Role::all();
        return view('admin.role-permission.create', $this->data);
    }

    public function edit($id) {
        abort_if(!$this->user->hasrole('superadmin'), 403);
        $this->role = Role::findOrFail($id);
        return view('admin.role-permission.edit', $this->data);
    }

}
