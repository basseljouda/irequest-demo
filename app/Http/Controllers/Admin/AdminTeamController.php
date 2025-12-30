<?php

namespace App\Http\Controllers\Admin;

use App\EmailSetting;
use App\Helper\Files;
use App\Helper\Reply;
use App\Http\Requests\StoreTeam;
use App\Http\Requests\UpdateTeam;
use App\Notifications\NewUser;
use App\Role;
use App\RoleUser;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;

class AdminTeamController extends AdminBaseController {

    public function __construct() {
        parent::__construct();
        $this->pageTitle = __('Manage Users');
        $this->pageIcon = 'fa fa-users';
    }

    public function index() {
        abort_if(!$this->user->can('view_user'), 403);

        $this->users = User::all();
        return view('admin.team.index', $this->data);
    }

    public function create() {
        abort_if(!$this->user->can('add_user'), 403);

        $this->roles = Role::all();
        
        $this->sites = \App\Hospitals::get();
        $this->companies = \App\Company::get();
        $this->regions = \App\Hospitals::distinct()->get(['city']);
        
        return view('admin.team.create', $this->data);
    }

    public function store(StoreTeam $request) {
        abort_if(!$this->user->can('add_user'), 403);

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $pwd = Hash::make($request->password);
        $user->password = $pwd;
        //$user->password = Hash::make($pwd);
        $user->calling_code = $request->calling_code;
        $user->mobile = $request->mobile;
        $user->status = 1;

        if ($request->hasFile('image')) {
            $user->image = Files::upload($request->image, 'profile');
        }
        $user->sites = $request->sites;
        $user->idns = $request->idns;
        $user->regions = $request->regions;
        $user->save();

        if ($request->role_id > 0) {
            //attach role
            $user->roles()->attach($request->role_id);
        }

        //send notification
        $user->notify(new NewUser($request->password));

        return Reply::redirect(route('admin.team.index'), __('menu.team') . ' ' . __('messages.createdSuccessfully'));
    }

    public function edit($id) {
        abort_if(!$this->user->can('edit_user'), 403);

        if ($id == $this->user->id) {
            abort(403);
        }

        $this->roles = Role::all();
        $this->team = User::find($id);
        $this->sites = \App\Hospitals::get();
        $this->companies = \App\Company::get();
        $this->regions = \App\Hospitals::distinct()->get(['city']);
        $this->userSite = 0;
        if ($this->team->is_staff){
            $this->userSite = $this->team->is_staff->hospital_id;
        }
        
        return view('admin.team.edit', $this->data);
    }

    public function update(UpdateTeam $request, $id) {
        abort_if(!$this->user->can('edit_user'), 403);

        if ($id == $this->user->id) {
            abort(403);
        }

        $user = User::find($id);
        $user->name = $request->name;
        $user->email = $request->email;

         if($request->password != ''){
          $user->password = Hash::make($request->password);
          }

        if (($request->mobile != $user->mobile || $request->calling_code != $user->calling_code) && $user->mobile_verified == 1) {
            $user->mobile_verified = 0;
        }

        $user->mobile = $request->mobile;
        $user->calling_code = $request->calling_code;

        if ($request->hasFile('image')) {
            $user->image = Files::upload($request->image, 'profile');
        }
        $user->sites = $request->sites;
        $user->idns = $request->idns;
        $user->regions = $request->regions;
        $user->save();

        //attach role
        RoleUser::where('user_id', $id)->delete();
        if ($request->role_id > 0) {
            $user->roles()->attach($request->role_id);
        }
        
        return Reply::redirect(route('admin.team.index'), __('menu.team') . ' ' . __('messages.updatedSuccessfully'));
    }

    public function data() {
        abort_if(!$this->user->can('view_user'), 403);

        $users = User::select('*')->where('status',1);
        $roles = Role::all();

        return DataTables::of($users)
                        ->addColumn('action', function ($row) {
                            $action = '';

                            //do not allow user to change own details
                            if ($row->id == $this->user->id) {
                                return $action;
                            }

                            if ($this->user->can('edit_user')) {
                                $action .= '<a href="' . route('admin.team.edit', [$row->id]) . '" class="btn btn-primary btn-circle"
                      data-toggle="" title="' . __('app.edit') . '">Edit</a>';
                            }

                            if ($this->user->can('reset_user')) {
                                $action .= ' <a href="javascript:;" class="btn btn-warning reset-pass btn-circle"
                              data-toggle="" data-value="' . $row->email . '" data-row-id="' . $row->id . '" title="Reset User Password">Reset</a>';
                            }
                            if ($this->user->can('delete_user')) {
                                $action .= ' <a href="javascript:;" class="btn btn-danger btn-circle sa-params"
                       data-row-id="' . $row->id . '" title="Delete User">Del!</a>';
                            }
                            return $action;
                        })
                        ->addColumn('role_name', function ($row) use ($roles) {

                            //do not allow user to change own role or a Superadmin
                            if ($row->id == $this->user->id || $row->id == 1) {
                                return $row->role->role->display_name;
                            }

                            if (!$this->user->can('edit_user')) {
                                return $row->role->role->display_name;
                            }
                            $selected = '';
                            $roleOption = '<select name="role_id" class="form-control role_id" data-row-id="' . $row->id . '">';
                            $roleOption .= '<option value="0">No role</option>';
                            foreach ($roles as $role) {
                                $roleOption .= '<option ';

                                if ($row->role && $row->role->role->id == $role->id) {
                                    $roleOption .= ' selected ';
                                    $selected = ucwords($role->display_name);
                                }

                                $roleOption .= 'value="' . $role->id . '">' . ucwords($role->display_name) . '</option>';
                            }
                            $roleOption .= '</select>';
                            if (\request()->input('length') == config('constant.data_max')) {
                                return $selected;
                            } else {
                                return $roleOption;
                            }
                        })
                        ->editColumn('name', function ($row) {
                            return '<div class="image-container"><div class="image"><img src=' . $row->profile_image_url . ' /></div>' . ucwords($row->name) . '</div>';
                        })
                        ->rawColumns(['name', 'action', 'role_name'])
                        ->addIndexColumn('id')
                        ->make(true);
    }

    public function destroy($id) {
        abort_if(!$this->user->can('delete_user'), 403);

        $user = User::findorfail($id);
        $user->status = 0;
        $user->save();
        /*$delete_staff=0;
        if ($user->is_staff != null) {
            $delete_staff = $user->is_staff->id;
            $exist = \App\Orders::where("staff_id", $delete_staff)
                            ->orwhere("staff_accepted", $delete_staff)->count();
            if ($exist > 0) {
                return Reply::error("Can not delete user, there are orders linked to this user");
            }
        }

        if ($user->orders->count() > 0) {
            return Reply::error("Can not delete user, there are orders created by this user");
        }
        
        if ($delete_staff > 0){
            \App\HospitalsStuff::destroy($delete_staff);
        }

        RoleUser::where('user_id', $id)->delete();
        
        User::destroy($id);*/

        return Reply::success(__('messages.recordDeleted'));
    }

    public function changeRole(Request $request) {
        //attach role
        $user = User::find($request->teamId);

        RoleUser::where('user_id', $request->teamId)->delete();
        if ($request->roleId > 0) {
            $user->roles()->attach($request->roleId);
        }

        return Reply::dataOnly(['status' => 'success']);
    }

}
