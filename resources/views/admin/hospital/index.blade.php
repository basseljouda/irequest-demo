@extends('layouts.app')


@permission('add_hospital')
@section('create-button')
    <a href="{{ route('admin.hospital.create') }}" class="modal-link btn btn-info btn-sm m-l-15"><i
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
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive m-t-40">
                        <table id="myTable" class="table table-bordered table-striped table-hover">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>@trans(IDN)</th>
                                <th>@trans(EntityNo)</th>
                                <th>@trans(Type)</th>
                                <th>@trans(Site) @trans(Name)</th>
                                <th>@trans(Market)</th>
                                <th>@trans(Phone)</th>
                                <th>@trans(Website)</th>
                                <th>@trans(Address)</th>
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
<script src="{{ asset('assets/node_modules/select2/dist/js/select2.full.min.js') }}"></script>
   @include('vendor.datatables.scripts')

    <script>
       let options = {
            ajax: {'url': '{!! route('admin.hospital.data') !!}',
            "data": function (d) {
                var result = {};
                $.each($('form#filter').serializeArray(), function () {
                    result[this.name] = this.value;
                });
                return $.extend({}, d, result);
            }
        },
            columns: [
                {data: 'id', name: 'id'},
                {data: 'company_name', name: 'companies.company_name'},
                {data: 'entity_no', name: 'entity_no'},
                {data: 'type', name: 'type'},
                {data: 'name', name: 'name'},
                {data: 'city', name: 'city'},
                {data: 'phone', name: 'phone'},
                {data: 'website', name: 'website'},
                {data: 'address_full', name: 'address_full'},
                { data: 'action', name: 'action', sortable:false, searchable:false }
            ]
        };
        let table = $.dataTable(options);

        $('body').on('click', '.sa-params', function () {
            var id = $(this).data('row-id');
            swal({
                title: "@trans(Are you sure)",
                text: "@trans(Delete Data)",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "@trans(Delete)",
                cancelButtonText: "@trans(Cancel)",
                closeOnConfirm: true,
                closeOnCancel: true
            }, function (isConfirm) {
                if (isConfirm) {

                    var url = "{{ route('admin.hospital-staff.destroy',':id') }}";
                    url = url.replace(':id', id);

                    var token = "{{ csrf_token() }}";

                    $.easyAjax({
                        type: 'POST',
                        url: url,
                        data: {'_token': token, '_method': 'DELETE'},
                        success: function (response) {
                            if (response.status == "success") {
                                $.unblockUI();
                                table.draw();
                            }
                        }
                    });
                }
            });
        });
        
        $('body').on('click', '.sa-accept', function () {
            var id = $(this).data('row-id');
            
            
            swal({
                title: "@trans(Are you sure)",
                text: '<div class="form-check">'+
                       '<input onclick="" class="form-check-input" type="checkbox" value="" id="createuser">'+
                       '<label class="form-check-label" for="createuser">'+
                       'Create user account to access portal</label></div>',
                type: "info",
                html:true,
                showCancelButton: true,
                confirmButtonColor: "#158f07",
                confirmButtonText: "Accept",
                cancelButtonText: "@trans(Cancel)",
                closeOnConfirm: true,
                closeOnCancel: true,
            }, function (isConfirm) {
                if (isConfirm) {
                    
                    var url = "{{ route('admin.hospital-staff.loginStatus') }}";

                    var token = "{{ csrf_token() }}";

                    $.easyAjax({
                        type: 'POST',
                        url: url,
                        data: {'_token': token,'id':id, 'createuser': $('#createuser').prop('checked')},
                        success: function (response) {
                            if (response.status == "success") {
                                $.unblockUI();
                                table.draw();
                            }
                        }
                    });
                }
            });
        });

    </script>
@endpush