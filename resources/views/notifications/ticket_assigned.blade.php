<a href="#" onclick="showTicket('{{$notification->data['data']['id']}}',true)" class="dropdown-item text-sm">
    <i class="fa fa-file-text mr-2"></i>
    <span class="text-truncate-notify text-info" style="overflow-y: hidden">Ticket #{{$notification->data['data']['id']}} assigned
    </span>
    <i class="text-warning fa fa-check-square-o mr-2 small"></i> New Status: <span class="badge text-white bg-{{$notification->data['data']['status']}}">{{ucwords($notification->data['data']['status'])}}</span>
    
    <br />
    <span class="float-right text-muted text-sm">
        {{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $notification->created_at)->diffForHumans() }}
    </span>
    
    <div class="clearfix"></div>
</a>