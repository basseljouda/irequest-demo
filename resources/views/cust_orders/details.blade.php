<style>
    .shipping-info-container {
        background-color: #f9f9f9;
        border: 1px solid #ddd;
        border-radius: 5px;
        padding: 20px;
        margin: 20px 0;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
    }

    .shipping-info-section {
        flex: 1;
        min-width: 250px;
    }

    .shipping-info-section h2 {
        font-size: 1.5em;
        margin-bottom: 20px;
    }

    .shipping-info-section p {
        margin: 0;
        font-size: 1.1em;
        margin-bottom: 10px;
    }

    .shipping-info-section .info-label {
        font-weight: bold;
        margin-right: 5px;
    }

    .shipping-info-section .info-value {
        font-weight: normal;
    }

    .shipping-info-section a {
        color: #007bff;
        text-decoration: none;
    }

    .shipping-info-section a:hover {
        text-decoration: underline;
    }
    #users_notify_div > span.select2-container{
        border:1px solid orange !important;
        border-radius: 5px;
    }
    #users_notify_div{
        margin-top: 15px;
    }
    .img-signature{
        max-width: 100%;
    }
    #tabContent > span > i.ti-close{
        cursor: pointer;
    }
    .status_change{
        margin: 10px;
        padding: 7px!important;

    }
    .dismiss-btn{
        padding: 7px!important;
        margin: 10px;

    }
    #alert-new-div > div > div > div > input{
        display: block;
    }
    .right-panel-box {
        overflow-x: scroll;
        max-height: 34rem;
    }
    .r-panel-body{
        padding-top: 0!important;
    }
    .w160{
        min-width: 160px;
    }


    #order_goal,.fw500{
        font-weight: 500;
    }

    #modalBody{
        max-height: 85vh;
        overflow-y: auto;
    }
    .order-section{

        background: #6f8fb50f;

        border-radius: 10px;
    }
    thead tr th{
        font-weight: normal;
    }
    .top-head{
        position: absolute;
        right: 2%;
        top: 15px;
    }
    div.col-12.row > div{
        text-align: center;
    }
    tbody tr td label {
        font-weight: 500!important;
        color:#7c818d;
    }
    .select2.select2-container{
        width: 100% !important;   
    }
    .order-dates-label{
        text-decoration: underline;
        color: #cd581e !important;
    }

</style>

<ul class="nav nav-tabs" id="tabContent">
    <li class="active show" href="#order_data" data-toggle="tab">Order NO: {{$order->order_id}}</li>
    <!--li href="#order_tracking" data-toggle="tab">Order Tracking</li-->
    <span class="top-head mobile-hide">
        
        <strong class="order-head-status text-white bg-{{$order->status}}">{{ucwords(config('constant.cust_orders.'.$order->status)).' '}}</strong>
        <i class="ti-close right-side-toggle"></i>
    </span>
