<style>
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
        padding: 10px;
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

</style>

<ul class="nav nav-tabs" id="tabContent">
    <li class="active show" href="#order_data" data-toggle="tab">#{{$order->id}} / SO: {{nil($order->order_no)}}</li>
    <li href="#order_tracking" data-toggle="tab">Order Tracking</li>
    <span class="top-head mobile-hide">
        @if ($order->reassigned_order != '')
        <a href="#" onclick="showOrder('{{$order->reassigned_order}}', true)"><span class="small text-danger">Reassigned From: <span class="text-bold">#{{$order->reassigned_order}}</span></span></a>
        @endif
        @if ($order->reassign_to != '')
        <a href="#" onclick="showOrder('{{$order->reassign_to}}', true)"><span class="small text-danger">Reassigned To: <span class="text-bold">#{{$order->reassign_to}}</span></span></a>
        @endif
        <strong class="order-head-status text-white bg-{{$order->status}}">{{ucwords($order->status).' '}}</strong>
        <i class="ti-close right-side-toggle"></i>
    </span>
</ul>
<div class="tab-content order-details">
    <div class="tab-pane active" id="order_data">     
        <div id="modalBody" class="modal-body"> 
            <div class="r-panel-body p-3">
                <div class="row font-12 order-section">
                    <div class="col-12 row">
                        <div class="col-3">
                            <label class="app-color">Patient</label><br />
                            {{ ucwords($order->patient_name) }}
                        </div>
                        <div class="col-2">
                            <label class="app-color">Bill Start</label><br />
                            {{dformat($order->bill_started)}}
                        </div>
                        <div class="col-3">
                            <label class="app-color">Bill End</label><br />
                            <label>{{dformat($order->bill_completed,true)}}</label>
                        </div>
                        <div class="col-2">
                            <label class="app-color">Days</label><br />
                            {{$total_days}}
                        </div>
                        <div class="col-2">
                            <label class="app-color">Total</label><br />
                            <label class="text-danger">${{$order_total}}</label>
                        </div>
                    </div>
                </div>
                <br />
                <div class="row order-section2">
                    <div class="col-12 row">
                        <div class="col-3">
                            <strong class="app-color">Site</strong><br />
                            {{ ucwords($order->hospital->name) }}<br />
                            {{$order->hospital->address}}<br />
                            {{$order->hospital->city.' '.$order->hospital->state.' '.$order->hospital->zip}}<br />
                            {{$order->hospital->website}}<br />
                        </div>
                        <div class="col-2">
                            <strong class="app-color">Staff</strong><br />
                            {{ ucwords($order->staff->firstname.' '.$order->staff->lastname) }}<br />
                            {{$order->staff->title->title_name}}<br />
                            {{$order->staff->phone}}<br />
                            {{$order->staff->email}}
                        </div>
                        <div class="col-3">
                            <strong class="app-color">Date Needed</strong><br />
                            {{dformat($order->date_needed)}}
                        </div>
                        <div class="col-2">
                            <strong class="app-color">Cost Center</strong><br />
                            {{ isset($order->costcenter->name) ? $order->costcenter->name : '' }}
                        </div>
                        <div class="col-2">
                            <strong class="app-color">Unit/Floor</strong><br />
                            {{ $order->unit_floor }}<br />
                            <strong class="app-color">Room</strong><br />
                            {{ $order->room_no }}
                        </div>
                    </div>
                </div>


            </div>
            <form class="ajax-form full-width" id="serialForm" role="form">
                <input type="hidden"name="id"value="{{$order->id}}" />
                @csrf
                <div class="r-panel-body p-3">
                    <div class="row font-12 col-12">
                        <table class="table order-table">
                            <thead>
                                <tr style="background-color: #f5f5f561;color:gray">
                                    <th>
                                        Equipment
                                    </th>

                                    <th><div class='row'>
                                            <div class="col-6">Inventory</div>
                                            <div class="col-6">
                                                Serial / DOT#
                                            </div>
                                        </div>
                                    </th>
                                    <th>
                                        Rate
                                    </th>
                                    <th>
                                        Notes
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order->equipments as $item)
                                <tr>
                                    <td style="width:30%">
                                        <label class="bold text-dark">
                                            {{$item->equipment->name.' - '.$item->equipment->modality->name.' - '.$item->equipment->sub_modality->name}}
                                            <input type="hidden" value="{{$item->equipment_id}}" class="eq_id" />
                                        </label>
                                    </td>

                                    <td>
                                        @if ($order->status == config('constant.orders.pending') && $order->submited_at=='')
                                        @permission('add_serials')
                                        <input type="hidden" name="equipment_id[]" value="{{ $item->id }}" />
                                        <input type="hidden" name="price_day[]" value="{{ $item->price_day }}" />
                                        <div class='row'>
                                            <div class="col-6">
                                                <select title="Inventory Selection"  class="select2 serials m-b-10 form-control inventory-list" required="" 
                                                        data-placeholder="Select Inventory" id="inventory" name='inventory_id'>
                                                    <option value=""></option>
                                                    @foreach ($inventories as $inv)
                                                    <option value="{{ $inv->id }}">{{ ucwords($inv->name) }}</option>   
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-6">
                                                <select title="Select Value"  class="select2 serials items m-b-10 form-control " required="" 
                                                        data-placeholder="Select Value" id="serial_value" name="serial[]">

                                                </select>
                                            </div>
                                        </div>
                                        @endpermission
                                        @else
                                        <div class="row">
                                            <div class="col-6">Local Inventory</div>
                                            <div class="col-6">
                                                @if(\App\InventoryItems::where("id", $item->serial_no)->first()!==null)
                                                <label><u>{{ \App\InventoryItems::where("id", $item->serial_no)->first()->serial_value.' / '.
                                                \App\InventoryItems::where("id", $item->serial_no)->first()->dot_value}}</u></label>
                                                @endif
                                            </div>
                                        </div>
                                        @endif
                                    </td>
                                    <td><label>${{ $item->equipment->price_day }}</label></td>
                                    <td>{{ $item->notes }}</td>
                                </tr>

                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="row font-12">
                        <div class="col-12 row order-section">
                            <div class="col-3">
                                @if ($order->status != config('constant.orders.deleted'))
                                <label class="app-color">Taken</label><br />
                                <span class="fw500">{{dformat($order->created_at)}}</span><br />
                                {{$order->createdby->name}}
                                <input id="submited_at" type="hidden" value="{{$order->submited_at}}"/>
                                @elseif ($order->status == config('constant.orders.deleted'))
                                <strong class="text-danger">Deleted at<br />
                                    {{dformat($order->deleted_at)}}<br />
                                    {{$order->deletedby->name}}
                                </strong>
                                @endif
                            </div>
                            <div class="col-2">
                                <label class="app-color">Order Goal</label><br />
                                <span id="order_goal">{{dformat($order_goal)}}</span>
                                {{isset($order->devliveredby->name) ? $order->devliveredby->name : ''}}
                            </div>
                            <div class="col-3">
                                <label class="app-color">Delivered</label><br />
                                <span id="delivered_at" class="fw500">{{ dformat($order->delivered_at) }}</span><br />
                                <span id="delivery_diff"style="font-weight: 600"></span>
                            </div>
                            <div class="col-2">
                                <label class="app-color">Accepted</label><br />
                                <span class="fw500">{{ dformat($order->accepted_at) }}</span><br />
                                @if (isset($order->staffaccepted->firstname))
                                {{$order->staffaccepted->firstname.' '.$order->staffaccepted->lastname}}<br/>
                                <a href="{{route('orders.show',$order->id)}}" target="_blank" class="view-order-note">Web</a>
                                &nbsp;&nbsp;-&nbsp;&nbsp;
                                <a href="{{route('orders.showPDF',$order->id)}}"  class="view-order-note">PDF</a>
                                <div class="hide sign-div">
                                    <img class="img-signature" src="{{asset ('user-uploads/signatures/'.$order->staff_signature)}}" />
                                </div>
                                @endif
                            </div>
                            <div class="col-2">
                                @if ($order->status != config('constant.orders.reassigned'))
                                <label class="app-color">Picked</label><br />
                                <span class="fw500">{{ dformat($order->picked_at,true) }}</span><br />
                                @if (isset($order->pickedupby->name))
                                {{$order->pickedupby->name}}
                                @endif
                                @else
                                <label class="text-reassign">Reassigned</label><br />
                                <span class="fw500">{{ dformat($order->reassigned_at) }}</span><br />
                                {{$order->reassigningby->name}}
                                @endif
                            </div>
                        </div>
                    </div>
                    @if ($order->status != config('constant.orders.reassigned') &&  $order->status != config('constant.orders.pickedup'))
                    <div class="col-12" id="users_notify_div">
                        <select class="select2 m-b-10 select2-multiple select-users" style="width: 100% !important; " multiple="multiple"
                                data-placeholder="Choose Users to Notify" id="notify_users" name="notify_users[]">
                            @foreach($employees as $emp)
                            <option value="{{ $emp->id }}">{{ ucwords($emp->name). ' ['.$emp->email.']' }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                </div>

                @include('orders._show_footer')

            </form>
        </div>
    </div>
    <div class="tab-pane" id="order_tracking" >
        <div id="modalBody" class="modal-body order_tracking-container"> 
            <table class="table">
                <tr>
                    <td>
                        {!!$order->notes!!}
                    </td>
                </tr>
            </table>
            <table class="table">
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
                <tbody>
                    @foreach ($order->statustrans as $trans)
                    <tr>
                        <td>{{$trans->created_at}}</td>
                        <td>{{isset($trans->user->name)?$trans->user->name:'not-found!'}}</td>
                        <td><b class="text-success">{{ucwords($trans->status)}}</b></td>
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

            $(".select-users").select2({
            formatNoMatches: function () {
            return "{{ __('messages.noRecordFound') }}";
            }
            });
            $('.serials').select2({ width: 'resolve' });
            $('#tabContent > li').click(function () {
            $('#tabContent > li').removeClass('active show');
            $(this).tab("show");
            });
            if ($('#submited_at').val() != '') {
            var goal = moment("{{$order_goal}}");
            //if (orderStatus != "{{config('constant.orders.pending')}}" && $('#submited_at').html() != '') {
            {
            var now;
            if ($('#delivered_at').html().trim() != '')
            {
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

            $('a.status_change').click(function () {
            let text = "Are you sure to " + $(this).text();
            if (orderStatus == "{{config('constant.orders.pending')}}" && $('#submited_at').val() != '') {
            text = text + '  ' + diff_label;
            } else if (orderStatus == "{{config('constant.orders.delivered')}}") {
            let url = "{{ route('orders.accept',':id') }}";
            url = url.replace(':id', "{{$order->id}}");
            showModal(url, '#application-lg-modal');
            return false;
            }else if (orderStatus == "{{config('constant.orders.accepted')}}") {
            let url = "{{ route('orders.complete',':id') }}";
            url = url.replace(':id', "{{$order->id}}");
            showModal(url, '#application-lg-modal');
            return false;
            } else if ($(this).hasClass('reassign')) {
            let url = "{{ route('orders.reassign',':id') }}";
            url = url.replace(':id', "{{$order->id}}");
            showModal(url);
            return false;
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
            url: "{{ route('orders.submitStatus') }}",
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
    //const elements = document.querySelectorAll('.inventory-list');
    /* document.querySelectorAll('.inventory-list').forEach(function(inv) {
     var select2Options = { width: 'resolve' };
     var apiUrl = "{{ route('admin.inventory.items-list',[':parentId:',':item_id']) }}";
     apiUrl = apiUrl.replace(':item_id', $('#eq_id').val());
     // $('select').select2(select2Options);                 
     var cascadLoading = new $.select2Link(inv, $('.items'), apiUrl, select2Options);
     cascadLoading.then( function(parent, child, items) {
     //console.log(items);
     });
     });*/
    /*    $( ".inventory-list" ).each(function( index ) {
     // alert( index + ": "  +document.getElementsByClassName("items")[index]);
     var select2Options = { width: 'resolve' };
     var apiUrl = "{{ route('admin.inventory.items-list',[':parentId:',':item_id']) }}";
     apiUrl = apiUrl.replace(':item_id', $('.eq_id:eq( '+index+' )').val());
     // $('select').select2(select2Options);                 
     var cascadLoading = new $.select2Link($(this), $('.items:eq( '+index+' )'), apiUrl, select2Options);
     cascadLoading.then( function(parent, child, items) {
     //console.log(items);
     });
     });*/
    var select2Options = { width: 'resolve' };
    var apiUrl = "{{ route('admin.inventory.items-list',[':parentId:',':item_id']) }}";
    apiUrl = apiUrl.replace(':item_id', $('.eq_id').val());
    // $('select').select2(select2Options);                 
    var cascadLoading = new $.select2Link($('#inventory'), $('.items'), apiUrl, select2Options);
    cascadLoading.then(function(parent, child, items) {
    //console.log(items);
    });
    });
</script>