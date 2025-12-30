<a href="#" onclick="showTicket('{{$notification->data['data']['id']}}',true)" class="dropdown-item text-sm">
    <i class="fa fa-file-text mr-2"></i>
    <span class="text-truncate-notify text-danger" style="overflow-y: hidden;font-weight: 500">Ticket Approval Notification #{{$notification->data['data']['id']}}
    </span>
    <i class="text-warning fa fa-exclamation-triangle mr-2 small"></i> Action Required - Click Here
    <br/>
    <span class="float-right text-muted text-sm">
        {{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $notification->created_at)->diffForHumans() }}
    </span>
    <div class="clearfix"></div>
</a>