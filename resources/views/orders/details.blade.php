<link rel="stylesheet" href="{{ asset('css/orders-design-system.css?v='.$build_version) }}">
<link rel="stylesheet" href="{{ asset('css/orders.css?v='.$build_version) }}">
<link rel="stylesheet" href="{{ asset('css/manager-orders.css?v='.$build_version) }}">

@php $bodyClass = 'control-sidebar-slide-open'; @endphp
@section('body-class', $bodyClass)
<div class="manager-order-panel legacy-order-panel">
    <div class="manager-order-panel__header">
        <div class="manager-order-panel__title">
            <span class="manager-order-panel__id">#{{ $order->order_id }} {{ optional($order->hospital)->name ?? '—' }}</span>
            <span class="manager-order-panel__so">{{ $order->order_no ?? '' }}</span>
            @if($order->consignment_order)
            <span class="manager-order-chip chip-consignment">Consignment</span>
            @endif
            
                @if ($order->reassigned_order)
                <span class="manager-order-chip chip-status">
                <a href="#" onclick="showOrder('{{ $order->reassigned_order }}', true)" class="meta-link">
                    Reassigned From <strong>#{{ $order->reassigned_order }}</strong>
                </a>
                </span>
                @endif
            
                @if ($order->reassign_to)
                <span class="manager-order-chip chip-status">
                <a href="#" onclick="showOrder('{{ $order->reassign_to }}', true)" class="meta-link">
                    Reassigned To <strong>#{{ $order->reassign_to }}</strong>
                </a>
                </span>
                @endif
            
            
            @if($order->inservice_status)
            <span class="manager-order-chip chip-inservice">Inservice {{ strtoupper($order->inservice_status) }}</span>
            @endif
            <div class="manager-order-subtitle">
                <span class="detail-meta">{{ optional($order->hospital)->address }}</span>
                <span class="detail-meta">
                    {{ optional($order->hospital)->city }} {{ optional($order->hospital)->state }} {{ optional($order->hospital)->zip }}
                </span>
                <span class="detail-meta">
                    @if(optional($order->staff)->title)
                    {{ optional($order->staff->title)->title_name }}
                    @endif
                    <strong>{{ optional($order->staff)->fullname() ?? '—' }}</strong>

                    <span class="detail-meta">{{ optional($order->staff)->email }}
                        @if($order->contact_phone)
                        <a href="tel:{{ $order->contact_phone }}" class="detail-meta">{{ $order->contact_phone }}</a>
                        @endif
                    </span>
                </span>
                <span class="detail-meta" style="margin-top:5px">Patient: <u style="color: black">{{ $order->patient_name ?? '—' }}</u> Floor: {{ $order->unit_floor ?? '—' }} Room: {{ $order->room_no ?? '—' }} 
                    @permission('change_patient_info')
                    
                    <a href="javascript:;" class="detail-action" style='margin-left:25px' onclick="showChangeRoom({{ $order->id }})">
                        Change patient
                    </a>
                    
                    @endpermission
                </span>
            </div>

        </div>

        <div class="manager-order-header-actions">
            @if(!empty($primaryActions))
            @foreach($primaryActions as $action)
            <a href="#" class="w160 {{ $action['class'] }} manager-order-action">
                @if(!empty($action['icon'])) <i class="fa {{ $action['icon'] }}"></i>@endif
                {{ $action['label'] }}
            </a>
            @endforeach
            @endif
        </div>
        <button class="manager-order-close right-side-toggle" type="button" aria-label="Close order drawer">&times;</button>
    </div>

    <div class="manager-order-panel__body">
        @if ($order->status == 'deleted' && !empty($order->delete_reason))
        <div class="alert-card urgent">
            <div class="alert-icon">🔴</div>
            <div class="alert-content">
                <div class="alert-title">Delete/Cancel Reason:</div>
                <div class="alert-message">{{$order->delete_reason}}</div>
            </div>
            <div class="alert-count">Deleted By {{$order->deletedby->name}} On: {{dformat($order->deleted_at,true)}}</div>
        </div>
        @endif
        <section class="manager-order-summary">
            <div class="summary-card border-order-goal {{$order->consignment_order ? 'inactive' : ''}}">
                <span class="summary-label">Order Goal <small>({{$deliver_goal_hours}} hours)</small></span>
                <strong>{{ $order_goal ? dformat($order_goal, false) : '—' }}</strong>
                <span class="delivery-goal summary-meta">
        {!! $deliveryDiff !!}
    </span>
            </div>
            <div class="summary-card border-order-start {{$order->bill_started ?? 'inactive'}}">
                <span class="summary-label">Bill Start</span>
                <strong>{{ $order->bill_started ? dformat($order->bill_started, true) : '—' }}</strong>
                <span class="summary-meta"><small class="nowrap">Accepted By</small>: {{ optional($order->acceptedby)->name ?? '' }}</span>
            </div>
            <div class="summary-card border-order-end {{$order->bill_started ?? 'inactive'}}">
                <span class="summary-label">Bill End</span>
                <strong>{{ $order->bill_completed ? dformat($order->bill_started, true) : '—' }}</strong>
                <span class="summary-meta">Duration <span class="text-info"><strong>{{ $total_days }}</strong></span> day(s)</span>
            </div>
            <div class="summary-card border-order-total {{$order->bill_started ?? 'inactive'}}">
                <span class="summary-label">Order Total</span>
                <strong>
                    @permission('view_amount')
                    ${{ number_format($order_total, 2) }}
                    @else
                    —
                    @endpermission
                </strong>
                <span class="summary-meta">{{ $order->equipments->count() }} equipment combo(s)</span>
            </div>
            <div class="summary-card border-order-status" style="color: {{config('constant.orders_color.'.$order->status)}}">
                <span class="summary-label">Order Status</span>
                <strong class="text-center status-card" style="color: {{config('constant.orders_color.'.$order->status)}}">
                    <i class="fa {{ $statusIcon }}"></i> {{ strtoupper(config('constant.orders.' . $order->status)) }}
                </strong>
                @if ($order->status != 'accepted')
                <span class="summary-meta status-meta">{{ $statusTimeDiff ?? '' }}</span>
                @endif
            </div>
        </section>

        @if($pickupIssues->count() > 0)
        <section>
            <div class="accordion" id="pickupIssuesAccordion">
                <div class="card border-0">
                    <div class="card-header p-0" id="pickupIssuesHeading">
                        <button class="btn btn-link w-100 text-left pickup-issues-toggle" type="button" data-toggle="collapse" data-target="#pickupIssuesCollapse" aria-expanded="false" aria-controls="pickupIssuesCollapse">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h4 class="mb-0">Pickup Issues</h4>
                                    <p class="mb-0 text-muted small">Items reported by staff during pickup</p>
                                </div>
                                <div class="d-flex align-items-center">
                                    <span class="badge badge-{{ $pickupIssues->where('status', 'pending')->count() > 0 ? 'danger' : 'success' }} mr-2">
                                        {{ $pickupIssues->where('status', 'pending')->count() }} Pending
                                    </span>
                                    <i class="fa fa-chevron-down"></i>
                                </div>
                            </div>
                        </button>
                    </div>
                    <div id="pickupIssuesCollapse" class="collapse" aria-labelledby="pickupIssuesHeading" data-parent="#pickupIssuesAccordion">
                        <div class="card-body p-0 pt-3">
                            <div class="manager-order-attachments">
                                @foreach($pickupIssues as $issue)
                                @php
                                $isResolved = ($issue->status ?? 'pending') === 'resolved';
                                @endphp
                                <div class="attachment-row {{ $isResolved ? 'issue-resolved' : '' }}">
                                    <div class="attachment-icon">
                                        @if($isResolved)
                                        <i class="fa fa-check-circle text-success"></i>
                                        @else
                                        <i class="fa fa-exclamation-triangle text-danger"></i>
                                        @endif
                                    </div>
                                    <div class="attachment-info flex-grow-1">
                                        <a href="javascript:void(0)" class="font-weight-bold">
                                            {{ optional($issue->orderEquipment->equipment)->name ?? 'Equipment' }} (ID: {{ $issue->order_equipment_id }})
                                        </a>
                                        <div class="attachment-meta">
                                            Reported {{ dformat($issue->created_at, true) }} · by {{ optional($issue->user)->name ?? '—' }}
                                        </div>
                                        @if(!empty($issue->missing_details))
                                        <div class="detail-meta mt-2">
                                            <strong>Issue Details:</strong> {{ $issue->missing_details }}
                                        </div>
                                        @endif

                                        @if($isResolved)
                                        <div class="resolution-details mt-3 p-3 bg-light rounded">
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="fa fa-check-circle text-success mr-2"></i>
                                                <strong class="text-success">Resolved</strong>
                                            </div>
                                            <div class="detail-meta">
                                                <div><strong>Resolved on:</strong> {{ $issue->resolved_at ? dformat($issue->resolved_at, true) : '—' }}</div>
                                                <div><strong>Resolved by:</strong> {{ optional($issue->resolvedByUser)->name ?? '—' }}</div>
                                                @if(!empty($issue->resolved_notes))
                                                <div class="mt-2">
                                                    <strong>Resolution Notes:</strong>
                                                    <div class="mt-1 p-2 bg-white rounded border-left border-success" style="border-left-width: 3px !important;">
                                                        {{ $issue->resolved_notes }}
                                                    </div>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                    <div class="attachment-actions">
                                        @if(!$isResolved)
                                        <a href="#" class="btn btn-sm btn-outline-success pickup-issue-resolve-btn" data-id="{{ $issue->id }}">Mark as Resolved</a>
                                        @else
                                        <span class="badge badge-success">Resolved</span>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        @endif

        <section class="manager-order-card tabs-card">
            <ul class="manager-order-tabs nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active tab-link" id="legacy-overview-tab" data-toggle="tab" href="#legacy-overview" role="tab" aria-controls="legacy-overview" aria-selected="true">Equipment &amp; Assets</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link tab-link" id="legacy-attachments-tab" data-toggle="tab" href="#legacy-attachments" role="tab" aria-controls="legacy-attachments" aria-selected="false">Attachments</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link tab-link" id="legacy-activity-tab" data-toggle="tab" href="#legacy-activity" role="tab" aria-controls="legacy-activity" aria-selected="false">Activity Log</a>
                </li>
            </ul>

            <div class="tab-content manager-order-tab-content">
                <div class="tab-pane fade show active" id="legacy-overview" role="tabpanel" aria-labelledby="legacy-overview-tab">
                    <div class="manager-order-layout">
                        <main class="manager-order-main">
                            <form class="ajax-form" id="serialForm" role="form">
                                @csrf
                                <input type="hidden" name="id" value="{{ $order->id }}" />
                                <input id="submited_at" type="hidden" value="{{ $order->submited_at }}" />
                                <span id="order_goal" class="d-none">{{ $order_goal ? dformat($order_goal, true) : '' }}</span>

                                <section class="manager-order-card">

                                    <div class="manager-order-table-wrapper">
                                        <table class="manager-order-table legacy-order-table">
                                            <thead>
                                                <tr>
                                                    <th>Equipment Combo</th>
                                                    <th>Assets</th>
                                                    @permission('view_amount')
                                                    <th class="text-right">Rate</th>
                                                    @endpermission
                                                    <th class="text-right">Days</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($order->equipments as $item)
                                                @php
                                                $assets = $order->viewAssets($item->id);
                                                $removedAssets = is_array($item->removed_assets) ? $item->removed_assets : [];
                                                @endphp
                                                <tr class="{{ $item->assets && $removedAssets && empty(array_diff($item->assets, $removedAssets)) ? 'asset-row-completed' : '' }}">
                                                    <td>
                                                        <div class="legacy-equipment-name">{{ optional($item->equipment)->name ?? '—' }}</div>
                                                        <div class="legacy-inventory-title detail-meta">{{ optional($item->inventory)->title ?? 'NA' }}</div>
                                                        <input type="hidden" value="{{ $item->equipment_id }}" class="eq_id" />
                                                    </td>
                                                    <td>
                                                        @if ($order->status == 'pending' && $order->submited_at == '')
                                                        @permission('add_serials')
                                                        <input type="hidden" name="equipment_id[]" value="{{ $item->id }}" />
                                                        <input type="hidden" name="price_day[]" value="{{ $item->price_day }}" />
                                                        <div class="legacy-inventory-grid">
                                                            <div>
                                                                <select class="select2 form-control inventory-list"
                                                                        required
                                                                        data-placeholder="Select Inventory"
                                                                        name="inventory_id[]">
                                                                    <option value=""></option>
                                                                    @foreach ($inventories as $inv)
                                                                    <option value="{{ $inv->id }}">{{ ucwords($inv->title) }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div>
                                                                <select class="select2 select2-multiple serials items form-control"
                                                                        multiple
                                                                        required
                                                                        data-placeholder="Select Asset"
                                                                        name="assets{{ $loop->index }}[]">
                                                                </select>
                                                            </div>
                                                        </div>
                                                        @endpermission
                                                        @else
                                                        <div class="legacy-inventory-display">
                                                            <div class="legacy-asset-list">
                                                                @if($assets->count())
                                                                @foreach($assets as $asset)
                                                                @php $isRemoved = in_array($asset->id, $removedAssets); @endphp
                                                                <span class="asset-pill {{ $isRemoved ? 'asset-pill--removed' : '' }}">
                                                                    @if (!user()->can('edit_orders') || $isRemoved)
                                                                    {{ $asset->name }}
                                                                    @else
                                                                    <a href="#" onclick="editAsset({{ $item->id }}, {{ $asset->id }})" title="Click to replace this asset">{{ $asset->name }}</a>
                                                                    @endif
                                                                </span>
                                                                @endforeach
                                                                @else
                                                                <span class="text-muted">No assets assigned</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        @endif
                                                    </td>
                                                    @permission('view_amount')
                                                    <td class="text-right">${{ number_format($item->price_day, 2) }}</td>
                                                    @endpermission
                                                    <td class="text-right">{{ $item->rental_days ?? '—' }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </section>
                                @if(!empty($primaryActions))
                                <section class="manager-order-card notes-section">
                                    <header class="manager-order-card__header">
                                        <div>
                                            <div class="notes-title">📝 Notes &amp; Notifications</div>
                                            <p>{{ __('Add optional context and choose teammates to notify when you change the order status.') }}</p>
                                        </div>
                                    </header>
                                    @include('orders.components.notify-fields', ['employees' => $employees])
                                </section>
                                @endif
                            </form>
                        </main>

                        <aside class="manager-order-aside">
                            <section class="manager-order-card sticky">
                                <header class="manager-order-card__header">
                                    <div>
                                        <h4>Order Timeline</h4>
                                        <p>Date needed: {{ $order->date_needed ? dformat($order->date_needed, true) : '—' }}</p>
                                    </div>
                                </header>
                                <div class="manager-order-timeline">
                                    @foreach($timeline as $index => $item)
                                    @php
                                    $isActive = isset($item['active']) && $item['active'] === true;
                                    $isLast = $loop->last;
                                    @endphp
                                    <div class="manager-order-timeline__item {{ !$isActive ? 'timeline-item-inactive' : '' }}">
                                        <div class="timeline-icon-wrapper">
                                            <div class="timeline-icon {{ $isActive ? 'timeline-icon-active' : '' }}">
                                                @if(isset($item['icon_type']) && $item['icon_type'] === 'fa')
                                                <i class="fa {{ $item['icon'] }}"></i>
                                                @else
                                                {{ $item['icon'] }}
                                                @endif
                                            </div>
                                            @if(!$isLast)
                                            <div class="timeline-connector"></div>
                                            @endif
                                        </div>
                                        <div class="timeline-content">
                                            <div class="timeline-title">{{ $item['title'] }}</div>
                                            <div class="timeline-timestamp">{{ $item['timestamp'] }}</div>
                                            @if(!empty($item['meta']))
                                            <div class="timeline-meta">{{ $item['meta'] }}</div>
                                            @endif
                                            @if(!empty($item['links']))
                                            <div class="timeline-links">
                                                <a href="{{ $item['links']['web'] }}" target="_blank">Web</a>
                                                <span>·</span>
                                                <a href="{{ $item['links']['pdf'] }}" target="_blank">PDF</a>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </section>
                        </aside>
                    </div>
                </div>

                <div class="tab-pane fade" id="legacy-attachments" role="tabpanel" aria-labelledby="legacy-attachments-tab">
                    <div class="row g-3 legacy-attachments-row">
                        @include('orders._attachments')
                    </div>
                </div>

                <div class="tab-pane fade" id="legacy-activity" role="tabpanel" aria-labelledby="legacy-activity-tab">
                    <section class="manager-order-card">
                        <header class="manager-order-card__header">
                            <div>
                                <h4>Status Activity</h4>
                                <p>Captured history for this order</p>
                            </div>
                        </header>
                        @if($statusLog->count())
                        <div class="manager-order-log">
                            @foreach($statusLog as $entry)
                            <div class="log-entry">
                                <div class="log-timestamp">{{ dformat($entry->created_at, true) }}</div>
                                <div class="log-body">
                                    <div class="log-title">{!! ucwords($entry->status) !!}</div>
                                    <div class="log-meta">
                                        {{ optional($entry->user)->name ?? 'System' }}
                                        @if($entry->notes)
                                        · <span class="text-muted">{{ $entry->notes }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="manager-order-placeholder">
                            <i class="fa fa-history"></i>
                            <span>No status transitions recorded yet.</span>
                        </div>
                        @endif
                    </section>
                </div>
            </div>
        </section>
    </div>
</div>

@include('orders._js')