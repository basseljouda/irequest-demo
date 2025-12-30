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

<form id="filter">
    <div class="row" style="margin-top: 10px">
        <div class="col-md-12">
            <div class="card search-filter">
                <div class="card-header ui-sortable-handle">
                    <div class="float-left">
                        <b class="text-primary">@trans(Search Filters)</b>
                    </div>
                </div>
                <div class="card-body form-body">
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <select name="filter_company" id="filter_company" class="select2 form-control select-idn">
                                    <option value=""><i class=""></i>@trans(IDN): @lang('app.all')</option>
                                    @foreach ($companies as $item)
                                    <option value="{{ $item->id }}">{{ ucwords($item->company_name) }}</option>   
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col">
                           <div class="form-group">
                                <select  id="filter_hospital" name="filter_hospital" class="select2 form-control select-site">
                                    <option value=""><i class="icon-badge"></i>@trans(Sites): @lang('app.all')</option>
                                    @foreach ($hospitals as $item)
                                    <option value="{{ $item->id }}">{{ ucwords($item->name) }}</option>   
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                               
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                               
                            </div>
                        </div>
                        
                        <div class="col">
                            <div class="form-group">
                                
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
                            <tr class="nowrap">
                                <th>@trans(Order ID)</th>
                                <th>@trans(Site)</th>
                                <th>@trans(Date)</th>
                                <th>@trans(Item)</th>
                                <th>@trans(Details)</th>
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


let options = {"order": [[0, 'desc']],responsive:false,
    ajax: {'url': '{!! route('pickup_issues.data') !!}',
        "data": function (d) {
                        var result = {};
                        $.each($('form#filter').serializeArray(), function () {
                            result[this.name] = this.value;
                        });
                        return $.extend({}, d, result);
                    }
    },
    columns: [
       {data: 'order_id', name: 'orders.order_id', class: 'text-bold text-info'},
            {data: 'hospital_name', name: 'hospitals.name'},
            {data: 'created_at', name: 'created_at'},
            {data: 'asset_title', name: 'orders_equipments.asset_no'},
          //  {data: 'username', name: 'users.name'},
            {data: 'missing_details', name: 'missing_details',class: 'text-truncate-notify'},
            //{data: 'asset_id', name: 'asset_id'},
            {data: 'status', name: 'status'}
            //{data: 'action', name: 'action', sortable: false, searchable: false, className: 'text-left nowrap'}
    ]
};

let table = $.dataTable(options);

$('.select2').change(function () {
    table.draw();
})
$('#reset-filters').click(function () {
    $('#filter-form')[0].reset();
    $('.select2').change();
    table.draw();
});

</script>


@endpush