@php
    $notifyEmployees = $employees ?? collect();
@endphp
<div class="order-notify-fields">
    <div class="form-group">
        <textarea name="notes" class="form-control" rows="2" placeholder="{{ __('Add any internal note for this action…') }}"></textarea>
    </div>

    <div class="form-group mb-0">
        <label class="text-muted text-uppercase small">{{ __('Notify teammates') }}</label>
        <select name="notify_users[]" class="form-control select2 order-notify-select" multiple
                data-placeholder="{{ __('Select users to notify') }}">
            @foreach($notifyEmployees as $employee)
                <option value="{{ $employee->id }}">{{ $employee->name }} [{{ $employee->email }}]</option>
            @endforeach
        </select>
        <small class="form-text text-muted mt-1">{{ __('Everyone selected will receive an email update about this change.') }}</small>
    </div>
</div>

