<?php

namespace App\Http\Controllers\Admin;

use App\Inventory;
use App\InventoryItems;
use Illuminate\Http\Request;
use App\Helper\Reply;
use Yajra\DataTables\Facades\DataTables;
use DB;

class InventoryController extends AdminBaseController {

    public function __construct() {
        parent::__construct();
        $this->pageTitle = 'Inventories';
        $this->pageIcon = 'fa fa-list-alt';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        
        return view('admin.inventory.index', $this->data);
    }

    public function data() {
        abort_if(!$this->user->can('view_inventory'), 403);

        $inventories = Inventory::select('*');

        return DataTables::of($inventories)
                        ->addColumn('details_url', function($row) {
                            return route('admin.inventory.items-data', $row->id);
                        })
                        ->addColumn('count', function ($row) {
                            return $row->items()->count();
                        })
                        ->editColumn('action', function ($row) {
                            $action = '';

                            if ($this->user->can('add_inventory_item')) {
                                $action .= '<a href="#" onclick="showModal(\'' . route('admin.inventory.item-add', [$row->id]) . '\',\'#application-lg-modal\')" class="btn btn-primary">Add Items</a> ';
                            }

                            if ($this->user->can('edit_inventory')) {
                                $action .= '<a href="#" onclick="showModal(\'' . route('admin.inventory.edit', [$row->id]) . '\')" class="btn btn-primary">Edit</a>';
                            }
                            return $action;
                        })
                        ->addColumn('address_full', function ($row) {
                            return $row->address . ', ' . $row->city . ', ' . $row->state . ' ' . $row->zip;
                        })
                        ->rawColumns(['action'])
                        ->addIndexColumn('id')
                        ->make(true);
    }

    public function getInventoryItems($id) {
        abort_if(!$this->user->can('view_inventory'), 403);

        $inventory_items = \App\InventoryItems::select("inventory_items.id", "equipments.name as name", "equipments.price_day as price", "balance", "serial_value", "dot_value")
                ->join("equipments", "equipments.id", "=", "inventory_items.equipment_id")
                ->where('inventory_items.inventory_id', $id);

        return DataTables::of($inventory_items)
                        ->addColumn('action', function ($row) {
                            $action = '';



                            return $action;
                        })
                        ->editColumn('balance', function ($row) {
                            if ($row->balance == 0) {
                                return "<span class='badge badge-danger' >No</span>";
                            } else {
                                return "<span class='badge badge-success' >Yes</span>";
                            }
                        })
                        /* ->editColumn('balance', function ($row) {
                          $action = '<div class="input-group" style="">
                          <span class="input-group-prepend">
                          <button type="button" class="btn btn-outline-secondary btn-number" data-type="minus" data-field="balance' . $row->id . '">
                          <span class="fa fa-minus"></span>
                          </button>
                          </span>
                          <input type="text" id="' . $row->id . '" name="update_balance[]" class="form-control input-number" value="' . $row->balance . '" data-oldValue="' . $row->balance . '" min="0" max="1000">
                          <input type="hidden" name="itemid[]" value="' . $row->id . '">
                          <span class="input-group-append">
                          <button type="button" class="btn btn-outline-secondary btn-number" data-type="plus" data-field="balance' . $row->id . '">
                          <span class="fa fa-plus"></span>
                          </button>
                          </span>
                          </div>';
                          return $action;
                          }) */
                        ->editColumn('name', function ($row) {
                            return $row->name;
                        })
                        /* ->editColumn('last_update', function ($row) {
                          return toCarbon($row->updated_at);
                          })
                          ->editColumn('updated_by', function ($row) {
                          return '';
                          }) */
                        ->rawColumns(['action', 'balance'])
                        ->addIndexColumn('inventory_items.id')
                        ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        abort_if(!$this->user->can('add_inventory'), 403);

        return view('admin.inventory.create', $this->data);
    }

