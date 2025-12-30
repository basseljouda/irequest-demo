@extends('layouts.app') 
@push('head-script')
<link rel="stylesheet" href="{{ asset('assets/node_modules/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css') }}">
<link rel="stylesheet" href="{{ asset('assets/plugins/colorpicker/bootstrap-colorpicker.min.css') }}">
<style>
    .tabbed {
  
  min-width: 400px;
  margin: 0 auto;
  margin-bottom: 20px;
  border-bottom: 4px solid #000;
  overflow: hidden;
  transition: border 250ms ease;
}
.tabbed ul {
  margin: 0px;
  padding: 0px;
  overflow: hidden;
  
  padding-left: 48px;
  list-style-type: none;
}
.tabbed > ul * {
  margin: 0px;
  padding: 0px;
}
.tabbed > ul li {
  display: block;
  float: left;
  padding: 10px 24px 8px;
  background-color: #FFF;
  margin-right: 46px;
  z-index: 2;
  position: relative;
  cursor: pointer;
  color: #777;

  text-transform: uppercase;
  font: 600 13px/20px roboto, "Open Sans", Helvetica, sans-serif;

  transition: all 250ms ease;
}
.tabbed > ul li:before,
.tabbed > ul li:after {
  display: block;
  content: " ";
  position: absolute;
  top: 0;
  height: 100%;
  width: 44px;  
  background-color: #FFF;
  transition: all 250ms ease;
}
.tabbed > ul li:before {
  right: -24px;
  transform: skew(30deg, 0deg);
  box-shadow: rgba(0,0,0,.1) 3px 2px 5px, inset rgba(255,255,255,.09) -1px 0;
}
.tabbed > ul li:after {
  left: -24px;
  transform: skew(-30deg, 0deg);
  box-shadow: rgba(0,0,0,.1) -3px 2px 5px, inset rgba(255,255,255,.09) 1px 0;
}
.tabbed > ul li:hover,
.tabbed > ul li:hover:before,
.tabbed > ul li:hover:after {
  background-color: #F4F7F9;
  color: #444;
}
.tabbed > ul li.active {
  z-index: 3;
}
.tabbed > ul li.active,
.tabbed > ul li.active:before,
.tabbed > ul li.active:after {
  background-color: #000;
  color: #fff !important;
}

/* Round Tabs */
.tabbed.round > ul li {
  border-radius: 8px 8px 0 0;
}
.tabbed.round > ul li:before {
  border-radius: 0 8px 0 0;
}
.tabbed.round > ul li:after {
  border-radius: 8px 0 0 0;
}
    .bg-inroute{
        background-color: #6b837a;
    }
    .mb-20 {
        margin-bottom: 20px
    }
    section.content > div> div > div > a > span{
        display: none;
    }
    .dataTables_length{
        margin: 0px 10px 0px 10px;   
    }
    div.dataTables_wrapper div.dataTables_info{
        padding-top: 0.40em;
        color:gray;
    }
    td.dtfc-fixed-left,td.dtfc-fixed-right{
    z-index: 99;
    background: #f4f7f7;
}
th.dtfc-fixed-left,th.dtfc-fixed-right{
    z-index: 99;
    background: #f4f7f7;
}
</style>

@endpush 

@permission('add_orders') 
@section('create-button')
<a href="{{ route('orders.create') }}" class="btn btn-info btn-sm m-l-15 hide">
    <i class="fa fa-plus-circle"></i> @trans(New) @trans(Order)</a>
@endsection
@endpermission 

