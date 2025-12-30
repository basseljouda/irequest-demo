@extends('layouts.app')

@section('content')
<style>
    .task-input{
        font-weight: 700;
    }
    .td-remove-row{
        text-align: right;
    }
</style>
<div class="container-fluid">
    <form id="import-form" enctype="multipart/form-data" autocomplete="off">
        @csrf
        <div class="row justify-content-center">
            <div id="import-form-result" class="col-12 mb-1"></div>

            <div class="col-xl-6 col-lg-12 col-md-12">
                <div class="card shadow mb-4 mt-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Create or Import Test Sheet Template</h4>
                    </div>
                    <div class="card-body">

                        <div class="mb-3">
                            <label class="form-label">Import Test Sheet from Excel</label>
                            <input type="file" name="excel_file" id="excel_file" class="form-control" accept=".xlsx,.xls">
                            <button type="button" class="hide btn btn-secondary mt-2" id="btn-import-excel">Import from Excel</button>
                            <div class="form-text">Supported formats: .xlsx, .xls</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Template Name <span class="text-danger">*</span></label>
                            <input type="text" name="template_name" id="template_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Assign to Equipment <span class="text-danger">*</span></label>
                            <select name="equipments[]" id="equipment_ids" class="form-select" multiple required style="width:100%;">
                                @foreach($equipments as $equipment)
                                <option value="{{ $equipment->name }}">{{ $equipment->name }}</option>
                                @endforeach
                            </select>
                            <div class="form-text">You can select one or more equipment assets for this template.</div>
                        </div>
                        <button type="submit" class="btn btn-primary mt-3 pull-right">Save Template</button>
                    </div>
                </div>
            </div>
            <div class="col-xl-6 col-lg-12 col-md-12">
                <div class="card shadow mb-4 mt-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Inspection Tasks List</h5>
                    </div>
                    <div class="card-body" style="max-height: 550px; overflow-y: auto;">
                        <div class="mb-3">
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle" id="tasks-table">
                                    <thead>
                                        <tr>
                                            <th>Tasks</th>
                                            <th style="width:40px">
                                                <button type="button" class="btn btn-success btn-xs btn-add-row">Add Task</button>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <input type="text" name="tasks[]" class="form-control task-input" required placeholder="Enter inspection task details">
                                            </td>
                                            
                                            <td class="td-remove-row">
                                                <button type="button" class="btn btn-danger btn-xs btn-remove-row" disabled><i class="fa fa-close"></i></button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('footer-script')
<script src="{{ asset('assets/node_modules/select2/dist/js/select2.full.min.js') }}"></script>


<script>
$(function () {
    document.getElementById('excel_file').addEventListener('change', function (e) {
        // Get the selected file
        const file = e.target.files[0];

        if (file) {
            let formData = new FormData();
            formData.append('excel_file', file);
            $.easyBlockUI();
            $.ajax({
                url: '{{ url("admin/test-sheets/import-excel") }}',
                type: 'POST',
                data: formData,
                processData: false, contentType: false,
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                success: function (resp) {
                    $.unblockUI();
                    $('#tasks-table tbody').empty();
                    if (Array.isArray(resp.tasks) && resp.tasks.length > 0) {
                        // Add imported tasks as rows
                        let $tbody = $('#tasks-table tbody');
                        resp.tasks.forEach(function (task) {
                            let row = `<tr>
                            <td><input type="text" name="tasks[]" class="form-control task-input" required value="${task}"></td>
                            <td class="td-remove-row"><button type="button" class="btn btn-danger btn-xs btn-remove-row"><i class="fa fa-close"></i></button></td>
                        </tr>`;
                            $tbody.append(row);
                        });
                        $('#import-form-result').html('<div class="alert alert-success">Imported ' + resp.tasks.length + ' tasks from Excel.</div>');
                        // Enable remove buttons
                        //$('#tasks-table .btn-remove-row').prop('disabled', $tbody.find('tr').length === 1);
                        // Remove the file extension
                        const fileNameWithoutExtension = file.name.replace(/\.[^/.]+$/, "");

                        // Find the template_name field and set its value
                        const templateNameField = document.getElementById('template_name');
                        if (templateNameField) {
                            templateNameField.value = fileNameWithoutExtension;
                        }
                    } else {
                        $('#import-form-result').html('<div class="alert alert-warning">No tasks found in the file.</div>');
                    }
                },
                error: function (xhr) {
                    $.unblockUI();
                    let msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Failed to import tasks.';
                    $('#import-form-result').html('<div class="alert alert-danger">' + msg + '</div>');
                }
            });

        }
    });
    $('#equipment_ids').select2({
        placeholder: "Select equipment...",
        allowClear: true,
        width: '100%'
    });

    // Add row
    $(document).on('click', '.btn-add-row', function () {
        let $tbody = $('#tasks-table tbody');
        let row = `<tr>
                            <td><input type="text" name="tasks[]" class="form-control task-input" required placeholder="Enter inspection task details"></td>
                            <td class="td-remove-row"><button type="button" class="btn btn-danger btn-xs btn-remove-row"><i class="fa fa-close"></i></button></td>
                        </tr>`;
                            $tbody.prepend(row);
        // Enable all remove buttons if more than one row
        $('#tasks-table .btn-remove-row').prop('disabled', $tbody.find('tr').length === 1);
    });

    // Remove row
    $(document).on('click', '.btn-remove-row', function () {
        let $tbody = $('#tasks-table tbody');
        if ($tbody.find('tr').length > 1) {
            $(this).closest('tr').remove();
        }
        $('#tasks-table .btn-remove-row').prop('disabled', $tbody.find('tr').length === 1);
    });

    // AJAX submit for saving template
    $('#import-form').on('submit', function (e) {
        $.easyBlockUI();
        e.preventDefault();
        let formData = $(this).serialize();
        $.ajax({
            url: '{{ url("admin/test-sheets/store") }}',
            type: 'POST',
            data: formData,
            success: function (resp) {
                $.unblockUI();
                $('#import-form-result').html('<div class="alert alert-success">Template saved successfully!</div>');
               
            },
            error: function (xhr) {
                $.unblockUI();
                let errors = xhr.responseJSON.errors || {};
                let msg = xhr.responseJSON.message || 'Could not save template.';
                let html = '<div class="alert alert-danger">' + msg;
                $.each(errors, function (k, v) {
                    html += '<br>' + v[0];
                });
                html += '</div>';
                $('#import-form-result').html(html);
            }
        });
    });

    // On first load, disable remove if only one row
    $('#tasks-table .btn-remove-row').prop('disabled', $('#tasks-table tbody tr').length === 1);
});
</script>
@endpush
