<script src="{{ asset('assets/node_modules/select2/dist/js/select2.full.min.js') }}"></script>
<script src="{{ asset('js/orders/orders-utils.js?v='.$build_version) }}"></script>
<script>
    // Pass data to JavaScript
    window.orderStatus = "{{$order->status}}";
    window.orderId = "{{$order->id}}";
    window.orderGoal = "{{$order_goal}}";
    window.noRecordFoundText = "{{ __('messages.noRecordFound') }}";
    window.ordersAcceptUrl = "{{ route('orders.accept',':id') }}";
    window.ordersCompleteUrl = "{{ route('orders.complete',':id') }}";
    window.ordersReassignUrl = "{{ route('orders.reassign',':id') }}";
    window.ordersPickupUrl = "{{ route('orders.pickup',':id') }}";
    window.ordersSubmitStatusUrl = "{{ route('orders.submitStatus') }}";
    window.ordersEditAssetUrl = "{{ route('orders.editAsset',[':item',':asset']) }}";
    window.ordersChangeRoomUrl = "{{ route('orders.changeRoom',':item') }}";
    window.inventoryItemsListUrl = "{{ route('admin.inventory.items-list',[':parentId:',':item_id']) }}";
    window.pickupIssueResolveUrl = "{{ route('orders.pickupIssues.resolve',':id') }}";
</script>
<script src="{{ asset('js/orders/orders-details.js?v='.$build_version) }}"></script>