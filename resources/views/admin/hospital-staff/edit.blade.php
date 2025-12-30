<style>
    .hide-in-create{
        display: none;
    }
    .show-in-create{
        display: block !important;
    }
</style>
<div class="modal-header">
    <h4 class="modal-title">@trans(Edit) @trans(Site) @trans(Staff): {{$staff->id}}</h4>
    <button type="button" class="close mclose"  aria-hidden="true">×</button>
</div>
<div class="modal-body">

    <form id="staffform" class="ajax-form mb-3" action="{{ route('admin.hospital-staff.update',$staff->id) }}" autocomplete="off"" method="PUT" autocomplete="off">
        @include('admin.hospital-staff._form')
        <div class="modal-footer">
            <div class="col-6">
                <button type="button" class="mclose btn btn-default"><i class="fa fa-times"></i> @trans(Cancel)</button>
            </div>
            <div class="col-6 text-right">
                <button type="submit" id="create-hospital" class="btn btn-primary"><i class="fa fa-check"></i> @trans(Save)</button>
            </div>
        </div>
    </form>
</div>
<script>
    $(document).ready(function () {
        //$('#application-md-modal').css('z-index','999999');
    });

    $('.mclose').click(function (e) {
        $('#staff-modal').modal('hide');
    })

    $('#staffform').submit(function (e) {
        e.preventDefault();
        const form = $(this);

        $.easyAjax({
            url: $(this).attr('action'),
            type: "PUT",
            container: '#staffform',
            data: $(this).serialize(),
            success: function (response) {
                if (response.status == 'success') {
                    if ($('.dataTable').is(':visible')) {
                        table.draw();
                    }
                    let newOption = new Option(response.text, response.id, false, false);
                    if ($('#staff').is(':visible')) {
                        $('#staff').append(newOption).trigger('change');
                        $('#staff').val(response.id);
                    } else if ($('#alert-new-div > #staff').is(':visible')) {
                        $('#alert-new-div > #staff').append(newOption).trigger('change');
                        $('#alert-new-div > #staff').val(response.id);
                    }
                }
                $('#staff-modal').modal('hide');
            }
        });
    });
</script>