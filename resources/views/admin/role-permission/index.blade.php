@extends('layouts.app')

@push('head-script')
    <link rel="stylesheet" href="{{ asset('assets/node_modules/switchery/dist/switchery.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/node_modules/icheck/skins/all.css') }}">
    <link href="{{ asset('assets/node_modules/select2/dist/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/node_modules/bootstrap-select/bootstrap-select.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/node_modules/multiselect/css/multi-select.css') }}">
    <link rel="stylesheet" href="{{ asset('css/role-permission.css') }}">
    
@endpush
@section('create-button')
    <a href="{{ route('admin.role-permission.create')}}" target-modal="#application-lg-modal" id="addRole" class="modal-link btn btn-info btn-sm btn-outline  waves-effect waves-light "><i class="fa fa-key"></i> Manage Roles</a>
@endsection
@section('content')
    @php
        $initialRole = $roles->first();
    @endphp
    @if($roles->isEmpty())
        <div class="alert alert-info">No roles have been created yet.</div>
    @else
    <div class="roles-permission-wrapper">
        <div class="role-selection-bar">
            <div class="role-field">
                <label for="role-selector">Role</label>
                <select id="role-selector" class="form-control select2">
                    @foreach($roles as $role)
                        @php
                            $assignedPermissionIds = $role->permissions->pluck('permission_id')->toArray();
                            $moduleTotal = $modules->count();
                            $moduleEnabled = 0;
                            foreach($modules as $moduleItem) {
                                if ($moduleItem->permissions->whereIn('id', $assignedPermissionIds)->count() > 0) {
                                    $moduleEnabled++;
                                }
                            }
                        @endphp
                        <option value="{{ $role->id }}"
                                data-display="{{ ucwords($role->display_name) }}"
                                data-members="{{ count($role->roleuser) }}"
                                data-modules-enabled="{{ $moduleEnabled }}"
                                data-modules-total="{{ $moduleTotal }}"
                                data-updated="{{ optional($role->updated_at)->diffForHumans() ?: 'n/a' }}">
                            {{ ucwords($role->display_name) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="role-summary">
                <span class="summary-pill" id="summary-modules"></span>
                <span class="summary-pill" id="summary-members"></span>
                <span class="summary-pill" id="summary-updated"></span>
            </div>
            <div class="role-bulk-actions">
                <button class="btn btn-outline btn-bulk" data-status="true" data-role-id="{{ optional($initialRole)->id }}">Enable all</button>
                <button class="btn btn-danger btn-bulk" data-status="false" data-role-id="{{ optional($initialRole)->id }}">Disable all</button>
                <button class="btn btn-outline modal-link show-members"
                        data-role-id="{{ optional($initialRole)->id }}"
                        target-modal="#application-lg-modal"
                        href="{{ $initialRole ? route('admin.role-permission.showMembers', $initialRole->id) : '#' }}">
                    View members
                </button>
            </div>
        </div>

        @foreach($roles as $role)
            @php
                $assignedPermissionIds = $role->permissions->pluck('permission_id')->toArray();
            @endphp
            <div class="role-permission-card {{ $loop->first ? 'is-active' : '' }}" id="role-permission-{{ $role->id }}">
                <div class="role-header">
                    <div>
                        <div class="role-title">{{ ucwords($role->display_name) }}</div>
                    </div>
                    <div class="role-members-badge">
                        <i class="fa fa-users text-info"></i> {{ count($role->roleuser) }} member(s)
                    </div>
                </div>
                <div class="role-body">
                    @foreach($modules as $module)
                        @php
                            $assignedCount = $module->permissions->whereIn('id', $assignedPermissionIds)->count();
                            $permissionCount = $module->permissions->count();
                            $statusClass = 'module-status--none';
                            $statusText = 'No access';
                            if ($permissionCount === 0) {
                                $statusClass = 'module-status--none';
                                $statusText = 'No permissions';
                            } elseif ($assignedCount === $permissionCount) {
                                $statusClass = 'module-status--all';
                                $statusText = 'All enabled';
                            } elseif ($assignedCount > 0) {
                                $statusClass = 'module-status--partial';
                                $statusText = 'Partially enabled';
                            }
                        @endphp
                        <div class="module-card">
                            <div class="module-header">
                                <div>
                                    <div class="module-title">{{ ucwords($module->module_name) }}</div>
                                    @if($module->description != '')
                                        <div class="module-desc">{{ $module->description }}</div>
                                    @endif
                                </div>
                                <span class="module-status {{ $statusClass }}">{{ $statusText }}</span>
                            </div>
                            <div class="permissions-grid">
                                @foreach($module->permissions as $permission)
                                    <div class="permission-item">
                                        <input type="checkbox"
                                               id="permission-{{ $role->id }}-{{ $permission->id }}"
                                               @if (in_array($permission->id, $assignedPermissionIds)) checked @endif
                                               class="js-switch assign-role-permission permission_{{ $role->id }}"
                                               data-size="small"
                                               data-color="#00c292"
                                               data-permission-id="{{ $permission->id }}"
                                               data-role-id="{{ $role->id }}" />
                                        <label class="permission-label" for="permission-{{ $role->id }}-{{ $permission->id }}">
                                            <strong>{{ ucwords(str_replace('_', ' ', $permission->display_name != '' ? $permission->display_name : $permission->name)) }}</strong>
                                            @if($permission->description)
                                                <span>{{ $permission->description }}</span>
                                            @endif
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
    @endif
@endsection

@push('footer-script')
    <script src="{{ asset('assets/node_modules/switchery/dist/switchery.min.js') }}"></script>
    <script>
        (function ($) {
            var switchInstances = [];

            function initSwitches($context) {
                $context.find('.js-switch').each(function () {
                    if (!this.switchery) {
                        var switchery = new Switchery(this, $(this).data());
                        switchInstances.push(switchery);
                    }
                });
            }

            function updateSummary($option) {
                var modulesEnabled = $option.data('modules-enabled');
                var modulesTotal = $option.data('modules-total');
                var members = $option.data('members');
                var updated = $option.data('updated');

                $('#summary-modules').text(modulesEnabled + ' of ' + modulesTotal + ' modules enabled');
                $('#summary-members').text(members + ' member(s) assigned');
                $('#summary-updated').text('Last updated ' + updated);
            }

            function setSwitchState(input, state) {
                if (input.checked !== state) {
                    input.checked = state;
                    if (input.switchery) {
                        input.switchery.setPosition(state);
                    }
                }
            }

            function assignRolePermission() {
                var $this = $(this);
                var roleId = $this.data('role-id');
                var permissionId = $this.data('permission-id');
                var assignPermission = $this.is(':checked') ? 'yes' : 'no';

                $.easyAjax({
                    url: "{{route('admin.role-permission.store')}}",
                    type: "POST",
                    data: {
                        'roleId': roleId,
                        'permissionId': permissionId,
                        'assignPermission': assignPermission,
                        '_token': "{{ csrf_token() }}"
                    }
                });
            }

            $(document).on('change', '.assign-role-permission', assignRolePermission);

            $('.btn-bulk').on('click', function () {
                var $button = $(this);
                var status = $button.data('status') === true;
                var roleId = $button.data('role-id');

                if (!roleId) {
                    return;
                }

                $.easyAjax({
                    url: "{{ route('admin.role-permission.assignAllPermission') }}",
                    type: "POST",
                    data: {
                        'roleId': roleId,
                        '_token': "{{ csrf_token() }}",
                        "status": status
                    },
                    success: function () {
                        var $panel = $('#role-permission-' + roleId);
                        $panel.find('.assign-role-permission').each(function () {
                            setSwitchState(this, status);
                        });
                    }
                });
            });

            var membersUrlTemplate = @json($initialRole ? route('admin.role-permission.showMembers', $initialRole->id) : route('admin.role-permission.showMembers', ['role' => 0]));
            var membersBaseUrl = membersUrlTemplate.replace(/\/\d+$/, '');

            $('#role-selector').on('change', function () {
                var $selected = $(this).find(':selected');
                var roleId = $selected.val();

                $('.role-permission-card').removeClass('is-active').hide();
                $('#role-permission-' + roleId).addClass('is-active').fadeIn(120);

                $('.btn-bulk').attr('data-role-id', roleId);
                $('.show-members').attr('data-role-id', roleId)
                    .attr('href', membersBaseUrl + '/' + roleId);

                updateSummary($selected);
                initSwitches($('#role-permission-' + roleId));
            });

            // Initialize page
            var $roleSelect = $('#role-selector');
            if ($roleSelect.length) {
                var $initialOption = $roleSelect.find(':selected');
                if ($initialOption.length) {
                    updateSummary($initialOption);
                    initSwitches($('.role-permission-card.is-active'));
                }
                document.body.classList.add('roles-permissions');
            }
        })(jQuery);
    </script>

@endpush