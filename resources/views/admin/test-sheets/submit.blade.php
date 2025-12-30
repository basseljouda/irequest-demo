@extends('layouts.app')
@section('content')
@push('head-script')
<link rel="stylesheet"
      href="{{ asset('assets/node_modules/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css') }}">
<style>
    
    input,select{
        font-weight:700;
    }
    .select2-selection__rendered{
       color: #072cbb !important;
    
    font-weight: 600; 
    }
    .td-remove-row{
        text-align:right;
    }
    .modal-body{
        overflow:hidden;
    }
</style>
<style>
    .select2-container{
        width: -webkit-fill-available !important;
    }
    #collapseOne{

        overflow: auto;
    }
    .help-block{
        position: absolute;
        font-size: 12px;
        right: 0;
    }
</style>
@endpush
@section('create-button')
<button type="submit" id="create-hospital" class="btn btn-info"><i class="fa fa-save"></i> Save Progress</button>
            <button type="submit" id="create-hospital" class="btn btn-success">Final Submit</button>
@endsection
<form id="acceptForm" class="ajax-form mb-3" action="{{ route('test-sheets.submit') }}" method="POST" autocomplete="off">
    @csrf
    <input type="hidden" name="asset_id" value="{{ $asset_id }}" />

    <div class="row justify-content-center">
        <div class="col-xl-10 col-lg-11 col-md-12">

            @if (!isset($template->name))
                <div class="alert alert-danger text-center mt-4">
                    <h4 class="mb-0">No inspection template found for this asset!</h4>
                </div>
            @else
                {{-- Asset Info --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-body py-3">
                        <div class="row align-items-center">
                            <div class="col-md-5 mb-2 mb-md-0">
                                <h5 class="mb-1">
                                    <span class="fw-bold">Asset Name:</span>
                                    <u>{{ $asset->name() }}</u>
                                </h5>
                            </div>
                            <div class="col-md-3">
                                <h5 class="mb-1">
                                    <span class="fw-bold">SN#:</span>
                                    <u>{{ $asset->serial }} {{ $asset->rental_old }}</u>
                                </h5>
                            </div>
                            <div class="col-md-2 text-center text-bold">
                    <h5 class="mb-1">
                                    <span class="fw-bold">Inspection Date:</span>
                                    08/01/2025
                                </h5>
                </div>
                            <div class="col-md-2 text-center text-bold">
                    <h5 class="mb-1">
                                    <span class="fw-bold">Inspection Due:</span>
                                    08/01/2026
                                </h5>
                </div>
                        </div>
                    </div>
                </div>

                {{-- Tasks Table --}}
                <div class="card shadow">
                    <div class="card-body" >
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle mb-0" id="tasks-table">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Task</th>
                                        <th>Value</th>
                                        <th>Comments</th>
                                        <th style="width:40px" class="hide">
                                            <button type="button" class="hide btn btn-success btn-xs btn-add-row">Add Task</button>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($template->tasks as $task)
                                        <tr>
                                            <td class="bg-light">
                                                <input type="text" name="tasks[]" class="form-control task-input" required placeholder="Enter inspection task details" value="{{ $task->task }}">
                                            </td>
                                            <td class="text-center">
                                                <button type="button"  class="btn btn-success"><i class="fa fa-check"></i> Pass</button>
                                                
                                                <button type="button"  class="btn btn-danger"><i class="fa fa-close"></i> Fail</button>
                                            </td>
                                            <td>
                                                <input type="text" name="tasks_comment[]" class="form-control" placeholder="Additional comments">
                                            </td>
                                            <td class="td-remove-row hide">
                                                <button type="button" class="btn btn-danger btn-xs btn-remove-row hide" disabled>
                                                    <i class="fa fa-close"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            @endif

        </div>
    </div>
</form>


@push('footer-script')
<script src="{{ asset('assets/node_modules/select2/dist/js/select2.full.min.js') }}" ></script>
<script>
    $(document).ready(function () {
    $('.tasks_options').select2({
    tags: true
    });
    $('#acceptForm').submit(function (e) {
    e.preventDefault();
    const form = $(this);
    $.easyAjax({
    url: form.attr('action'),
            type: 'POST',
            container: '#acceptForm',
            data: $('#acceptForm').serialize(),
            file: true,
            success: function (response) {
            if (response.status == 'success') {
            $('.modal').modal('hide');
            $("body").removeClass("control-sidebar-slide-open");
            if (typeof table !== 'undefined') {
            table.draw();
            }
            }
            }
    });
    });
    })
</script>

@endpush
@endsection