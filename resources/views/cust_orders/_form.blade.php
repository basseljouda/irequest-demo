@push('head-script')
<link rel="stylesheet"
      href="{{ asset('assets/node_modules/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css') }}">
<style>
    .select2-container{
        width: -webkit-fill-available !important;
    }
    #collapseOne{

        overflow: auto;
    }
    .help-block{
        position: absolute;
        font-size: 12px;
        right: 0;
    }
</style>
@endpush
<form class="ajax-form" id="OrderForm" method="{{isset($order) ? 'PUT' : 'POST'}}" role="form" action="{{isset($order) ? route('orders.update', $order->id) : route('orders.store')}}">
    @csrf

    <div class="row">
        <div class="col-md-12 card" style="padding: 15px">
            
            <div class="card-body">
                @isset($order)
                <p class="text-right" title="Click here to preview the order">
                    <small>@trans(Last update): {{dformat($order->updated_at).' By: '.$order->createdby->name}}  </small>
                    <strong onclick="showOrder('{{$order->id}}', true)" class="cursor-pointer order-head-status text-white bg-{{$order->status}}">{{ucwords($order->status).' '}}</strong></p>
                @endisset
                <div class="row">
                    <div class="offset-md-1 col-md-3">
                        <div class="form-group">
                            <label>@trans(PO number)</label>
                            <input type="text" maxlength="10" value="{{isset($order->order_no) ? $order->order_no : ''}}" class="form-control" id="order_no" name="order_no" placeholder="" />
                            <button>Attach PO</button>
                        </div>
                    </div>
                   
                    <div class="col-md-3">
                        <div class="form-group">
                                <label>@trans(Type)</label>
                                <select class="form-control select2">
                                    <option>Contracted</option>
                                    <option>Not Contracted</option>
                                </select>
                        </div>
                    </div>
                     <div class="col-md-3 ">
                        <div class="form-group">
                            <label>Attach Packing Slip</label>
                            <input type="file" title="" />
                        </div>
                    </div>
                    <div class="col-md-3 hide">
                        <div class="form-group">
                            <label>@trans(Order Date) <span class="required"> *</span></label>
                            <input type="text" required="" value="{{isset($order->created_at) ? dformat($order->created_at) : dformat('now')}}" class="form-control datepiktime" id="created_at" value="" name="created_at" placeholder="Select date">
                        </div>
                    </div>
                    <div class="col">
                    
                </div>
                </div>

                <div class="row">
                    <div class="col-md-3 offset-md-1">
                        <div class="form-group">
                            <label @permission('add_hospital') class="add-modal modal-link" href="{{ route('admin.hospital.create')}}" @endpermission>Site<span class="required"> *</span></label>
                            <select xdata-link="#staff"  xdata-list='' class="select2 m-b-10 form-control" 
                                    data-placeholder="@trans(Site): @trans(viewAll)" name="hospital" id="hospital" required="">

                                @foreach ($hospitals as $item)
                                <option {{selected(isset($order) ? $order->hospital_id : '',$item->id,$item->id)}} value="{{ $item->id }}">{{ ucwords($item->name) }}</option>   
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>@trans(Contact Name)<span class="required"> *</span></label>
                            <input type="text" maxlength="200" value="{{isset($order->contact_phone) ? $order->contact_phone : ''}}" class="form-control" required="" id="contact_phone" name="contact_phone" />
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>@trans(Contact Email)<span class="required"> *</span></label>
                            <input type="text" maxlength="20" value="{{isset($order->contact_phone) ? $order->contact_phone : ''}}" class="form-control" required="" id="contact_phone" name="contact_phone" />
                        </div>
                    </div>
                     <div class="col-md-2">
                        <div class="form-group">
                            <label>@trans(Contact Phone)<span class="required"> *</span></label>
                            <input type="text" maxlength="20" value="{{isset($order->contact_phone) ? $order->contact_phone : ''}}" class="form-control" required="" id="contact_phone" name="contact_phone" />
                        </div>
                    </div>
                </div>
                <hr>
                     <div class="row">
    
    <div class="col-md-3 offset-md-1">
        <div class="form-group">
            <label for="address" class="info-label"><b>Shipping Address</b>:</label>
            <input type="text" class="form-control" id="address" name="address" value="{{ old('address') }}">
        </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
            <label for="city" class="info-label">City:</label>
            <input type="text" class="form-control" id="city" name="city" value="{{ old('city') }}">
        </div>
    </div>
    <div class="col-md-1">
        <div class="form-group">
            <label for="state" class="info-label">State:</label>
            <input type="text" class="form-control" id="state" name="state" value="{{ old('state') }}">
        </div>
    </div>
    <div class="col-md-1">
        <div class="form-group">
            <label for="zip_code" class="info-label">Zip Code:</label>
            <input type="text" class="form-control" id="zip_code" name="zip_code" value="{{ old('zip_code') }}">
        </div>
    </div>
                         <div class="col-md-1">
        <div class="form-group">
            <label for="country" class="info-label">Country:</label>
            <input type="text" class="form-control" id="country" name="country" value="{{ old('country') }}">
        </div>
    </div>
