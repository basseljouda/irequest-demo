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
                        <div class="col-md-4">
                            <div class="form-group">
                                <select title="Inventory Selection"  class="select2 items m-b-10 form-control " required="" 
                                        data-placeholder="Select Inventory" id="inventory">
                                    <option value=""></option>
                                    @foreach ($inventories as $item)
                                    <option {{selected(isset($order) ? $order->inventory_id : '',$item->id)}} value="{{ $item->id }}">{{ ucwords($item->name) }}</option>   
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-1">
                            <div class="form-group">
                                <button type="button" id="apply-filters" class="btn btn-info btn-xs"><i class="fa fa-check"></i> @lang('app.apply')</button>
                            </div>
                        </div>
                        <div class="col-1">
                            <div class="form-group">
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
                    <table id="myTable" class="table table-bordered table-striped orders-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Inventory</th>
                                <th>Equipment</th>
                                <th>Serial</th>
                                <th>DOT#</th>
                                <th>Available</th>
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

let options = {"order": [[1, 'desc']],
    ajax: {'url': '{!! route('reports.equipments-data') !!}',
        "data": function (d) {
            return $.extend({}, d, {
                "inventory": $('#inventory').val(),
            });
        }
    },
    columns: [
        {data: 'DT_Row_Index', name: 'dynamic', sortable:false, searchable: false},
        {data: 'invname', name: 'inventories.name'},
        {data: 'eqname', name: 'equipments.name'},
        {data: 'serial_value', name: 'serial_value'},
        {data: 'dot_value', name: 'dot_value'},
        {data: 'balance', name: 'balance'},
    ]
};

let table = $.dataTable(options);

$('input[name=groupby]').change(function (){
    table.draw();
    $('#group_colname').text($(this).next().text());
});

$('#apply-filters').click(function () {
    table.draw();
})
$('#reset-filters').click(function () {
    $('#filter-form')[0].reset();
    $('.select2').change();
    table.draw();
});

</script>


@endpush