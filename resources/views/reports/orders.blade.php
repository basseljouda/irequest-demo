@extends('layouts.app') 
@push('head-script')
<link rel="stylesheet" href="{{ asset('assets/node_modules/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css') }}">

<style>
    table.table-bordered.dataTable tbody th, table.table-bordered.dataTable tbody td{
     white-space: nowrap!important;   
    }
    .mb-20 {
        margin-bottom: 20px
    }
    .border-right{
        border-right: 1px #47a5a8a6 solid !important;
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
                        <div class="col-md-2 col-sm-4 col-8">
                            <div class="form-group">
                                <select  id="filter_company"  name="filter_company"  class="select2 form-control select-idn">
                                    <option value=""><i class="icon-badge"></i>@trans(IDN): @lang('app.all')</option>
                                    @foreach ($companies as $item)
                                    <option value="{{ $item->id }}">{{ ucwords($item->company_name) }}</option>   
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-3 col-sm-6 col-12">
                            <div class="form-group">
                                <select id="filter_hospital" name="filter_hospital" class="select2 form-control select-site">
                                    <option value="">@trans(Sites): @trans(All)</option>
                                    @foreach ($hospitals as $item)
                                    <option value="{{ $item->id }}">{{ ucwords($item->name) }}</option>   
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 col-12">
                            <div class="form-group">
                                <select class="select2 form-control select-combo" id="filter_equipment" name="filter_equipment">
                                    <option value=""><i class="icon-badge"></i>Equipment: All</option>
                                    @foreach($equipments as $equipment)
                                    <option value="{{ $equipment->id }}">{{ ucfirst($equipment->name) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-2 col-sm-4 col-8">
                            <div class="form-group">
                                <select name="filter_costcenter" id="filter_costcenter"  class="select2 form-control">
                                    <option value=""><i class="icon-badge"></i>@lang('app.costcenter'): @trans(All)</option>
                                    @foreach ($cost_centers as $item)
                                    <option value="{{ $item->id }}">{{ ucwords($item->name) }}</option>   
                                    @endforeach
                                </select>
                            </div>
                        </div>
                         <div class="col-md-2 col-sm-4 col-8">
                            <div class="form-group">
                                <input type="text" class="form-control" id="order_no" value="" name="order_no" placeholder="Order No">
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
                        <x-date-filter placeholder="Date Range"/>
                        <div class="col-md-3 col-sm-6 col-12 mt-3">
                            <div class="form-group">
                                <button type="button" id="apply-filters" class="btn btn-info"><i class="fa fa-check"></i> @lang('app.apply')</button>
                                <button type="button" id="reset-filters" class="btn btn-default"><i class="fa fa-refresh"></i> @lang('app.reset')</button>
                            </div>
                        </div>
                    </div>
                     <!--div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <div class="input-group">
                                    <select id="sign" name="sign" class="select2 form-control sign">
                                        <option value=""><i class="icon-badge"></i>Orders Total</option>
                                        <option value="=" >=</option>
                                        <option value=">" >></option>
                                        <option value="<" ><</option>
                                </select>
                                    
                                    <input type="number" class="form-control" id="orders_total" value="" name="orders_total" placeholder="value">
                                </div>
                            </div>
                        </div>
                     </div-->
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
                    <table id="myTable" class="table table-bordered table-striped orders-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Year</th>
                                <th>Site</th>
                                <th>@trans(Equipment Combo)</th>
                                <th>@trans(Assets)</th>
                                <!--th>Product</th-->
                                <th>Patient</th>
                                <th>RoomNo</th>
                                <th>Floor</th>
                                <th>Billed</th>
                                <th>Returned</th>
                                <th>Status</th>
                                <th>Jan:Days</th>
                                <th>Jan:Rate</th>
                                <th>Feb:Days</th>
                                <th>Feb:Rate</th>
                                <th>Mar:Days</th>
                                <th>Mar:Rate</th>
                                <th>Apr:Days</th>
                                <th>Apr:Rate</th>
                                <th>May:Days</th>
                                <th>May:Rate</th>
                                <th>Jun:Days</th>
                                <th>Jun:Rate</th>
                                <th>Jul:Days</th>
                                <th>Jul:Rate</th>
                                <th>Aug:Days</th>
                                <th>Aug:Rate</th>
                                <th>Sep:Days</th>
                                <th>Sep:Rate</th>
                                <th>Oct:Days</th>
                                <th>Oct:Rate</th>
                                <th>Nov:Days</th>
                                <th>Nov:Rate</th>
                                <th>Dec:Days</th>
                                <th>Dec:Rate</th>
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
<script src="https://cdn.datatables.net/fixedcolumns/4.0.1/js/dataTables.fixedColumns.min.js"></script>
@include('vendor.datatables.scripts')

<script>
$('#date-start').on('change', function (e, date)
{
    $('#date-end').bootstrapMaterialDatePicker('setMinDate', date);
});

let options = {"order": [[0, 'desc']],
    fixedColumns: {
                            left: 3,
                        },
                        responsive:false,
                        scrollX: true,
                        scrollCollapse: true,
   /* rowCallback: function(row, data, index) {
 
        $(row).find('.border-right').each(function( index ){
            if (parseInt($(this).text()) > 0)
            $(this).find('.border-right').addClass('label label-info');
        });
        
},*/
    ajax: {'url': '{!! route('reports.orders-data') !!}',
        "data": function (d) {
            var result = {};
                $.each($('form#filter-form').serializeArray(), function () {
                    result[this.name] = this.value;
                });
                return $.extend({}, d, result);
        }
    },
    columns: [
        {data: 'theid', name: 'theid',class:"text-bold"},
        {data: 'year', name: 'year'},
        {data: 'hospital', name: 'hospital'},
        {data: 'combo', name: 'combo'},
        {data: 'assets', name: 'assets'},
        {data: 'patient_name', name: 'patient_name'},
        {data: 'room_no', name: 'room_no'},
        /*{data: 'product', name: 'product'},
        {data: 'serial', name: 'serial'},*/
        {data: 'unit_floor', name: 'unit_floor'},
        
        {data: 'bill_started', name: 'bill_started'},
        {data: 'completed_date', name: 'completed_date'},
        {data: 'status', name: 'status',class:"border-right"},
        
        {data: 'Jan', name: 'Jan',class:"label"},
        {data: 'tjan', name: 'tjan',class:"border-right"},
        {data: 'Feb', name: 'Feb',class:"label"},
        {data: 'tfeb', name: 'tfeb',class:"border-right"},
        {data: 'Mar', name: 'Mar',class:"label"},
       {data: 'tmar', name: 'tmar',class:"border-right"}, 
        {data: 'Apr', name: 'Apr',class:"label"},
        {data: 'tapr', name: 'tapr',class:"border-right"},
        {data: 'May', name: 'May',class:"label"},
        {data: 'tmay', name: 'tmay',class:"border-right"},
        {data: 'Jun', name: 'Jun',class:"label"},
        {data: 'tjun', name: 'tjun',class:"border-right"},
        {data: 'Jul', name: 'Jul',class:"label"},
        {data: 'tjul', name: 'tjul',class:"border-right"},
        {data: 'Aug', name: 'Aug',class:"label"},
        {data: 'taug', name: 'taug',class:"border-right"},
        {data: 'Sep', name: 'Sep',class:"label"},
        {data: 'tsep', name: 'tsep',class:"border-right"},
        {data: 'Oct', name: 'Oct',class:"label"},
        {data: 'toct', name: 'toct',class:"border-right"},
        {data: 'Nov', name: 'Nov',class:"label"},
        {data: 'tnov', name: 'tnov',class:"border-right"},
        {data: 'Dec', name: 'Dec',class:"label"},
        {data: 'tdec', name: 'tdec',class:"border-right"}
    ]
};

let table = $.dataTable(options);


$('#apply-filters').click(function () {
/*    let check = false;
    $.each($(".form-check-input"), function(){
                if ($(this).prop('checked'))
                    check = true;
            });
    
    if (!check && $('input[name="groupby"]:checked').val()=='none')
        return;*/
    table.draw();
});
$('#reset-filters').click(function () {
    $('#filter-form')[0].reset();
    $('.select2').change();
    table.draw();
});

</script>


@endpush