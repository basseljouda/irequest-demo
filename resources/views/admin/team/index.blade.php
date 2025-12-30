@extends('layouts.app')

@permission('add_user')
@section('create-button')
    <a href="{{ route('admin.team.create') }}" class="btn btn-info btn-sm m-l-15"><i
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
                                <th>@lang('app.name')</th>
                                <th>@lang('app.email')</th>
                                <th>Mobile</th>
                                <th>@lang('modules.permission.roleName')</th>
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
    <script>
        let options = {
            ajax: '{!! route('admin.team.data') !!}',
            columns: [
                {data: 'id', name: 'id'},
                {data: 'name', name: 'name'},
                {data: 'email', name: 'email'},
                {data: 'mobile', name: 'mobile'},
                {data: 'role_name', name: 'role_name'},
                {data: 'action', name: 'action',class:''}
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

                    var url = "{{ route('admin.team.destroy',':id') }}";
                    url = url.replace(':id', id);

                    var token = "{{ csrf_token() }}";

                    $.easyAjax({
                        type: 'POST',
                        url: url,
                        data: {'_token': token, '_method': 'DELETE'},
                        success: function (response) {
                            if (response.status == "success") {
                                $.unblockUI();
                                swal("User Deleted!", response.message, "success");
                                table.draw();
                            }
                        }
                    });
                }
            });
        });

        $('body').on('change', '.role_id', function () {
            var id = $(this).val();
            var teamId = $(this).data('row-id');
            var url = "{{route('admin.team.changeRole')}}";

            $.easyAjax({
                url: url,
                type: 'POST',
                data: {roleId: id, teamId: teamId, _token: "{{ csrf_token() }}"},
                success: function (response) {
                    if (response.status == "success") {
                        $.unblockUI();
                        swal("Status changed!", response.message, "success");
                        table.draw();
                    }
                }
            })
        });
        $('body').on('click', '.reset-pass', function () {
            var email = $(this).data('value');
            swal({
                title: "Reset Password",
                text: "Are you sure to send a Reset Password Email to user?",
                type: "info",
                showCancelButton: true,
                confirmButtonText: "Send",
                cancelButtonText: "@trans(Cancel)",
                closeOnConfirm: true,
                closeOnCancel: true
            }, function (isConfirm) {
                if (isConfirm) {
                    var url = "{{route('password.email')}}";

                    $.easyAjax({
                        url: url,
                        type: 'POST',
                        data: {email: email, _token: "{{ csrf_token() }}"},
                        success: function (response) {
                            //if (response.status == "success") {
                                $.unblockUI();
                                $.showToastr("Reset Email Sent Successfully","success");
                           // }
                        }
                    });
                }
            });
            
        });

    </script>
@endpush