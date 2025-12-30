<?php

namespace App\Http\Controllers\Admin;

use App\Hospitals;
use Illuminate\Http\Request;
use App\Helper\Reply;
use App\Helper\Files;
use Yajra\DataTables\Facades\DataTables;

class HospitalsController extends AdminBaseController {

    public function __construct() {
        parent::__construct();
        $this->pageTitle = lang('Sites');
        $this->pageIcon = 'fa fa-hospital-o';
    }

    public function index() {
        abort_if(!$this->user->can('view_hospital'), 403);
        
        $this->companies = \App\Company::get();

        return view('admin.hospital.index', $this->data);
    }

    public function data() {
        abort_if(!$this->user->can('view_hospital'), 403);

        $hospitals = Hospitals::join("companies", "companies.id", "hospitals.company_id")
                ->select('hospitals.*','company_name');
        
        if (auth()->user()->is_staff){
            $hospitals->where("hospitals.id", auth()->user()->is_staff->hospital_id);
        }
        
       // \Log::info("s"+request()->company_id);
        
      //  return "s"+request()->company_id;
        
        $this->filter($hospitals, request());

        return DataTables::of($hospitals)
                        ->addColumn('action', function ($row) {
                            $action = '';

                            if ($this->user->can('edit_hospital')) {
                                $action .= '<a href="#" onclick="showModal(\'' . route('admin.hospital.edit', [$row->id]) . '\')" class="notmodal-link btn btn-primary btn-circle"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
                            }
                            return $action;
                        })
                        ->addColumn('address_full', function ($row) {
                            return $row->address . ', ' . $row->city . ', ' . $row->state . ' ' . $row->zip;
                        })
                        ->editColumn('name', function ($row) {
                            return ucwords($row->name);
                        })
                        ->editColumn('type', function ($row) {
                            if ($row->type === 'mob')
                                return lang('MOB');
                            else 
                                return lang('Main');
                        })
                        ->rawColumns(['action','name'])
                        ->addIndexColumn('id')
                        ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        abort_if(!$this->user->can('add_hospital'), 403);
        
        $this->companies = \App\Company::get();

        return view('admin.hospital.create', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(\App\Http\Requests\StoreHospital $request) {
        abort_if(!$this->user->can('add_hospital'), 403);
        
        $hospital = new Hospitals();
        $hospital->name = $request->name;
        $hospital->phone = $request->phone;
        $hospital->address = $request->address;
        $hospital->city = $request->city;
        $hospital->state = $request->state;
        $hospital->zip = $request->zip;
        $hospital->entity_no = $request->entity_no;
        $hospital->notes = $request->notes;
        $hospital->website = $request->website;
        $hospital->type = $request->type;
        $hospital->company_id = $request->company_id;
        if ($request->hasFile('image')) {
            $hospital->image = Files::upload($request->image, 'hospitals', 400, 400, false);
        }
        
        $hospital->save();
        
        return Reply::successWithData('Site Added Successfully', ["id" => $hospital->id, 'text' => ucfirst($hospital->name)]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Hospitals  $hospitals
     * @return \Illuminate\Http\Response
     */
    public function show(Hospitals $hospitals) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Hospitals  $hospitals
     * @return \Illuminate\Http\Response
     */
    public function edit(Hospitals $hospital) {
        abort_if(!$this->user->can('edit_hospital'), 403);
        
        $this->hospital = $hospital;
        $this->companies = \App\Company::get();
        
        return view('admin.hospital.edit', $this->data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Hospitals  $hospitals
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Hospitals $hospital) {
        abort_if(!$this->user->can('edit_hospital'), 403);
        
        $hospital->name = $request->name;
        $hospital->phone = $request->phone;
        $hospital->address = $request->address;
        $hospital->city = $request->city;
        $hospital->state = $request->state;
        $hospital->zip = $request->zip;
        $hospital->entity_no = $request->entity_no;
        $hospital->notes = $request->notes;
        $hospital->website = $request->website;
        $hospital->type = $request->type;
        $hospital->company_id = $request->company_id;
        if ($request->hasFile('image')) {
            $hospital->image = Files::upload($request->image, 'hospitals', 400, 400, false);
        }
        
        $hospital->save();
        
        return Reply::success('Site Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Hospitals  $hospitals
     * @return \Illuminate\Http\Response
     */
    public function destroy(Hospitals $hospitals) {
        //
    }

}
