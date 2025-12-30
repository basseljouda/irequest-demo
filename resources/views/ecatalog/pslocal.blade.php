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
@section('create-button')
<a href="{{ route('ecatalog.create') }}" class="modal-link btn btn-info btn-sm m-l-15"><i
        class="fa fa-plus-circle"></i> @trans(Add to Inventory)</a>
@endsection
@section('content')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive m-t-40">
                    <table id="myTable" class="dataTable responsive table" style="width: 100%">
                        <thead>
                            <tr class="nowrap">
                                <th></th>
                                <th>#</th>
                                <th>Title</th>
                                <th>OEM P/N</th>
                                <th>Brand</th>
                                <th>Description</th>
                                <th>PS Price</th>
                                <th>iMed Price</th>
                                <th>iMed Refurbished Price</th>
                                <th>Qty</th>
                                <th>Location</th>
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
            dom: 'Bfrtip',
            pageLength: 10,
            responsive: false,
            scrollX: true,
        scrollCollapse: true,
            ajax: {'url': "{!! route('parts.ps') !!}",
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
                {data: 'thumbnailUrl', name: 'thumbnailUrl'},
                {data: 'myid', name: 'myid'},
                {data: 'title', name: 'title'},
                {data: 'partNumber', name: 'partNumber'},
                {data: 'brand', name: 'brand'},
                {data: 'description', name: 'description'},
                {data: 'price', name: 'price'},
                {data: 'price_imed', name: 'price_imed'},
                {data: 'price_imed_ref', name: 'price_imed_ref'},
                {data: 'qty', name: 'qty'},
                {data: 'location', name: 'location'},
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
</script>

@endpush