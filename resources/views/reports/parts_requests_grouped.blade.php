@extends('layouts.app') 
@push('head-script')
<link rel="stylesheet" href="{{ asset('assets/node_modules/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css') }}">
<style>
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
    .buttons-colvis{
        display: none !important;
    }

</style>

@endpush

@section('content')

<form id="filter-form">
    <div class="row" style="margin-top: 10px">
        <div class="col-md-12">
            <div class="card" id="ticket-filters">
                <div class="card-header ui-sortable-handle">
                    <div class="float-left">
                        Report Filters
                    </div>
                    <div class="float-right">
                        
                        <label class="radio-inline pl-lg-2">
                            <input class="form-check-input" type="checkbox" name="hospital" id="hospital" checked="">
                            <label class="form-check-label" for="hospital">
                                <b>@trans(Report By Site)</b>
                            </label>
                        </label>
                        <label class="radio-inline pl-lg-2">
                            <input class="form-check-input" type="checkbox" name="equipment" id="equipment" >
                            <label class="form-check-label" for="equipment">
                                <b>@trans(Report By Part Name)</b>
                            </label>
                        </label>
                        <label class="radio-inline pl-lg-2">
                            <input class="form-check-input" type="checkbox" name="delay_reason" id="delay_reason" >
                            <label class="form-check-label" for="delay_reason">
                                <b>@trans(Report By Delay Reason)</b>
                            </label>
                        </label>
                         <label class="radio-inline pl-lg-2">
                            <input class="form-check-input" type="checkbox" name="by_user" id="by_user" >
                            <label class="form-check-label" for="by_user">
                                <b>@trans(Report By Staff User)</b>
                            </label>
                        </label>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">

                        <div class="col-md-3 col-sm-6 col-12">
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
                                <select id="filter_hospital" class="select2 form-control select-site">
                                    <option value="">@trans(Sites): @trans(All)</option>
                                    @foreach ($hospitals as $item)
                                    <option value="{{ $item->id }}">{{ ucwords($item->name) }}</option>   
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-3 col-sm-6 col-12">
                            <div class="form-group">
                                <select name="delayReason" id="delayReason" class="select2 form-control">
                                    <option value="">@trans(Delay Reason): @trans(All)</option>
                            <option value="Backordered">Backordered</option>
                            <option value="Lead time">Lead time</option>
                            <option value="Missed cutoff">Missed cutoff</option>
                            <option value="Holiday closure">Holiday closure</option>
                        </select>
                            </div>
                        </div>
                        
                        <div class="col-md-3 col-sm-6 col-12">
                            <div class="form-group">
                                <select id="filter_type" class="select2 form-control">
                                    <option value="">@trans(Report Type): @trans(All)</option>
                                    <option value="requests">Requests</option>
                                    <option value="orders">Orders</option>
                                </select>
                            </div>
                        </div>
                        </div>
                    <div class="row">
                        <div class="col-md-3 col-sm-6 col-12">
                            <div class="form-group">
                                <div class="input-daterange input-group hcustom">
                                    <input type="text" class="form-control datepik" id="date-start" value="" name="start_date" placeholder="From Date">
                                    <span class="input-group-addon bg-custom b-0 text-white p-1" style="padding-top: 7px !important"><></span>
                                    <input type="text" class="form-control datepik" id="date-end" name="end_date" value="" placeholder="To Date">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-4 col-8">
                            <div class="form-group">
                                <button type="button" id="apply-filters" class=""><i class="fa fa-check"></i> @lang('app.apply')</button>
                                &nbsp;
                                <button type="button" id="reset-filters" class=""><i class="fa fa-refresh"></i> @lang('app.reset')</button>
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
                    <table id="myTable" style="width: 100%" class="orders-table nowrap table table-bordered table-hover table-light ">
                        <thead>
                            <tr>
                                <th>@trans(User)</th>
                                <th>@trans(Site)</th>
                                <th>@trans(Part Name)</th>
                                <th>@trans(Delay Reason)</th>
                                <th>@trans(Requests Count)</th>
                                <th>@trans(Parts Qty)</th>
                                <th>@trans(Orders Total) $  </th>
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

