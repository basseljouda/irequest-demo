@extends('layouts.app') @push('head-script')
<link rel="stylesheet" href="//cdn.datatables.net/fixedheader/3.1.5/css/fixedHeader.bootstrap.min.css">
<link rel="stylesheet" href="//cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">

<link rel="stylesheet" href="{{ asset('assets/node_modules/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css') }}">
<link rel="stylesheet" href="{{ asset('assets/plugins/colorpicker/bootstrap-colorpicker.min.css') }}">
<style>
    .mb-20 {
        margin-bottom: 20px
    }
</style>


@endpush @permission('add_jobs') 
@section('create-button')
<a href="{{ route('orders.create') }}" class="btn btn-info btn-sm m-l-15">
    <i class="fa fa-plus-circle"></i> @trans(Create New)</a>
@endsection
@endpermission 
@section('content')
<input type="hidden" id="tab_status">
<div class="row">
    <div class="col-md-12">
        <div class="orders-statuses">
            <a class="active" data-status="" style="min-width: 100px" href="#">
             <i class="icon-badge"></i>
                    All <span>({{ number_format($statusCount['all']) }})</span>
                </a>
            <a href="#" data-status="pending">
                <i class="icon-badge"></i>
                Pending <span>({{ number_format($statusCount['pending']) }})</span>
            </a>
            <a href="#" data-status="delivered">
                <i class="icon-badge"></i>
                Delivered  <span>({{ number_format($statusCount['delivered']) }})</span>
            </a>
            <a href="#" data-status="accepted">
                <i class="icon-badge"></i>
                Accepted  <span>({{ number_format($statusCount['accepted']) }})</span>
            </a>
            <a href="#" data-status="picked">
                <i class="icon-badge"></i>
                PickUp  <span>({{ number_format($statusCount['picked']) }})</span>
            </a>
            <a href="#" data-status="reassigned">
                <i class="icon-badge"></i>
                Reassigned  <span>({{ number_format($statusCount['reassigned']) }})</span>
            </a>
        </div>
    </div>
</div>
<form id="filter-form">
<div class="row" style="margin-top: 10px">
    <div class="col-md-12">
        <div class="card" id="ticket-filters">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <select name="" id="filter_hospital" class="form-control">
                                <option value=""><i class="icon-badge"></i>@trans(Site): @trans(All)</option>
                                @foreach ($hospitals as $item)
                                <option value="{{ $item->id }}">{{ ucwords($item->name) }}</option>   
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <select name="" id="filter_stuff" class="form-control">
                                <option value=""><i class="icon-badge"></i>@lang('app.staff'): @trans(All)</option>
                                @foreach ($stuff as $item)
                                <option value="{{ $item->id }}">{{ ucwords($item->user->name) }}</option>   
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <select name="" id="filter_status" class="form-control">
                                <option value=""><i class="icon-badge"></i>@lang('app.status'): @trans(All)</option>
                                <option value="1">@lang('app.active')</option>
                                <option value="deleted">@lang('app.deleted')</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <div class="input-daterange input-group hcustom">
                                <input type="text" class="form-control" id="date-start" value="" name="start_date" placeholder="From Date">
                                <span class="input-group-addon bg-info b-0 text-white p-1" style="font-size: 20px"><></span>
                                <input type="text" class="form-control" id="date-end" name="end_date" value="" placeholder="To Date">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <select name="" id="filter_costcenter" class="form-control">
                                <option value=""><i class="icon-badge"></i>@lang('app.costcenter'): @trans(All)</option>
                                @foreach ($cost_centers as $item)
                                <option value="{{ $item->id }}">{{ ucwords($item->name) }}</option>   
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <select name="" id="filter_modality" class="form-control">
                                <option value=""><i class="icon-badge"></i>@lang('app.modality'): @trans(All)</option>
                                @foreach ($modalities as $item)
                                <option value="{{ $item->id }}">{{ ucwords($item->name) }}</option>   
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <select name="" id="filter_submodality" class="form-control">
                                <option value=""><i class="icon-badge"></i>@lang('app.submodality'): @trans(All)</option>
                                @foreach ($sub_modalities as $item)
                                <option value="{{ $item->id }}">{{ ucwords($item->name) }}</option>   
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <button type="button" id="apply-filters" class="btn btn-info btn-xs"><i class="fa fa-check"></i> @lang('app.apply')</button>
                            <button type="button" id="reset-filters" class="btn btn-default btn-xs"><i class="fa fa-refresh"></i> @lang('app.reset')</button>
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
                    <table id="myTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>@lang('app.orderno')</th>
                                <th>@lang('app.date')</th>
                                <th>@trans(Site)</th>
                                <th>@lang('app.staff')</th>
                                <th>@lang('app.equipment')</th>
                                <th>@lang('app.serial')</th>
                                <th>@lang('app.expected')</th>
                                <th>@lang('app.status')</th>
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
<script src="//cdn.datatables.net/fixedheader/3.1.5/js/dataTables.fixedHeader.min.js"></script>
<script src="//cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
<script src="//cdn.datatables.net/responsive/2.2.3/js/responsive.bootstrap.min.js"></script>
<script src="{{ asset('assets/node_modules/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js') }}" type="text/javascript"></script>

