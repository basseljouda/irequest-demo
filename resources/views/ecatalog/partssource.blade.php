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
                        <div class="col">
                            <div class="form-group">
                                <input type="text" class="form-control" id="filter_search" name="filter_search" placeholder="Enter (P/N, description, model, keyword) to search">
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <button type="button" id="apply-filters" class="btn btn-info"><i class="fa fa-check"></i> @lang('Search')</button>
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
                    <p><a class='btn btn-default'  id='export_excel' href='javascript:;'>Export to Excel with Images</a></p>
                    <table id="myTable" class="dataTable responsive table" style="width: 100%">
                        <thead>
                            <tr class="nowrap">
                                
                                <th></th>
                                <th>id</th>
                                <th>Title</th>
                                <th>OEM P/N</th>
                                <th>Brand</th>
                                <th>Description</th>
                                <th>Models</th>
                                <th>PS Price</th>
                                <th>iMed New Price</th>
                                <th>iMed Refurbished Price</th>
                                <th>Qty</th>
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



@include('vendor.datatables.scripts')

<script>
    $(document).ready(function () {
        let options = {
            dom: 'Brtip',
            pageLength: 10,
            "ordering": false,
            responsive: false,
            scrollX: true,
        scrollCollapse: true,
            ajax: {'url': "{!! route('parts.catalog') !!}",
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
                /*{ data: 'image', name:'image',title: 'Image', render: function(data) {
                return '<img src="data:image/png;base64,' + data + '" alt="Image">';
            }},*/
    
                {data: 'thumbnailUrl', name: 'thumbnailUrl'},
                {data: 'id', name: 'id'},
                {data: 'title', name: 'title'},
                {data: 'partNumber', name: 'partNumber'},
                {data: 'brand', name: 'brand'},
                {data: 'description', name: 'description'},
                {data: 'models', name: 'models'},
                {data: 'price', name: 'price'},
                {data: 'price_imed', name: 'price_imed'},
                {data: 'price_imed_ref', name: 'price_imed_ref'},
                {data: 'qty', name: 'qty'},
                {data: 'action', name: 'action', sortable: false, searchable: false, className: 'text-left nowrap'}
            ]


        };
        var table = $.dataTable(options);

        $('#apply-filters').click(function () {
            table.draw();
        });
        
        $('body').on('click', '.add-to-inventory', function(){
        var id = $(this).attr('row-data');
        
        
        var url = "{{ route("ps.showinv", ":id")}}";
        url = url.replace(':id', id);

        $('#modelHeading').html('Role Members');
        showModal(url);
    });

    });
    
    $('#export_excel').on('click', function () {
    var searchValue = $('#filter_search').val();
    
    if (searchValue == '')
        return false;

    // Redirect to export route with the filter parameter
    window.location.href = "{{ route('psproducts.export') }}?filter_search=" + encodeURIComponent(searchValue);
});

</script>

@endpush