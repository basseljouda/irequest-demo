@extends('layouts.app')
@permission('view_dashboard')
@push('head-script')
<link rel="stylesheet" href="{{ asset('css/dashboard-enhanced/user-dashboard.css') }}">
<link rel="stylesheet" href="{{ asset('css/dashboard-enhanced/customization.css') }}">
@endpush

@section('content')
<section class="content">
    <div class="container-fluid">
        <!-- Dashboard Header -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="mb-0">
                            <i class="fa fa-home"></i> My Dashboard
                        </h2>
                        <p class="text-muted mb-0">Your orders, notifications, and quick actions</p>
                    </div>
                    <div>
                        <button id="customize-dashboard-btn" class="btn btn-primary">
                            <i class="fa fa-cog"></i> Customize
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dashboard Widgets Container -->
        <div id="dashboard-widgets-container" class="dashboard-widgets-container" data-dashboard-type="user">
            <!-- Widgets will be loaded here dynamically -->
            <div class="widgets-loading text-center p-5">
                <i class="fa fa-spinner fa-spin fa-3x"></i>
                <p class="mt-3">Loading dashboard...</p>
            </div>
        </div>

        <!-- Customization Modal -->
        <div id="customization-modal" class="modal fade" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fa fa-cog"></i> Customize Dashboard
                        </h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div id="widget-customization-panel">
                            <!-- Widget list for customization will be loaded here -->
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-success" id="save-customization">
                            <i class="fa fa-save"></i> Save Changes
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('footer-script')
<script>
    window.dashboardEnhancedRoutes = {
        manager: "{{ route('admin.dashboard.enhanced.manager') }}",
        user: "{{ route('admin.dashboard.enhanced.user') }}",
        savePreferences: "{{ route('admin.dashboard.enhanced.preferences.save') }}",
        resetPreferences: "{{ route('admin.dashboard.enhanced.preferences.reset') }}",
        widget: "{{ url('admin/dashboard-enhanced/widget') }}/:widgetId"
    };
    window.dashboardWidgets = @json($widgets);
    window.dashboardType = '{{ $dashboardType }}';
    window.csrfToken = "{{ csrf_token() }}";
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
<script src="{{ asset('js/dashboard-enhanced/widget-system.js') }}"></script>
<script src="{{ asset('js/dashboard-enhanced/customization.js') }}"></script>
<script src="{{ asset('js/dashboard-enhanced/user-dashboard.js') }}"></script>
@endpush
@endpermission

