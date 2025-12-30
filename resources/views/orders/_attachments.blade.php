<div class="col-12">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-light">
            <h6 class="mb-0"><i class="fa fa-upload text-success"></i> @trans(Upload New Files)</h6>
        </div>
        <div class="card-body">
            <form class="ajax-form" id="attach_form" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id" value="{{ $order->id }}" />
                <div class="mb-3">
                    <label class="form-label text-muted small">@trans(Select Files)</label>
                    <input type="file" name="attachment[]" id="file-input"
                           class="form-control" 
                           multiple 
                           accept=".pdf,.jpg,.png,.docx,.xls,.xlsx,.csv,.doc,.txt,.rtf,.jpeg,.gif,.mp3">
                </div>
                <button type="button" class="btn btn-primary w-100 submit-attach" style="display: none;">
                    <i class="fa fa-cloud-upload-alt"></i> @trans(Upload Files)
                </button>
            </form>
        </div>
    </div>
</div>
<div class="col-12">
    <div class="card shadow-sm border-0 mb-3">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h6 class="mb-0"><i class="fa fa-paperclip text-primary"></i> @trans(Attached Files) <span class="badge bg-secondary">
                {{ $order->file && $order->file->orders()->count() ? $order->file->files->count() : 0 }}
            </span></h6>
        </div>

        <div class="card-body p-0">
            @if($order->file && $order->file->orders()->count() > 0)
                <table class="table align-middle mb-0 table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>@trans(Original Name)</th>
                            <th>@trans(Uploaded On)</th>
                            <th>@trans(User)</th>
                            <th class="text-end">@trans(Actions)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $i = 1; @endphp
                        @foreach($order->file->files as $file)
                            <tr>
                                <td>{{ $i++ }}</td>
                                <td>
                                    <a href="{{ asset('user-uploads/orders/'.$file->filename) }}" 
                                       target="_blank" 
                                       class="text-decoration-none fw-semibold text-primary">
                                        <i class="fa fa-file-alt me-1"></i> {{ $file->original_name }}
                                    </a>
                                </td>
                                <td>{{ dformat($file->created_at) }}</td>
                                <td><i class="fa fa-user text-muted"></i> {{ $file->user->name }}</td>
                                <td class="text-end">
                                    <a href="{{ asset('user-uploads/orders/'.$file->filename) }}" 
                                       download 
                                       class="btn btn-sm btn-outline-secondary me-2">
                                        <i class="fa fa-download"></i> @trans(Download)
                                    </a>
                                    @if (user()->hasrole('superadmin') || user()->id == $file->user_id)
                                    <a href="#" class="btn btn-sm btn-outline-danger delete-file" rowid="{{ $file->id }}">
                                        <i class="fa fa-trash"></i> @trans(Delete)
                                    </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="p-3 text-center text-muted">
                    <i class="fa fa-folder-open fa-2x mb-2"></i><br>
                    @trans(No files attached yet)
                </div>
            @endif
        </div>
    </div>
</div>
<script>
    // Show/hide upload button based on file selection
    $('#file-input').on('change', function() {
        if (this.files && this.files.length > 0) {
            $('.submit-attach').fadeIn();
        } else {
            $('.submit-attach').fadeOut();
        }
    });

    $('.submit-attach').click(function () {
        var fileInput = document.getElementById('file-input');
        
        // Prevent submission if no files are selected
        if (!fileInput.files || fileInput.files.length === 0) {
            swal({
                title: "@trans(No Files Selected)",
                text: "Please select at least one file to upload.",
                type: "warning",
                confirmButtonText: "@trans(OK)"
            });
            return false;
        }

        $.easyAjax({
            url: "{{ route('orders.attach') }}",
            container: '#attach_form',
            type: 'POST',
            redirect: true,
            data: $('#attach_form').serialize(),
            file: true,
            success: function (response) {
                //$("body").removeClass("control-sidebar-slide-open");
                // Reset form and hide button after successful upload
                $('#attach_form')[0].reset();
                $('.submit-attach').fadeOut();
                showOrder({{$order->id}},true,'attachment');
            }
        });
    });

    $('body').on('click', '.delete-file', function () {
        id = $(this).attr('rowid');
        swal({
            title: "@trans(Deleting file)",
            text: "Are you sure want to delete the file?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "@trans(Delete)",
            cancelButtonText: "@trans(Cancel)",
            closeOnConfirm: true,
            closeOnCancel: true
        }, function (isConfirm) {
            if (isConfirm) {
                var url = "{{ route('orders.delete-attach',[':order_id',':id']) }}";
                url = url.replace(':id', id);
                url = url.replace(':order_id', {{$order->id}});
                var token = "{{ csrf_token() }}";
                $.easyAjax({
                    type: 'POST',
                    url: url,
                    data: {'_token': token, '_method': 'DELETE'},
                    success: function (response) {
                        if (response.status == "success") {
                            $.unblockUI();
                            showOrder({{$order->id}},true,'attachment');
                        }
                    }
                });
            }
        });
    });
</script>