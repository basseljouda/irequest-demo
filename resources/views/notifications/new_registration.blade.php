<a href="{{ route('admin.hospital-staff.index') }}" class="dropdown-item text-sm">
    <i class="fa fa-user-plus mr-2"></i>
    <span class="text-truncate-notify" style="overflow-y: hidden" title="full name">New Staff Registration
    </span>
    <i class="fa mr-2"></i>
    <small class="text-truncate-notify">{{isset($notification->data['data']['name'])  ? $notification->data['data']['name'].' - '.$notification->data['data']['phone'] : ''}}</small>
    <br />
    <span class="float-right text-muted text-sm">{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $notification->created_at)->diffForHumans() }}</span>
    <div class="clearfix"></div>
</a>