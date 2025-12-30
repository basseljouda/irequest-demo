@extends('layouts.app')

@push('head-script')
<link rel="stylesheet" href="{{ asset('assets/node_modules/switchery/dist/switchery.min.css') }}">
@endpush

@permission('add_equipment')
@section('create-button')
<a href="{{ route('admin.equipment.create') }}" class="modal-link btn btn-info btn-sm m-l-15"><i
        class="fa fa-plus-circle"></i> @trans(Create New)</a>
@endsection
@endpermission

@section('content')


<div class="row">
    <div class="col-12">
            <form id="filter">
            @role('superadmin')
                        @if (!isset(user()->is_staff))
                        <div class="col">
                            <div class="form-group">
                                <select onchange="table.draw()"  id="filter_company_id" name="filter_company_id" class="select2 form-control select-idn">
                                    <option value=""><i class="icon-badge"></i>@trans(IDN): @lang('app.all')</option>
                                    @foreach ($companies as $item)
                                    <option value="{{ $item->id }}">{{ ucwords($item->company_name) }}</option>   
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @endif
                        @endrole
            </form>
    </div>
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive m-t-40">
                    <table id="myTable" class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>@trans(OEM)#</th>
                                <th>@trans(IDN)</th>
                                <th>@trans(Combo Description)</th>
                                <th>@trans(Model)</th>
                                <th>@trans(Price)</th>
                                <th>@trans(Refurbished Price)</th>
                                <th>@trans(Active)</th>
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
<script src="{{ asset('assets/node_modules/switchery/dist/switchery.min.js') }}"></script>
<script src="{{ asset('assets/node_modules/select2/dist/js/select2.full.min.js') }}"></script>
<script>
let options = {
    ajax: {'url': '{!! route('admin.equipment.data') !!}',
            "data": function (d) {
                var result = {};
                $.each($('form#filter').serializeArray(), function () {
                    result[this.name] = this.value;
                });
                return $.extend({}, d, result);
            }
        },
    fnDrawCallback: function (oSettings) {
        // Switchery
        Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
        $('.js-switch').each(function () {
            new Switchery($(this)[0], $(this).data());
        });

        $("body").tooltip({
            selector: '[data-toggle="tooltip"]'
        });
    },
    columns: [
        
        {data: 'oem_no', name: 'equipments.oem_no',class:'nowrap'},
        {data: 'company_name', name: 'companies.company_name'},
        {data: 'name', name: 'equipments.name'},
        {data: 'model', name: 'model'},
        {data: 'price_day', name: 'price_day'},
        {data: 'refurbished_price', name: 'refurbished_price'},
        {data: 'active', name: 'active'},
        {data: 'action', name: 'action', sortable: false, searchable: false}
    ]
};
let table = $.dataTable(options);

function changeActiveStatus($el) {

    let url = "{{route('admin.equipment.changeActive')}}";
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