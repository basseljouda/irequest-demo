@extends('layouts.app')

@push('head-script')
    <link rel="stylesheet" href="{{ asset('assets/node_modules/dropify/dist/css/dropify.min.css') }}">
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form id="editSettings" class="ajax-form">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="company_name">@lang('modules.accountSettings.companyName')</label>
                            <input type="text" class="form-control" id="company_name" name="company_name"
                                   value="{{ $global->company_name }}">
                        </div>
                        <div class="form-group">
                            <label for="company_email">@lang('modules.accountSettings.companyEmail')</label>
                            <input type="email" class="form-control" id="company_email" name="company_email"
                                   value="{{ $global->company_email }}">
                        </div>
                        <div class="form-group">
                            <label for="company_phone">@lang('modules.accountSettings.companyPhone')</label>
                            <input type="tel" class="form-control" id="company_phone" name="company_phone"
                                   value="{{ $global->company_phone }}">
                        </div>
                        <div class="form-group">
                            <label for="exampleInputPassword1">@lang('modules.accountSettings.companyWebsite')</label>
                            <input type="text" class="form-control" id="website" name="website"
                                   value="{{ $global->website }}">
                        </div>
                        <div class="form-group">
                            <label for="exampleInputPassword1">@lang('modules.accountSettings.companyLogo')</label>
                             <div class="card">
                                    <div class="card-body">
                                        <input type="file" id="input-file-now" name="logo" class="dropify"
                                               @if(is_null($global->logo))
                                                   data-default-file="{{ asset('app-logo.png') }}"
                                               @else
                                                   data-default-file="{{ asset('user-uploads/app-logo/'.$global->logo) }}"
                                               @endif
                                        />
                                    </div>
                                </div>
                        </div>


                        <div class="form-group">
                            <label for="address">@lang('modules.accountSettings.companyAddress')</label>
                            <textarea class="form-control" id="address" rows="5"
                                      name="address">{{ $global->address }}</textarea>
                        </div>

                        <div class="form-group">
                            <label for="address">@lang('modules.accountSettings.defaultTimezone')</label>
                            <select name="timezone" id="timezone" class="form-control select2 custom-select">
                                @foreach($timezones as $tz)
                                    <option @if($global->timezone == $tz) selected @endif>{{ $tz }}</option>
                                @endforeach
                            </select>
                        </div>

                        
                        </div>


                        <div class="form-group hide">
                            {{--<a href="javascript:;" id="getLoaction" class="btn btn-warning m-b-10"><i class="ti-location-pin"></i> @lang('modules.accountSettings.getLocation')</a>--}}
                            <label for="address">@lang('modules.accountSettings.getLocation')</label>

                            <input type="text" class="form-control" id="gmap_geocoding_address">
                            <input type="hidden" id="latitude" name="latitude"
                                   value="{{ $global->latitude }}">
                            <input type="hidden" id="longitude" name="longitude"
                                   value="{{ $global->longitude }}">

                            <div id="gmap_geocoding" class="gmaps"></div>
                        </div>


                        <button type="button" id="save-form"
                                class="btn btn-success waves-effect waves-light m-r-10">
                            @lang('app.save')
                        </button>
                        <button type="reset"
                                class="btn btn-inverse waves-effect waves-light">@lang('app.reset')</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('footer-script')
    <script src="{{ asset('assets/node_modules/select2/dist/js/select2.full.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/node_modules/bootstrap-select/bootstrap-select.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/node_modules/dropify/dist/js/dropify.min.js') }}" type="text/javascript"></script>
    
    
    <script>
        $('[data-toggle="tooltip"]').tooltip()
        // For select 2
        $(".select2").select2();

        $('.dropify').dropify({
            messages: {
                default: '@lang("app.dragDrop")',
                replace: '@lang("app.dragDropReplace")',
                remove: '@lang("app.remove")',
                error: '@lang('app.largeFile')'
            }
        });

        $(document).ready(function () {
            $("#getLoaction").click(function () {
                $('body').block({
                    message: '<p style="margin:0;padding:8px;font-size:24px;">Just a moment...</p>'
                    , css: {
                        color: '#fff'
                        , border: '1px solid #fb9678'
                        , backgroundColor: '#fb9678'
                    }
                });

                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(showPosition);
                } else {
                    alert("Geolocation is not supported by this browser.");
                    $("#locationMsg").html('');
                }
            });
        });


        function showPosition(position) {
            $('#latitude').val(position.coords.latitude);
            $('#longitude').val(position.coords.longitude);
            initialize();
            $('body').unblock();
        }

        $('#save-form').click(function () {
            $.easyAjax({
                url: "{{route('admin.settings.update', ['1'])}}",
                container: '#editSettings',
                type: "POST",
                redirect: true,
                file: true
            })
        });
    </script>

    
@endpush