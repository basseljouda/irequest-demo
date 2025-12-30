@extends('layouts.app')

@push('head-script')
<link rel="stylesheet" href="https://cdn.datatables.net/select/1.3.3/css/select.dataTables.min.css" crossorigin="anonymous">

<style>
    table.dataTable td.dt-control:before{
        font-size: 20px;
        display: contents;
    }
    .breakspace{
        white-space:  break-spaces !important;
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
                        <b class="text-primary">@trans(Search Filter)</b>
                    </div>
                </div>
                <div class="card-body form-body">
                    <div class="row">
                        <div class="col-3">
                            <select class="select2 m-b-10 form-control" 
                                    name="filter_asset_type" id="filter_asset_type">
                                <option value="">@trans(Asset Type): @trans(All)</option>
                                <option value="whole">Whole</option>
                                <option value="parts">Parts</option>
                                <option value="accessories">Accessories</option>
                                
                            </select>
                        </div>
                        <div class="col-3">
                            <div class="form-group">
                                <select class="select2 m-b-10 form-control" 
                                        placeholder="" name="filter_category" id="category" >
                                    <option value="">@trans(Category/SubCategory): @trans(All)</option>
                                    @foreach ($categories as $item)
                                    <option {{selected(isset($asset) ? $asset->category : '',$item->id)}} value="{{ $item->id }}">{{ ucwords($item->title) }}</option>   
                                    @endforeach
                                </select>
                            </div>
                        </div>
                     
       
                        <div class="col-3">
                            <div class="form-group">
                                <select class="select2 m-b-10 form-control" 
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
                    <table id="myTable" class="table table-hover">
                        <thead>
                            <tr>
                                <th></th>
                                <th style="text-align: center"><input type="checkbox" id="select_all" /></th>
                                <th>@trans(Product ID)</th>
                                <th>@trans(Category/SubCategory)</th>
                                <th>@trans(Manufacturer)</th>
                                <th>@trans(Model)</th>
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
    <script>
        $(document).ready(function() {
            
            table.button().add(5, {
                        text: '<i class="fa fa-check text-info" ></i> @trans(Order Items)',
                        "titleAttr": '@trans(Select items to order)',
                        "className": "ecatalog custom-button btn btn-default",
                        action: function () {
                            orderItems();
                        }
                    } ); 
                    $('button.custom-button').removeClass('dt-button');
        });
    </script>

@include('vendor.datatables.scripts')

<script src="https://cdn.datatables.net/select/1.3.3/js/dataTables.select.min.js"></script>
<script>
    let options = {
        pageLength: 20,
        responsive: false,
        scrollX: true,
        scrollCollapse: true,
        order: [[1, 'desc']],
        
        columnDefs: [{
                orderable: false,
                className: 'noexport select-checkbox',
                targets: 1
            }],
        select: {
            style: 'multi',
            selector: 'td:nth-child(2)'
        },
        ajax: {'url': "{!! route('ecatalog.index') !!}",
            "data": function (d) {
                var result = {};
                $.each($('form#filter').serializeArray(), function () {
                    result[this.name] = this.value;
                });
                return $.extend({}, d, result);
            }
        },
        fnDrawCallback: function (oSettings) {
            

            $("body").tooltip({
                selector: '[data-toggle="tooltip"]'
            });
        },

        columns: [
            {
                "className":      'dt-control',
                "searchable": false,
                "orderable":      false,
                "data":           "thumb",
                "defaultContent": ''
            },
            {data: 'chk', name: 'chk', sortable: false, searchable: false},
            {data: 'id', name: 'models.id'},
            {data: 'title', name: 'categories.title'},
            {data: 'mt', name: 'manufacturer.title'},
            {data: 'model_name', name: 'model_name'},
            
            
        ]
    };
    var table = $.dataTable(options);
    
   
function format ( d ) {
    
    return d.info;
}  
$('#myTable tbody').on('click', 'td.dt-control', function () {
        var tr = $(this).closest('tr');
        var row = table.row( tr );
        if ($(row.data().tasks).find('tr').length === 1)
            return;
        //console.log($(row.data().tasks).find('tr').length);
        if ( row.child.isShown() ) {
            row.child.hide();
            tr.removeClass('shown');
        }
        else {
            row.child( format(row.data()) ).show();
            tr.addClass('shown');
        }
    } );
    
    
    function orderItems() {
        var count = table.rows({selected: true}).count();

        if (count <= 0) {
            swal("@trans(Please select items to order)!",'@trans(Click on checkbox at first column)');
            return 0;
        }
        let values = [];
        for (let i = 0; i < table.rows({selected: true}).data().length; i++) {
            values[i] = table.rows({selected: true}).data()[i]['name'];
        }
        //window.open('/inventory-items/print?values=' + JSON.stringify(values), '_blank');
        window.location = '/order-catalog?values=' + JSON.stringify(values);
    }

    $('body').on('click', '#select_all', function () {
        if ($('#select_all:checked').val() === 'on')
            table.rows().select();
        else
            table.rows().deselect();
    });
    $('form#filter select').change(function () {
                table.draw();
            });
</script>
@endpush