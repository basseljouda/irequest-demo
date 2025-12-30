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
                                <select class="select2 m-b-10 form-control apply-filters" 
                                        placeholder="" name="filter_availability" id="availability">
                                    <option value="">@trans(Availability): @trans(All)</option>
                                    <option  value="available">@trans(Available)</option>
                                    <option  value="in rental">@trans(In Rental)</option>
                                    <option  value="work order">@trans(Work Order)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <select class="select2 m-b-10 form-control apply-filters" 
                                        placeholder="" name="filter_category" id="category" >
                                    <option value="">@trans(Category/SubCategory): @trans(All)</option>
                                    @foreach ($categories as $item)
                                    <option {{selected(isset($asset) ? $asset->category : '',$item->id)}} value="{{ $item->id }}">{{ ucwords($item->title) }}</option>   
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <select class="select2 m-b-10 form-control apply-filters" 
                                        placeholder="" name="filter_warehouse_location" id="warehouse_location" >
                                    <option value="">@trans(Warehouse Location): @trans(All)</option>
                                    @foreach ($locations as $item)
                                    <option {{selected(isset($asset) ? $asset->warehouse_location : '',$item->id)}} value="{{ $item->id }}">{{ ucwords($item->title) }}</option>   
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="col">
                            <div class="form-group">
                                <select class="select2 m-b-10 form-control apply-filters" 
                                        placeholder="" name="filter_manufacturer" id="manufacturer">
                                    <option value="">@trans(Manufacturer): @trans(All)</option>
                                    @foreach ($list as $item)
                                    <option {{selected(isset($asset) ? $asset->manufacturer : '',$item->id)}} value="{{ $item->id }}">{{ ucwords($item->title) }}</option>   
                                    @endforeach
                                </select>
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
                                <th>@trans(Rental ID)</th>
                                <th>@trans(Category/SubCategory)</th>
                                <th>@trans(Manufacturer)</th>
                                <th>@trans(Model Name)</th>
                                <th>@trans(Serial No)</th>
                                <th>@trans(Availability)</th>
                                <!--th>@trans(Asset ID)</th-->
                                <th>@trans(Warehouse Location)</th>
                                <th>@trans(Old Rental ID)</th>
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


let options = {"order": [[0, 'desc']],
    ajax: {'url': '{!! route('admin.inventory.assets') !!}',
        "data": function (d) {
                        var result = {};
                        $.each($('form#filter').serializeArray(), function () {
                            result[this.name] = this.value;
                        });
                        return $.extend({}, d, result);
                    }
    },
    columns: [
       {data: 'rental_asset_id', name: 'rental_asset_id', class: 'text-bold text-info'},
            {data: 'title', name: 'categories.title'},
            {data: 'mt', name: 'manufacturer.title'},
            {data: 'model_name', name: 'model_name'},
            {data: 'serial', name: 'serial'},
            {data: 'availability', name: 'availability'},
            //{data: 'asset_id', name: 'asset_id'},
            {data: 'warehouse', name: 'locations.title'},
            {data: 'rental_old', name: 'rental_old'},
            {data: 'action', name: 'action', sortable: false, searchable: false, className: 'text-left nowrap'}
    ]
};

let table = $.dataTable(options);

$('.apply-filters').change(function () {
    table.draw();
})
$('#reset-filters').click(function () {
    $('#filter-form')[0].reset();
    $('.select2').change();
    table.draw();
});

</script>


@endpush