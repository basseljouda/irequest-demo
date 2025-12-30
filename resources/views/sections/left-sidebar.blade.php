<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary">
    <!-- Brand Logo -->
    
    <!-- Sidebar -->
    <div class="sidebar">

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" id="sidebarnav" role="menu" data-accordion="false">
                @permission('view_dashboard')
                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->is('admin/dashboard*') ? 'active' : '' }}">
                        <i class="fa fa-bar-chart-o"></i>
                        <p>
                            @lang('menu.dashboard')
                        </p>
                    </a>
                </li>
                @endpermission
                <!--li class="nav-item">
                    <a href="{{ route('chat') }}" class="nav-link {{ request()->is('admin/dashboard*') ? 'active' : '' }}">
                        <i class="fa fa-bar-chart-o"></i>
                        <p>
                            Chat
                        </p>
                    </a>
                </li-->
                
               
                <li class="nav-item has-treeview @if(\Request()->is('part_request*')) active menu-open @endif">
                    <a href="#" class="nav-link">
                        <i class="ti-angle-double-right"></i>
                        <p>
                            @lang('Parts Orders')
                            <i class="right fa fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        @permission('add_part_request')
                        <li class="nav-item">
                            <a href="{{ url('part_request/create') }}" class="nav-link {{ request()->is('part_request/create') ? 'active' : '' }}">
                                <i class="fa fa-file-text"></i>
                                <p>
                                    @lang('Part Request')
                                </p>
                            </a>
                        </li>
                        @endpermission
                        @permission('view_part_request')
                        <li class="nav-item">
                            <a href="{{ route('part_request.index') }}" class="nav-link {{ request()->is('part_request') ? 'active' : '' }}">
                                <i class="fa fa-file-text"></i>
                                <p>
                                    @lang('Manage Requests')
                                </p>
                            </a>
                        </li>
                        @endpermission
                        
                       
                         @permission('manage_rma_requests')
                        <li class="nav-item">
                            <a href="{{ route('part_request.index_reply') }}" class="nav-link {{ request()->is('part_request/index-reply') ? 'active' : '' }}">
                                <i class="fa fa-tasks"></i>
                                <p>
                                    @lang('RFQ Requests')
                                </p>
                            </a>
                        </li>
                        @endpermission
                        @permission('manage_order_request')
                        <li class="nav-item">
                            <a href="{{ route('part_request.index_order') }}" class="nav-link {{ request()->is('part_request/index-order') ? 'active' : '' }}">
                                <i class="fa fa-tasks"></i>
                                <p>
                                    @lang('Manage Orders')
                                </p>
                            </a>
                        </li>
                        @endpermission
                         @permission('manage_rma_orders')
                        <li class="nav-item">
                            <a href="{{ route('part_request.index_rma_orders') }}" class="nav-link {{ request()->is('part_request/index-rma-orders') ? 'active' : '' }}">
                                <i class="fa fa-tasks"></i>
                                <p>
                                    @lang('Manage RMA Orders')
                                </p>
                            </a>
                        </li>
                        @endpermission
                    </ul>
                </li>
                <li class="nav-item has-treeview @if(\Request()->is('orders*')) active menu-open @endif">
                    <a href="#" class="nav-link">
                        <i class="ti-archive"></i>
                        <p>
                            @lang('menu.rental')
                            <i class="right fa fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        @permission('add_orders')
                        <li class="nav-item">
                            <a href="{{ route('orders.create') }}" class="nav-link {{ request()->is('orders/create*') ? 'active' : '' }}">
                                <i class="fa fa-file-text"></i>
                                <p>
                                    @lang('menu.rentalNew')
                                </p>
                            </a>
                        </li>
                        @endpermission

                        @permission('view_orders')
                        <li class="nav-item">
                            <a href="{{ route('orders.cards') }}" class="nav-link {{ request()->is('orders/cards') ? 'active' : '' }}">
                                <i class="fa fa-tasks"></i>
                                <p>
                                    Orders Cards View
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('orders.index') }}" class="nav-link {{ request()->is('orders') ? 'active' : '' }}">
                                <i class="fa fa-tasks"></i>
                                <p>
                                    Orders Table View
                                </p>
                            </a>
                        </li>
                        @endpermission
                        @permission('view_pickup_issues')
                        <li class="nav-item">
                            <a href="{{ route('pickup_issues.index') }}" class="nav-link {{ request()->is('orders/pickup_issues') ? 'active' : '' }}">
                                <i class="fa fa-tasks"></i>
                                <p>
                                    @lang('Orders Pickup Issues')
                                </p>
                            </a>
                        </li>
                        @endpermission
                    </ul>
                </li>
                <li class="nav-item has-treeview @if(\Request()->is('ticket*') 
                    || \Request()->is('admin/ticket'))active menu-open @endif">
                    <a href="#" class="nav-link">
                        <i class="ti-ticket"></i>
                        <p>
                            @lang('menu.tickets')
                            <i class="right fa fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        @permission('add_ticket')
                        <li class="nav-item">
                            <a href="{{ route('ticket.create') }}" class="nav-link {{ request()->is('ticket/create') ? 'active' : '' }}">
                                <i class="ti-new-window nav-icon"></i>
                                <p>
                                    @lang('menu.ticketNew')
                                </p>
                            </a>
                        </li>
                        @endpermission

                        @permission('view_ticket')
                        <li class="nav-item">
                            <a href="{{ route('ticket.index') }}" class="nav-link {{ request()->is('ticket') ? 'active' : '' }}">
                                <i class="ti-list nav-icon"></i>
                                <p>
                                    @lang('menu.ticketsManage')
                                </p>
                            </a>
                        </li>
                        @endpermission
                    </ul>
                </li>
                <li class="nav-item has-treeview @if(\Request()->is('admin/equipment*') 
                  || \Request()->is('admin/test-sheets*')  || \Request()->is('admin/inventory*'))active menu-open @endif">
                    <a href="#" class="nav-link">
                        <i class="fa fa-building-o"></i>
                        <p>
                            @lang('menu.inventory')
                            <i class="right fa fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        @permission('view_equipment')
                        <li class="nav-item">
                            <a href="{{ route('admin.equipment.index') }}" class="nav-link {{ request()->is('admin/equipment*') ? 'active' : '' }}">
                                <i class="ti-hummer nav-icon"></i>
                                <p>
                                    Manage Inventory
                                </p>
                            </a>
                        </li>
                        @endpermission
                        @permission('view_inventory')
                        <li class="nav-item">
                            <a href="{{ route('admin.inventory.assets') }}" class="nav-link {{ request()->is('admin/inventory/assets') ? 'active' : '' }}">
                                <i class="fa fa-list-alt nav-icon"></i>
                                <p>
                                    @trans(Assets Inventory)
                                </p>
                            </a>
                        </li>
                        @endpermission
                        @permission('view_test_sheet_templates')
                        <li class="nav-item hide new_version">
                            <a href="{{ route('test-sheets.index') }}" class="nav-link {{ request()->is('admin/test-sheets/import') ? 'active' : '' }}">
                                <i class="fa fa-list-alt nav-icon"></i>
                                <p>
                                    @trans(Inspection Templates)
                                </p>
                            </a>
                        </li>
                        @endpermission
                    </ul>
                </li>

                <li class="nav-item has-treeview @if(\Request()->is('reports/orders') 
                    || \Request()->is('reports/*') || \Request()->is('reports/equipments'))active menu-open @endif">
                    <a href="#" class="nav-link">
                        <i class="ti-bar-chart"></i>
                        <p>
                            @lang('menu.reports')
                            <i class="right fa fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        @permission('parts_requests_report')
                        
                        <!--li class="nav-item">
                            <a href="{{ route('part_request.report') }}" class="nav-link {{ request()->is('reports/parts-requests') ? 'active' : '' }}">
                                <i class="ti-list"></i>
                                <p>
                                    @trans(Parts Requests List)
                                </p>
                            </a>
                        </li-->
                        
                        <li class="nav-item">
                            <a href="{{ route('part_request.reportgroup') }}" class="nav-link {{ request()->is('reports/parts-requests-group') ? 'active' : '' }}">
                                <i class="ti-list"></i>
                                <p>
                                    @trans(Parts Orders)
                                </p>
                            </a>
                        </li>
                        <!--li class="nav-item">
                            <a href="{{ route('part_request.reportbypart') }}" class="nav-link {{ request()->is('reports/parts-requests-bypart') ? 'active' : '' }}">
                                <i class="ti-list"></i>
                                <p>
                                    @trans(Parts Requests By Part)
                                </p>
                            </a>
                        </li-->
                        
                        @endpermission
                        @permission('view_billing_detail')
                        
                        <li class="nav-item">
                            <a href="{{ route('reports.billing') }}" class="nav-link {{ request()->is('reports/billing*') ? 'active' : '' }}">
                                <i class="ti-list"></i>
                                <p>
                                    @trans(Billing Detail)
                                </p>
                            </a>
                        </li>
                        
                        @endpermission
                        @permission('view_orders_by_month')
                        <li class="nav-item">
                            <a href="{{ route('reports.orders') }}" class="nav-link {{ request()->is('reports/orders') ? 'active' : '' }}">
                                <i class="fa fa-list-alt"></i>
                                <p>
                                    @lang('menu.reportMonthly')
                                </p>
                            </a>
                        </li>
                      @endpermission
                      @permission('view_orders_totals')
                        <li class="nav-item">
                            <a href="{{ route('reports.sales') }}" class="nav-link {{ request()->is('reports/sales') ? 'active' : '' }}">
                                <i class="ti-stats-up"></i>
                                <p>
                                    @trans(Order Totals)
                                </p>
                            </a>
                        </li>
                          @endpermission
                          @permission('view_orders_assets')
                        <li class="nav-item">
                            <a href="{{ route('reports.assets-show') }}" class="nav-link {{ request()->is('reports/assets*') ? 'active' : '' }}">
                                <i class="ti-list"></i>
                                <p>
                                    @trans(Rental Detail)
                                </p>
                            </a>
                        </li>
                        @endpermission
                    </ul>
                </li>
                <li class="nav-item has-treeview @if(\Request()->is('parts-ps') 
                    || \Request()->is('parts-catalog'))active menu-open @endif">
                    <a href="#" class="nav-link">
                        <i class="ti-bar-chart"></i>
                        <p>
                            @lang('PartsSource')
                            <i class="right fa fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                @permission('query_parts_source')
                <li class="nav-item">
                    <a href="{{ route('parts.ps') }}" class="nav-link {{ request()->is('parts-ps') ? 'active' : '' }}">
                        <i class="fa fa-list"></i>
                        <p>
                            @lang('Inventory')
                        </p>
                    </a>
                </li>
                @endpermission
                @permission('query_parts_source')
                <li class="nav-item">
                    <a href="{{ route('parts.catalog') }}"  class="nav-link {{ request()->is('parts-catalog') ? 'active' : '' }}">
                        <i class="fa fa-list"></i>
                        <p>
                            @lang('Online Query')
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('parts.local_catalog') }}" target="_blank" class="nav-link {{ request()->is('imedical-parts') ? 'active' : '' }}">
                        <i class="fa fa-list"></i>
                        <p>
                            iMedical Parts (Public)
                        </p>
                    </a>
                </li>
                @endpermission
                    </ul>
                </li>
                <li class="nav-item has-treeview @if(\Request()->is('admin/settings*') ||\Request()->is('admin/hospital*') || \Request()->is('admin/team') || \Request()->is('admin/costcenter'))active menu-open @endif">
                    <a href="#" class="nav-link">
                        <i class="fa fa-cogs"></i>
                        <p>
                            @lang('menu.admin')
                            <i class="right fa fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <!--li class="nav-item">
                            <a href="{{ route('admin.language-settings.index') }}" class="nav-link {{ request()->is('admin/settings/language-settings') ? 'active' : '' }}">
                                <i class="fa fa-circle-o nav-icon"></i>
                                <p>@lang('app.language') @lang('menu.settings')</p>
                            </a>
                        </li-->
                        @permission('view_hospital')
                        <li class="nav-item">
                            <a href="{{ route('admin.hospital.index') }}" class="nav-link {{ request()->is('admin/hospital') ? 'active' : '' }}">
                                <i class="fa fa-hospital-o nav-icon"></i>
                                <p>
                                    @lang('Sites')
                                </p>
                            </a>
                        </li>
                        @endpermission
                        @permission('view_hospital_staff')
                        <li class="nav-item">
                            <a href="{{ route('admin.hospital-staff.index') }}" class="nav-link {{ request()->is('admin/hospital-staff*') ? 'active' : '' }}">
                                <i class="fa fa-user-md nav-icon"></i>
                                <p>
                                    @lang('Site Staff')
                                </p>
                            </a>
                        </li>
                        @endpermission
                        @permission('view_cost_center')
                        <li class="nav-item">
                            <a href="{{ route('admin.costcenter.index') }}" class="nav-link {{ request()->is('admin/costcenter*') ? 'active' : '' }}">
                                <i class="ti-cloud-up"></i>
                                <p>
                                    @lang('menu.costCenter')
                                </p>
                            </a>
                        </li>
                        @endpermission
                        @permission('view_user')
                        <li class="nav-item">
                            <a href="{{ route('admin.team.index') }}" class="nav-link {{ request()->is('admin/team') ? 'active' : '' }}">
                                <i class="fa fa-users"></i>
                                <p>
                                    @lang('menu.users')
                                </p>
                            </a>
                        </li>
                        @endpermission
                        @role('superadmin')
                        <li class="nav-item">
                            <a href="{{ route('admin.role-permission.index') }}" class="nav-link {{ request()->is('admin/settings/role-permission') ? 'active' : '' }}">
                                <i class="ti-lock nav-icon"></i>
                                <p>
                                   @lang('menu.permissions') 
                                </p>
                            </a>
                        </li>
                        <!--li class="nav-item">
                            <a href="{{ route('admin.smtp-settings.index') }}" class="nav-link {{ request()->is('admin/settings/smtp-settings') ? 'active' : '' }}">
                                <i class="fa fa-envelope nav-icon"></i>
                                <p>@lang('menu.mailSetting')</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.sms-settings.index') }}" class="nav-link {{ request()->is('admin/settings/sms-settings') ? 'active' : '' }}">
                                <i class="fa fa-mobile-phone nav-icon"></i>
                                <p>@lang('menu.smsSetting')</p>
                            </a>
                        </li-->
                        @endrole
                    </ul>
                </li>
                @permission('view_contact_grm')
                <li class="nav-item">
                    <a href="{{ route('contacts.index') }}" class="nav-link {{ request()->is('contacts*') ? 'active' : '' }}">
                        <i class="fa fa-phone-square"></i>
                        <p>
                            @lang('menu.contactUs')
                        </p>
                    </a>
                </li>
                @endpermission
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
        <!--input type="image"  id="btnZoomIn" style="height: 30px; width: 30px;"  
           src="http://icons.iconarchive.com/icons/visualpharm/must-have/256/Zoom-In-icon.png" />  
       <input type="image" id="btnZoomOut" style="height: 30px; width: 30px"  
           src="http://icons.iconarchive.com/icons/visualpharm/must-have/256/Zoom-Out-icon.png" /--> 
    </div>
    <!-- /.sidebar -->

</aside>