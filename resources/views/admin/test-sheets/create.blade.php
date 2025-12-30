@extends('layouts.app')
@section('content')
<h2>Create Test Sheet Template</h2>
<form method="POST" action="{{ url('admin/test-sheets/store') }}">
    @csrf
    <label>Template Name</label>
    <input type="text" name="name" required>
    <br><br>

    <label for="equipment_ids">Assign to Equipment</label>
    <select name="equipment_ids[]" id="equipment_ids" multiple style="width: 50%;">
        @foreach($equipments as $equipment)
            <option value="{{ $equipment->id }}">{{ $equipment->name }} (SN: {{ $equipment->serial_number }})</option>
        @endforeach
    </select>
    <br><br>

    <div id="fields"></div>
    <button type="button" id="add-field">Add Field</button>
    <button type="submit">Save Template</button>
</form>
@endsection

@section('scripts')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>
<script>
$(function(){
    $('#equipment_ids').select2({placeholder: "Select equipment...", allowClear: true});
    let fieldIdx = 0;
    $('#add-field').click(function() {
        $('#fields').append(`
        <div>
            <input type="text" name="fields[${fieldIdx}][label]" placeholder="Label" required>
            <select name="fields[${fieldIdx}][input_type]">
                <option value="select">Select (✓/✗/N/A)</option>
                <option value="checkbox">Checkbox</option>
                <option value="text">Text</option>
                <option value="textarea">Textarea</option>
            </select>
            <input type="text" name="fields[${fieldIdx}][options]" placeholder='["✓","✗","N/A"] for select/radio'>
            <label>Required <input type="checkbox" name="fields[${fieldIdx}][is_required]" value="1"></label>
        </div>
        `);
        fieldIdx++;
    });
});
</script>
@endsection
