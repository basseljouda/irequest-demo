<?php

namespace App\Http\Controllers\Admin;

use App\Helper\Reply;
use App\HospitalsStuff;
use App\User;
use Illuminate\Http\Request;
use \App\Http\Requests\StoreStaff;
use DB;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;

class HospitalsStaffController extends AdminBaseController {

    public function __construct() {
        parent::__construct();
        $this->pageTitle = 'Site Staff';
        $this->pageIcon = 'fa fa-user-md';
    }

    public function index() {
        abort_if(!$this->user->can('view_hospital_staff'), 403);

        return view('admin.hospital-staff.index', $this->data);
    }

    public function create() {
        abort_if(!$this->user->can('add_hospital_staff'), 403);

        $this->hospitals = \App\Hospitals::all();
        $this->titles = \App\StaffTitle::all();

        return view('admin.hospital-staff.create', $this->data);
    }

    public function store(StoreStaff $request) {
        abort_if(!$this->user->can('add_hospital_staff'), 403);

        $hospital = $request->hospital;

        DB::beginTransaction();

        try {

            $user = \App\HospitalsStuff::create(['email' => $request->email,
                        'phone' => $request->mobile,
                        'firstname' => $request->firstname,
                        'lastname' => $request->lastname,
                        'hospital_id' => $hospital,
                        'supervisor_name' => $request->supervisor_name,
                        'supervisor_email' => $request->supervisor_email,
                        'title_id' => $request->title
            ]);

            if ($request->createuser == "on") {
                $pwd = str_random(8);
                $newuser = User::create([
                            'name' => $user->firstname . ' ' . $user->lastname,
                            'email' => $user->email,
                            'mobile' => $user->phone,
                            'status' => '1',
                            'password' => Hash::make($pwd),
                ]);

                $newuser->roles()->attach(5); // hospital staff role

                $user->user_id = $newuser->id;

                $newuser->notify(new \App\Notifications\NewUser($pwd));
                
                $user->login_status = 1;

                $user->save();
            }
            DB::commit();

            return Reply::successWithData('Staff Member Added Successfully',
                            ["id" => $user->id, 'text' => ucfirst($user->firstname . ' ' . $user->lastname)]);
        } catch (Exception $ex) {
            DB::rollback();
            return Reply::error($ex->getMessage(), $ex->getCode());
        }
    }

    public function edit($id) {
        abort_if(!$this->user->can('edit_hospital_staff'), 403);

        $this->staff = HospitalsStuff::findorfail($id);

        $this->hospitals = \App\Hospitals::all();
        $this->titles = \App\StaffTitle::all();


        return view('admin.hospital-staff.edit', $this->data);
    }

    public function update(StoreStaff $request, $id) {
        abort_if(!$this->user->can('edit_hospital_staff'), 403);

        $staff = HospitalsStuff::findorfail($id);

        $staff->update(['email' => $request->email,
            'phone' => $request->mobile,
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'hospital_id' => $request->hospital,
            'supervisor_name' => $request->supervisor_name,
            'supervisor_email' => $request->supervisor_email,
            'title_id' => $request->title
        ]);
        return Reply::success('Staff Member Updated Successfully');
    }

    public function data() {
        abort_if(!$this->user->can('view_hospital_staff'), 403);

        $staff = \App\HospitalsStuff::join("hospitals", "hospitals.id", "hospital_id")
                ->join("companies", "companies.id", "hospitals.company_id")
                ->select("hospitals.name","hospitals_stuff.*",'company_name')
                
                ->where("login_status", ">=", 0);
        
        if (auth()->user()->is_staff){
            $staff->where("hospital_id", auth()->user()->is_staff->hospital_id);
        }

        return DataTables::of($staff)
                        ->addColumn('action', function ($row) {
                            $action = '';

                            if ($this->user->can('edit_hospital_staff')) {
                                $action .= '<a href="#" onclick="showModal(\'' . route('admin.hospital-staff.edit', [$row->id]) . '\',\'#staff-modal\')" class="notmodal-link btn btn-primary btn-circle"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
                            }

                            if ($this->user->can('delete_hospital_staff') && $row->login_status == 0) {
                                $action .= ' <a href="javascript:;" class="btn btn-danger btn-circle sa-params"
                                data-toggle="tooltip" data-row-id="' . $row->id . '" data-original-title="' . __('app.delete') . '"><i class="fa fa-times" aria-hidden="true"></i></a>';
                            }
                            return $action;
                        })
                        
                       
                        ->editColumn('email', function ($row) {
                            if (!isset($row->user))
                                return $row->email;
                            else
                                return "<span class='text-primary'>" . $row->email . '</span>';
                        })
                        ->editColumn('login_status', function ($row) {
                            if ($this->user->can('add_hospital_staff')){
                            $accept = ' <a href="javascript:;" class="btn btn-default sa-accept"
                      data-toggle="tooltip" data-row-id="' . $row->id . '" data-original-title="">Create User</a>';
                            return ($row->login_status == 1) ? '<strong class="text-success" style="margin-left:10px">Ok</strong>' : $accept;
                            }else{
                                return '';
                            }
                        })
                        ->editColumn('firstname', function ($row) {
                            return ucwords($row->firstname . ' ' . $row->lastname);
                        })
                        ->rawColumns(['action', 'login_status', 'user', 'email'])
                        ->addIndexColumn('hospitals_stuff.id')
                        ->make(true);
    }

    public function destroy($id) {
        abort_if(!$this->user->can('delete_hospital_staff'), 403);

        DB::beginTransaction();

        try {

            $staff = \App\HospitalsStuff::findorFail($id);
            if ($staff->user()) {
                $staff->user()->delete();
            }
            $staff->login_status = -1; // deleted
            $staff->save();

            DB::commit();

            return Reply::success(__('messages.recordDeleted'));
        } catch (Exception $e) {
            DB::rollback();

            return Reply::error($e->getMessage(), $e->getCode());
        }
    }

    public function loginStatus(Request $request) {
        abort_if(!$this->user->can('edit_hospital_staff'), 403);
        
        DB::beginTransaction();
        $usercreated = '';
        try {
            $user = \App\HospitalsStuff::findorFail($request->id);
            if ($request->createuser == "true") {
                abort_if($user->email=='', 403,'Email is required to create a User');
                $user->login_status = 1;
                $pwd = str_random(8);
                $newuser = User::create([
                            'name' => $user->firstname . ' ' . $user->lastname,
                            'email' => $user->email,
                            'mobile' => $user->phone,
                            'status' => '1',
                            'password' => Hash::make($pwd),
                ]);
                $newuser->roles()->attach(5); // hospital staff role
                $user->user_id = $newuser->id;
                
                $usercreated = 'New User created, an email have been sent to his inbox ';

                $newuser->notify(new \App\Notifications\NewUser($pwd));
                $user->save();
            } else {
                abort_if($user->email=='', 403,'You have to click Create User checkbox');
            }
            DB::commit();

            return Reply::success($usercreated);
        } catch (Exception $e) {
            DB::rollback();
            return Reply::error($e->getMessage(), $e->getCode());
        }
    }

}
