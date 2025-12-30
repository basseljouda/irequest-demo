@extends('layouts.app')

@push('head-script')
    <link rel="stylesheet" href="{{ asset('css/manager-orders.css') }}">
@endpush

@section('create-button')
    @permission('add_orders')
        <a href="{{ route('orders.create') }}" class="btn btn-info btn-sm">
            <i class="fa fa-plus-circle"></i> @lang('app.add') @lang('app.order')
        </a>
    @endpermission
@endsection

@section('content')
    <div class="manager-orders">
        <div class="header-row">
            <div>
                <h3 class="title">Manager Orders</h3>
                <p class="subtitle">A modern overview of all rental activity with manager-focused insights.</p>
            </div>
            <div class="status-chips">
                @foreach($statusCount as $status => $count)
                    <span class="status-chip status-{{ $status }}">
                        {{ ucwords(config('constant.orders.' . $status)) }}
                        <strong>{{ number_format($count) }}</strong>
                    </span>
                @endforeach
            </div>
        </div>

        <div class="cards-grid">
            <div class="card filters-card">
                <h4>Filters</h4>
                <form id="manager-orders-filters" class="filters-form">
                    @csrf
                    <div class="form-row">
                        <div class="form-group">
                            <label for="filter_status">@lang('app.status')</label>
                            <select id="filter_status" name="filter_status" class="form-control select2">
                                <option value="">All statuses</option>
                                @foreach(config('constant.orders') as $key => $label)
                                    @if(user()->can('view_' . $key))
                                        <option value="{{ $key }}">{{ ucwords($label) }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="filter_company">@lang('app.company')</label>
                            <select id="filter_company" name="filter_company" class="form-control select2">
                                <option value="">All IDNs</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id }}">{{ $company->company_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="filter_hospital">@lang('app.site')</label>
                            <select id="filter_hospital" name="filter_hospital" class="form-control select2">
                                <option value="">All sites</option>
                                @foreach($hospitals as $hospital)
                                    <option value="{{ $hospital->id }}">{{ $hospital->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="filter_stuff">@lang('app.staffMember')</label>
                            <select id="filter_stuff" name="filter_stuff" class="form-control select2">
                                <option value="">All staff</option>
                                @foreach($staff as $member)
                                    <option value="{{ $member->id }}">{{ $member->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="filter_patient_name">@lang('app.patient')</label>
                            <input type="text" id="filter_patient_name" name="filter_patient_name" class="form-control"
                                   placeholder="Patient name">
                        </div>
                        <div class="form-group">
                            <label for="filter_room_no">@lang('app.room')</label>
                            <input type="text" id="filter_room_no" name="filter_room_no" class="form-control"
                                   placeholder="Room number">
                        </div>
                        <div class="form-group">
                            <label for="filter_item">@lang('app.equipment')</label>
                            <select id="filter_item" name="filter_item" class="form-control select2">
                                <option value="">All equipment</option>
                                @foreach($equipments as $equipment)
                                    <option value="{{ $equipment->id }}">{{ $equipment->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="filter_costcenter">@lang('app.costcenter')</label>
                            <select id="filter_costcenter" name="filter_costcenter" class="form-control select2">
                                <option value="">All cost centers</option>
                                @foreach($cost_centers as $center)
                                    <option value="{{ $center->id }}">{{ $center->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>@lang('app.dateRange')</label>
                            <div class="date-row">
                                <input type="text" id="date-start" name="startDate" class="form-control datepik"
                                       placeholder="From">
                                <input type="text" id="date-end" name="endDate" class="form-control datepik"
                                       placeholder="To">
                                <select id="filter_date_type" name="filter_date_type" class="form-control">
                                    <option value="created_at">Created</option>
                                    <option value="submited_at">Routed</option>
                                    <option value="delivered_at">Delivered</option>
                                    <option value="accepted_at">Accepted</option>
                                    <option value="closed_at">Completed</option>
                                    <option value="picked_at">Picked Up</option>
                                    <option value="reassigned_at">Reassigned</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group button-group">
                            <button type="button" class="btn btn-primary" id="manager-orders-apply">
                                <i class="fa fa-filter"></i> Apply
                            </button>
                            <button type="button" class="btn btn-outline-secondary" id="manager-orders-reset">
                                <i class="fa fa-repeat"></i> Reset
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="card table-card">
                <div class="table-header">
                    <div>
                        <h4>Orders</h4>
                        <p class="table-subtitle">Live feed of all orders respecting your scope.</p>
                    </div>
                    <div class="table-actions">
                        <button class="btn btn-light" id="manager-orders-refresh">
                            <i class="fa fa-refresh"></i> Refresh
                        </button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="manager-orders-table" class="table table-hover">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>SO</th>
                            <th>@lang('app.site')</th>
                            <th>@lang('app.company')</th>
                            <th>@lang('app.patient')</th>
                            <th>@lang('app.status')</th>
                            <th>@lang('app.updatedOn')</th>
                            <th>Bill Start</th>
                            <th>Bill End</th>
                            <th>Cost Center</th>
                            <th class="text-right">Actions</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('footer-script')
    <script src="{{ asset('assets/node_modules/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('assets/node_modules/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js') }}"></script>
    @include('vendor.datatables.scripts')
    <script>
        window.managerOrdersRoutes = {
            data: "{{ route('manager.orders.data') }}"
        };
    </script>
    <script src="{{ asset('js/manager/orders-index.js') }}"></script>
@endpush

