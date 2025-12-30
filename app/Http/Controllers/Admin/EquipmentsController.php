<?php

namespace App\Http\Controllers\Admin;

use App\Equipments;
use App\Helper\Files;
use App\Helper\Reply;
use Illuminate\Http\Request;
use DB;
use App\Http\Requests\StoreEquipment;
use Yajra\DataTables\Facades\DataTables;

class EquipmentsController extends AdminBaseController
{
    public function __construct() {
        parent::__construct();
        $this->pageTitle = 'Equipments';
        $this->pageIcon = 'ti-hummer';
    }

    public function index() {
        abort_if(!$this->user->can('view_equipment'), 403);
        $this->companies = \App\Company::get();
        return view('admin.equipment.index', $this->data);
    }
    
    public function data() {
        abort_if(!$this->user->can('view_equipment'), 403);

        /*$items = \App\Equipments::select('equipments.*','modality.name as mname','sub_modality.name as smname','equipment_manufacturers.name as manfname')
                ->join("modality","modality.id","=","equipments.modality_id")
                ->join("sub_modality","sub_modality.id","=","equipments.sub_modality_id")
                ->join("equipment_manufacturers","equipment_manufacturers.id","=","equipments.manufacturer_id");*/
        
        $combo = \App\Equipments::select('equipments.*','company_name')
                ->join("companies", "companies.id", "equipments.company_id");
        
        if (auth()->user()->is_staff){
            $combo->where("company_id",auth()->user()->is_staff->hospital->company_id);
        }
        
        $this->filter($combo, request());
        
        return DataTables::of($combo)
                        ->addColumn('action', function ($row) {
                            $action = '';
                            if ($this->user->can('edit_equipment')) {
                                $action .= '<a href="#" onclick="showModal(\'' . route('admin.equipment.edit', [$row->id]) . '\')" class="notmodal-link btn btn-primary btn-circle"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
                            }
                            return $action;
                        })
                        ->editColumn('active', function ($row) {
                            if ($this->user->can('edit_equipment')) {
                                $checked = ($row->active == 1) ? 'checked' : '';
                                $active = '<div class="switchery-div">
                                         <input ' . $checked . ' id="nexmo_status" name="nexmo_status" type="checkbox"
                                           value="active" class="js-switch" data-id=' . $row->id . '
                                           data-color="#99d683" data-size="small" onchange="changeActiveStatus($(this))"/>
                                        </div>';
                                return $active;
                            } else
                                return ($row->active == 1) ? 'Yes' : 'No';
                        })
                        ->rawColumns(['action','active'])
                        ->addIndexColumn('id')
                        ->make(true);
    }
    
     public function changeActive() {
        abort_if(!$this->user->can('edit_cost_center'), 403);
        
        $costc = Equipments::findorfail(request('id'));
        $costc->active=request('active');
        $costc->save();
        return '';
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort_if(!$this->user->can('add_equipment'), 403);
        $this->companies = \App\Company::get();
        return view('admin.equipment.create', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreEquipment $request)
    {
        abort_if(!$this->user->can('add_equipment'), 403);
        
        $equipment = new Equipments();
        $equipment->company_id = $request->company_id;
        $equipment->name = $request->name;
        $equipment->model = $request->model;
        $equipment->price_day = $request->price_day;
        $equipment->refurbished_price = $request->refurbished_price;
        $equipment->oem_no = $request->oem_no;
        
        $equipment->save();
        
        return Reply::success('Equipment Added Successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Equipments  $equipments
     * @return \Illuminate\Http\Response
     */
    public function show(Equipments $equipments)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Equipments  $equipments
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        abort_if(!$this->user->can('edit_equipment'), 403);
        
        $this->equipment = Equipments::findorfail($id);
        $this->companies = \App\Company::get();
        
        return view('admin.equipment.edit', $this->data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Equipments  $equipments
     * @return \Illuminate\Http\Response
     */
    public function update(StoreEquipment $request, $id)
    {
        abort_if(!$this->user->can('edit_equipment'), 403);
        
        $equipment = Equipments::findorfail($id);
        $equipment->company_id = $request->company_id;
        $equipment->name = $request->name;
        $equipment->model = $request->model;
        $equipment->price_day = $request->price_day;
        $equipment->refurbished_price = $request->refurbished_price;
        $equipment->oem_no = $request->oem_no;
        
        $equipment->save();
        
        return Reply::success('Equipment Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Equipments  $equipments
     * @return \Illuminate\Http\Response
     */
    public function destroy(Equipments $equipments)
    {
        //
    }
}
