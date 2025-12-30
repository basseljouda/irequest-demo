@extends('layouts.app')
@section('content')
@section('header-title')
@trans(Order) @trans(Items)
@endsection
@section('create-button')
<a href="#" id="submit-form" class="btn btn-info btn-sm m-l-15"><i class="ti-save"></i>
    Submit Order
</a>
@endsection
<div class="row">
    <div class="col-md-12 card" style="padding: 15px">
        <div class="wrapper wrapper-content animated fadeInRight">   
            <div class="row">
                <div class="col-10 offset-1">
                    <form class="ajax-form" id="CatalogForm" method="POST" role="form" action="{{ route('ecatalog.store') }}">
                        @csrf
                        <input type="hidden" name="_method" value="POST"/>
                        <div class="row">
                            <div class="col">
                                <div class="panel panel-default">
                                    <div class="control-label">
                                        @trans(Site)
                                    </div>
                                    <div class="panel-body form-group text-primary">
                                        @if (isset(Auth::user()->is_staff->hospital->name))
                                        {{Auth::user()->is_staff->hospital->name}}
                                        @else
                                        <select class="select2 m-b-10 form-control" 
                                                data-placeholder="@trans(Select Site)" name="hospital" id="hospital" required="">
                                            <option value=""></option>
                                            @foreach ($hospitals as $item)
                                            <option  value="{{ $item->name }}">{{ ucwords($item->name) }}</option>   
                                            @endforeach
                                        </select>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="panel panel-default">
                                    <div class="control-label required">
                                        @trans(Requester)
                                    </div>
                                    <div class="panel-body form-group">
                                        <input required="" class="form-control" name="requester" value='{{Auth::user()->name}}' placeholder="@trans(Enter requester name)">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="panel panel-default">
                                    <div class="control-label">
                                        @trans(Notes)
                                    </div>
                                    <div class="panel-body form-group">
                                        <textarea name="notes" rows="4" cols="50" class="form-control" maxlength="2500">{{$order->notes ?? ''}}</textarea>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <hr/>
                        <h2 class="bg-whitesmoke" style="padding: 10px;color: #002a80 !important">@trans(Ordered Items)</h2>
                        <div class="card card-body">
                            <table class="table-no-bordered table table-nohover">
                                <tr>
                                    <th></th>
                                    <th style="font-weight: normal;color: darkred">Please select required quantity and click <span style="color: darkblue">[Submit Order]</span> button</th>
                                    <th>Quantity</th>
                                </tr>
                                @foreach (json_decode(request()->values) as $value)
                                <tr>
                                    <td>
                                      <input type="hidden" name="order[]" value="{{$loop->index}}"/>
                                    </td>
                                    <td class="text-bold">
                                        <input type="hidden" name="description[]" value="{{$value}}"/>
                                        - {{$value}}
                                    </td>
                                    <td style="width: 100px">
                                        <input type="number" min="1" value="1" required="" name="qty[]" class="form-control"/>
                                    </td>
                                </tr>
                                @endforeach
                            </table>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('footer-script')
<script src="{{ asset('assets/node_modules/select2/dist/js/select2.full.min.js') }}"></script>
<script>
$('#submit-form').click(function () {
    swal({
        title: $(this).text(),
        text: "@trans(Are you sure)?",
        type: "info",
        showCancelButton: true,
        confirmButtonText: "@lang('app.yes')",
        cancelButtonText: "@lang('app.no')",
        closeOnConfirm: true,
        closeOnCancel: true,
    }, function (isConfirm) {
        if (isConfirm) {

            $.easyAjax({
                url: $('#CatalogForm').attr('action'),
                container: '#CatalogForm',
                type: $('#CatalogForm').attr('method'),
                redirect: true,
                data: $('#CatalogForm').serialize()
            }
            );
        }
    }
    )
});
</script>
@endpush


