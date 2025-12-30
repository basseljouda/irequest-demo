<a href="#" onclick="showOrder('{{$notification->data['data']['id']}}',true)" class="dropdown-item text-sm">
    <i class="text-danger fa fa-file-text mr-2"></i>
    <span class="text-truncate-notify" style="overflow-y: hidden">New Order #{{$notification->data['data']['id']}}
    </span>
    <i class="fa mr-2"></i>
    <small class="text-truncate-notify">{{isset($notification->data['data']['notes'])  ? $notification->data['data']['notes'] : ''}}</small>
    <br />
    <span class="float-right text-muted text-sm">{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $notification->created_at)->diffForHumans() }}</span>
    <div class="clearfix"></div>
</a>