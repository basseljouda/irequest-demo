@extends('layouts.app') 
@push('head-script')
<link rel="stylesheet" href="{{ asset('css/orders.css?v='.$build_version) }}">
@endpush 

@permission('add_orders') 
@section('create-button')
<a href="{{ route('orders.create') }}" class="btn btn-info btn-sm m-l-15">
    <i class="fa fa-plus-circle"></i> @trans(New) @trans(Order)</a>
@endsection
@endpermission 

@section('content')
<input type="hidden" id="tab_status">
<div class="row mobile-hide">
    <div class="col-12">
        <div class="orders-statuses">
            @permission('view_pending')
            <a href="#"  data-status="pending" class="text-info">
                <i class="icon-badge"></i>
                @lang('app.pending') <span>({{ number_format($statusCount['pending']) }})</span>
            </a>
            @endpermission
            @permission('view_inroute')
            <a href="#"  data-status="inroute" class="text-inroute">
                <i class="icon-badge"></i>
                @lang('InRoute') <span>({{ number_format($statusCount['inroute']) }})</span>
            </a>
            @endpermission
            @permission('view_delivered')
            <a href="#" data-status="delivered" class="text-deliver">
                <i class="icon-badge"></i>
                @lang('app.delivered')  <span>({{ number_format($statusCount['delivered']) }})</span>
            </a>
            @endpermission
            @permission('view_accepted')
            <a href="#" data-status="accepted" class="text-success">
                <i class="icon-badge"></i>
                @trans(Accepted)  <span>({{ number_format($statusCount['accepted']) }})</span>
            </a>
            @endpermission
            @permission('view_completed')
            <a href="#" data-status="completed" class="text-dark">
                <i class="icon-badge"></i>
                @trans(Completed)  <span>({{ number_format($statusCount['completed']) }})</span>
            </a>
            @endpermission
            @permission('view_pickedup')
            <a href="#" data-status="pickedup" class="text-pick">
                <i class="icon-badge"></i>
                @trans(Picked Up)  <span>({{ number_format($statusCount['pickedup']) }})</span>
            </a>
            @endpermission
            @permission('view_reassigned')
            <a href="#" data-status="reassigned" class="text-reassign">
                <i class="icon-badge"></i>
                @trans(Reassigned)  <span>({{ number_format($statusCount['reassigned']) }})</span>
            </a>
            @endpermission
        </div>
    </div>
