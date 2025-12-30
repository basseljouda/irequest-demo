<?php

namespace App\Http\Controllers\Admin;

use App\CostCenter;
use Illuminate\Http\Request;
use App\Helper\Reply;
use \App\Orders;
use Yajra\DataTables\Facades\DataTables;

class CostCenterController extends AdminBaseController {

    public function __construct() {
        parent::__construct();
        $this->pageTitle = 'Cost Centers';
        $this->pageIcon = 'ti-cloud-up';
    }

    public function index() {
        abort_if(!$this->user->can('view_cost_center'), 403);

        return view('admin.costcenter.index', $this->data);
    }

    public function data() {
        abort_if(!$this->user->can('view_cost_center'), 403);

        $costcenters = \App\CostCenter::leftjoinSub(Orders::costcenters(), 'orders', function ($join) {
                    $join->on('cost_centers.id', '=', 'orders.cost_center_id');
                });

        return DataTables::of($costcenters)
                        ->addColumn('action', function ($row) {
                            $action = '';
                            if ($this->user->can('edit_cost_center')) {
                                $action .= '<a href="#" onclick="showModal(\'' . route('admin.costcenter.edit', [$row->id]) . '\')" class="notmodal-link btn btn-primary btn-circle"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
                            }
                            return $action;
                        })
                        ->editColumn('total', function ($row) {
                            return '$' . number_format($row->total, 2);
                        })
                        ->editColumn('active', function ($row) {
                            if ($this->user->can('edit_cost_center')) {
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
                        ->rawColumns(['action', 'name', 'active'])
                        ->addIndexColumn('id')
                        ->make(true);
    }

    public function changeActive() {
        abort_if(!$this->user->can('edit_cost_center'), 403);
        
        $costc = CostCenter::findorfail(request('id'));
        $costc->active=request('active');
        $costc->save();
        return '';
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        abort_if(!$this->user->can('add_cost_center'), 403);

        //$this->hospitals = \App\Hospitals::all();

        return view('admin.costcenter.create', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        abort_if(!$this->user->can('add_cost_center'), 403);

        CostCenter::create($request->except('_method', '_token'));

        return Reply::success(__('messages.createdSuccessfully'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\CostCenter  $costCenter
     * @return \Illuminate\Http\Response
     */
    public function show(CostCenter $costCenter) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CostCenter  $costCenter
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        abort_if(!$this->user->can('edit_cost_center'), 403);

        //$this->hospitals = \App\Hospitals::all();

        $this->costcenter = CostCenter::findorfail($id);

        return view('admin.costcenter.edit', $this->data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\CostCenter  $costCenter
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        abort_if(!$this->user->can('edit_cost_center'), 403);

        $costcenter = CostCenter::findorfail($id);

        $costcenter->update($request->except('_method', '_token'));

        return Reply::success("Cost Center Updated Successfully");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CostCenter  $costCenter
     * @return \Illuminate\Http\Response
     */
    public function destroy(CostCenter $costCenter) {
        //
    }

}
