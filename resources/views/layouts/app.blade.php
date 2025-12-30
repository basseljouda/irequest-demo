<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <!-- Favicon icon -->
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}"> 
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
        <link rel="manifest" href="{{ asset('site.webmanifest') }}">
        <link rel="mask-icon" href="{{ asset('safari-pinned-tab.svg') }}" color="#5bbad5">
        <meta name="msapplication-TileColor" content="#da532c">
        <meta name="theme-color" content="#ffffff">

        <title>{{ $pageTitle }}</title>
        <!-- Tell the browser to be responsive to screen width -->
        <meta name="viewport" content="width=device-width, user-scalable=no">
        
        {{-- Preconnect to CDN domains for faster resource loading --}}
        <link rel="preconnect" href="https://maxcdn.bootstrapcdn.com" crossorigin>
        <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
        <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
        {{-- DataTables CDN - DNS prefetch only (no preconnect due to CORS) --}}
        <link rel="dns-prefetch" href="//cdn.datatables.net">

        {{-- Core Framework CSS (Load First) --}}
        <link rel="stylesheet" href="{{ asset('assets/dist/css/adminlte.min.css') }}">

        {{-- Icon Fonts (CDN) --}}
        <!--link rel="stylesheet" 
              href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css" 
              crossorigin="anonymous"-->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" rel="stylesheet">

        <link rel="stylesheet" 
              href="https://cdnjs.cloudflare.com/ajax/libs/simple-line-icons/2.4.1/css/simple-line-icons.css" 
              crossorigin="anonymous">
        <link rel="stylesheet" 
              href="{{ asset('assets/icons/themify-icons/themify-icons.css') }}">

        {{-- Plugin CSS (Load Second) --}}
        <link href="{{ asset('assets/node_modules/select2/dist/css/select2.min.css') }}" rel="stylesheet" type="text/css"/>
        <link rel="stylesheet" href="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.css') }}">
        {{-- DataTables CDN doesn't support CORS, so no crossorigin attribute --}}
        <link rel="stylesheet" href="//cdn.datatables.net/fixedheader/3.1.5/css/fixedHeader.bootstrap.min.css">
        <link rel="stylesheet" href="//cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">
        <link rel="stylesheet" href="//cdn.datatables.net/buttons/1.6.5/css/buttons.dataTables.min.css">
        <link rel="stylesheet" 
              href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.0/dist/css/bootstrap-select.min.css" 
              crossorigin="anonymous">
        <link rel="stylesheet" 
              href="//cdnjs.cloudflare.com/ajax/libs/flag-icon-css/0.8.2/css/flag-icon.min.css" 
              crossorigin="anonymous">

        {{-- Application Helper CSS (Load Third) --}}
        <link href="{{ asset('ajax-helper/helper.css') }}" rel="stylesheet">
        <link href="{{ asset('assets/node_modules/toast-master/css/jquery.toast.css') }}" rel="stylesheet">
        <link href="{{ asset('assets/node_modules/sweetalert/sweetalert.css') }}" rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('assets/node_modules/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/plugins/colorpicker/bootstrap-colorpicker.min.css') }}">
        {{-- Page-Specific CSS (via @stack) --}}
        @stack('head-script')

        {{-- Custom Application CSS (Load Last - Highest Priority) --}}
        <link href="{{ asset('css/custom.css?v='.time ()) }}" rel="stylesheet">
        <link href="{{ asset('css/app-search.css') }}" rel="stylesheet">

        <style>
            :root {
                --main-color: {{ $theme->primary_color }};
                @foreach (config('constant.orders') as $key => $value)
                --color-{{$key}}: {{ config('constant.orders_color.'.$key) }}!important;
                @endforeach

            }
            @foreach (config('constant.orders') as $key => $value)
            .bg-{{$key}} { background:  {{ config('constant.orders_color.'.$key) }}!important;}
            @endforeach

            @foreach (config('constant.ticket') as $key => $value)
            .bg-{{$key}} { background:  {{ config('constant.orders_color.'.$key) }}!important;}
            @endforeach

            {!! $theme->admin_custom_css !!}
        </style>


    </head>
    <body class="hold-transition sidebar-mini @yield('body-class')">
        <!-- Site wrapper -->
        <div class="wrapper">
            <!-- Navbar -->
            <nav class="main-header navbar navbar-expand navbar-light border-bottom fixed-top">
                <!-- Left navbar links -->
                <ul class="navbar-nav">
                    <li class="nav-item" style="margin-left:-20px" >
                        <a class="nav-link" data-widget="pushmenu" href="#"><i style="display:initial" class="fa fa-bars"></i></a>
                    </li>
                    <li class="nav-item" style="margin-top:5px;margin-left:0">
                        <img class="brand-image img-fluid img-logo" src="{{ asset('imedical_2024.png') }}" alt="logo"/>
                    </li>
                    <li class="nav-item hide">
                        <a class="nav-link" title="Click to Call!" href="tel:{{$global->company_phone}}">
                         @trans(To speak with an iMedical Rental Associate, please call) {{$global->company_phone}}</a>
                    </li>

                </ul>
                <!-- Global Search Bar -->
                <div class="navbar-search-wrapper">
                    <div class="navbar-search">
                        <i class="fa fa-search navbar-search-icon"></i>
                        <input type="text" id="global-search" class="navbar-search-input" placeholder="Search orders..." autocomplete="off">
                        <button type="button" class="navbar-search-clear" id="global-search-clear" style="display: none;">
                            <i class="fa fa-times"></i>
                        </button>
                        <div id="global-search-dropdown" class="navbar-search-dropdown" style="display: none;">
                            <div class="search-dropdown-loading" style="display: none;">
                                <i class="fa fa-spinner fa-spin"></i> Searching...
                            </div>
                            <div class="search-dropdown-results"></div>
                            <div class="search-dropdown-footer" style="display: none;">
                                <a href="#" id="view-all-results" class="view-all-link">
                                    <i class="fa fa-list"></i> View all results
                                </a>
                            </div>
                            <div class="search-dropdown-empty" style="display: none;">
                                <div class="text-muted text-center p-3">
                                    <i class="fa fa-search"></i><br>
                                    No orders found
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Right navbar links -->
                <ul class="navbar-nav ml-auto">
                    @role('superadmin')
                    <li class="nav-item mobile-hide" style="border-right:1px #ddd solid">
                        <a class="image-container nav-link waves-effect waves-light"
                           href="{{ route('admin.settings.index') }}">
                            <span><u>@lang('menu.companySettings')</u></span>
                        </a>
                    </li>
                    @endrole
                    <li class="nav-item nowrap">
                        <a class="image-container nav-link waves-effect waves-light"
                           href="{{ route('admin.profile.index') }}">
                            <span title="Profile details" class="text-muted"><u>{{ ucwords($user->name) }}</u></span>
                        </a>
                    </li>

                    <li class="nav-item dropdown hide" id="top-notification-dropdown">
                        <a class="nav-link" data-toggle="dropdown" href="#">
                            <i class="fa fa-bell" style="display:initial"></i>
                            @if(count($user->unreadNotifications) > 0)
                            <span class="badge badge-danger navbar-badge ">{{ count($user->unreadNotifications) <=20 ? count($user->unreadNotifications) : '20+' }}</span>
                            @endif
                        </a>
                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                            <div class="scrollable">
                                @foreach ($user->unreadNotifications->take(20) as $notification)
                                @include('notifications.'.snake_case(class_basename($notification->type)))
                                @endforeach
                            </div>
                            @if(count($user->unreadNotifications) > 0)
                            <a id="mark-notification-read" href="javascript:void(0);" class="dropdown-item dropdown-footer">@lang('app.markNotificationRead') <i class="fa fa-check"></i></a>
                            @else
                            <a  href="javascript:void(0);" class="dropdown-item dropdown-footer">@lang('messages.notificationNotFound') </a>
                            @endif
                        </div>
                    </li>
                    <li class="nav-item dropdown hide">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            {{ strtoupper(App::getLocale()) }}
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                            @foreach ($languages as $lang)
                            @if ($lang->language_code != App::getLocale())
                            <a class="language-switcher dropdown-item text-sm" id="{{$lang->language_code}}" href="#"> {{$lang->language_name .'('.$lang->language_code.')'}}</a>
                            @endif
                            @endforeach
                        </div>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link waves-effect waves-light" href="{{ route('logout') }}" title="Logout" onclick="event.preventDefault();
                            document.getElementById('logout-form').submit();"><small>(@lang('menu.logout'))</small>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                {{ csrf_field() }}
                            </form>
                        </a>

                    </li>
                </ul>
            </nav>
            <!-- /.navbar -->

            @include('sections.left-sidebar')

            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">

                @include('sections.breadcrumb')

                <!-- Main content -->
                <section class="content">

                    @yield('content')

                </section>
                <!-- /.content -->
            </div>


            {{--Ajax Modals--}}
            <div class="modal fade bs-modal-lg in" id="application-lg-modal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-scrollable" id="modal-data-application">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                            <span class="text-center caption-subject font-red-sunglo bold uppercase" id="modelHeading">Loading . . .</span>
                        </div>
                        <div class="modal-body">
                            <img src="{{ asset('assets/images/loading.gif') }}" width="100%"/>
                        </div>

                    </div>
                </div>
            </div>
            <div class="modal fade bs-modal-md in" id="application-md-modal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-md" id="modal-data-application">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">
                                Loading . . .
                            </h4>
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                            <span class="caption-subject font-red-sunglo bold uppercase" id="modelHeading"></span>
                        </div>
                        <div class="modal-body">
                            <img src="{{ asset('assets/images/loading.gif') }}" width="100%"/>
                        </div>
                        <div class="modal-footer">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade bs-modal-md in" id="staff-modal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-md" id="modal-data-application">
                    <div class="modal-content">
                        <div class="modal-header">
                            Loading . . .
                        </div>
                        <div class="modal-body">
                            <img src="{{ asset('assets/images/loading.gif') }}" width="100%"/>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Resolve Pickup Issue Modal -->
        <div class="modal fade" id="pickup-issue-resolve-modal" tabindex="-1" role="dialog" aria-labelledby="pickupIssueResolveLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="pickupIssueResolveLabel">Resolve Pickup Issue</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form id="pickupIssueResolveForm">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="resolve_notes">Resolution Notes</label>
                                <textarea class="form-control" name="notes" id="resolve_notes" rows="4" placeholder="Add notes about the resolution..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-success">Mark as Resolved</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
            {{--Ajax Modals Ends--}}


            <footer class="main-footer">
                &copy; {{ \Carbon\Carbon::today()->year }} Powered @lang('app.by') {{$global->company_name}}.<small>v.{{$build_version}}</small>
            </footer>

            @include('sections.right-sidebar')
        </div>
        <!-- ./wrapper -->

        {{-- Preconnect to CDN domains for JavaScript --}}
        <link rel="preconnect" href="https://js.pusher.com" crossorigin>

        {{-- Core Framework JavaScript (Load First) --}}
        <script src="{{ asset('assets/plugins/jquery/jquery.min.js') }}"></script>
        <script src="{{ asset('assets/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ asset('assets/dist/js/adminlte.min.js') }}"></script>

        {{-- Plugin JavaScript (Load Second) --}}
        <script src="{{ asset('assets/plugins/datatables/jquery.dataTables.js') }}"></script>
        <script src="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.js') }}"></script>
        <script src="{{ asset('assets/plugins/slimScroll/jquery.slimscroll.min.js') }}"></script>
        <script src="{{ asset('assets/plugins/fastclick/fastclick.js') }}"></script>
        <script src="{{ asset('assets/node_modules/select2/dist/js/select2.full.min.js') }}"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.0/dist/js/bootstrap-select.min.js" 
                crossorigin="anonymous"></script>
        <script src="{{ asset('assets/node_modules/sweetalert/sweetalert.min.js') }}"></script>
        <script src="{{ asset('assets/node_modules/toast-master/js/jquery.toast.js') }}"></script>
        <script src="{{ asset('assets/node_modules/Magnific-Popup-master/dist/jquery.magnific-popup.min.js') }}"></script>
        <script src="{{ asset('assets/node_modules/moment/moment.js') }}" type="text/javascript"></script>
        <script src="https://js.pusher.com/7.0/pusher.min.js" crossorigin="anonymous"></script>

        {{-- Application Helper JavaScript (Load Third) --}}
        <script src="{{ asset('ajax-helper/helper.js?v='.$build_version) }}"></script>
        <script src="{{ asset('js/cbpFWTabs.js') }}"></script>
        
        {{-- Pass Blade data to JavaScript --}}
        <script>
            // CSRF Token
            window.csrfToken = "{{ csrf_token() }}";
            
            // Routes
            window.showOrderUrl = "{{ route('orders.showdetails', ':id') }}";
            window.showOrderRequestUrl = "{{ route('part_request.showdetails', ':id') }}";
            window.showOrderRMAUrl = "{{ route('part_request.showRMAdetails', ':id') }}";
            window.showTicketUrl = "{{ route('ticket.showdetails', ':id') }}";
            window.languageChangeUrl = "{{ route('admin.language-settings.change-language') }}";
            window.markNotificationReadUrl = "{{ route('mark-notification-read') }}";
            window.cancelPickupUrl = "{{ route('orders.cancel_pickup', ':id') }}";
            
            // Sticky Notes Routes
            window.stickyNoteStoreUrl = "{{ route('admin.sticky-note.store') }}";
            window.stickyNoteUpdateUrl = "{{ route('admin.sticky-note.update', ':id') }}";
            window.stickyNoteCreateUrl = "{{ route('admin.sticky-note.create') }}";
            window.stickyNoteEditUrl = "{{ route('admin.sticky-note.edit', ':id') }}";
            window.stickyNoteDestroyUrl = "{{ route('admin.sticky-note.destroy', ':id') }}";
            window.stickyNoteIndexUrl = "{{ route('admin.sticky-note.index') }}";
            
            // DataTable Language Options
            window.dtProcessing = "@lang('modules.datatables.processing')";
            window.dtSearch = "@lang('modules.datatables.search')";
            window.dtLengthMenu = "@lang('modules.datatables.lengthMenu')";
            window.dtInfo = "@lang('modules.datatables.info')";
            window.dtInfoEmpty = "@lang('modules.datatables.infoEmpty')";
            window.dtInfoFiltered = "@lang('modules.datatables.infoFiltered')";
            window.dtInfoPostFix = "@lang('modules.datatables.infoPostFix')";
            window.dtLoadingRecords = "@lang('modules.datatables.loadingRecords')";
            window.dtZeroRecords = "@lang('modules.datatables.zeroRecords')";
            window.dtEmptyTable = "@lang('modules.datatables.emptyTable')";
            window.dtPaginateFirst = "@lang('modules.datatables.paginate.first')";
            window.dtPaginatePrevious = "@lang('modules.datatables.paginate.previous')";
            window.dtPaginateNext = "@lang('modules.datatables.paginate.next')";
            window.dtPaginateLast = "@lang('modules.datatables.paginate.last')";
            window.dtAriaSortAscending = "@lang('modules.datatables.aria.sortAscending')";
            window.dtAriaSortDescending = "@lang('modules.datatables.aria.sortDescending')";
            
            // Translations
            window.transSites = "@trans(Sites)";
            window.transAll = "@trans(All)";
            window.transEquipment = "@trans(Equipment)";
            window.transSelectModel = "@trans(Select Model)";
            
            // Session Flash Messages
            @if (session('error'))
                $.showToastr("{{ session('error') }}", "error", {});
            @endif
            @if (session('success'))
                $.showToastr("{{ session('success') }}", "success", {});
            @endif
        </script>

        {{-- Load extracted JavaScript files --}}
        <script src="{{ asset('js/app-global.js') }}"></script>
        <script src="{{ asset('js/app-sidebar.js') }}"></script>
        <script src="{{ asset('js/app-sticky-notes.js') }}"></script>
        <script src="{{ asset('js/app-initialization.js') }}"></script>
        <script src="{{ asset('js/app-orders.js?v='.$build_version) }}"></script>

        {{-- Pusher Configuration (commented out) --}}
        <script>
            // Enable pusher logging - don't include this in production
            /*Pusher.logToConsole = true;
             
             var pusher = new Pusher('5628198322007176de2f', {
             cluster: 'eu'
             });
             
             var channel = pusher.subscribe('my-channel');
             channel.bind('Illuminate\\Notifications\\Events\\BroadcastNotificationCreated', function(data) {
             alert(JSON.stringify(data));
             });*/
        </script>

        {{-- Site Staff Handler - Keep inline due to Blade @isset/@else directives --}}
        @if(isset($asset))
        <script>
            window.assetModelName = "{{ $asset->model_name }}";
        </script>
        @endif
        <style>
            html.drawer-locked {
                overflow-y: hidden;
            }
            html.drawer-locked .control-sidebar::before {
                display: none !important;
            }
        </style>

        @stack('footer-script')
        
        <!-- Global Search -->
        <script src="{{ asset('js/app-search.js?v='.$build_version) }}"></script>
        
        <script>
            (function() {
                var html = document.documentElement;
                var body = document.body;

                function syncDrawerState() {
                    if (body.classList.contains('control-sidebar-slide-open')) {
                        html.classList.add('drawer-locked');
                    } else {
                        html.classList.remove('drawer-locked');
                    }
                }

                var observer = new MutationObserver(function(mutations) {
                    mutations.forEach(function(mutation) {
                        if (mutation.attributeName === 'class') {
                            syncDrawerState();
                        }
                    });
                });

                observer.observe(body, { attributes: true });
                syncDrawerState();
            })();
        </script>

    </body>
</html>
