{{-- Signature Capture Component
    Parameters:
    - $name: Field name for signature (default: 'signature')
    - $label: Label text (default: 'Signature')
    - $resetButtonText: Reset button text (default: 'Clear Signature')
    - $resetButtonClass: Reset button class (default: 'btn-danger')
    - $signatureId: ID for signature div (default: 'signature')
    - $resetId: ID for reset button (default: 'reset')
--}}
@php
    $name = $name ?? 'signature';
    $label = $label ?? 'Signature';
    $resetButtonText = $resetButtonText ?? 'Clear Signature';
    $resetButtonClass = $resetButtonClass ?? 'btn-danger';
    $signatureId = $signatureId ?? 'signature';
    $resetId = $resetId ?? 'reset';
@endphp

<div class="row">
    <div class="col-6">
        <span class="signature-label">{{ $label }}:</span>
    </div>
    <div class="col-6 text-right">
        <a href="#" id="{{ $resetId }}" class="btn {{ $resetButtonClass }} btn-sm signature-label">{{ $resetButtonText }}</a>
    </div>
    <div id="{{ $signatureId }}"></div>
</div>
<textarea id="signature_capture" name="{{ $name }}" class="hide"></textarea>

