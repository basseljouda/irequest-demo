<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Controllers\Admin;

use App\TestSheetTemplate;
use App\TestSheetTask;
use App\TestSheetTemplateEquipment;
use App\Equipments;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use DB;

class TestSheetTemplateController extends AdminBaseController {

    public function __construct() {
        parent::__construct();
        $this->pageTitle = 'Inspection Templates';
        $this->pageIcon = 'ti-cloud-up';
    }

    // List all templates
    public function index() {
        $this->templates = TestSheetTemplate::with('equipments')->get();
        return view('admin.test-sheets.index', $this->data);
    }

    // Show manual create form
    public function create() {
        $equipments = Equipments::all(); // Load all equipment for selection
        return view('admin.test-sheets.create', compact('equipments'));
    }

    // Store manual template
    public function store(Request $request) {
        $request->validate([
            'template_name' => 'required|string|max:255',
            'equipments' => 'required|array|min:1',
            'tasks' => 'required|array|min:1',
        ]);
        $template = TestSheetTemplate::create([
                    'name' => $request->input('template_name'),
                    'is_active' => 1,
        ]);

        foreach ($request->input('tasks', []) as $order => $task) {
            TestSheetTask::create([
                'template_id' => $template->id,
                'task' => $task,
                'order' => $order,
            ]);
        }
        $rows = [];
        foreach ($request->input('equipments') as $name) {
            $rows[] = [
                'template_id' => $template->id,
                'equipment_name' => $name,
                'created_at' => now()
            ];
        }
        TestSheetTemplateEquipment::insert($rows);
        return response()->json(['message' => 'Template Saved'], 422);
    }

    // Show import form
    public function importForm() {
        /* $this->equipments = Equipments::whereIn('id', function($query) {
          $query->selectRaw('MIN(id)')
          ->from('equipments')
          ->groupBy('name');
          })->get(); */
        $this->equipments = \App\CMMS\InventoryItems::
                join("unified.order_assets as assets", "assets.asset_id", "inventory_items.id")
                ->join("unified.orders_equipments", "orders_equipments.id", "assets.order_eq_id")
                ->join("categories", "categories.id", "inventory_items.category")
                ->leftJoin("manufacturer", "manufacturer.id", "inventory_items.manufacturer")
                ->join("locations", "locations.id", "inventory_items.warehouse_location")
                ->join("unified.equipments", "equipments.id", "orders_equipments.equipment_id")
                ->where('service_type', 'rental')
                ->selectRaw("concat(categories.title,', ',ifnull(model_name,''),', ',ifnull(manufacturer.title,'')) as name")
                ->distinct()
                ->get();

        return view('admin.test-sheets.import', $this->data);
    }

    public function importExcel(Request $request) {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls',
        ]);
        $file = $request->file('excel_file');
        $sheet = \Maatwebsite\Excel\Facades\Excel::toArray([], $file)[0];

        // Find header row
        $headerRowIdx = null;
        foreach ($sheet as $i => $row) {
            if (isset($row[0]) && trim(strtolower($row[0])) === 'inspection task') {
                $headerRowIdx = $i;
                break;
            }
        }
        if ($headerRowIdx === null) {
            return response()->json(['message' => 'Header row "Inspection Task" not found.'], 422);
        }

        $tasks = [];
        $consecutiveEmptyRows = 0;
        for ($i = $headerRowIdx + 1; $i < count($sheet); $i++) {
            $row = $sheet[$i];
            $isEmpty = true;
            foreach ($row as $cell) {
                if (!empty($cell) && trim($cell) !== '') {
                    $isEmpty = false;
                    break;
                }
            }
            if ($isEmpty) {
                $consecutiveEmptyRows++;
                if ($consecutiveEmptyRows >= 2)
                    break;
                continue;
            } else {
                $consecutiveEmptyRows = 0;
            }
            $label = isset($row[0]) ? trim($row[0]) : null;
            if ($label)
                $tasks[] = $label;
        }
        return response()->json(['tasks' => $tasks]);
    }

    // Store imported template after review
    public function importReviewStore(Request $request) {
        $request->validate([
            'template_name' => 'required|string|max:255',
            'fields' => 'required|array|min:1',
            'equipments' => 'required|array|min:1',
        ]);
        $template = TestSheetTemplate::create([
                    'name' => $request->input('template_name'),
                    'is_active' => 1,
                    'user_id' => user()->id
        ]);
        foreach ($request->input('fields', []) as $order => $field) {
            TestSheetField::create([
                'template_id' => $template->id,
                'label' => $field['label'],
                'input_type' => $field['input_type'],
                'options' => $field['options'] ? $field['options'] : null,
                'is_required' => $field['is_required'] ?? false,
                'order' => $order,
            ]);
        }
        $rows = [];
        foreach ($request->input('equipments') as $name) {
            $rows[] = [
                'template_id' => $template->id,
                'equipment_name' => $name,
                'created_at' => now()
            ];
        }
        TestSheetTemplateEquipment::insert($rows);
        return redirect()->route('admin.test-sheets.index')->with('success', 'Template imported.');
    }

    // Show single template
    public function show($id) {
        $template = TestSheetTemplate::with('fields', 'equipments')->findOrFail($id);
        return view('admin.test-sheets.show', compact('template'));
    }

    public function showSubmit($asset_id) {
        abort_if(!user()->can('view_tasks_sheet'), 403);
        \Log::info($asset_id);
        $this->asset_id = $asset_id;

        $this->asset = \App\CMMS\InventoryItems::findOrFail($asset_id);

        // 1. Generate the unique name as per your new structure:
        $name = $this->asset->name();/*$this->asset->category->title . '-' .
                ($this->asset->model_name ?? '') . '-' .
                ($this->asset->manufacturer ? $this->asset->manufacturer->title : '');*/
        $this->item_id = $name;

// 2. Now search for the template with this equipment_name in pivot:
        $this->template = \App\TestSheetTemplate::whereHas('equipmentNames', function($q) use ($name) {
                    $q->where('equipment_name', $name);
                })->latest()->first();




        return view('admin.test-sheets.submit', $this->data);
    }

}
