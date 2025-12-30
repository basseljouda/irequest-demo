{{-- Staff Selector Component
    Parameters:
    - $staff: Collection of staff members
    - $selectedStaffId: Optional - ID of selected staff (default: $order->staff_id if $order exists)
    - $name: Field name (default: 'staff')
    - $id: Field ID (default: 'staff')
    - $required: Boolean - whether field is required (default: false)
    - $label: Label text (default: 'Staff member')
    - $colClass: Column class (default: 'col-6')
--}}
@php
    $name = $name ?? 'staff';
    $id = $id ?? 'staff';
    $required = $required ?? false;
    $label = $label ?? 'Staff member';
    $colClass = $colClass ?? 'col-6';
    $selectedStaffId = $selectedStaffId ?? (isset($order) ? $order->staff_id : null);
@endphp

<div class="{{ $colClass }}">
    <div class="form-group">
        <label class="add-modal" onclick="showModal('{{ route('admin.hospital-staff.create') }}', '#staff-modal')">{{ $label }}</span></label>
        <select class="select2 m-b-10 form-control" 
                data-placeholder="Select Staff Member" 
                name="{{ $name }}" 
                id="{{ $id }}"
                @if($required) required @endif>
            @foreach ($staff as $item)
                <option @if ($item->id == $selectedStaffId) selected @endif value="{{ $item->id }}">
                    {{ ucwords($item->firstname .' '.$item->lastname) }}
                </option>   
            @endforeach
        </select>
    </div>
</div>

