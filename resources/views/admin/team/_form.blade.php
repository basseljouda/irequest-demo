@push('head-script')
<link rel="stylesheet" href="{{ asset('assets/node_modules/dropify/dist/css/dropify.min.css') }}">
@endpush
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">{{__($title)}}</h4>

                <form id="users-form" method="{{isset($team) ? 'PUT' : 'POST'}}" role="form" action="{{isset($team) ? route('admin.team.update', $team->id) : route('admin.team.store')}}">
                    @csrf
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label for="name">@lang('app.name')*</label>
                                <input required="" type="text" class="form-control" id="name" name="name" value="{{ $team->name ?? '' }}">
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label for="email">@lang('app.email')*</label>
                                <input required="" type="email" class="form-control" id="email" name="email" value="{{ $team->email ?? '' }}">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label>@lang('app.mobile')</label>
                                <div class="row">
                                    <div class="col-md-11">
                                        <div class="form-row">
                                            <div class="col-md-4 mb-2">
                                                <select name="calling_code" id="calling_code" class="form-control selectpicker" data-live-search="true" data-width="100%">
                                                    @foreach ($calling_codes as $code => $value)
                                                    <option value="{{ $value['dial_code'] }}"
                                                            @isset($team)
                                                            @if ($team->calling_code)
                                                        {{ $team->calling_code == $value['dial_code'] ? 'selected' : '' }}
                                                        @endif
                                                        @endisset>
                                                        {{ $value['dial_code'] . ' - ' . $value['name'] }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="text" class="form-control" name="mobile" value="{{ $team->mobile ?? '' }}">
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                   
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label for="role_id">@lang('modules.permission.roleName')</label>
                                <select class="form-control select2" name="role_id" id="role_id">
                                    <option value="0">No role</option>
                                    @foreach($roles as $role)
                                    <option
                                        @isset($team)
                                        @if($team->role && $role->id == $team->role->role_id) selected 
                                        @endif
                                        @endisset
                                        value="{{ $role->id }}">{{ $role->display_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                          <div class="col">
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="text" class="form-control" id="password" name="password" value="{{isset($team) ? '' : str_random(8)}}">
                                @isset($team)
                                <span class="help-block"> (Leave blank to keep current password)</span>
                                @endisset
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <h5>@lang('Roles Scope')</h5>
                            <div class="form-group">
                                <select class="select2 m-b-10 select2-multiple" style="width: 100% !important; " multiple="multiple"
                                        data-placeholder="Choose IDN" name="idns[]">
                                    @foreach ($companies as $item)
                                    <option value="{{ $item->id }}"
                                            {{ (isset($team) && in_array($item->id, $team->idns??[])) ? 'selected' : '' }}>
                                        {{ ucwords($item->company_name) }}
                                    </option>   
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <select class="select2 m-b-10 select2-multiple" style="width: 100% !important;" multiple="multiple"
                                        data-placeholder="Choose Regions" name="regions[]" autocomplete="off">
                                    @foreach($regions as $region)
                                    <option value="{{ $region->city }}"
                                            {{ (isset($team) && in_array($region->city, $team->regions??[])) ? 'selected' : '' }}
                                            >{{ ucwords($region->city) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <select class="select2 m-b-10 select2-multiple" style="width: 100% !important; " multiple="multiple"
                                        data-placeholder="Choose Sites" name="sites[]">
                                    @foreach($sites as $site)
                                    <option value="{{ $site->id }}"
                                            {{ (isset($team) && in_array($site->id, $team->sites??[])) ? 'selected' : '' }}
                                        {{ (isset($team) && $userSite==$site->id ??[]) ? 'selected' : '' }}
                                            >{{ ucwords($site->name) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
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
        @push('footer-script')
        <script src="{{ asset('assets/node_modules/select2/dist/js/select2.full.min.js') }}" type="text/javascript"></script>
        <script src="{{ asset('assets/node_modules/dropify/dist/js/dropify.min.js') }}" type="text/javascript"></script>
        <script>
        $('.dropify').dropify({
            messages: {
                default: '@lang("app.dragDrop")',
                replace: '@lang("app.dragDropReplace")',
                remove: '@lang("app.remove")',
                error: '@lang('app.largeFile')'
            }
        });

        $('#save-form').click(function () {
            $.easyAjax({
                url: $('#users-form').attr('action'),
                container: '#users-form',
                type: $('#users-form').attr('method'),
                redirect: true,
                data: $('#users-form').serialize()
            })
        });
        </script>

        @endpush