</div>
                

<div class="row">
    <div class="col-md-3 offset-md-1">
        <div class="form-group">
            <label for="shipment_type" class="control-label">Shipment Type*</label>
            <select id="shipment_type" name="shipment_type" class="select2 form-control" required="1">
                <option value="standard">Fedix</option>
                <option value="expedited">Expedited (Air)</option>
                <option value="freight">Freight</option>
                <option value="pickup">Customer Pickup</option>
                <option value="courier">Courier Delivery (Local Delivery)</option>
                <option value="customer_provided">Customer Provided</option>
            </select>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label for="shipment_method" class="control-label">Shipment Method</label>
            <select name="shipment_method" class="select2 form-control" required="true">
                <option value="ground">Ground</option>
                <option value="two_day">Two-Day</option>
                <option value="overnight">Overnight</option>
                <option value="freight_ltl">Freight LTL (Less than Truckload)</option>
                <option value="freight_ftl">Freight FTL (Full Truckload)</option>
                <option value="local_courier">Local Courier</option>
            </select>
        </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
            <label for="tracking_no" class="control-label">Tracking No*</label>
            <input type="text" id="tracking_no" name="tracking_no" class="form-control" required="1" value="">
        </div>
    </div>
    
</div>
                <div class="row hide" id="customer_provided_details">
            <div class="col-md-3 offset-md-1">
                <div class="form-group">
                    <label for="customer_provided_details" class="control-label">Account No</label>
                    <input type="text" id="customer_provided_details_input" name="customer_provided_details" class="form-control">
                </div>
            </div>
        </div>


                <hr>
                
                <div class="row">
                    <div class="offset-md-1 col-md-3">
                        <div class="form-group">
                            <label>@trans(Date Needed)<span class="required"> *</span></label>
                            <input required="" maxlength="" value="{{isset($order->date_needed) ? dformat($order->date_needed,true) : ''}}" type="text" class="form-control datepik" id="date_needed" value="" name="date_needed" placeholder="@trans(Select date)">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>@trans(Notes)</label>
                            <textarea name="notes" class="form-control" style="height: 34px;min-height: 34px;max-height: 110px">{{isset($order->notes) ? $order->notes : ''}}</textarea>
                        </div>
                    </div>
                </div>

            </div>
            <div class="row">
                
                <div class="col-md-12 info-box">
                    <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                        <div class="panel">
                            <div class="panel-heading panel-info" role="tab" id="headingOne">
                                <h4 class="panel-title"> 
                                    <a data-toggle="collapse">
                                        @trans(Parts)<span class="required"> *</span>
                                    </a>
                                </h4>
                            </div>
                            <div id="collapseOne" class="panel-collapse collapse show" role="tabpanel" aria-labelledby="headingOne">
                                <table id="eq_table" class="psitems" style="width: 99%">
                                    @if (isset($order))

                                    @php $i=0 @endphp
                                    @foreach($equipments as $order_equipment)
                                    <tr class="tr_clone">
                                        <td style="width:80%">
                                            <div class="form-group">
                                                <div class="col-md-12">
                                                    <select class="select-tag select2 items m-b-10 form-control equipments select-combo" 
                                                            data-eq="{{$order_equipment->id}}"       data-placeholder="@trans(Click to Select)" name="equipments[]" id="items">

                                                    </select>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group">
                                                <div class="col-md-12">
                                                    <input type="hidden" class="form-control" id="quantity" name="quantity[]" value ="1" placeholder="Qnt">
                                                    <input type="hidden" class="eq-id" name="id[]" value ="{{$order_equipment->id}}">
                                                </div>
                                            </div>
                                        </td>
                                        <td >
                                            <!--div class="form-group">
                                                <div class="col-md-12">
                                                    <input disabled="" type="text" value="{{isset($order_equipment->notes) ? $order_equipment->notes : ''}}" class="form-control" id="equipment_notes" name="" placeholder="Notes">
                                                </div>
                                            </div-->
                                        </td>
                                        <td style='width:10%;text-align: right' >
                                            <div class="form-group">
                                                <div class="col-md-12">
                                                    @if ($i == 0)
                                                    <button class="btn btn-success tr_clone_add" type="button"><i class="fa fa-plus"></i></button>
                                                    @else
                                                    <button class="btn tr_clone_remove btn-danger" type="button"><i class="fa fa-minus"></i></button>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @php $i++ @endphp
                                    @endforeach
                                    @else

                                    <tr class="tr_clone">
                                        <td style="width:30%">
                                            <div class="form-group">
                                                <div class="col-md-12">
                                                    Part
                                                    <select class="select2 items m-b-10 form-control" 
                                                            data-placeholder="@trans(Click to Select)" name="xequipments[]" id="items">
                                                        <option afterMarket="$0" refurbished_price="$0">Search Parts... </option>
                                                        @foreach ($equipments as $equipment)
                                                        <option afterMarket="{{$equipment->new_price}}" refurbished_price="{{$equipment->refurbished_price}}" value="{{$equipment->id}}">{{$equipment->title}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group">
                                                <div class="col-md-12">
                                                    Price
                                            <select class="form-control prices select2">
                                                <option>AfterMarket Price: $0</option>
                                                <option>Refurbished  Price: $0</option>
                                            </select>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group">
                                                Quantity
                                                <div class="col-md-12">
                                                    <input type="text" class="form-control" id="quantity" name="quantity[]" value ="1" >
                                                </div>
                                            </div>
                                        </td>
                                        <td >
                                            <!--div class="form-group">
                                                <div class="col-md-12">
                                                    <input type="text" class="form-control" id="equipment_notes" name="equipment_notes[]" placeholder="Notes">
                                                </div>
                                            </div-->
                                        </td>
                                        <td style='width:10%;text-align: right' >
                                            <div class="form-group">
                                                <div class="col-md-12">
                                                    <button class="btn btn-success tr_clone_add" type="button"><i class="fa fa-plus"></i></button>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @endif
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@push('footer-script')
<script src="{{ asset('assets/node_modules/select2/dist/js/select2.full.min.js') }}"
type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js') }}"
type="text/javascript"></script>
<script>
    
                        $("table").on('click', '.tr_clone_add', function () {

                        $('.items').select2("destroy");
                        var $tr = $(this).closest('.tr_clone');
                        var $clone = $tr.clone();
                        $tr.after($clone);
                        $('.items').select2({tags: true});
                        $clone.find('.items').select2({'val': ''});
                        $clone.find('input[type=text]').val('1');
                        $clone.find('input.eq-id').val('0');
                        $clone.find('.tr_clone_add').
                                removeClass('tr_clone_add').
                                removeClass('btn-success').
                                addClass('tr_clone_remove').
                                addClass('btn-danger').
                                html('<i class="fa fa-minus"></i>');
                        $('.items').select2({tags: true});  
                        });
                        $("table").on('click', '.tr_clone_remove', function () {
                        $(this).closest('.tr_clone').remove();
                        });
                        $('#preview-form').click(function () {
                        swal({
                        title: $(this).text(),
                                text: "@trans(Are you sure)?",
                                type: "info",
                                showCancelButton: true,
                                confirmButtonText: "@lang('app.yes')",
                                cancelButtonText: "@lang('app.no')",
                                closeOnConfirm: true,
                                closeOnCancel: true,
                        }, function (isConfirm) {
                        if (isConfirm) {

                        $.easyAjax({
                        url: $('#OrderForm').attr('action'),
                                container: '#OrderForm',
                                type: $('#OrderForm').attr('method'),
                                redirect: true,
                                data: $('#OrderForm').serialize()
                        }
                        );
                        }
                        }
                        )
                        });</script>
<script>

    $(document).ready(function() {
         $('.items').select2({tags: true});
         
         $('#shipment_type').change(function() {
                if ($(this).val() === 'customer_provided') {
                    $('#customer_provided_details').removeClass('hide');
                    $('#customer_provided_details_input').attr('required', 'required');
                } else {
                    $('#customer_provided_details').addClass('hide');
                    $('#customer_provided_details_input').removeAttr('required');
                }
            });
        
         
    $('.datepiktime').bootstrapMaterialDatePicker({format: 'MM/DD/YYYY HH:mm', weekStart: 0, time: true});
    $('#hospital').change(function () {
    var site_id = $(this).val();
    $('.select-staff').empty().append('<option value="">@trans(Staff): @trans(All)</option>');
    $('.select-combo').empty().append('<option value="">@trans(Equipment): @trans(All)</option>');
    $.ajax({
    url: '/orders/fetch',
            data: {"site_id":site_id},
            type: 'GET',
            success: function (data) {
            $.each(data.staff, function (key, value) {
            var selected = '';
            var is_staff = "{{isset(user()->is_staff) ? user()->is_staff->id : ''}}";
            if (is_staff === key)
                    selected = "selected";
            @isset($order)
                    var staff = '{{$order->staff_id}}';
            if (staff === key)
                    selected = 'selected';
            @endisset
                    $('.select-staff').append('<option ' + selected + ' value="' + key + '">' + value + '</option>');
            });
            $('.select-combo').each(function(index) {
            var selectCombo = $(this);
            var eq = $(this).attr("data-eq");
            $.each(data.combo, function (key, value) {
            var option = '<option value="' + key + '">' + value + '</option>';
            selectCombo.append(option);
            if (key === eq) {
            selectCombo.val(key).trigger('change');
            }
            });
            });
            }
    });
    });
    $('#hospital').change();
     $(document).on('change', '.items', function() {
         console.log(2);
        var $row = $(this).closest('tr'); // Get the closest row
        var selectedOption = $(this).find('option:selected');
        var afterMarket = selectedOption.attr('afterMarket');
        var refurbishedPrice = selectedOption.attr('refurbished_price');

        var $pricesDropdown = $row.find('.prices'); // Find the prices dropdown in the same row
        $pricesDropdown.empty(); // Clear existing options
        $pricesDropdown.append('<option value="' + afterMarket + '">AfterMarket Price: ' + afterMarket + '</option>');
        $pricesDropdown.append('<option value="' + refurbishedPrice + '">Refurbished Price: ' + refurbishedPrice + '</option>');
    });
    
    });


</script>
<script>
       
            
    </script>
@endpush