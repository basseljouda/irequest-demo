@extends('layouts.app')
@push('head-script')
<link rel="stylesheet" href="{{ asset('css/orders.css?v='.$build_version) }}">
<link rel="stylesheet" href="{{ asset('css/orders-cards.css?v='.$build_version) }}">
@endpush

@section('content')
@php $hidePageHeader = true; @endphp

<input type="hidden" id="tab_status">

<!-- Filters & Sort Accordion -->
<div class="row mb-3">
    <div class="col-12">
        <div class="card orders-filters-card">
            <div class="card-header p-2 p-md-3" id="filtersHeading">
                <div class="d-flex justify-content-between align-items-center flex-wrap filters-header-row" data-toggle="collapse" data-target="#filtersCollapse" aria-expanded="false" aria-controls="filtersCollapse">
                    <div class="d-flex align-items-center flex-grow-1 filters-toggle-area">
                        <i class="fa fa-filter"></i>
                        <strong class="ml-2">Filters</strong>
                        <span id="active-filters-count" class="badge badge-info ml-2">0</span>
                        <!--i class="fa fa-chevron-down ml-auto text-muted filters-chevron"></i-->
                    </div>
                    <div class="d-flex align-items-center ml-2 ml-md-3 filters-sort-wrapper" onclick="event.stopPropagation();">
                        <label class="mb-0 mr-2 text-muted small d-none d-sm-inline" for="filter_status">Status:</label>
                        <select id="filter_status" class="form-control form-control-sm filters-sort-select" onclick="event.stopPropagation();" style="min-width: 250px;">
                            <option value="">@lang('app.all')</option>
                            @foreach (config('constant.orders') as $key => $value)
                            @if ($user->can('view_'.$key))
                            <option value="{{ $key }}">{{ ucfirst($value) }}</option>
                            @endif
                            @endforeach
                        </select>
                        <label class="mb-0 mr-2 ml-3 text-muted small d-none d-sm-inline" for="sort-orders">Sort:</label>
                        <select id="sort-orders" class="form-control form-control-sm filters-sort-select" onclick="event.stopPropagation();">
                            <option value="updated_at_desc">Newest First</option>
                            <option value="updated_at_asc">Oldest First</option>
                            <option value="created_at_desc">Created (Newest)</option>
                            <option value="created_at_asc">Created (Oldest)</option>
                            <option value="order_id_desc">Order ID (High to Low)</option>
                            <option value="order_id_asc">Order ID (Low to High)</option>
                        </select>
                    </div>
                </div>
            </div>
            <div id="filtersCollapse" class="collapse" aria-labelledby="filtersHeading">
                <div class="card-body">
                    <form id="filter-form">
                        <!-- Section 1: Primary Filters (Most Common) -->
                        <div class="filter-section">
                            <div class="filter-section-title">
                                <i class="fa fa-filter"></i> Primary Filters
                            </div>
                            <div class="row">
                                <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
                                    <label class="small text-muted mb-1">Site</label>
                                    <select id="filter_hospital" class="select2 form-control form-control-sm select-site">
                                        <option value="">@lang('app.all')</option>
                                        @foreach ($hospitals as $item)
                                        <option value="{{ $item->id }}">{{ ucwords($item->name) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-2 col-md-4 col-sm-6 col-12 mb-3">
                                    <label class="small text-muted mb-1">IDN</label>
                                    <select id="filter_company" class="select2 form-control form-control-sm select-idn">
                                        <option value="">@lang('app.all')</option>
                                        @foreach ($companies as $item)
                                        <option value="{{ $item->id }}">{{ ucwords($item->company_name) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-2 col-md-4 col-sm-6 col-12 mb-3">
                                    <label class="small text-muted mb-1">Date Type</label>
                                    <select id="filter_date_type" class="select2 form-control form-control-sm">
                                        <option value="created_at">Creation Date</option>
                                        <option value="submited_at">Routing Date</option>
                                        <option value="delivered_at">Delivery Date</option>
                                        <option value="accepted_at">Acceptance Date</option>
                                        <option value="bill_completed">Completion Date</option>
                                        <option value="picked_at">PickUp Date</option>
                                        <option value="reassigned_at">Reassigning Date</option>
                                    </select>
                                </div>
                                <div class="col-lg-3 col-md-8 col-sm-12 col-12 mb-3">
                                    <label class="small text-muted mb-1">Date Range</label>
                                    <div class="input-daterange input-group hcustom">
                                        <input type="text" class="form-control form-control-sm datepik" id="date-start" name="start_date" placeholder="From">
                                        <span class="input-group-addon">-</span>
                                        <input type="text" class="form-control form-control-sm datepik" id="date-end" name="end_date" placeholder="To">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Section 2: Equipment & Assets -->
                        <div class="filter-section">
                            <div class="filter-section-title">
                                <i class="fa fa-cogs"></i> Equipment & Assets
                            </div>
                            <div class="row">
                                <div class="col-lg-4 col-md-6 col-sm-6 col-12 mb-3">
                                    <label class="small text-muted mb-1">Equipment</label>
                                    <select class="select2 items form-control form-control-sm equipments select-combo">
                                        <option value="">@lang('app.all')</option>
                                        @foreach($equipments as $equipment)
                                        <option value="{{ $equipment->id }}">{{ ucfirst($equipment->name) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @if (!isset(user()->is_staff))
                                <div class="col-lg-4 col-md-6 col-sm-6 col-12 mb-3">
                                    <label class="small text-muted mb-1">Asset</label>
                                    <select name="filter_asset" id="filter_asset" class="select2 form-control form-control-sm">
                                        <option value="">@lang('app.all')</option>
                                        @foreach ($cmms_assets as $item)
                                        <option value="{{ $item->id }}">{{ ucwords($item->name) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @endif
                                <div class="col-lg-4 col-md-6 col-sm-6 col-12 mb-3">
                                    <label class="small text-muted mb-1">Rental ID</label>
                                    <input type="text" class="form-control form-control-sm" id="filter_serial" name="filter_serial" placeholder="Enter Rental ID">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Section 3: Patient & Location -->
                        <div class="filter-section">
                            <div class="filter-section-title">
                                <i class="fa fa-user"></i> Patient & Location
                            </div>
                            <div class="row">
                                <div class="col-lg-4 col-md-6 col-sm-6 col-12 mb-3">
                                    <label class="small text-muted mb-1">Patient Name</label>
                                    <input type="text" class="form-control form-control-sm" id="filter_patient_name" name="filter_patient_name" placeholder="Enter patient name">
                                </div>
                                <div class="col-lg-3 col-md-6 col-sm-6 col-12 mb-3">
                                    <label class="small text-muted mb-1">Room No</label>
                                    <input type="text" class="form-control form-control-sm" id="filter_room_no" name="filter_room_no" placeholder="Enter room number">
                                </div>
                                @if (!isset(user()->is_staff))
                                @permission('view_cost_center')
                                <div class="col-lg-3 col-md-6 col-sm-6 col-12 mb-3 hide">
                                    <label class="small text-muted mb-1">Cost Center</label>
                                    <select id="filter_costcenter" class="select2 form-control form-control-sm">
                                        <option value="">@lang('app.all')</option>
                                        @foreach ($cost_centers as $item)
                                        <option value="{{ $item->id }}">{{ ucwords($item->name) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @endpermission
                                @endif
                                <div class="col-lg-2 col-md-6 col-sm-6 col-12 mb-3">
                                    <label class="small text-muted mb-1">Inservice Status</label>
                                    <select id="filter_inservice_status" class="select2 form-control form-control-sm">
                                        <option value="">@lang('app.all')</option>
                                        <option value="accepted">@trans(Accepted)</option>
                                        <option value="declined">@trans(Declined)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="filter-actions">
                            <button type="button" id="apply-filters" class="btn btn-info btn-sm">
                                <i class="fa fa-check"></i> @lang('app.apply')
                            </button>
                            <button type="button" id="reset-filters" class="btn btn-default btn-sm">
                                <i class="fa fa-refresh"></i> @lang('app.reset')
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Cards Grid -->
<div class="row">
    <div class="col-12">
        <div id="orders-cards-container" class="orders-cards-grid">
            <!-- Cards will be loaded here via AJAX -->
        </div>
        <div id="orders-cards-loading" class="text-center py-5" style="display: none;">
            <i class="fa fa-spinner fa-spin fa-2x"></i>
            <p class="mt-2">Loading orders...</p>
        </div>
        <div id="orders-cards-empty" class="text-center py-5" style="display: none;">
            <i class="fa fa-inbox fa-3x text-muted"></i>
            <p class="mt-2 text-muted">No orders found</p>
        </div>
        <div class="text-center mt-4">
            <button id="load-more-btn" class="btn btn-info" style="display: none;">
                <i class="fa fa-arrow-down"></i> Load More
            </button>
        </div>
    </div>
</div>
@endsection

@push('footer-script')
<script src="{{ asset('assets/node_modules/select2/dist/js/select2.full.min.js') }}"></script>
<script src="{{ asset('assets/node_modules/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js') }}" type="text/javascript"></script>

<script>
    // Pass data to JavaScript
    window.ordersCardsDataUrl = '{!! route('orders.cards-data') !!}';
    window.ordersDestroyUrl = '{{ route('orders.destroy',':id') }}';
    window.ordersRequestUrl = '{{ route('orders.request',':id') }}';
    window.csrfToken = "{{ csrf_token() }}";
    window.showOrderUrl = "{{ route('orders.showdetails', ':id') }}";
    
    // Get search parameter from URL
    var urlParams = new URLSearchParams(window.location.search);
    window.initialSearch = urlParams.get('search') || '';
    
    // Pre-fill global search input if search parameter exists
    if (window.initialSearch) {
        $(document).ready(function() {
            $('#global-search').val(window.initialSearch);
            $('#global-search-clear').show();
        });
    }
</script>
<script src="{{ asset('js/orders/orders-utils.js?v='.$build_version) }}"></script>
<script src="{{ asset('js/orders/orders-cards.js?v='.$build_version) }}"></script>
<script src="{{ asset('js/orders/orders-modals.js?v='.$build_version) }}"></script>
<script>
    // Save view preference when switching
    $(document).ready(function() {
        $('.view-switcher-btn').on('click', function() {
            var view = $(this).data('view');
            try {
                localStorage.setItem('orders_view_preference', view);
            } catch (e) {
                console.warn('Could not save view preference:', e);
            }
        });
    });
</script>
@endpush

