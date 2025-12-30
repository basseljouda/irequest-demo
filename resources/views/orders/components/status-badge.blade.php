{{-- Status Badge Component
    Parameters:
    - $status: Order status string
    - $text: Optional custom text (default: uses config('constant.orders.'.$status))
    - $class: Additional CSS classes (default: '')
    - $showIcon: Show close icon (default: false)
    - $onclick: Optional onclick attribute (default: '')
--}}
@php
    $text = $text ?? ucwords(config('constant.orders.'.$status, $status));
    $class = $class ?? '';
    $showIcon = $showIcon ?? false;
    $onclick = $onclick ?? '';
@endphp

<strong class="order-head-status text-white bg-{{ $status }} {{ $class }}" @if($onclick) onclick="{{ $onclick }}" @endif>
    {{ $text }}
    @if($showIcon)
        <i class="ti-close right-side-toggle"></i>
    @endif
</strong>

