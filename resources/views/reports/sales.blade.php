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

                        <label class="radio-inline pl-lg-2 hide">
                            <input type="radio"  name="groupby" value="" >
                            <span>Site</span>
                        </label>
                        <label class="radio-inline pl-lg-2 hide">
                            <input type="radio"  name="groupby" value="monthyear">
                            <span>Month</span>
                        </label>
                        <label class="radio-inline pl-lg-2 hide">
                            <input type="radio"  name="groupby" value="quarter">
                            <span>Quarter</span>
                        </label>
                        <label class="radio-inline pl-lg-2 hide">
                            <input type="radio"  name="groupby" value="year">
                            <span>Year</span>
                        </label>
                        <label class="radio-inline pl-lg-2">
                            <input class="form-check-input" type="checkbox" name="hospital" id="hospital" checked="">
                            <label class="form-check-label" for="hospital">
                                Site
                            </label>
                        </label>
                        <label class="radio-inline pl-lg-2">
                            <input class="form-check-input" type="checkbox" name="city" id="city" >
                            <label class="form-check-label" for="city">
                                @trans(Market)
                            </label>
                        </label>
                        <label class="radio-inline pl-lg-2">
                            <input class="form-check-input" type="checkbox" name="type" id="type" >
                            <label class="form-check-label" for="type">
                                @trans(Site Type)
                            </label>
                        </label>
                        <label class="radio-inline pl-lg-2 hide">
                            <input class="form-check-input" type="checkbox" name="costcenter" id="costcenter">
                            <label class="form-check-label" for="costcenter">
                                Cost Center
                            </label>
                        </label>
                        <label class="radio-inline pl-lg-2 hide">
                            <input type="radio"  name="groupby" value="">
                            <span>Order ID</span>
                        </label>
                        <label class="radio-inline pl-lg-2">
                            <input class="form-check-input" type="checkbox" name="orders" id="orders" >
                            <label class="form-check-label" for="orders">
                                Order Id
                            </label>
                        </label>
                        <label class="radio-inline pl-lg-2">
                            <input class="form-check-input" type="checkbox" name="equipment" id="equipment" >
                            <label class="form-check-label" for="equipment">
                                Equipment
                            </label>
                        </label>
                        <label class="radio-inline pl-lg-2 hide">
                            <input class="form-check-input" type="checkbox" name="asset" id="asset" >
                            <label class="form-check-label" for="asset">
                                Asset
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
                                <select class="select2 form-control select-combo" id="filter_equipment">
                                    <option value=""><i class="icon-badge"></i>Equipment: All</option>
                                    @foreach($equipments as $equipment)
                                    <option value="{{ $equipment->id }}">{{ ucfirst($equipment->name) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-3 col-sm-6 col-12">
                            <div class="form-group">
                                <select name="" id="filter_costcenter" class="select2 form-control">
                                    <option value=""><i class="icon-badge"></i>@lang('app.costcenter'): @trans(All)</option>
                                    @foreach ($cost_centers as $item)
                                    <option value="{{ $item->id }}">{{ ucwords($item->name) }}</option>   
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="col hide">
                            <div class="form-group">
                                <label class="radio-inline pl-lg-2">
                                    <input class="form-check-input" type="checkbox" id="accepted" value="accepted" name="status[]" checked="">
                                    <label class="form-check-label" for="accepted" >
                                        Accepted
                                    </label>
                                </label>
                                <label class="radio-inline pl-lg-2">
                                    <input class="form-check-input" type="checkbox" id="completed" value="completed" name="status[]" checked="">
                                    <label class="form-check-label" for="completed" >
                                        Completed
                                    </label>
                                </label>
                                <label class="radio-inline pl-lg-2">
                                    <input class="form-check-input" type="checkbox" id="pickedup" value="pickedup" name="status[]" checked="">
                                    <label class="form-check-label" for="pickedup" >
                                        Picked Up
                                    </label>
                                </label>
                                <label class="radio-inline pl-lg-2">
                                    <input class="form-check-input" type="checkbox" id="reassigned" value="reassigned" name="status[]" checked="">
                                    <label class="form-check-label" for="reassigned" >
                                        Reassigned
                                    </label>
                                </label>
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
                        <div class="col hide">
                            <div class="form-group">
                                <div class="input-group">
                                    <select id="sign" name="sign" class="select2 form-control sign">
                                        <option value=""><i class="icon-badge"></i>@trans(Select) @trans(Billed Amount)</option>
                                        <option value="=" >@trans(Billed Amount) =</option>
                                        <option value=">" >@trans(Billed Amount) ></option>
                                        <option value="<" >@trans(Billed Amount) <</option>
                                    </select>

                                    <input type="number" class="form-control" id="orders_total" value="" name="orders_total" placeholder="value">
                                </div>
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
                    <table id="myTable" style="width:100%" class="table table-bordered table-hover table-light">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>@trans(Order ID)</th>
                                <th>@trans(Site)</th>
                                <th>@trans(Market)</th>
                                <th>@trans(Site Type)</th>
                                <th>@trans(Cost Center)</th>
                                <th>@trans(Equipment)</th>
                                <th>@trans(Inventory Asset)</th>
                                <th>@trans(Orders Count)</th>
                                <th>@trans(Rental Days)</th>
                                <th>@trans(Billing Amount)</th>
                                <th>@trans(Delivery Time AVG)</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th></th><th></th><th></th><th></th><th></th><th></th><th></th>
                                <th id="total_label" class="text-right"></th>
                                <th>0</th>
                                <th style="color: var(--red) !important;">0</th>
                                <th style="color: var(--red) !important;">$0</th>
                                <th style="color: var(--red) !important;"></th>
                                <th style="color: var(--red) !important;"></th>
                            </tr>
                        </tfoot>
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
function formatTime(diff) {
    if (isNaN(diff))
        return "";
    var h = 0, m = diff;
    //console.log(diff);
  if (Math.abs(diff) >= 60) {
            h = diff / 60 | 0,
            m = diff % 60 | 0;
            var diff_label = Math.abs(h) + " Hour " +  Math.floor(Math.abs(m)) + " Minute";
            } else {
            var diff_label =  Math.floor(Math.abs(m)) + " Minutes";
            }
  //if (sign == '-')
     return '<span class="text-success">' + diff_label + '</span>';
 //else
   //  return '<span class="text-danger">' + sign + hours + " hours " + remainingMinutes + " minutes" + '</span>';

  //return sign + hours + " hours " + remainingMinutes + " minutes";
}
</script>
<script>
    $('#date-start').on('change', function (e, date)
    {
        $('#date-end').bootstrapMaterialDatePicker('setMinDate', date);
    });

    const startOfMonth = moment().startOf('month').format('MM/DD/YYYY');
    const endOfMonth = moment().format('MM/DD/YYYY');
    $('#date-start').val(startOfMonth);
    $('#date-end').val(endOfMonth);


    let options = {"order": [0], "pageLength": 1000, dom: '<"top"Bi>rt<"bottom"p>',
        ajax: {'url': "{!! route('reports.sales-data') !!}",
            "data": function (d) {
                return $.extend({}, d, {
                    "filter_hospital": $('#filter_hospital').val(),
                    "filter_equipment": $('#filter_equipment').val(),
                    "filter_company": $('#filter_company').val(),
                    "groupby":"", //document.querySelector('input[name="groupby"]:checked').value,
                    "hospital": $('#hospital').prop('checked'),
                    "costcenter": $('#costcenter').prop('checked'),
                    "city": $('#city').prop('checked'),
                    "type": $('#type').prop('checked'),
                    "orders": $('#orders').prop('checked'),
                    "equipment": $('#equipment').prop('checked'),
                    "asset": $('#asset').prop('checked'),
                    "filter_costcenter": $('#filter_costcenter').val(),
                    "startDate": $('#date-start').val(),
                    "endDate": $('#date-end').val(),
                    "sign": $('#sign').val(),
                    "orders_total": $('#orders_total').val(),
                    status: $('input[name="status[]"]:checked').map(function () {
                        return this.value;
                    }).get()
                });
            }
        },
        "footerCallback": function (row, data, start, end, display) {
            var api = this.api(), data;
            // converting to interger to find total
            var intVal = function (i) {
                return typeof i === 'string' ?
                        i.replace(/[\$,]/g, '') * 1 :
                        typeof i === 'number' ?
                        i : 0;
            };

            // computing column Total of the complete result 
            var daysTotal = api
                    .column('OrdersCount:name')
                    .data()
                    .reduce(function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

            var ordersTotal = api
                    .column('total_days:name')
                    .data()
                    .reduce(function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

            var sumTotal = api
                    .column('OrdersSum:name')
                    .data()
                    .reduce(function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

        let totalMinutes = 0;
        let count = 0;
        
        // Iterate over each row's 'average_delivery_time' column
        data.forEach(function (row) {
            let timeStr = row.average_delivery_time; // Replace 'delivery_time' with your column name
            
            if (timeStr && timeStr !== 'null') {
                let [hours, minutes] = timeStr.match(/\d+/g).map(Number); // Extract hours and minutes
                totalMinutes += (hours * 60) + minutes;
                count++;
            }
        });
        
        // Calculate average in minutes
        let avgMinutes = count > 0 ? totalMinutes / count : 0;
        
        // Convert average minutes back to "XX hours YY minutes"
        let avgHours = Math.floor(avgMinutes / 60);
        let avgRemainingMinutes = Math.floor(avgMinutes % 60);
        let formattedAvgTime = `${avgHours} hours ${avgRemainingMinutes} minutes`;

            $(api.column('OrdersCount:name').footer()).html(daysTotal);
            $(api.column('total_days:name').footer()).html(ordersTotal);
            $(api.column('OrdersSum:name').footer()).html('$' + sumTotal.toFixed(3));
            $(api.column('average_delivery_time:name').footer()).html(formattedAvgTime);
            //$("#total_label").html('Totals:');

        },
        columns: [
            {data: 'DT_Row_Index', name: 'dynamic', sortable: false, searchable: false, visible: false},
            {data: 'orders', name: 'orders', sortable: true, visible: false},
            {data: 'hospital', name: 'hospital', sortable: true, visible: false},
            {data: 'city', name: 'city', sortable: true, visible: false},
            {data: 'type', name: 'type', sortable: true, visible: false},
            {data: 'costcenter', name: 'costcenter', sortable: true, visible: false},
            {data: 'equipment', name: 'equipment', sortable: true, visible: false},
            {data: 'asset', name: 'asset', sortable: true, visible: false},
            {data: 'OrdersCount', name: 'OrdersCount', visible: false},
            {data: 'total_days', name: 'total_days', class: 'bg-light'},
            {data: 'OrdersSum', name: 'OrdersSum', class: 'bg-light text-bold'},
            {data: 'average_delivery_time', name: 'average_delivery_time'},
            {data: 'delivery_time', name: 'delivery_time',visible:false}
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