@section('content')
<input type="hidden" id="tab_status">
<div class="row mobile-hide">
    <div class="col-12">
        <div class="tabbed round">
            <ul>
            @permission('view_pending')
            <!--li href="#"  data-status="{{config('constant.orders.pending')}}" class="text-info active">
                <i class=""></i>
               RFQ Requests <span>({{ number_format($statusCount[config('constant.orders.pending')]) }})</span>
           
            </li>
            <li href="#"  data-status="{{config('constant.orders.pending')}}" class="text-info">
                <i class=""></i>
               Quotes <span>({{ number_format($statusCount[config('constant.orders.pending')]) }})</span>
           
            </li-->
            @endpermission
            @permission('view_in_route')
            <li href="#"  data-status="{{config('constant.orders.inroute')}}" class="text-inroute active">
                <i class=""></i>
                Pending Review <span>({{ number_format($statusCount[config('constant.orders.inroute')]) }})</span>
                </li>
            @endpermission
            @permission('view_delivered')
            <li href="#" data-status="{{config('constant.orders.delivered')}}" class="text-deliver">
                <i class=""></i>
                Processing  <span>({{ number_format($statusCount[config('constant.orders.delivered')]) }})</span>
                </li>
            @endpermission
            @permission('view_accepted')
            <li href="#" data-status="{{config('constant.orders.accepted')}}" class="text-success">
                <i class=""></i>
                @trans(Shipped)  <span>({{ number_format($statusCount[config('constant.orders.accepted')]) }})</span>
                </li>
            @endpermission
            @permission('view_completed')
            <li href="#" data-status="{{config('constant.orders.completed')}}" class="text-dark">
                <i class=""></i>
                @trans(Delivered)  <span>({{ number_format($statusCount[config('constant.orders.completed')]) }})</span>
                </li>
            @endpermission
            </ul>
        </div>
    </div>
