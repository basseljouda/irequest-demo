<script src="https://cdn.jsdelivr.net/npm/handlebars@latest/dist/handlebars.js"></script>
<script id="details-template" type="text/x-handlebars-template">
    <form autocomplete="off" class="ajax-form mb-3 item-form" method="POST" action="{{ route('admin.inventory.item-update') }}">
    @csrf
    
@verbatim
<input type="hidden" value="{{ id }}" />
        <div class="label label-info padding-5" style="height:42px;margin-bottom:10px"> {{ name }}'s Items
        <button type="submit" class="item-submitbtn btn btn-sm hide float-sm-right">
    <i class="fa fa-check"></i> Save Changes</button>
</div>
        <table class="table details-table" id="items-{{id}}">
            <thead>
            <tr>
                <th>#</th>
                <th>Desc</th>
                <th>Serial</th>
                <th>DOT#</th>
                <th>Available</th>
                <!--th>Last Update</th>
                <th>By User</th-->
                <th>Rate</th>
            </tr>
            </thead>
        </table>
        @endverbatim
        </form>
    </script>
    <script>

$('body').on('dblclick', 'table.details-table td.serial_value,table.details-table td.dot_value', function (e) {
    let td_value = $(this).text();
    $(this).html('');
    let row = document.createElement("div");
    row.className = 'input-group sd-form';
    $(this).append(row);
    let input = $('<input/>').attr({ type: 'text', id: 'sd-value', name: 'sd-value',
            value: td_value,class: 'form-control form-control-sm sd-value'}).appendTo(row);
    let btns  = '<a href="#" class="btn btn-primary btn-circle sd-save"><i class="fa fa-check" aria-hidden="true"></i></a>';
        btns += '<a href="#" data="'+ td_value +'" class="btn btn-danger btn-circle sd-cancel"><i class="fa fa-close" aria-hidden="true"></i></a>';
    $(row).append(btns);
    input.focus().select();
});

$('body').on('click','a.sd-cancel', function (e) {
    e.preventDefault();
    let value = $(this).attr('data');
    $(this).parent().parent().html(value);
    
});
$('body').on('click','a.sd-save', function (e) {
    e.preventDefault();
    let currentRow=$(this).closest("tr");
    let currentValue=$(this).closest("td").find("input").val();
    let id = currentRow.find("td:eq(0)").text();
    let td = $(this).closest("td");
    td.html(currentValue);
    $.easyAjax({
            url: '{!! route('admin.inventory.item-update') !!}',
            type: 'POST',
            data: {'id': id,'field': td.attr('class'),'value': currentValue, _token: "{{ csrf_token() }}"},
            success: function (response) {
            }
        });
    let value = $(this).attr('data');
    $(this).parent().parent().html(value);
    
});
$('body').on('click', 'button.btn-number', function (e) {
    e.preventDefault();
   
    fieldName = $(this).attr('data-field');
    type      = $(this).attr('data-type');
    var input = $(this).closest('tr').find("input.input-number");
    var currentVal = parseInt(input.val());
     console.log(fieldName);
    
    if (!isNaN(currentVal)) {
        if(type == 'minus') {
            
            if(currentVal > input.attr('min')) {
                input.val(currentVal - 1).change();
            } 
            if(parseInt(input.val()) == input.attr('min')) {
                $(this).attr('disabled', true);
            }
            

        } else if(type == 'plus') {

            if(currentVal < input.attr('max')) {
                input.val(currentVal + 1).change();
            }
            if(parseInt(input.val()) == input.attr('max')) {
                $(this).attr('disabled', true);
            }
             

        }
    } else {
        input.val(0);
    }
});


$('body').on('focusin', '.input-number', function (e) {
   $(this).data('oldValue', $(this).val());
});
$('body').on('change', '.input-number', function (e) {
    
    minValue =  parseInt($(this).attr('min'));
    maxValue =  parseInt($(this).attr('max'));
    valueCurrent = parseInt($(this).val());
    
    name = $(this).attr('name');
    if(valueCurrent >= minValue) {
        $(".btn-number[data-type='minus'][data-field='"+name+"']").removeAttr('disabled');
    } else {
        alert('Sorry, the minimum value was reached');
        $(this).val($(this).data('oldValue'));
    }
    if(valueCurrent <= maxValue) {
        $(".btn-number[data-type='plus'][data-field='"+name+"']").removeAttr('disabled')
    } else {
        alert('Sorry, the maximum value was reached');
        $(this).val($(this).data('oldValue'));
    }
    $('.item-submitbtn').removeClass('hide');
});
$('body').on('keydown', function (e) {
    if (e.keyCode==13){
        e.preventDefault();
        if ($('.sd-value').is(':focus'))
            $(':focus').next('a.sd-save').click();
    }else if(e.keyCode==27){
        e.preventDefault();
        if ($('.sd-value').is(':focus'))
            $(':focus').next().next().click();
    }
    /*// Allow: backspace, delete, tab, escape, enter and .
    if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 190]) !== -1 ||
         // Allow: Ctrl+A
        (e.keyCode == 65 && e.ctrlKey === true) || 
         // Allow: home, end, left, right
        (e.keyCode >= 35 && e.keyCode <= 39)) {
             // let it happen, don't do anything
             return;
    }
    // Ensure that it is a number and stop the keypress
    if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
        e.preventDefault();
    }*/
});
    
    
    $('body').on('submit','form.item-form',function (e) {
        e.preventDefault();
        $.easyAjax({
            url: $(this).attr('action'),
            type: $(this).attr('method'),
            container: 'form.item-form',
            data: $(this).serialize(),
            success: function (response) {
                if (response.status == 'success') {
                   inventory_items.draw();
                   $('.item-submitbtn').addClass('hide');
                }
            }
        });
    });
</script>