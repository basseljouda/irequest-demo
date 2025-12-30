@extends('layouts.app')


@permission('add_inventory')
@section('create-button')
    <!--a href="{{ route('admin.inventory.create') }}" class="modal-link btn btn-info btn-sm m-l-15"><i
                class="fa fa-plus-circle"></i> New Inventory</a-->
@endsection
@endpermission

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive m-t-40">
                        <table id="myTable" class="table table-bordered table-striped table-hover">
                            <thead>
                            <tr>
                                <th></th>
                                <th>#</th>
                                <th>Code</th>
                                <th>Description</th>
                                <th>Count</th>
                                <th>Address</th>
                                <th>Notes</th>
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
   @include('admin.inventory._items')

    <script>
       var template = Handlebars.compile($("#details-template").html());
       let options = {
            ajax: '{!! route('admin.inventory.data') !!}',
            order: [[1, 'desc']],
            columns: [
                {data: null,defaultContent: '', className: 'details-control',orderable: false,searchable: false},
                {data: 'id', name: 'id'},
                {data: 'code', name: 'code'},
                {data: 'name', name: 'name'},
                {data: 'count', name: 'count'},
                {data: 'address_full', name: 'address_full'},
                {data: 'notes', name: 'notes'}
            ]
        };
        var table = $.dataTable(options);
        var inventory_items;
        
        $('#myTable tbody').on('click', 'td.details-control', function () {
        var tr = $(this).closest('tr');
        var row = table.row(tr);
        var tableId = 'items-' + row.data().id;

        if (row.child.isShown()) {
            row.child.hide();
            tr.removeClass('shown');
            $('#myTable').find('tr').css('opacity','1.0');
        } else {
            $('#myTable').find('tr').css('opacity','0.2');
            $(this).closest('tr').css('opacity','1.0');
            row.child(template(row.data())).show();
            inventory_items = initItems(tableId, row.data());
            tr.addClass('shown');
            tr.next().find('td').addClass('item-table');
        }
    });

    function initItems(tableId, data) {
        return $('#' + tableId).DataTable({
            
            processing: true,
            
            serverSide: true,
            order: [[0, 'desc']],
            ajax: data.details_url,
            columns: [
                { data: 'id', name: 'inventory_items.id'},
                { data: 'name', name: 'equipments.name' },
                { data: 'serial_value', name: 'serial_value',className: 'serial_value' },
                { data: 'dot_value', name: 'dot_value',className: 'dot_value' },
                { data: 'balance', name: 'balance', className: 'text-center bold' },
                //{ data: 'last_update', name: 'last_update' },
                //{ data: 'updated_by', name: 'updated_by' },
                { data: 'price', name: 'price', visible: false }
            ],
            fnDrawCallback: function (oSettings) {
                $.scroll($(this).closest('tr').offset().top - 100);
            }
            
        });
    }
    </script>
@endpush