</div>
<form id="filter-form">
    <div class="row" style="margin-top: 10px">
        <div class="col-md-12">
            <div class="card" id="ticket-filters">
                <div class="card-body">
                    <div class="row">
                        
                        <div class="col hide">
                            <div class="form-group">
                                <select  id="filter_company" class="select2 form-control select-idn">
                                    <option value=""><i class=""></i>@trans(IDN): @lang('app.all')</option>
                                    @foreach ($companies as $item)
                                    <option value="{{ $item->id }}">{{ ucwords($item->company_name) }}</option>   
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        
                        <div class="col">
                            <div class="form-group">
                                <select  id="filter_hospital" class="select2 form-control select-site">
                                    <option value=""><i class=""></i>@trans(Sites): @lang('app.all')</option>
                                    @foreach ($hospitals as $item)
                                    <option value="{{ $item->id }}">{{ ucwords($item->name) }}</option>   
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="col hide">
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

                        <div class="col">
                            <div class="form-group">
                                <div class="input-daterange input-group hcustom">
                                    <input type="text" class="form-control datepik" id="date-start" value="" name="start_date" placeholder="From Date">
                                    <!--span class="input-group-addon bg-custom b-0 text-white p-1" style="font-size: 20px"><></span-->
                                    <input type="text" class="form-control datepik" id="date-end" name="end_date" value="" placeholder="To Date">
                                </div>
                            </div>
                        </div>
                        <div class="col-1">
                            <div class="form-group">
                                <button type="button" id="apply-filters" class="btn btn-info"><i class="fa fa-check"></i> @lang('Filter')</button>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <button type="button" id="reset-filters" class="btn btn-default"><i class="fa fa-refresh"></i> @lang('app.reset')</button>
                            </div>
                        </div>
                    </div>
                    <div class="row hide">
                        <div class="col hide">
                            <div class="form-group">
                                <select class="select2 items m-b-10 form-control equipments select-combo">
                                    <option value=""><i class=""></i>Equipment: All</option>
                                    @foreach($equipments as $equipment)
                                    <option value="{{ $equipment->id }}">{{ ucfirst($equipment->name) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @if (!isset(user()->is_staff))
                        <div class="col hide">
                            <div class="form-group">
                                <select name="filter_asset" id="filter_asset" class="select2 form-control">
                                    <option value=""><i class=""></i>@trans(Assets) : @trans(All)</option>
                                    @foreach ($cmms_assets as $item)
                                    <option value="{{ $item->id }}">{{ ucwords($item->name) }}</option>   
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @endif
                        <div class="col">
                            <div class="form-group">
                                <input type="text" class="form-control" id="filter_serial" name="filter_serial" placeholder="Rental ID">
                            </div>
                        </div>
                         <div class="col">
                            <div class="form-group">
                                <input type="text" class="form-control" id="filter_patient_name" name="filter_patient_name" placeholder="Patient Name">
                            </div>
                         </div>
                             <div class="col">
                            <div class="form-group">
                                <input type="text" class="form-control" id="filter_room_no" name="filter_room_no" placeholder="Room No">
                            </div>
                        </div>
                        @if (!isset(user()->is_staff))
                        @permission('view_cost_center')
                        <div class="col hide">
                            <div class="form-group">
                                <select name="" id="filter_costcenter" class="select2 form-control">
                                    <option value=""><i class=""></i>@lang('app.costcenter'): @lang('app.all')</option>
                                    @foreach ($cost_centers as $item)
                                    <option value="{{ $item->id }}">{{ ucwords($item->name) }}</option>   
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @endpermission
                        @endif
                        <div class="col">
                            <div class="form-group">
                                <button type="button" id="apply-filters" class="btn btn-info"><i class="fa fa-check"></i> @lang('app.apply')</button>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
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
                    <table style="zoom:80%" id="myTable" class="table table-bordered orders-table nowrap">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>@lang('Updated')</th>
                                <th>@trans(Site)</th>
                                <th>@trans(Contact)</th>
                                <th class="text-center">@lang('app.status')</th>
                                <th>@trans(Action)</th>
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
$('#date-start').on('change', function (e, date)
{
    $('#date-end').bootstrapMaterialDatePicker('setMinDate', date);
});

//$('.orders-statuses > a').css("width", (95 / $('.orders-statuses').children().length) + "%");

let options = {"order": [[2, 'desc']],responsive:false,scrollX: true,
                        scrollCollapse: true,
                        fixedColumns: {
                            left: 1,
                        },
    ajax: {'url': '{!! route('cust_orders.data') !!}',
        "data": function (d) {
            return $.extend({}, d, {
                "filter_hospital": $('#filter_hospital').val(),
                "filter_company": $('#filter_company').val(),
                "filter_stuff": $('#filter_stuff').val(),
                "filter_asset": $('#filter_asset').val(),
                "filter_serial":$('#filter_serial').val(),
                "filter_item": $('.equipments').val(),
                "filter_status": $('#filter_status').val(),
                "filter_costcenter": $('#filter_costcenter').val(),
                "filter_patient_name": $('#filter_patient_name').val(),
                "filter_room_no": $('#filter_room_no').val(),
                "startDate": $('#date-start').val(),
                "endDate": $('#date-end').val()
            });
        }
    },
    initComplete: function (settings) {
        let order_id = "{{ request()->query('id') }}";
        if (order_id !== '')
            showOrder(order_id, true);
    },
    columns: [
        {data: 'orderno', name: 'orders.order_id'},
        /*{data: 'order_no', name: 'order_no'},*/
        
        {data: 'date', name: 'updated_at'},
        {data: 'hospital_name', name: 'hospitals.name'},
        
        {data: 'staff.staffname', name: 'staff.firstname'},
        {data: 'status', name: 'status'},
        
        {data: 'action', name: 'action', sortable: false, searchable: false, className: 'text-left nowrap'}]
};

let table = $.dataTable(options);

$('#apply-filters').click(function () {
    $('div.orders-statuses > a').removeClass('active');
    if ($('#filter_status').val() != '' && $('#filter_status').val() != "{{config('constant.orders.deleted')}}") {
        $('div.orders-statuses a[data-status=' + $('#filter_status').val() + "]").addClass('active');
    }
    table.draw();
})
$('#reset-filters').click(function () {
    $('#filter-form')[0].reset();
    $('.select2').change();
    table.draw();
});

$('div.tabbed li').click(function () {
    $('div.tabbed li').removeClass('active');
    $(this).addClass('active');
    $('#filter_status').val($(this).attr('data-status')).change();
    table.draw();
});


$('body').on('click', '.sa-params', function () {
    var id = $(this).data('row-id');
     var buttons = '<br/><textarea placeholder="" class="form-control" id="cancel_reason" rows="2"></textarea><br/><div>';
        buttons += '<a href=# id="cancel_order" class="btn btn-danger btn-block"><i class="fa fa-times"></i>@trans(Cancel Order)</a></div>';


        swal({
            title: "@trans(Enter order cancellation/delete reason):",
            text: buttons,
            html: true,
            showCancelButton: true,
            showConfirmButton: false,
            closeOnConfirm: true,
            closeOnCancel: true,
            cancelButtonText: "@trans(Close)"
        });
        $('#cancel_order').click(function () {
            cancelOrder();

        });
        
        function cancelOrder() {
            var url = "{{ route('orders.destroy',':id') }}";
            url = url.replace(':id', id);
            var token = "{{ csrf_token() }}";
            $.easyAjax({
                type: 'POST',
                url: url,
                data: {'_token': token, '_method': 'DELETE','cancel_reason': $("#cancel_reason").val()},
                success: function (response) {
                    swal.close();
                    if (response.status == "success") {
                        $.unblockUI();
                        table.draw();
                    }
                }
            });
        }
    });
    
    $('#filter_hospital').change(function () {
                var site_id = $(this).val();

                $('.select-combo').empty().append('<option value="">@trans(Equipment): @trans(All)</option>');
                
                    $.ajax({
                        url: '/orders/fetch',
                        data: {"site_id":site_id}, 
                        type: 'GET',
                        success: function (data) {
                                $.each(data.combo, function (key, value) {
                                $('.select-combo').append('<option value="' + key + '">' + value + '</option>');
                            });
                        }
                    });
                
            });

    /*swal({
        title: "@trans(Are you sure)",
        text: "Are you sure want to delete the order?",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "@trans(Delete)",
        cancelButtonText: "@trans(Cancel)",
        closeOnConfirm: true,
        closeOnCancel: true
    }, function (isConfirm) {
        if (isConfirm) {

            var url = "{{ route('orders.destroy',':id') }}";
            url = url.replace(':id', id);
            var token = "{{ csrf_token() }}";
            $.easyAjax({
                type: 'POST',
                url: url,
                data: {'_token': token, '_method': 'DELETE'},
                success: function (response) {
                    if (response.status == "success") {
                        $.unblockUI();
                        table.draw();
                    }
                }
            });
        }
    });*/


table.on('click', '.show-detail', function () {
    
                    $("body").removeClass("control-sidebar-slide-open");
            var url = "{{ route('cust_orders.showdetails',':id') }}";
            url = url.replace(':id', $(this).data('row-id'));
            $.easyAjax({
            type: 'GET',
                    url: url,
                    success: function (response) {
                    if (response.status == "success") {
                    $('#right-sidebar-content').html(response.view);
                    if ($('#asset_no').is(':visible')) {
                    $('#asset_no').focus();
                    }
                    }
                    }
            });
});

$('body').on('click', '.request-pickup', function () {
    var id = $(this).data('row-id');
    var url = "{{ route('orders.request',':id') }}";
    url = url.replace(':id', id);
   /* swal({
        title: "@trans(Are you sure)",
        text: "Are you sure want to send Pickup request notification?",
        type: "warning",
        showCancelButton: true,
        confirmButtonText: "@trans(Yes)",
        cancelButtonText: "@trans(Cancel)",
        closeOnConfirm: true,
        closeOnCancel: true
    }, function (isConfirm) {
        if (isConfirm) {
            var url = "{{ route('orders.request',':id') }}";
            url = url.replace(':id', id);
            var token = "{{ csrf_token() }}";
            $.easyAjax({
                type: 'POST',
                url: url,
                data: {'_token': token, '_method': 'POST'},
                success: function (response) {
                    if (response.status == "success") {
                        $.unblockUI();
                    }
                }
            });
        }
    });*/
        showModal(url, '#application-lg-modal');
});

</script>


@endpush