</ul>
<div class="tab-content order-details">
    <div class="tab-pane active" id="order_data">     
        <div id="modalBody" class="modal-body" style="{{ $order->consignment_order ==1 ? 'background:aliceblue;margin:0' :'margin:0'}}"> 
            <div class="r-panel-body p-3">

                <div class="row order-section2">
                    <div class="col-12 row">
                        <div class="col">
                            <strong class="app-color">Site</strong><br />
                            {{ ucwords($order->hospital->name) }}<br />
                            {{$order->hospital->address}}<br />
                            {{$order->hospital->city.' '.$order->hospital->state.' '.$order->hospital->zip}}<br />
                            {{$order->hospital->website}}<br />
                        </div>
                        <div class="col">
                            <strong class="app-color">@trans(Contact) @trans(info)</strong><br />
                            {{ ucwords($order->staff->firstname.' '.$order->staff->lastname) }}<br />
                            {{$order->staff->title->title_name}}<br />
                            {{$order->staff->email}}<br />
                            <a href="tel:{{$order->contact_phone}}">{{$order->contact_phone}}</a>

                        </div>
                        <div class="col">
                            <strong class="app-color">Date Needed</strong><br />
                            {{dformat($order->date_needed)}}
                        </div>
                        <div class="col-2">
                            <strong class="app-color">PO Number</strong><br />

                        </div>
                    </div>
                </div>


            </div>
            <form class="ajax-form full-width" id="serialForm" role="form">
                <input type="hidden"name="id"value="{{$order->id}}" />
                @csrf
                <div class="r-panel-body p-3">
                    <div class="row font-12 col-12">
                        <table style="font-size:12px" class="table order-table">
                            <thead>
                                <tr style="background-color: whitesmoke">
                                    <th>
                                        Description
                                    </th>
                                    <th>Model</th>
                                    
                                    <th>
                                        Condition
                                    </th>
                                    
                                    <th>
                                        Price
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order->equipments as $item)
                                <tr class="{{$item->assets!= '' && $item->removed_assets!= '' && empty(array_diff($item->assets, $item->removed_assets))?'bg-completed-item':''}}">
                                    <td style="width:30%">

                                        {{$item->equipment->name}}
                                        <input type="hidden" value="{{$item->equipment_id}}" class="eq_id" />

                                    </td>

                                    <td>
                                        @if (false)
                                        @permission('add_serials')
                                        <input type="hidden" name="equipment_id[]" value="{{ $item->id }}" />
                                        <input type="hidden" name="price_day[]" value="{{ $item->price_day }}" />
                                        <div class='row'>
                                            <div class="col-4">
                                                <select title="Inventory Selection"  class="select2 m-b-10 form-control inventory-list" required="" 
                                                        data-placeholder="Select Inventory" id="inventory" name='inventory_id[]'>
                                                    <option value=""></option>
                                                    @foreach ($inventories as $inv)
                                                    <option value="{{ $inv->id }}">{{ ucwords($inv->title) }}</option>   
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-8">
                                                <select title="Select Asset"  class="select2 select2-multiple serials items m-b-10 form-control " multiple="multiple" required="" 
                                                        data-placeholder="Select Asset" id="serial_value" name="assets{{$loop->index}}[]">

                                                </select>
                                            </div>
                                        </div>
                                        @endpermission
                                        @else
                                        <div class="row">
                                            <div class="col-2 nowrap">{{ $item->inventory->title ?? 'NA' }}</div>

                                        </div>
                                        @endif
                                    </td>
                                    
                                    <td>Refurbished Price</td>
                                    
                                    <td><span id='rental_total' style="color:palevioletred">{{'$'.number_format($item->price_day,2)}}</span></td>
                                </tr>

                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if ($order->status == config('constant.orders.deleted') && $order->delete_reason != '')
                    <div class="row">
                        <div class="col-12 text-center">
                            <h3 style="font-size:1.2rem;background-color: #a72733;padding: 5px"><span class="text-white">Cancellation/Delete Reason:  </span><u class="text-white">{{$order->delete_reason}}</u></h3><br/>
                        </div>
                    </div>
                    @endif
                    <div class="container mt-1">
                    
                    @if ($order->status == 'accepted' || $order->status == 'completed')
                    <h2>Shipment Information</h2>
                        <div class="shipping-info-container">
                            <div class="shipping-info-section">
                                <p><span class="info-label">Address:</span> <span class="info-value">1234 Elm Street, Apt 5B, Springfield, IL 62701</span></p>
                                <p><span class="info-label">City:</span> <span class="info-value">Springfield</span></p>
                                <p><span class="info-label">State:</span> <span class="info-value">Illinois</span></p>
                                <p><span class="info-label">Zip Code:</span> <span class="info-value">62701</span></p>
                                <p><span class="info-label">Country:</span> <span class="info-value">USA</span></p>
                                <p><span class="info-label">Phone:</span> <span class="info-value">(555) 123-4567</span></p>
                                <p><span class="info-label">Email:</span> <span class="info-value">johndoe@example.com</span></p>
                            </div>
                            <div class="shipping-info-section">
                                <p><span class="info-label">Shipment Attention:</span> <span class="info-value">Fragile - Handle with Care</span></p>
                                <p><span class="info-label">Shipment Tracking URL:</span> <span class="info-value"><a href="http://tracking.example.com/123456789">Track your shipment</a></span></p>
                                <p><span class="info-label">Tracking No:</span> <span class="info-value">123456789</span></p>
                                <p><span class="info-label">Shipment Insurance:</span> <span class="info-value">$500</span></p>
                            </div>
                        </div>
                    @endif
                    @if ($order->status == 'pending')
                    <h2>Complete RFQ Information</h2>
            <div class="shipping-info-container">
                <div class="shipping-info-section">
                    <div class="form-group">
                        <label for="address" class="info-label">Reply:</label>
                        <input type="text" class="form-control" id="address" name="address" value="{{ old('address') }}">
                    </div>
                </div>
            </div>
                    @endif
                    @if ($order->status == 'delivered')
                    <h2>iMed WO Reference</h2>
                    <div class="form-group col-md-4">
                    <input type="text" class="form-control" id="city" name="city" value="">
                    </div>
                    
            <!--button type="submit" class="btn btn-primary mt-2 mb-2 pull right">Update Shipment Info</button-->
        
    </div>
                    @endif
                    <div class="row font-12">
                        <div class="col-12 row order-section" style="border: 1px outset #eee;padding: 10px;">
                            <div class="hide sign-div">
                                <img class="img-signature" src="{{asset ('user-uploads/signatures/'.$order->pickup_signature)}}" />
                            </div>

                            @if($order->id == $order->order_id)
                            <div class="col">
                                @if ($order->status != config('constant.orders.deleted'))
                                <label class="order-dates-label">Taken On</label><br />
                                <span class="fw500">{{dformat($order->created_at)}}</span><br />
                                {{$order->createdby->name}}
                                <input id="submited_at" type="hidden" value="{{$order->submited_at}}"/>
                                @elseif ($order->status == config('constant.orders.deleted'))
                                <strong class="text-danger">Deleted at</strong><br />
                                {{dformat($order->deleted_at)}}<br />
                                {{$order->deletedby->name}}<br/>
                                @endif
                            </div>
                            @if ($order->consignment_order !=1)
                            <div class="col">
                                <label class="order-dates-label">Processed On</label><br/>
                                <span id="order_goal">{{dformat($order_goal)}}</span><br/>
                                {{isset($order->devliveredby->name) ? $order->devliveredby->name : ''}}<br/>
                            </div>
                            @endif
                            <div class="col">
                                <label class="order-dates-label">Shipped On</label><br />
                                <span id="delivered_at" class="fw500">{{ dformat($order->delivered_at) }}</span><br />
                                <span id="delivery_diff"style="font-weight: 600"></span>
                            </div>
                            @if ($order->consignment_order !=1)
                            <div class="col">
                                <label class="order-dates-label">Delivered On</label><br />
                                <span class="fw500">{{ dformat($order->accepted_at) }}</span><br />
                                @if (isset($order->staffaccepted->firstname))
                                {{$order->staffaccepted->firstname.' '.$order->staffaccepted->lastname}}<br/>
                                <a href="{{route('orders.show',[$order->id,'note'=>'noteaccept'])}}" target="_blank" class="view-order-note">Web</a>
                                &nbsp;&nbsp;-&nbsp;&nbsp;
                                <a target="_blank" href="{{route('orders.showPDF',[$order->id,'note'=>'noteaccept'])}}"  class="view-order-note">PDF</a>
                                @endif
                            </div>

                            @endif
                            @else
                            <div class="col">
                                @if ($order->status != config('constant.orders.reassigned'))
                                <label class="order-dates-label">Pickedup On</label>
                                <span class="fw500">{{ dformat($order->picked_at,true) }}</span>
                                @if (isset($order->pickedupby->name))
                                @if (isset($order->staffpicked->firstname))
                                {{$order->staffpicked->firstname.' '.$order->staffpicked->lastname}}
                                <a href="{{route('orders.show',[$order->id,'note'=>'notepickup'])}}" target="_blank" class="view-order-note">Web</a>
                                &nbsp;&nbsp;-&nbsp;&nbsp;
                                <a target="_blank" href="{{route('orders.showPDF',[$order->id,'note'=>'notepickup'])}}"  class="view-order-note">PDF</a>
                                @else
                                {{$order->pickedupby->name}}
                                @endif
                                @endif
                                @else
                                <label class="text-reassign">Reassigned On</label>
                                <span class="fw500">{{ dformat($order->reassigned_at) }}</span>
                                {{$order->reassigningby->name}}
                                @endif
                            </div>
                            <div class="col">
                                <a href="#" onclick="showOrder('{{explode("/", $order->order_id)[0]}}', true)" class="text-success">Click here to view Original Order</a>
                            </div>
                            @endif
                        </div>
                    </div>
                    @if ($order->status != config('constant.orders.reassigned') &&  $order->status != config('constant.orders.pickedup'))
                    <div class="col-12" id="users_notify_div hide">
                        <!--select class="select2 m-b-10 select2-multiple select-users" style="width: 100% !important; " multiple="multiple"
                                data-placeholder="Choose Users to Notify" id="notify_users" name="notify_users[]">
                            @foreach($employees as $emp)
                            <option value="{{ $emp->id }}">{{ ucwords($emp->name). ' ['.$emp->email.']' }}</option>
                            @endforeach
                        </select-->
                    </div>
                    @endif
                </div>

                @include('cust_orders._show_footer')

            </form>
        </div>
    </div>
    <div class="tab-pane hide" id="order_tracking" >
        <div id="modalBody" style="margin:0" class="modal-body order_tracking-container"> 
            <table class="table">
                <tr>
                    <td>
                        <b class="text-info">@trans(Customer) @trans(Notes): </b>   {!!$order->notes!!}
                    </td>
                </tr>
            </table>
            <table class="table" style="height: 300px;overflow-y: scroll;font-size: 11px">
                <thead>
                    <tr class="bg-whitesmoke">
                        <th>
                            Date At
                        </th>
                        <th>
                            By User
                        </th>
                        <th>
                            Status
                        </th>
                        <th>
                            User Notes
                        </th>
                    </tr>
                </thead>
                <tbody >
                    @foreach ($order->statustrans as $trans)
                    <tr>
                        <td>{{$trans->created_at}}</td>
                        <td>{{isset($trans->user->name)?$trans->user->name:'not-found!'}}</td>
                        <td><b class="text-success">{!!$trans->status!!}</b></td>
                        <td>{{$trans->notes}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="{{ asset('assets/node_modules/select2/dist/js/select2.full.min.js') }}"></script>

<script>
                                    var orderStatus = "{{$order->status}}";
                                    //let select2Options = ;

                                    $(".select-users,.items").select2({
                                    formatNoMatches: function () {
                                    return "{{ __('messages.noRecordFound') }}";
                                    }
                                    });
                                    $('.inventory-list').select2({ width: 'auto' });
                                    $('#tabContent > li').click(function () {
                                    $('#tabContent > li').removeClass('active show');
                                    $(this).tab("show");
                                    });
                                    if ($('#submited_at').val() != '') {
                                    var goal = moment("{{$order_goal}}");
                                    //if (orderStatus != "{{config('constant.orders.pending')}}" && $('#submited_at').html() != '') {
                                    {
                                    var now;
                                    if ($('#delivered_at').length) {
                                    if ($('#delivered_at').html().trim() != ''){

                                    now = moment($('#delivered_at').html(), 'MM/DD/YYYY HH:mm');
                                    } else {
                                    now = moment();
                                    }

                                    var diff = goal.diff(now, 'minutes');
                                    var h = 0, m = diff;
                                    if (Math.abs(diff) >= 60) {
                                    h = diff / 60 | 0,
                                            m = diff % 60 | 0;
                                    var diff_label = Math.abs(h) + " Hour " + Math.abs(m) + " Minute";
                                    } else {
                                    diff_label = Math.abs(m) + " Minute";
                                    }
                                    if (diff >= 0) {
                                    diff_label = '<span class="text-success">(-' + diff_label + ')</span>';
                                    } else {
                                    diff_label = '<span class="text-danger">(+' + diff_label + ')</span>';
                                    }
                                    }
                                    if ($('#delivered_at').html() != '') {

                                    $('#delivery_diff').html(diff_label);
                                    }
                                    }
                                    }

                                    $('a.status_change').click(function () {
                                    let text = "Are you sure to " + $(this).text();
                                    if (orderStatus == "{{config('constant.orders.pending')}}" && $('#submited_at').val() != '') {
                                    text = text + '  ' + diff_label;
                                    } else if (orderStatus == "{{config('constant.orders.delivered')}}") {

                                    if (!$(this).hasClass('activate-order')){

                                    let url = "{{ route('orders.accept',':id') }}";
                                    url = url.replace(':id', "{{$order->id}}");
                                    showModal(url, '#application-lg-modal');
                                    return false;
                                    }

                                    } else if (orderStatus == "{{config('constant.orders.accepted')}}") {
                                    let url = "{{ route('orders.complete',':id') }}";
                                    url = url.replace(':id', "{{$order->id}}");
                                    showModal(url, '#application-lg-modal');
                                    return false;
                                    } else if (orderStatus == "{{config('constant.orders.completed')}}") {
                                    if ($(this).hasClass('reassign')) {
                                    let url = "{{ route('orders.reassign',':id') }}";
                                    url = url.replace(':id', "{{$order->id}}");
                                    showModal(url);
                                    return false;
                                    } else if ($(this).hasClass('pickup')){
                                    let url = "{{ route('orders.pickup',':id') }}";
                                    url = url.replace(':id', "{{$order->id}}");
                                    showModal(url, '#application-lg-modal');
                                    return false;
                                    }
                                    }
                                    swal({
                                    title: $(this).text(),
                                            text: text,
                                            type: "warning",
                                            html: true,
                                            showCancelButton: true,
                                            confirmButtonColor: $(this).css("background-color"),
                                            confirmButtonText: "@lang('app.yes')",
                                            cancelButtonText: "@lang('app.no')",
                                            closeOnConfirm: true,
                                            closeOnCancel: true,
                                    }, function (isConfirm) {
                                    if (isConfirm) {
                                    let params = '';
                                    $.easyAjax({
                                    url: "{{ route('cust_orders.submitStatus') }}",
                                            container: '#serialForm',
                                            type: "POST",
                                            data: $('#serialForm').serialize() + params,
                                            success: function (response) {
                                            $("body").removeClass("control-sidebar-slide-open");
                                            if (response.status === 'success') {
                                            if (typeof table !== 'undefined') {
                                            table.draw();
                                            }
                                            }
                                            }
                                    });
                                    }
                                    });
                                    });
                                    $('a.view-signature').click(function () {
                                    $('#application-md-modal #modal-data-application > div > div.modal-header > button.close').html('X');
                                    $('#application-md-modal #modal-data-application > div > div.modal-header > h4.modal-title').html('Staff Signature');
                                    $.viewModal('#application-md-modal');
                                    $('#application-md-modal #modal-data-application > div > div.modal-body').html($('.sign-div').html());
                                    });</script>
<script>
    $(document).ready(function() {

    var current_items;
    var select2Options = { width: 'resolve' };
    var apiUrl = "{{ route('admin.inventory.items-list',[':parentId:',':item_id']) }}";
    apiUrl = apiUrl.replace(':item_id', $('.eq_id').val());
    // $('select').select2(select2Options);                 
    var cascadLoading = new $.select2Link($('.inventory-list'), $('.items'), apiUrl, select2Options);
    cascadLoading.then(function(parent, child, items) {
    current_items = items;
    //$('#rental_total').html('$00.00');

    });
    /*if ($('.inventory-list').val() !== '')
     $('.inventory-list').change();*/

    });
    function editAsset(item, asset){
    var url = "{{ route('orders.editAsset',[':item',':asset']) }}";
    url = url.replace(':item', item);
    url = url.replace(':asset', asset);
    showModal(url);
    }

    function showChangeRoom(id){
    var url = "{{ route('orders.changeRoom',':item') }}";
    url = url.replace(':item', id);
    showModal(url);
    }
</script>