    public function showAddItem($inventory) {
        abort_if(!$this->user->can('add_inventory_item'), 403);

        $this->inventory = Inventory::findorfail($inventory);
        $this->equipments = \App\Equipments::active()->get();
        //whereNotIn('id', DB::table('inventory_items')->select('equipment_id')->where('inventory_id', '=', $inventory)->pluck('equipment_id'))->get();

        return view('admin.inventory.new_item', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        abort_if(!$this->user->can('add_inventory'), 403);

        $inventory = new Inventory();
        $inventory->name = $request->name;
        $inventory->address = $request->address;
        $inventory->city = $request->city;
        $inventory->state = $request->state;
        $inventory->zip = $request->zip;
        $inventory->code = $request->code;
        $inventory->notes = $request->notes;

        $inventory->save();

        return Reply::successWithData('Inventory Added Successfully', ["id" => $inventory->id, 'text' => ucfirst($inventory->name)]);
    }

    public function storeNewItems(Request $request) {
        abort_if(!$this->user->can('add_inventory_item'), 403);

        DB::beginTransaction();

        try {
            if (count(array_unique($request->equipments)) < count($request->equipments)) {
                return Reply::error('You have Duplicated Equipments!');
            }
            $post_id = 1 + InventoryItems::max('post_id');
            for ($i = 0; $i < count($request->equipments); $i++) {
                if ($request->dot_value[$i] == "" && $request->serial_value[$i] == "") {
                    return Reply::error('You must enter either Serial number or DOT# number');
                }
                $inventory_item = new InventoryItems;
                $inventory_item->post_id = $post_id;
                $inventory_item->inventory_id = $request->inventory_id;
                $inventory_item->equipment_id = $request->equipments[$i];
                $inventory_item->balance = 1; //$request->item_balance[$i];
                $inventory_item->dot_value = $request->dot_value[$i];
                $inventory_item->serial_value = $request->serial_value[$i];
                $inventory_item->price = 0;
                $inventory_item->added_by = $this->user->id;
                $inventory_item->updated_by = $this->user->id;
                $inventory_item->save();
            }
            DB::commit();
            return Reply::success('Equipments Added to Inventory Successfully');
        } catch (Exception $e) {
            DB::rollback();
            return Reply::error($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Inventory  $inventory
     * @return \Illuminate\Http\Response
     */
    public function show(Inventory $inventory) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Inventory  $inventory
     * @return \Illuminate\Http\Response
     */
    public function edit(Inventory $inventory) {
        abort_if(!$this->user->can('edit_inventory'), 403);

        $this->inventory = $inventory;

        return view('admin.inventory.edit', $this->data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Inventory  $inventory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Inventory $inventory) {
        abort_if(!$this->user->can('edit_inventory'), 403);

        $inventory->name = $request->name;
        $inventory->address = $request->address;
        $inventory->city = $request->city;
        $inventory->state = $request->state;
        $inventory->zip = $request->zip;
        $inventory->code = $request->code;
        $inventory->notes = $request->notes;

        $inventory->save();

        return Reply::success('Inventory Updated Successfully');
    }

    public function updateItemBalance(Request $request) {
        abort_if(!$this->user->can('add_inventory_item'), 403);

        $inventory_item = InventoryItems::findorFail($request->id);
        $inventory_item->update([$request->field => $request->value]);

        /* for ($i = 0; $i < count($request->update_balance); $i++) {
          $inventory_item = InventoryItems::find($request->itemid[$i]);

          if ($inventory_item->balance !== $request->update_balance[$i]){
          $inventory_item->balance = $request->update_balance[$i];
          $inventory_item->updated_by = $this->user->id;
          $inventory_item->save();
          }
          } */
        return Reply::success('Inventory Balances Updated Successfully');
    }

    public function listInventoryItems($id, $item) {

        /* $serials = \App\InventoryItems::select(DB::raw('equipment_id,
          CASE
          WHEN serial_value is not null THEN concat("Serial: ",serial_value)
          WHEN dot_value is not null THEN concat("DOT#: ",dot_value)
          END as name,
          CASE
          WHEN serial_value is not null THEN serial_value
          WHEN dot_value is not null THEN dot_value
          END as id'))->where('inventory_id', '=', $id)->get(); */
        /* $serials = \App\InventoryItems::select(DB::raw('equipment_id,id,
          concat(IFNULL(serial_value,"Nil")," / ",IFNULL(dot_value,"Nil")) as name'))
          ->where('balance',1)->where('inventory_id', $id)->where('equipment_id', $item)->get(); */
        $serials = \App\CMMS\InventoryItems::
                        select(DB::raw("inventory_items.id,concat(categories.title,'-',ifnull(model_name,''),'-',ifnull(manufacturer.title,''),' #',serial,' ',ifnull(rental_old,'NA')) as name"))
                        ->join("categories", "categories.id", "=", "inventory_items.category")
                        ->leftjoin("manufacturer", "manufacturer.id", "=", "inventory_items.manufacturer")
                        ->join("locations", "locations.id", "=", "inventory_items.warehouse_location")
                        ->where('warehouse_location', $id)
                        ->where('service_type', 'rental')->where('production_status', 'ready')
                        ->where('availability', 'available')->where('active', 1)->get();

        return json_encode($serials);
    }

    public function assets() {
        abort_if(!$this->user->can('view_inventory'), 403);
        
        $this->pageTitle = 'Assets Inventory';
        
        if (request()->ajax()) {

            $inventoryItems = \App\CMMS\InventoryItems::
                    select('inventory_items.*', 'locations.title as warehouse', 'categories.title', 'manufacturer.title as mt')
                    ->join("categories", "categories.id", "=", "inventory_items.category")
                    ->leftjoin("manufacturer", "manufacturer.id", "=", "inventory_items.manufacturer")
                    ->join("locations", "locations.id", "=", "inventory_items.warehouse_location")
                    ->where('service_type', 'rental')->where('production_status', 'ready')
                    ->where('active', 1);

            $this->filter($inventoryItems, request());

            return DataTables::of($inventoryItems)
                    ->addColumn('action', function ($row) {
                            $url = "showModal('/admin/test-sheets/show-submit/{$row->id}', '#application-lg-modal');";
                            $action='';
                            if (user()->can('create_inspection')) {
                                $action = '<a href="#" onclick="'.$url.'" class="btn btn-primary"
                                             >Start Inspection</i></a>';
                            }
                            $action = '<a href="' . route('test-sheets.showsubmit', [$row->id]) . '" class=""
                                            >Create Inspection</a>';
                            return $action;
                    })
                            ->editColumn('rental_price', function ($row) {
                                if ($row->rental_price <= 0)
                                    return '$0.00';
                                else
                                    return '$' . number_format($row->rental_price, 2);
                            })
                            ->editColumn('availability', function ($row) {
                                return '<label class="badge text-white badge-status btn-block bg-'
                                        . $row->availability . '">' . ucwords($row->availability) . '</label>';
                            })
                            ->rawColumns(['availability','action'])
                            ->addIndexColumn()
                            ->make(true);
        } else {
            
            $this->list = \App\CMMS\Manufacturer::get();
            $this->locations = \App\CMMS\Locations::get();
            $this->categories = \App\CMMS\Categories::get();
            //$this->assets = \App\CMMS\InventoryItems::get();
            return view('admin.inventory.assets', $this->data);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Inventory  $inventory
     * @return \Illuminate\Http\Response
     */
    public function destroy(Inventory $inventory) {
        //
    }

}
