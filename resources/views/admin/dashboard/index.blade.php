@extends('layouts.app')
@permission('view_dashboard')
@push('head-script')
<link rel="stylesheet" href="//cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">
<link rel="stylesheet" href="{{ asset('css/dashboard.css?v='.$build_version) }}">
@endpush
@section('content')

<section class="content">
    <div class="container-fluid">
        <button id="customize-dashboard-btn" class="btn btn-primary mb-3 hide new_version">
            <i class="fa fa-cog"></i> Customize Dashboard
        </button>

        <!-- Drill-Down Breadcrumbs -->
        <div id="drill-breadcrumbs" class="drill-breadcrumbs" style="display: none;"></div>

        <!-- Alerts Section -->
        <div id="dashboard-alerts" class="dashboard-alerts" style="display: none;"></div>

        <!-- Enhanced KPI Cards -->
        <div class="row dashboard-widgets d-flex flex-wrap">
            <div class="col-md-3">
                <div class="kpi-card" id="kpi-revenue" data-type="revenue">
                    <div class="kpi-title">Order Value</div>
                    <div class="kpi-value">$0</div>
                    <div class="kpi-trend neutral">No change</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="kpi-card" id="kpi-orders" data-type="orders">
                    <div class="kpi-title">Rental Orders</div>
                    <div class="kpi-value">0</div>
                    <div class="kpi-trend neutral">No change</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="kpi-card" id="kpi-assets" data-type="assets">
                    <div class="kpi-title">Active Rental Assets</div>
                    <div class="kpi-value">0</div>
                    <div class="kpi-trend neutral">No change</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="kpi-card" id="kpi-rental-days" data-type="rentalDays">
                    <div class="kpi-title">Total Rental Days</div>
                    <div class="kpi-value">0</div>
                    <div class="kpi-trend neutral">No change</div>
                </div>
            </div>
        </div>

        <div class="row bg-whitesmoke pt-3 hide">
            <div class="col-12 col-md-6">
                <div class="form-group">
                    <select name="" id="year_filter" class="form-control select2">
                        @for($i = date('Y'); $i >= date('Y') - 5; $i--)
                        <option {{selected (isset($_GET["year"]) ? $_GET["year"] : date('Y'),$i)}} value="{{ $i }}">{{ $i }}</option>
                        @endfor
                    </select>
                </div>
            </div>

            <div class="col-12 col-md-6">
                <div class="form-group">
                    <select name="" id="filter_hospital" class="form-control select2">
                        <option value=""><i class="icon-badge"></i>@trans(Sites): @trans(All)</option>
                        @foreach ($hospitals as $item)
                        <option {{selected (isset($_GET["data"]) ? $_GET["data"] : '',$item->id)}} value="{{ $item->id }}">{{ ucwords($item->name) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- IDN Summary Section (for drill-down) -->
        @permission('view_idn_summary')
        <div id="idn-summary-section" class="mt-4" style="display: none;">
            <h4 class="mb-3">IDN Summary</h4>
            <div id="idn-summary-container"></div>
        </div>
        @endpermission
        <!-- Charts (Phase 3: Lazy Loaded) -->
        <div class="row">
            @permission('view_orders_chart')
            <div class="col-12 col-md-12 chart-container" style="height:500px;" id='3DChart_forecast'></div>
            @endpermission
             @permission('view_spend_combo_chart')
            <div class='col-md-12 chart-container' id='chartdiv' style="height: 500px"></div>
            @endif
            @permission('view_orders_created_chart')
            <div class="col-12 col-md-6 chart-container" style="height:500px;" id='3DChart_orders'></div>
            @endpermission
            @permission('view_assets_chart')
            <div class="col-12 col-md-6 chart-container" style="height:500px;" id='3DChart_assets'></div>
            @endpermission
            @permission('view_sites_chart')
            <div class="col-12 chart-container" style="height:500px;" id='3DChart_sites'></div>
            @endpermission
        </div>


    </div>
</section>

<div id="dashboard-customize-modal" class="modal fade" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Customize Dashboard</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <ul id="dashboard-widget-list" class="list-group">
                    {{-- Populated by JS --}}
                </ul>
            </div>
            <div class="modal-footer">
                <button id="save-dashboard-config" class="btn btn-success">Save</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('footer-script')
{{-- Phase 3: AmCharts loaded asynchronously (see dashboard-api-optimized.js) --}}
{{-- AmCharts will be loaded on-demand when charts are needed --}}

{{-- Sortable Library for Widget Customization --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>

{{-- Dashboard Routes Configuration --}}
<script>
    window.dashboardRoutes = {
        ajax: "{{ route('admin.dashboard.ajax') }}",
        kpis: "{{ route('admin.dashboard.kpis') }}",
        charts: "{{ route('admin.dashboard.charts') }}",
        enhanced: "{{ route('admin.dashboard.enhanced') }}",
        widgets: {
            get: "{{ route('admin.dashboard.widgets.get') }}",
            save: "{{ route('admin.dashboard.widgets.save') }}"
        }
    };
    window.csrfToken = "{{ csrf_token() }}";
</script>


{{-- Load charts.js first (defines chart initialization functions) --}}
<script src="{{ asset('js/dashboard/dashboard-charts.js?v='.$build_version) }}"></script>
{{-- Load API optimized (defines fetch functions) --}}
<script src="{{ asset('js/dashboard/dashboard-api-optimized.js?v='.$build_version) }}"></script>
{{-- Load lazy loading (handles on-demand chart loading) --}}
<script src="{{ asset('js/dashboard/dashboard-lazy-load.js?v='.$build_version) }}"></script>
{{-- Load other modules --}}
<script src="{{ asset('js/dashboard/dashboard-widgets.js?v='.$build_version) }}"></script>
<script src="{{ asset('js/dashboard/dashboard-enhanced.js?v='.$build_version) }}"></script>
{{-- Load main last (initializes everything) --}}
<script src="{{ asset('js/dashboard/dashboard-main.js?v='.$build_version) }}"></script>

{{-- Superadmin flag for forecast chart --}}
@role('superadmin')
<script>
    window.isSuperAdmin = true;
</script>
@endrole

@endpush
@endpermission