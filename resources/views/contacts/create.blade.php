<div class="modal-header">
    <h4 class="modal-title">Send Message</h4>
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
</div>
<form id="sendContact" class="ajax-form mb-3"  action="{{ route('contacts.store') }}" method="POST" autocomplete="off">
    @csrf
    <div class="modal-body">
        <div class="form-body">
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <label>Text</label>
                        <textarea required="" rows="10" maxlength="999" id='msg' name="msg" placeholder="Write you message ..." class="form-control" style="min-height: 34px;max-height: 400px"></textarea>
                    </div>
                </div>
                
            </div>
           
        </div>
    </div>
    <div class="modal-footer">
        <div class="col-6">
            <button type="button" class="btn btn-sm btn-default" data-dismiss="modal"><i class="fa fa-times"></i> @trans(Cancel)</button>
        </div>
        <div class="col-6 text-right">
            <button type="submit" id="send-contact" class="btn btn-sm btn-primary"><i class="fa fa-check"></i> Send</button>
        </div>
    </div>
</form>
<script>
    if (caller.search('@') === -1){
        $('#msg').attr("maxlength","320")
        $('.modal-title').text('Send message to: '+caller);
    }else{
        $('#msg').attr("maxlength","999")
        $('.modal-title').text('Send email to: '+caller);
    }
    $('#sendContact').submit(function (e) {
        e.preventDefault();
        const form = $(this);
        $.easyAjax({
            url: $(this).attr('action'),
            type: $(this).attr('method'),
            data: $(this).serialize()+"&caller="+caller,
            success: function (response) {
               $('.modal').modal('hide');
            }
        });
    });
</script>