<script>
$('#date-end').bootstrapMaterialDatePicker({ weekStart : 0, time: false });
$('#date-start').bootstrapMaterialDatePicker({ weekStart : 0, time: false }).on('change', function(e, date)
{
$('#date-end').bootstrapMaterialDatePicker('setMinDate', date);
});

var table = $('#myTable').dataTable({
responsive: true,
        processing: true,
        serverSide: true,
        "order": [[ 0, 'desc' ]],
        ajax: {'url' : '{!! route('orders.data') !!}',
                "data": function (d) {
                return $.extend({}, d, {
                "filter_hospital": $('#filter_hospital').val(),
                "filter_stuff": $('#filter_stuff').val(),
                "filter_modality": $('#filter_modality').val(),
                "filter_submodality": $('#filter_submodality').val(),
                "filter_status": $('#filter_status').val(),
                "filter_costcenter": $('#filter_costcenter').val(),
                "startDate": $('#date-start').val(),
                "endDate": $('#date-end').val(),
                "tab_status": $('#tab_status').val()
                });
                }
        },
        language: languageOptions(),
        "fnDrawCallback": function(oSettings) {
        $("body").tooltip({
        selector: '[data-toggle="tooltip"]'
        });
        },
        columns: [
        { data: 'orderno', name: 'id'},
        { data: 'date', name: 'created_at' },
        { data: 'hospital', name: 'hospital_id' },
        { data: 'staff', name: 'staff_id'},
        { data: 'item', name: 'orders_equipments.name' },
        { data: 'serial', name: 'orders_equipments.serial_no' },
        { data: 'expected', name: 'date_needed' },
        { data: 'status', name: 'status' },
        { data: 'action', name: 'action', width: '20%', orderable: false, searchable: false }
        ]
});
new $.fn.dataTable.FixedHeader(table);
$('#apply-filters').click(function () {
table.draw();
})
$('#reset-filters').click(function () {
            $('#filter-form')[0].reset();
            table.draw();
        })

$('div.orders-statuses a').click(function () {
    $('div.orders-statuses > a').removeClass('active');
    $(this).addClass('active');
    $('#tab_status').val($(this).attr('data-status'));
    table.draw();
});
        $('body').on('click', '.open-url', function(){
var url = $(this).data('row-open-url');
var $temp = $("<input>");
$("body").append($temp);
$temp.val(url).select();
document.execCommand("copy");
$temp.remove();
$.showToastr('@lang('messages.copiedToClipboard')', 'success')
});
$('body').on('click', '.sa-params', function(){
var id = $(this).data('row-id');
swal({
title: "@trans(Are you sure)",
        text: "@trans(Delete Data)",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "@trans(Delete)",
        cancelButtonText: "@trans(Cancel)",
        closeOnConfirm: true,
        closeOnCancel: true
}, function(isConfirm){
if (isConfirm) {

var url = "{{ route('admin.jobs.destroy',':id') }}";
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
});
});

</script>


@endpush