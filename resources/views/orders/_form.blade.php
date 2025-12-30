@push('head-script')
<link rel="stylesheet"
      href="{{ asset('assets/node_modules/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css') }}">
<link rel="stylesheet" href="{{ asset('css/orders-design-system.css') }}">
<link rel="stylesheet" href="{{ asset('css/orders.css') }}">
@endpush
<form class="ajax-form" id="OrderForm" method="{{isset($order) ? 'PUT' : 'POST'}}" role="form" action="{{isset($order) ? route('orders.update', $order->id) : route('orders.store')}}">
    @csrf

    <div class="order-form-container">
        @include('orders.components.form-header', ['order' => $order ?? null])
        
        @include('orders.components.form-basic-info', ['order' => $order ?? null])
        
        @include('orders.components.form-hospital-staff', ['hospitals' => $hospitals, 'order' => $order ?? null])
        
        @include('orders.components.form-patient-info', ['cost_centers' => $cost_centers, 'order' => $order ?? null])
        
        @include('orders.components.form-equipment', ['order' => $order ?? null])
    </div>
</form>
@push('footer-script')
<script src="{{ asset('assets/node_modules/select2/dist/js/select2.full.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js') }}" type="text/javascript"></script>
<script>
    // Pass data to JavaScript
    window.currentStaffId = "{{ isset(user()->is_staff) ? user()->is_staff->id : '' }}";
            @isset($order)
    window.orderStaffId = '{{ $order->staff_id }}';
            @endisset
</script>
<script src="{{ asset('js/orders/orders-form.js?v='.$build_version) }}"></script>
@endpush