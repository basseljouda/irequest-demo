<div class="modal-header">
    <h4 class="modal-title">@trans(Update Order Asset)</h4>
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
</div>
<form id="completeForm" class="ajax-form mb-3" action="{{ route('orders.updateAsset') }}" method="POST" autocomplete="off">
    @csrf
    <input type="hidden" name="item" value="{{$item}}" />
    <input type="hidden" name="asset" value="{{$asset}}" />
    <div class="modal-body">

        <div class="row">
            <div class="col-12">
                <div class="form-group">
                    <label><u><b  class="text-info">{{$name}}</b></u></label>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="form-group">
                    <label>Select a new asset then click on Replace button</label>
                </div>
                <div class="form-group">
                    <select name="new_asset" class="select2 form-control" required>
                        <option value=""><i class="icon-badge"></i>@trans(Select)  @trans(New Asset)</option>
                        @foreach ($cmms_assets as $item)
                        <option value="{{ $item->id }}">{{ ucwords($item->name) }}</option>   
                        @endforeach
                    </select>
                </div>
                <div class="text-right">
                    <button type="submit" class="btn btn-info" ><i class="fa fa-times"></i> @trans(Replace)</button>        
                    <div>
                    </div>
                </div>
                <hr/>
                <br/><br/>
                <div class="row">
                    <div class="col-12">
                        <div class="form-group text-center">
                            <a style="color:#555" href="#" onclick="removeAsset()">Click here if you want to <b class="text-danger">Remove</b> the asset from the order</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

</form>
<script>
    function removeAsset(){
        swal({
        title: "@trans(Are you sure)",
        text: "Are you sure want to delete the asset from the order?",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "@trans(Delete)",
        cancelButtonText: "@trans(Cancel)",
        closeOnConfirm: true,
        closeOnCancel: true
    }, function (isConfirm) {
        if (isConfirm) {
            $('#completeForm').submit();
        }
    });
        
    }
    $('.select2').select2();
    $('#completeForm').submit(function (e) {
        e.preventDefault();
        const form = $(this);
        $.easyAjax({
            url: form.attr('action'),
            type: 'POST',
            container: '#completeForm',
            data: $('#completeForm').serialize(),
            success: function (response) {
                if (response.status == 'success') {
                    if (response.data) {
                        $("body").removeClass("control-sidebar-slide-open");
                        showOrder(response.data, true);
                        $('.modal').modal('hide');
                        return false;
                    }
                }
            }
        });
    });
</script>