const startOfMonth = moment().startOf('month').format('MM/DD/YYYY');
const endOfMonth = moment().format('MM/DD/YYYY');
$('#date-start').val(startOfMonth);
$('#date-end').val(endOfMonth);


let options = {"order": [0], "pageLength": 20, dom: '<"top"Bi>rt<"bottom"p>', responsive: false,
    scrollX: true,
    scrollCollapse: true,
    ajax: {'url': "{!! route('part_request.report-group-data') !!}",
        "data": function (d) {
            return $.extend({}, d, {
                "delayReason": $('#delayReason').val(),
                "filter_hospital": $('#filter_hospital').val(),
                "filter_type": $('#filter_type').val(),
                "filter_equipment": $('#filter_equipment').val(),
                "filter_company": $('#filter_company').val(),
                "groupby": "", //document.querySelector('input[name="groupby"]:checked').value,
                "hospital": $('#hospital').prop('checked'),
                "by_user": $('#by_user').prop('checked'),
                "city": true,
                "delay_reason": $('#delay_reason').prop('checked'),
                "orders": true,
                "equipment": $('#equipment').prop('checked'),
                "asset": true,
                "filter_costcenter": $('#filter_costcenter').val(),
                "startDate": $('#date-start').val(),
                "endDate": $('#date-end').val(),
                "order_no": $('#order_no').val(),
                "orders_total": $('#orders_total').val(),
                status: $('input[name="status[]"]:checked').map(function () {
                    return this.value;
                }).get()
            });
        }
    },
    "footerCallback": function (row, data, start, end, display) {
        var api = this.api(), data;

    },
    columns: [
        {data: 'by_user', name: 'by_user', sortable: true, visible: false},
        {data: 'hospital', name: 'hospital', sortable: true, visible: true},
        {data: 'equipment', name: 'equipment', sortable: true, visible: false},
        {data: 'delay_reason', name: 'delay_reason', sortable: true, visible: false},
        {data: 'total_requests', name: 'total_requests'},
        {data: 'total_part_request_items_qty', name: 'total_part_request_items_qty'},
        {data: 'total_order_value', name: 'total_order_value', class: 'bg-light text-bold'},
    ],
    fnDrawCallback: function (oSettings) {
        $("body").removeClass("control-sidebar-slide-open");
        $("body").tooltip({
            selector: '[data-toggle="tooltip"]'
        });


        table.column('hospital:name').visible($('#hospital').prop('checked'));
        table.column('costcenter:name').visible($('#costcenter').prop('checked'));
        table.column('equipment:name').visible($('#equipment').prop('checked'));
        table.column('asset:name').visible($('#asset').prop('checked'));
        table.column('orders:name').visible($('#orders').prop('checked'));
        table.column('by_user:name').visible($('#by_user').prop('checked'));
        table.column('delay_reason:name').visible($('#delay_reason').prop('checked'));
        if ($('input[name="groupby"]:checked').val() == 'xnone')
            table.column('groupbycol:name').visible(false);
        else
            table.column('groupbycol:name').visible(true);
        $('#group_colname').text($('input[name="groupby"]:checked').next().text());
        table.columns.adjust();
    }
};

let table = $.dataTable(options);

$('input[name=groupby]').change(function () {
    runReport();
});

function runReport() {
    let check = false;
    $.each($(".form-check-input"), function () {
        if ($(this).prop('checked'))
            check = true;
    });

    if (!check && $('input[name="groupby"]:checked').val() == 'xnone')
        return;
    table.draw();
}

$('#apply-filters').click(function () {
    runReport();
});

$('select').change(function () {
    //runReport();
});

$('#reset-filters').click(function () {
    $('#filter-form')[0].reset();
    $('.select2').change();
    table.draw();
});

</script>


@endpush