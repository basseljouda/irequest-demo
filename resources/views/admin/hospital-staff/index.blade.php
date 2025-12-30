@extends('layouts.app')

@push('head-script')
    <link rel="stylesheet" href="{{ asset('assets/plugins/iCheck/all.css') }}">
@endpush

@permission('add_hospital_staff')
@section('create-button')
<a href="{{ route('admin.hospital-staff.create') }}" target-modal="#staff-modal" class="modal-link btn btn-info btn-sm m-l-15"><i
                class="fa fa-plus-circle"></i> @trans(Create New)</a>
@endsection
@endpermission

@section('content')


    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive m-t-40">
                        <table id="myTable" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>@trans(IDN)</th>
                                <th>@trans(Name)</th>
                                <th>@trans(Site)</th>
                                <th>@trans(Email)</th>
                                <th>@trans(Phone)</th>
                                <th>@trans(Supervisor)</th>
                                <th>@trans(User Login)</th>
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
    <script src="{{ asset('assets/plugins/iCheck/icheck.min.js') }}"></script>

    <script>
        let options = {
            ajax: '{!! route('admin.hospital-staff.data') !!}',
            columns: [
                {data: 'id', name: 'hospitals_stuff.id'},
                {data: 'company_name', name: 'companies.company_name'},
                {data: 'firstname', name: 'firstname'},
                {data: 'name', name: 'hospitals.name'},
                {data: 'email', name: 'email'},
                {data: 'phone', name: 'phone'},
                {data: 'supervisor_name', name: 'supervisor_name'},
                {data: 'login_status', name: 'login_status'},
                { data: 'action', name: 'action', sortable:false, searchable:false}
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
                       '@trans(Create) @trans(User Login)</label></div>',
                type: "info",
                html:true,
                showCancelButton: true,
                confirmButtonColor: "#158f07",
                confirmButtonText: "@trans(Accept)",
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