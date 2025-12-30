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
                        @trans(Report Filters)
                    </div>
                    <div class="float-right">
                        <label class="radio-inline pl-lg-2">
                            <input checked="" class="form-check-input" type="checkbox" name="order_id" id="order_id" >
                            <label class="form-check-label" for="order_id">
                                @trans(Order ID)
                            </label>
                        </label>
                        <label class="radio-inline pl-lg-2">
                            <input class="form-check-input" type="checkbox" name="room_no" id="room_no" >
                            <label class="form-check-label" for="room_no">
                                @trans(Room No)
                            </label>
                        </label>
                        <label class="radio-inline pl-lg-2">
                            <input class="form-check-input" type="checkbox" name="patient_name" id="patient_name" >
                            <label class="form-check-label" for="patient_name">
                                @trans(Patient)
                            </label>
                        </label>
                        <label class="radio-inline pl-lg-2">
                            <input class="form-check-input" type="checkbox" name="hospital" id="hospital" checked="">
                            <label class="form-check-label" for="hospital">
                                @trans(Site)
                            </label>
                        </label>
                        <label class="radio-inline pl-lg-2 hide">
                            <input class="form-check-input" type="checkbox" name="city" id="city" >
                            <label class="form-check-label" for="city">
                                @trans(Market)
                            </label>
                        </label>
                        <label class="radio-inline pl-lg-2 hide">
                            <input class="form-check-input" type="checkbox" name="type" id="type" >
                            <label class="form-check-label" for="type">
                                @trans(Site Type)
                            </label>
                        </label>
                        <label class="radio-inline pl-lg-2">
                            <input class="form-check-input" checked="" type="checkbox" name="category" id="category">
                            <label class="form-check-label" for="category">
                                @trans(Category)
                            </label>
                        </label>
                        <label class="radio-inline pl-lg-2">
                            <input class="form-check-input" checked="" type="checkbox" name="manufacturer" id="manufacturer" >
                            <label class="form-check-label" for="manufacturer">
                                @trans(Manufacturer)
                            </label>
                        </label>
                        <label class="radio-inline pl-lg-2">
                            <input class="form-check-input" checked="" type="checkbox" name="model" id="model" >
                            <label class="form-check-label" for="model">
                                @trans(Model)
                            </label>
                        </label>
                        <!--label class="radio-inline pl-lg-2">
                            <input class="form-check-input" type="checkbox" name="asset" id="asset" >
                            <label class="form-check-label" for="asset">
                                @trans(Asset)<span class="small">&nbsp;(@trans(Category),@trans(Manufacturer),@trans(Model))</span>
                            </label>
                        </label-->
                        
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
                                   <option value="">@trans(Sites) @trans(All)</option>
                                    @foreach ($hospitals as $item)
                                    <option value="{{ $item->id }}">{{ ucwords($item->name) }}</option>   
                                    @endforeach

                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 col-12">
                            <div class="form-group">
                                <select class="select2 form-control select-combo" id="filter_equipment">
                                   <option value=""><i class="icon-badge"></i>@trans(Equipment): @trans(All)</option>
                                    @foreach($equipments as $equipment)
                                    <option value="{{ $equipment->id }}">{{ ucfirst($equipment->name) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                    </div>
                    <div class="row hide">
                        <div class="col">
                            <div class="form-group">
                                <select class="select2 m-b-10 form-control apply-filters" 
                                        placeholder="" name="filter_category" id="filter_category" >
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
                                        placeholder="" name="filter_manufacturer" id="filter_manufacturer">
                                    <option value="">@trans(Manufacturer): @trans(All)</option>
                                    @foreach ($list as $item)
                                    <option {{selected(isset($asset) ? $asset->manufacturer : '',$item->id)}} value="{{ $item->id }}">{{ ucwords($item->title) }}</option>   
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <select class="product select2 m-b-10 form-control" 
                                        placeholder="" name="filter_model_name" id="filter_model_name">
                                    <option value="">@trans(Models): @trans(All)</option>
                                    @foreach ($models as $item)
                                    <option value="{{ $item->model_name }}">{{ ucwords($item->model_name) }}</option>   
                                    @endforeach
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
                                <th>@trans(OrderID)</th>
                                <th>@trans(Patient)</th>
                                <th>@trans(Room No)</th>
                                <th>@trans(Site)</th>
                                <th>@trans(Market)</th>
                                <th>@trans(Site Type)</th>
                                <th>@trans(Asset)</th>
                                <th>@trans(Category)</th>
                                <th>@trans(Manufacturer)</th>
                                <th>@trans(Model Name)</th>
                                <th>@trans(Quantity)</th>
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


    let options = {"order": [0], "pageLength": 1000, dom: '<"top"Bi>rt<"bottom"p>',
        ajax: {'url': "{!! route('reports.assets-show') !!}",
            "data": function (d) {
                return $.extend({}, d, {
                    "filter_hospital": $('#filter_hospital').val(),
                    "filter_company": $('#filter_company').val(),
                    "filter_equipment": $('#filter_equipment').val(),
                    "filter_model_name": $('#filter_model_name').val(),
                    "filter_category": $('#filter_category').val(),
                    "filter_manufacturer": $('#filter_manufacturer').val(),
                    "hospital": $('#hospital').prop('checked'),
                    "category": $('#category').prop('checked'),
                    "city": $('#city').prop('checked'),
                    "type": $('#type').prop('checked'),
                    "manufacturer": $('#manufacturer').prop('checked'),
                    "model": $('#model').prop('checked'),
                    "asset": $('#asset').prop('checked'),
                    "order_id": $('#order_id').prop('checked'),
                    "patient_name": $('#patient_name').prop('checked'),
                    "room_no": $('#room_no').prop('checked'),
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
        columns: [
            {data: 'DT_Row_Index', name: 'dynamic', sortable: false, searchable: false, visible: false},
            
            {data: 'order_id', name: 'order_id', sortable: true, visible: false},
            {data: 'patient_name', name: 'patient_name', sortable: true, visible: false},
            {data: 'room_no', name: 'room_no', sortable: true, visible: false},
            
            {data: 'hospital', name: 'hospital', sortable: true, visible: false},
            {data: 'city', name: 'city', sortable: true, visible: false},
            {data: 'type', name: 'type', sortable: true, visible: false},
            {data: 'asset', name: 'asset', sortable: true, visible: false},
            {data: 'category', name: 'category', sortable: true, visible: false},
            {data: 'manufacturer', name: 'manufacturer', sortable: true, visible: false},
            {data: 'model', name: 'model', sortable: true, visible: false},
            {data: 'OrdersCount', name: 'OrdersCount', visible: true,sortable: true}
        ],
        fnDrawCallback: function (oSettings) {
            $("body").removeClass("control-sidebar-slide-open");
            $("body").tooltip({
                selector: '[data-toggle="tooltip"]'
            });


            table.column('hospital:name').visible($('#hospital').prop('checked'));
            table.column('category:name').visible($('#category').prop('checked'));
            table.column('model:name').visible($('#model').prop('checked'));
            table.column('asset:name').visible($('#asset').prop('checked'));
            table.column('manufacturer:name').visible($('#manufacturer').prop('checked'));
            table.column('city:name').visible($('#city').prop('checked'));
            table.column('type:name').visible($('#type').prop('checked'));
            
            table.column('order_id:name').visible($('#order_id').prop('checked'));
            table.column('patient_name:name').visible($('#patient_name').prop('checked'));
            table.column('room_no:name').visible($('#room_no').prop('checked'));
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