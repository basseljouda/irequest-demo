<?php

namespace App\Http\Controllers\Manager;

use App\Company;
use App\Orders;
use App\CostCenter;
use App\Helper\Reply;
use App\Hospitals;
use App\HospitalsStuff;
use App\Http\Controllers\Admin\AdminBaseController;
use App\Services\ManagerOrders\OrdersQuery;
use App\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class OrdersController extends AdminBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'Manager Orders';
        $this->pageIcon = 'fa fa-table';
    }

    public function index()
    {
        abort_if(!user()->can('view_orders'), 403);

        $this->companies = Company::get();
        $this->hospitals = Hospitals::get();
        $this->staff = HospitalsStuff::select('*', \DB::raw("CONCAT(firstname,' ',lastname) as name"))->get();
        $this->cost_centers = CostCenter::get();
        $this->equipments = \App\Equipments::where('active', 1)->orderBy('name')->get();
        $this->statusCount = \App\Orders::getStatuses();

        return view('manager.orders.index', $this->data);
    }

    public function data(Request $request)
    {
        abort_if(!user()->can('view_orders'), 403);

        $service = new OrdersQuery(user());
        $query = $service->applyFilters($service->baseQuery(), $request);

        return DataTables::of($query)
            ->orderColumn('orders.order_id', function ($query, $order) {
                $query->orderBy('orders.id', $order);
            })
            ->editColumn('updated_at', function ($row) {
                return dformat($row->updated_at, true);
            })
            ->editColumn('bill_started', function ($row) {
                return $row->bill_started ? dformat($row->bill_started, true) : '';
            })
            ->editColumn('bill_completed', function ($row) {
                return $row->bill_completed ? dformat($row->bill_completed, true) : '';
            })
            ->editColumn('cost_center_id', function ($row) {
                return $row->cost_center_name ?? '--';
            })
            ->editColumn('order_id', function ($row) {
                return $row->order_id;
            })
            ->editColumn('hospital_name', function ($row) {
                return $row->hospital_name;
            })
            ->editColumn('status', function ($row) {
                return ucwords(config('constant.orders.' . $row->status));
            })
            ->addColumn('actions', function ($row) {
                return [
                    'view' => route('manager.orders.show', $row->id),
                    'edit' => user()->can('edit_orders') ? route('orders.edit', $row->id) : null,
                    'pickupRequest' => user()->can('send_pickup_request') && $row->status == 'accepted'
                        ? route('orders.request', $row->id)
                        : null,
                    'delete' => user()->can('delete_orders') ? route('orders.destroy', $row->id) : null,
                ];
            })
            ->rawColumns([])
            ->make(true);
    }

    public function show($id)
    {
        $order = Orders::with([
            'hospital.company',
            'staff',
            'costcenter',
            'equipments.equipment',
            'equipments.inventory',
            'file.files.user',
            'statustrans.user',
            'createdby',
            'devliveredby',
            'acceptedby',
            'staffaccepted',
            'pickupRequest',
            'pickedupby',
            'staffpicked',
            'reassigningby',
            'deletedby'
        ])->findOrFail($id);

        abort_if(!user()->can('view_' . $order->status), 403);

        $orderGoal = null;
        $deliveryDiff = null;
        $orderTotal = 0;
        $totalDays = 0;

        if ($order->created_at && optional(optional($order->hospital)->company)->deliver_goal) {
            $orderGoal = $order->created_at->copy()->addHours($order->hospital->company->deliver_goal);
        }

        $deliverGoalHours = optional(optional($order->hospital)->company)->deliver_goal;

        if ($orderGoal && $order->delivered_at) {
            $diff = $orderGoal->diff($order->delivered_at);
            $sign = $orderGoal->gt($order->delivered_at) ? '-' : '+';
            $hours = ($diff->days * 24) + $diff->h;
            $minutes = $diff->i;
            $deliveryDiff = sprintf('%s %d hr %d min', $sign, $hours, $minutes);
        }

        if (!$order->consignment_order) {
            $result = CalcOrderRental($order);
            $orderTotal = $result->orderTotal;
            $totalDays = $result->orderRentalDays;
        }

        $timeline = collect([
            [
                'icon' => '🕐',
                'title' => 'Taken On',
                'timestamp' => $order->created_at ? dformat($order->created_at, true) : '--',
                'meta' => optional($order->createdby)->name,
            ],
            [
                'icon' => '🎯',
                'title' => 'Goal ' . ($deliverGoalHours ? '(+' . $deliverGoalHours . 'h)' : ''),
                'timestamp' => $orderGoal ? dformat($orderGoal, true) : '--',
                'meta' => optional($order->devliveredby)->name,
            ],
            [
                'icon' => '🚚',
                'title' => 'Delivered On',
                'timestamp' => $order->delivered_at ? dformat($order->delivered_at, true) : '--',
                'meta' => $deliveryDiff,
            ],
            [
                'icon' => '✅',
                'title' => 'Accepted On',
                'timestamp' => $order->accepted_at ? dformat($order->accepted_at, true) : '--',
                'meta' => optional($order->staffaccepted)->fullname() ?? optional($order->acceptedby)->name,
                'links' => $order->accepted_at ? [
                    'web' => route('orders.show', [$order->id, 'note' => 'noteaccept']),
                    'pdf' => route('orders.showPDF', [$order->id, 'note' => 'noteaccept']),
                ] : null,
            ],
            [
                'icon' => $order->status === 'reassigned' ? '🔁' : '📦',
                'title' => $order->status === 'reassigned' ? 'Reassigned' : 'Pickup',
                'timestamp' => $order->status === 'reassigned'
                    ? ($order->reassigned_at ? dformat($order->reassigned_at, true) : '--')
                    : ($order->picked_at ? dformat($order->picked_at, true) : '--'),
                'meta' => $order->status === 'reassigned'
                    ? optional($order->reassigningby)->name
                    : optional($order->staffpicked)->fullname() ?? optional($order->pickedupby)->name,
            ],
        ]);

        $actions = $this->buildPrimaryAction($order);
        $attachments = $order->file ? $order->file->files : collect();
        $statusLog = $order->statustrans;

        return Reply::successWithData('Order detail fetched', [
            'html' => view('manager.orders.show', [
                'order' => $order,
                'orderGoal' => $orderGoal,
                'deliveryDiff' => $deliveryDiff,
                'orderTotal' => $orderTotal,
                'totalDays' => $totalDays,
                'timeline' => $timeline,
                'attachments' => $attachments,
                'statusLog' => $statusLog,
                'primaryAction' => $actions['primary'],
                'secondaryActions' => $actions['secondary'],
            ])->render(),
        ]);
    }

    protected function buildPrimaryAction(Orders $order): array
    {
        $actions = [
            'primary' => null,
            'secondary' => [],
        ];

        switch ($order->status) {
            case 'pending':
                if (user()->can('add_serials')) {
                    $actions['primary'] = [
                        'label' => 'Mark In Route',
                        'icon' => 'fa fa-arrow-right',
                        'endpoint' => route('manager.orders.transition', ['id' => $order->id, 'action' => 'route']),
                        'method' => 'POST',
                    ];
                }
                if (user()->can('edit_orders')) {
                    $actions['secondary'][] = [
                        'label' => 'Edit Order',
                        'icon' => 'fa fa-pencil',
                        'href' => route('orders.edit', $order->id),
                    ];
                }
                break;

            case 'inroute':
                if (user()->can('confirm_delivered')) {
                    $actions['primary'] = [
                        'label' => 'Confirm Delivery',
                        'icon' => 'fa fa-truck',
                        'endpoint' => route('manager.orders.transition', ['id' => $order->id, 'action' => 'deliver']),
                        'method' => 'POST',
                    ];
                }
                break;

            case 'delivered':
                if (user()->can('confirm_accepted')) {
                    $actions['primary'] = [
                        'label' => 'Accept Order',
                        'icon' => 'fa fa-check-circle',
                        'endpoint' => route('manager.orders.transition', ['id' => $order->id, 'action' => 'accept']),
                        'method' => 'POST',
                    ];
                }
                break;

            case 'accepted':
                if (user()->can('confirm_completed')) {
                    $actions['primary'] = [
                        'label' => 'Complete Order',
                        'icon' => 'fa fa-flag-checkered',
                        'endpoint' => route('manager.orders.transition', ['id' => $order->id, 'action' => 'complete']),
                        'method' => 'POST',
                    ];
                }
                if (user()->can('send_pickup_request')) {
                    $actions['secondary'][] = [
                        'label' => 'Request Pickup',
                        'icon' => 'fa fa-truck',
                        'href' => route('orders.request', $order->id),
                    ];
                }
                break;

            case 'completed':
                if (user()->can('confirm_pickedup')) {
                    $actions['primary'] = [
                        'label' => 'Confirm Pickup',
                        'icon' => 'fa fa-box',
                        'endpoint' => route('manager.orders.transition', ['id' => $order->id, 'action' => 'pickup']),
                        'method' => 'POST',
                    ];
                }
                break;

            case 'pickedup':
            case 'reassigned':
                $actions['primary'] = [
                    'label' => 'Order Closed',
                    'icon' => 'fa fa-lock',
                    'disabled' => true,
                ];
                break;
        }

        if (user()->can('delete_orders') && $order->status != 'deleted') {
            $actions['secondary'][] = [
                'label' => 'Delete / Cancel',
                'icon' => 'fa fa-trash',
                'endpoint' => route('manager.orders.transition', ['id' => $order->id, 'action' => 'delete']),
                'method' => 'DELETE',
            ];
        }

        return $actions;
    }

    public function transition(Request $request, $id, $action)
    {
        abort_if(!user()->can('view_orders'), 403);

        // Placeholder response; actual workflow integration pending.
        return Reply::success('Action received. (Manager flow implementation in progress.)');
    }
}

