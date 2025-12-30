@extends('layouts.app')

@push('head-script')
<link rel="stylesheet" href="{{ asset('assets/node_modules/switchery/dist/switchery.min.css') }}">
@endpush

@permission('add_cost_center')
@section('create-button')
<a href="{{ route('admin.costcenter.create') }}" class="modal-link btn btn-info btn-sm m-l-15"><i
        class="fa fa-plus-circle"></i> @trans(Create New)</a>
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
                                <th>#</th>
                                <th>Code</th>
                                <th>Name</th>
                                <th>Floor/Unit</th>
                                <th>Total</th>
                                <th>Active</th>
                                <th>@trans(Action)</th>
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
<script src="{{ asset('assets/node_modules/switchery/dist/switchery.min.js') }}"></script>
<script>
let options = {
    ajax: '{!! route('admin.costcenter.data') !!}',
    fnDrawCallback: function (oSettings) {
        $("body").tooltip({
            selector: '[data-toggle="tooltip"]'
        });
        // Switchery
        Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
        $('.js-switch').each(function () {
            new Switchery($(this)[0], $(this).data());
        });
    },
    columns: [
        {data: 'id', name: 'id'},
        {data: 'code', name: 'code'},
        {data: 'name', name: 'name'},
        {data: 'unit_floor', name: 'unit_floor'},
        {data: 'total', name: 'orders.total'},
        {data: 'active', name: 'active'},
        {data: 'action', name: 'action', sortable: false, searchable: false}
    ]
};
let table = $.dataTable(options);

    function changeActiveStatus($el) {

    let url = "{{route('admin.costcenter.changeActive')}}";
    let active = 0;
    if ($el.is(':checked')) {
        active = 1;
    }
    $.easyAjax({
        url: url,
        type: "POST",
        data: {'id': $el.data('id'), 'active': active, '_token': "{{ csrf_token() }}"}
    });
}

</script>
@endpush