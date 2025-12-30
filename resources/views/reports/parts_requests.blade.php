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
                                <th>@trans(Request ID)</th>
                                <th>@trans(Requested On)</th>
                                <th>@trans(Site)</th>
                                <th>@trans(Contact)</th>
                                <th>@trans(Parts)</th>
                                <th>@trans(Date Needed)</th>
                                <th>@trans(Total)</th>
                                <th>@trans(Status)</th>
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


    let options = {"order": [0], "pageLength": 20, dom: '<"top"Bi>rt<"bottom"p>',responsive:false,
         scrollX: true,
                        scrollCollapse: true,
        ajax: {'url': "{!! route('part_request.report-data') !!}",
            "data": function (d) {
                return $.extend({}, d, {
                    "filter_hospital": $('#filter_hospital').val(),
                    "filter_equipment": $('#filter_equipment').val(),
                    "filter_company": $('#filter_company').val(),
                    "groupby":"", //document.querySelector('input[name="groupby"]:checked').value,
                    "hospital": true,
                    "costcenter": true,
                    "city": true,
                    "type": true,
                    "orders": true,
                    "equipment": true,
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
            
            {data: 'part_request_id', name: 'part_request_id', sortable: true, visible: true},
            {data: 'request_date', name: 'request_date'},
            {data: 'hospital_name', name: 'hospital_name', sortable: true, visible: true},
            {data: 'contact_details', name: 'contact_details'},
            {data: 'part_request_items', name: 'part_request_items', sortable: true, visible: true},
            {data: 'date_needed', name: 'date_needed'},
            {data: 'order_total', name: 'order_total', class: 'bg-light text-bold'},
            {data: 'order_request_status', name: 'order_request_status'},
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
            table.column('city:name').visible($('#city').prop('checked'));
            table.column('type:name').visible($('#type').prop('checked'));
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