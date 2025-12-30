{{-- Details Patient Info Component
    Parameters:
    - $order: Order model
--}}
<div class="row order-section2">
    <div class="col-12 row">
        <div class="col">
            <strong class="app-color">Patient</strong><br />
            {{ ucwords($order->patient_name) }}<br/>
            <strong class="app-color">Unit/Floor: &nbsp;</strong>{{ $order->unit_floor }}<br />
            <strong class="app-color">Room No: &nbsp;</strong>{{ $order->room_no }}<br/>
            @permission('edit_orders')
            @if (user()->hasrole('superadmin') || $order->status == 'pending')
            <a href="javasript:;" class="" onclick="showChangeRoom({{$order->id}})"><u>Change Patient information?</u></a>
            @endif
            @endpermission
        </div>
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
            {{dformat($order->date_needed,true)}}<br/><br/>
            <strong class="app-color">Cost Center</strong><br />
            {{ isset($order->costcenter->name) ? $order->costcenter->name : '' }}
        </div>
        
    </div>
</div>