</div>
<form id="filter-form">
    <div class="row filter-section">
        <div class="col-md-12">
            <div class="card" id="ticket-filters">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2 col-sm-4 col-8">
                            <div class="form-group">
                                <select  id="filter_company" class="select2 form-control select-idn">
                                    <option value=""><i class="icon-badge"></i>@trans(IDN): @lang('app.all')</option>
                                    @foreach ($companies as $item)
                                    <option value="{{ $item->id }}">{{ ucwords($item->company_name) }}</option>   
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 col-12">
                            <div class="form-group">
                                <select  id="filter_hospital" class="select2 form-control select-site">
                                    <option value=""><i class="icon-badge"></i>@trans(Sites): @lang('app.all')</option>
                                    @foreach ($hospitals as $item)
                                    <option value="{{ $item->id }}">{{ ucwords($item->name) }}</option>   
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-2 col-sm-4 col-8">
                            <div class="form-group">
                                <select name="" id="filter_status" class="select2 form-control">
                                    <option value="">@lang('app.status'): @lang('app.all')</option>
                                    @foreach (config('constant.orders') as $key => $value)
                                    @if ($user->can('view_'.$key))
                                    <option value="{{ $key }}"> @trans({{ucfirst($value)}}) </option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-4 col-8">
                            <div class="form-group">
                                <select name="" id="filter_date_type" class="select2 form-control">
                                    <option value="created_at">Select Date Filter Type</option>
                                    <option value="created_at">Creation Date</option>
                                    <option value="submited_at">Routing Date</option>
                                    <option value="delivered_at">Delivery Date</option>
                                    <option value="accepted_at">Acceptance Date</option>
                                    <option value="bill_completed">Completion Date</option>
                                    <option value="picked_at">PickUp Date</option>
                                    <option value="reassigned_at">Reassigning Date</option>
                                    
                                    
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 col-12">
                            <div class="form-group">
                                <div class="input-daterange input-group hcustom">
                                    <input type="text" class="form-control datepik" id="date-start" value="" name="start_date" placeholder="From Date">
                                    <!--span class="input-group-addon bg-custom b-0 text-white p-1" style="font-size: 20px"><></span-->
                                    <input type="text" class="form-control datepik" id="date-end" name="end_date" value="" placeholder="To Date">
                                </div>
                            </div>
                        </div>
                        
                    </div>
                    <div class="row">
                        <div class="col-md-3 col-sm-6 col-12">
                            <div class="form-group">
                                <select class="select2 items m-b-10 form-control equipments select-combo">
                                    <option value=""><i class="icon-badge"></i>Equipment: All</option>
                                    @foreach($equipments as $equipment)
                                    <option value="{{ $equipment->id }}">{{ ucfirst($equipment->name) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @if (!isset(user()->is_staff))
                        <div class="col-md-3 col-sm-6 col-12">
                            <div class="form-group">
                                <select name="filter_asset" id="filter_asset" class="select2 form-control">
                                    <option value=""><i class="icon-badge"></i>@trans(Assets) : @trans(All)</option>
                                    @foreach ($cmms_assets as $item)
                                    <option value="{{ $item->id }}">{{ ucwords($item->name) }}</option>   
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @endif
                        <div class="col-md-2 col-sm-6 col-12">
                            <div class="form-group">
                                <input type="text" class="form-control" id="filter_serial" name="filter_serial" placeholder="Rental ID">
                            </div>
                        </div>
                         <div class="col-md-2 col-sm-6 col-12">
                            <div class="form-group">
                                <input type="text" class="form-control" id="filter_patient_name" name="filter_patient_name" placeholder="Patient Name">
                            </div>
                         </div>
                             <div class="col-md-2 col-sm-6 col-12">
                            <div class="form-group">
                                <input type="text" class="form-control" id="filter_room_no" name="filter_room_no" placeholder="Room No">
                            </div>
                        </div>
                        @if (!isset(user()->is_staff))
                        @permission('view_cost_center')
                        <div class="col hide">
                            <div class="form-group">
                                <select name="" id="filter_costcenter" class="select2 form-control">
                                    <option value=""><i class="icon-badge"></i>@lang('app.costcenter'): @lang('app.all')</option>
                                    @foreach ($cost_centers as $item)
                                    <option value="{{ $item->id }}">{{ ucwords($item->name) }}</option>   
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @endpermission
                        @endif
                        <div class="col-md-2 col-sm-6 col-12">
                            <div class="form-group">
                                <select  id="filter_inservice_status" class="select2 form-control">
                                    <option value="">@trans(Inservice Status): @lang('All')</option>
                                    <option value="accepted">@trans(Accepted)</option>
                                    <option value="declined">@trans(Declined)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 col-12">
                            <div class="form-group">
                                <button type="button" id="apply-filters" class="btn btn-info"><i class="fa fa-check"></i> @lang('app.apply')</button>
                                <button type="button" id="reset-filters" class="btn btn-default"><i class="fa fa-refresh"></i> @lang('app.reset')</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive m-t-40">
                    <table id="myTable" class="table table-bordered orders-table nowrap orders-datatable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>@lang('IDN')</th>
                                <th>@lang('Updated On')</th>
                                <th>@trans(Site)</th>
                                <th>@trans(Patient)</th>
                                <th>@trans(Room No)</th>
                                <th>@trans(Contact)</th>
                                <th class="text-center">@lang('app.status')</th>
                                <th>@trans(Bill Start)</th>
                                <th>@trans(Bill End)</th>
                                <th id='table-date-col' class="text-bold">Created On</th>
                                <th></th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('footer-script')
<script src="{{ asset('assets/node_modules/select2/dist/js/select2.full.min.js') }}"></script>
<script src="{{ asset('assets/node_modules/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js') }}" type="text/javascript"></script>

@include('vendor.datatables.scripts')

<script>
    // Pass data to JavaScript
    window.ordersDataUrl = '{!! route('orders.data') !!}';
    window.ordersDestroyUrl = '{{ route('orders.destroy',':id') }}';
    window.ordersRequestUrl = '{{ route('orders.request',':id') }}';
    window.initialOrderId = "{{ request()->query('id') }}";
    window.csrfToken = "{{ csrf_token() }}";
</script>
<script src="{{ asset('js/orders/orders-utils.js?v='.$build_version) }}"></script>
<script src="{{ asset('js/orders/orders-index.js?v='.$build_version) }}"></script>
@endpush