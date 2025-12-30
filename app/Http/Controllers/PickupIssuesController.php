<?php

namespace App\Http\Controllers;

use Yajra\DataTables\Facades\DataTables;
use App\Helper\Reply;

/**
 * DEMO SKELETON: Pickup Issues Controller
 * 
 * PURPOSE & WORKFLOW:
 * This controller manages pickup issues and discrepancies for medical equipment orders.
 * 
 * Original Functionality:
 * - Track and manage pickup issues when equipment cannot be located or retrieved
 * - Filter issues by company, hospital, staff, equipment, date ranges
 * - Display issues with order details, asset information, and resolution status
 * - Resolve pickup issues with user tracking and status updates
 * - Integration with orders, equipment, hospitals, and staff data
 * - Permission-based access control for viewing and managing issues
 * 
 * Key Features (Original):
 * 1. Issue Tracking: Record and track pickup problems with detailed information
 * 2. Filtering: Filter by company, hospital, staff member, equipment, date ranges
 * 3. Status Management: Track issue status (open, resolved, pending)
 * 4. Resolution Tracking: Record who resolved issues and when
 * 5. Asset Linking: Link issues to specific equipment assets and orders
 * 6. DataTables Integration: Server-side processing for large issue datasets
 * 
 * For demo purposes, all database queries and business logic have been removed.
 * The controller now returns dummy data matching the expected structure for portfolio presentation.
 */
class PickupIssuesController extends Admin\AdminBaseController {

    public function __construct() {
        parent::__construct();
        $this->pageTitle = __('Orders Pickup Issues');
        $this->pageIcon = 'fa fa-tasks';
    }

    /**
     * DEMO: Display pickup issues index - List all pickup issues
     * 
     * Original: Loaded hospitals, staff, status counts, inventories, equipment, and companies from database
     * Filtered data based on user role (staff vs admin) and hospital assignments
     * Returns view with filter options and issue listing
     * 
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index() {
        abort_if(!user()->can('view_pickup_issues'), 403);

        $this->hospitals = collect([
            (object)['id' => 1, 'name' => 'Demo Hospital A'],
            (object)['id' => 2, 'name' => 'Demo Hospital B'],
        ]);

        $this->staff = collect([
            (object)['id' => 1, 'name' => 'John Doe', 'hospital_id' => 1],
            (object)['id' => 2, 'name' => 'Jane Smith', 'hospital_id' => 2],
        ]);

        $this->statusCount = [
            'open' => 5,
            'resolved' => 12,
            'pending' => 3,
        ];

        $this->inventories = collect([
            (object)['id' => 1, 'name' => 'Main Inventory'],
            (object)['id' => 2, 'name' => 'Warehouse B'],
        ]);

        $this->cmms_assets = collect([
            (object)['id' => 1, 'name' => 'Ventilator-Vent-X2000-Demo Brand #12345 NA'],
            (object)['id' => 2, 'name' => 'Hospital Bed-Bed-Pro500-Demo Brand #67890 NA'],
        ]);

        $this->equipments = collect([
            (object)['id' => 1, 'name' => 'Ventilator'],
            (object)['id' => 2, 'name' => 'Hospital Bed'],
            (object)['id' => 3, 'name' => 'Infusion Pump'],
        ]);

        $this->companies = collect([
            (object)['id' => 1, 'company_name' => 'Demo Company A'],
            (object)['id' => 2, 'company_name' => 'Demo Company B'],
        ]);

        return view('pickup_issues.index', $this->data);
    }

    /**
     * DEMO: Get pickup issues data - DataTables endpoint for issues listing
     * 
     * Original: Queried pickup_issues table with joins to orders, equipment, hospitals, companies, users
     * Applied filters for company, hospital, user, resolved_by, status, equipment, date ranges
     * Returned formatted DataTables response with issue details, order info, and resolution status
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function data() {
        abort_if(!user()->can('view_pickup_issues'), 403);

        $request = request();

        $issues = collect([
            (object)[
                'id' => 1,
                'order_id' => 'ORD-001',
                'asset_title' => 'VENT-12345',
                'hospital_name' => 'Demo Hospital A',
                'company_name' => 'Demo Company A',
                'username' => 'John Doe',
                'status' => 'open',
                'created_at' => '2024-01-15 10:30:00',
                'missing_details' => 'Equipment not found at specified location',
                'order_equipment_id' => 1,
            ],
            (object)[
                'id' => 2,
                'order_id' => 'ORD-002',
                'asset_title' => 'BED-67890',
                'hospital_name' => 'Demo Hospital B',
                'company_name' => 'Demo Company B',
                'username' => 'Jane Smith',
                'status' => 'resolved',
                'created_at' => '2024-01-20 14:15:00',
                'missing_details' => 'Equipment located in different unit',
                'order_equipment_id' => 2,
            ],
            (object)[
                'id' => 3,
                'order_id' => 'ORD-003',
                'asset_title' => 'PUMP-11111',
                'hospital_name' => 'Demo Hospital A',
                'company_name' => 'Demo Company A',
                'username' => 'Bob Johnson',
                'status' => 'pending',
                'created_at' => '2024-02-01 09:00:00',
                'missing_details' => 'Awaiting staff confirmation',
                'order_equipment_id' => 3,
            ],
        ]);

        $filteredIssues = $issues;

        if ($request->filter_company != null) {
            $filteredIssues = $filteredIssues->filter(function ($issue) use ($request) {
                return $issue->company_name == $request->filter_company;
            });
        }

        if ($request->filter_hospital != null) {
            $filteredIssues = $filteredIssues->filter(function ($issue) use ($request) {
                return $issue->hospital_name == $request->filter_hospital;
            });
        }

        return DataTables::of($filteredIssues)
            ->orderColumn('orders.order_id', function ($query, $order) {
                return $query;
            })
            ->addColumn('action', function ($row) {
                $action = '';
                if ($row->status == 'open') {
                    $action .= '<a href="#" class="btn btn-sm btn-primary resolve-issue" data-id="' . $row->id . '">Resolve</a>';
                }
                return $action;
            })
            ->editColumn('created_at', function ($row) {
                return dformat($row->created_at, true);
            })
            ->editColumn('status', function ($row) {
                $badges = [
                    'open' => '<span class="badge badge-warning">Open</span>',
                    'resolved' => '<span class="badge badge-success">Resolved</span>',
                    'pending' => '<span class="badge badge-info">Pending</span>',
                ];
                return $badges[$row->status] ?? '<span class="badge badge-secondary">' . ucfirst($row->status) . '</span>';
            })
            ->rawColumns(['status', 'action', 'asset_title', 'missing_details'])
            ->make(true);
    }

}
