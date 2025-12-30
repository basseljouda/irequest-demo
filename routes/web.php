<?php

/*
  |--------------------------------------------------------------------------
  | Web Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register web routes for your application. These
  | routes are loaded by the RouteServiceProvider within a group which
  | contains the "web" middleware group. Now create something great!
  |
 */

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PartRequestController;
use App\Http\Controllers\Admin\TestSheetTemplateController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\EnhancedDashboardController;

// ============================================================================
// Public Routes
// ============================================================================

// Authentication Routes
Auth::routes();

Route::get('/', 'Auth\LoginController@showLoginForm');
Route::get('/home', 'Auth\LoginController@showLoginForm');
Route::post('login', 'Auth\LoginController@login')->name('auth.login');
Route::get('/privacy-policy', 'Auth\LoginController@showPrivacyPolicy')->name('privacy-policy');
Route::get('/terms-conditions', 'Auth\LoginController@showTermsConditions')->name('terms-conditions');

// Okta SSO Routes
Route::get('/login/okta', 'Auth\LoginOkta@redirectToProvider')->name('login-okta');
Route::get('/login/okta/callback', 'Auth\LoginOkta@handleProviderCallback');

// ============================================================================
// Authenticated Routes
// ============================================================================

Route::group(['middleware' => 'auth'], function () {

    // Notifications
    Route::post('mark-notification-read', 'NotificationController@markAllRead')->name('mark-notification-read');

    // ========================================================================
    // Orders Management
    // ========================================================================
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/data', 'OrdersController@data')->name('data');
        Route::get('/cards', 'OrdersController@cards')->name('cards');
        Route::get('/cards-data', 'OrdersController@cardsData')->name('cards-data');
        Route::get('/showdetails/{id}', 'OrdersController@showdetails')->name('showdetails');
        Route::get('/showPDF/{id}', 'OrdersController@showPDF')->name('showPDF');
        Route::get('/fetch', 'OrdersController@fetchAjax')->name('fetch');

        // Order Status Actions
        Route::get('/reassign/{id}', 'OrdersController@reassign')->name('reassign');
        Route::get('/accept/{id}', 'OrdersController@accept')->name('accept');
        Route::get('/complete/{id}', 'OrdersController@complete')->name('complete');
        Route::get('/pickup/{id}', 'OrdersController@pickup')->name('pickup');
        Route::post('/submitStatus', 'OrdersController@submitStatus')->name('submitStatus');

        // Pickup Management
        Route::get('/request/{id}', 'OrdersController@showPickupRequest')->name('request');
        Route::post('/send-request', 'OrdersController@sendPickupRequest')->name('send-request');
        Route::post('/delete-request/{id}', 'OrdersController@deletePickupRequest')->name('pickup-request-delete');
        Route::get('/cancel_pickup/{id}', 'OrdersController@showCancelPickup')->name('cancel_pickup');
        Route::get('/cancel_pickup_note/{id}', 'OrdersController@showCancelPickupNote')->name('cancel_pickup_note');
        Route::post('/pickup-issues/{id}/resolve', 'OrdersController@resolvePickupIssue')->name('pickupIssues.resolve');

        // Room Management
        Route::get('/changeRoom/{id}', 'OrdersController@showChangeRoom')->name('changeRoom');
        Route::post('/changeRoom', 'OrdersController@changeRoom')->name('showchangeRoom');

        // Asset Management
        Route::get('/editAsset/{item}/{asset}', 'OrdersController@editAsset')->name('editAsset');
        Route::post('/updateAsset', 'OrdersController@updateAsset')->name('updateAsset');

        // File Attachments
        Route::post('/attach', 'OrdersController@attachFiles')->name('attach');
        Route::delete('/deleteAttach/{order_id}/{id}', 'OrdersController@deleteFile')->name('delete-attach');

        // Pickup Issues
        Route::get('/pickup_issues', 'PickupIssuesController@index')->name('pickup_issues.index');
        Route::get('/pickup_issues/data', 'PickupIssuesController@data')->name('pickup_issues.data');
    });

    Route::resource('orders', 'OrdersController');

    // ========================================================================
    // Customer Orders
    // ========================================================================
    Route::prefix('cust_orders')->name('cust_orders.')->group(function () {
        Route::get('/data', 'CustOrdersController@data')->name('data');
        Route::get('/new_request', 'CustOrdersController@newRfq')->name('new_request');
        Route::get('/showdetails/{id}', 'CustOrdersController@showdetails')->name('showdetails');
        Route::post('/submitStatus', 'CustOrdersController@submitStatus')->name('submitStatus');
    });

    Route::resource('cust_orders', 'CustOrdersController');

    // ========================================================================
    // Parts Requests & Orders
    // ========================================================================
    Route::prefix('part_request')->name('part_request.')->group(function () {
        // Main Views
        Route::get('/index-order', [PartRequestController::class, 'indexOrder'])->name('index_order');
        Route::get('/index-order-data', [PartRequestController::class, 'indexOrderData'])->name('index_order_data');
        Route::get('/index-reply', [PartRequestController::class, 'indexReply'])->name('index_reply');
        Route::get('/index-rma', [PartRequestController::class, 'indexRMA'])->name('index_rma');
        Route::get('/index-rma-orders', [PartRequestController::class, 'indexRMAOrders'])->name('index_rma_orders');
        Route::get('/index-rma-data', [PartRequestController::class, 'indexRMAOrdersData'])->name('index_rma_data');

        // Order Management
        Route::get('/create-order/{id}', [PartRequestController::class, 'createOrder'])->name('create_order');
        Route::get('/edit-order/{id}', [PartRequestController::class, 'editOrder'])->name('edit_order');
        Route::post('/storeOrder', [PartRequestController::class, 'storeOrder'])->name('storeOrder');
        Route::put('/{id}/update_order', [PartRequestController::class, 'updateOrder'])->name('updateOrder');
        Route::get('/{id}/print', [PartRequestController::class, 'printOrder'])->name('print');

        // Details & Modals
        Route::get('/showdetails/{id}', [PartRequestController::class, 'showDetails'])->name('showdetails');
        Route::get('/rmashowdetails/{id}', [PartRequestController::class, 'showRMADetails'])->name('showRMAdetails');

        // RFQ Management
        Route::post('/request-rfq', [PartRequestController::class, 'requestRfq'])->name('request_rfq');
        Route::post('/{id}/reject-rfq', [PartRequestController::class, 'rejectRfq'])->name('reject_rfq');
        Route::post('/{id}/reply', [PartRequestController::class, 'replyRfq'])->name('reply_rfq');

        // Status & Actions
        Route::post('/submitStatus', [PartRequestController::class, 'submitStatus'])->name('submitStatus');
        Route::post('/{id}/delayOrder', [PartRequestController::class, 'delayOrder'])->name('delayOrder');

        // RMA Management
        Route::get('/rma-order/{id}', [PartRequestController::class, 'rmaOrder'])->name('rma_order');
        Route::post('/submitRMA', [PartRequestController::class, 'submitRMA'])->name('submitRMA');
        Route::post('/submitRMAStatus', [PartRequestController::class, 'submitRMAStatus'])->name('submitRMAStatus');

        // Work Order Files
        Route::post('/upload_wo_file', [PartRequestController::class, 'uploadWoFile'])->name('upload.wo_file');

        // Shippo Integration
        Route::get('/shipoocarriers', [PartRequestController::class, 'getcarriers'])->name('shipoocarriers');
    });

    Route::resource('part_request', 'PartRequestController');

    // ========================================================================
    // RMA Routes
    // ========================================================================
    Route::prefix('rma')->name('rma.')->group(function () {
        Route::post('/{id}/approve', [PartRequestController::class, 'approveRMA'])->name('approve');
        Route::post('/{id}/reject', [PartRequestController::class, 'rejectRMA'])->name('reject');
        Route::get('/{id}/print', [PartRequestController::class, 'printRMA'])->name('print');
        Route::get('/resolve-modal/{rmaId}', [PartRequestController::class, 'showResolveModal'])->name('resolveModal');
        Route::post('/resolve-post', [PartRequestController::class, 'submitCustomerResolution'])->name('customer.response');
    });

    // ========================================================================
    // Shippo Integration Routes
    // ========================================================================
    Route::prefix('shippo')->name('shippo.')->group(function () {
        Route::post('/webhook/', [PartRequestController::class, 'handleShippoWebhook'])->name('webhook');
        Route::post('/shipping-options/{orderId}', [PartRequestController::class, 'getAvailableShippingOptions'])->name('shipping-options');
        Route::get('/tracking-modal/{trackingNumber}/{id}', [PartRequestController::class, 'showTrackingModal'])->name('tracking');
        Route::get('/cancel-shipment/{trackingNumber}/{id}', [PartRequestController::class, 'cancelShipment'])->name('cancelShipment');
    });

    // ========================================================================
    // Parts Requests Reports
    // ========================================================================
    Route::prefix('reports/parts-requests')->name('part_request.report')->group(function () {
        Route::get('/', 'PartRequestController@reportRequests')->name('');
        Route::get('-data', 'PartRequestController@reportRequestsData')->name('-data');
        Route::get('-group', 'PartRequestController@reportRequestsGroup')->name('group');
        Route::get('-group-data', 'PartRequestController@reportRequestsGroupData')->name('-group-data');
        Route::get('-bypart', 'PartRequestController@reportRequestsBypart')->name('bypart');
        Route::get('-bypart-data', 'PartRequestController@reportRequestsBypartData')->name('-bypart-data');
    });

    // ========================================================================
    // Tickets
    // ========================================================================
    Route::prefix('ticket')->name('ticket.')->group(function () {
        Route::get('/data', 'TicketController@data')->name('data');
        Route::get('/showdetails/{id}', 'TicketController@showdetails')->name('showdetails');
        Route::get('/showPDF/{id}', 'TicketController@showPDF')->name('showPDF');
        Route::get('/assign/{id}', 'TicketController@assign')->name('assign');
        Route::get('/complete/{id}', 'TicketController@complete')->name('complete');
        Route::get('/pickup/{id}', 'TicketController@pickup')->name('pickup');
        Route::post('/submitStatus', 'TicketController@submitStatus')->name('submitStatus');
        Route::post('/attach', 'TicketController@AttachTicket')->name('attach');
    });

    Route::resource('ticket', 'TicketController');

    // ========================================================================
    // Contacts (GRM)
    // ========================================================================
    Route::resource('contacts', 'ContactGRMController');

    // ========================================================================
    // Electronic Catalog
    // ========================================================================
    Route::prefix('ecatalog')->name('ecatalog.')->group(function () {
        Route::get('/search-ps-ajax', 'eCatalogController@search_ajax')->name('search-ps-ajax');
        Route::get('/search-hillrom', 'eCatalogController@getAutocompleteResults')->name('search-hillrom');
        Route::get('/order-catalog', 'eCatalogController@viewOrderCatalog')->name('order-catalog');
    });

    Route::resource('ecatalog', 'eCatalogController');

    Route::prefix('parts-ps')->name('ps.')->group(function () {
        Route::get('/', 'eCatalogController@showPartsSource')->name('index');
        Route::post('/', 'eCatalogController@postPS')->name('post');
        Route::get('/showinv/{id}', 'eCatalogController@showAddInv')->name('showinv');
        Route::put('/update/{id}', 'eCatalogController@updatePS')->name('update');
    });

    // ========================================================================
    // Reports
    // ========================================================================
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/orders', 'ReportsController@orders')->name('orders');
        Route::get('/orders-data', 'ReportsController@ordersData')->name('orders-data');
        Route::get('/equipments', 'ReportsController@equipments')->name('equipments');
        Route::get('/equipments-data', 'ReportsController@equipmentsData')->name('equipments-data');
        Route::get('/sales', 'ReportsController@sales')->name('sales');
        Route::get('/sales-data', 'ReportsController@salesData')->name('sales-data');
        Route::get('/assets-show', 'ReportsController@assets')->name('assets-show');
        Route::get('/billing', 'ReportsController@billing')->name('billing');
        Route::get('/billing-data', 'ReportsController@billingdata')->name('billing-data');
    });

    // ========================================================================
    // Chat
    // ========================================================================
    Route::get('chat', 'ChatsController@index')->name('chat');
    Route::get('messages', 'ChatsController@fetchMessages')->name('messages');
    Route::post('messages', 'ChatsController@sendMessage')->name('messages.send');

    // ========================================================================
    // Mobile Verification
    // ========================================================================
    Route::get('change-mobile', 'VerifyMobileController@changeMobile')->name('changeMobile');
    Route::post('send-otp-code', 'VerifyMobileController@sendVerificationCode')->name('sendOtpCode');
    Route::post('send-otp-code/account', 'VerifyMobileController@sendVerificationCode')->name('sendOtpCode.account');
    Route::post('verify-otp-phone', 'VerifyMobileController@verifyOtpCode')->name('verifyOtpCode');
    Route::post('verify-otp-phone/account', 'VerifyMobileController@verifyOtpCode')->name('verifyOtpCode.account');
    Route::get('remove-session', 'VerifyMobileController@removeSession')->name('removeSession');

    // ========================================================================
    // Test Sheets
    // ========================================================================
    Route::prefix('admin/test-sheets')->name('test-sheets.')->group(function () {
        Route::get('/', [TestSheetTemplateController::class, 'index'])->name('index');
        Route::get('/create', [TestSheetTemplateController::class, 'create'])->name('create');
        Route::post('/store', [TestSheetTemplateController::class, 'store'])->name('store');
        Route::get('/import', [TestSheetTemplateController::class, 'importForm'])->name('import');
        Route::post('/import-excel', [TestSheetTemplateController::class, 'importExcel'])->name('import-excel');
        Route::get('/show-submit/{asset_id}', [TestSheetTemplateController::class, 'showSubmit'])->name('showsubmit');
        Route::post('/submit', [TestSheetTemplateController::class, 'submit'])->name('submit');
        Route::get('/{id}', [TestSheetTemplateController::class, 'show'])->name('show');
    });

    // ========================================================================
    // Admin Routes
    // ========================================================================
    Route::group(['namespace' => 'Admin', 'prefix' => 'admin', 'as' => 'admin.'], function () {

        // Dashboard Routes
        Route::prefix('dashboard')->name('dashboard.')->group(function () {
            Route::get('/', 'AdminDashboardController@index')->name('index');
            Route::get('/widgets/get', 'AdminDashboardController@index')->name('widgets.get');
            Route::get('/widgets/save', 'AdminDashboardController@index')->name('widgets.save');
            Route::get('/ajax', [AdminDashboardController::class, 'ajax'])->name('ajax');
            Route::get('/kpis', [AdminDashboardController::class, 'getKPIs'])->name('kpis');
            Route::get('/charts', [AdminDashboardController::class, 'getCharts'])->name('charts');
            Route::get('/enhanced', [AdminDashboardController::class, 'getEnhanced'])->name('enhanced');
        });

        // Enhanced Dashboard Routes
        Route::prefix('dashboard-enhanced')->name('dashboard.enhanced.')->group(function () {
            Route::get('/manager', [EnhancedDashboardController::class, 'manager'])->name('manager');
            Route::get('/user', [EnhancedDashboardController::class, 'user'])->name('user');
            Route::post('/preferences/save', [EnhancedDashboardController::class, 'savePreferences'])->name('preferences.save');
            Route::post('/preferences/reset', [EnhancedDashboardController::class, 'resetPreferences'])->name('preferences.reset');
            Route::get('/kpi/{kpiId}', [EnhancedDashboardController::class, 'getKPI'])->name('kpi');
            Route::get('/chart/{chartId}', [EnhancedDashboardController::class, 'getChart'])->name('chart');
            Route::get('/widget/{widgetId}', [EnhancedDashboardController::class, 'getWidget'])->name('widget');
        });

        // Equipment Management
        Route::prefix('equipment')->name('equipment.')->group(function () {
            Route::get('/data', 'EquipmentsController@data')->name('data');
            Route::post('/active', 'EquipmentsController@changeActive')->name('changeActive');
        });
        Route::resource('equipment', 'EquipmentsController');

        // Hospital Management
        Route::prefix('hospital')->name('hospital.')->group(function () {
            Route::get('/data', 'HospitalsController@data')->name('data');
        });
        Route::resource('hospital', 'HospitalsController');

        // Hospital Staff Management
        Route::prefix('hospital-staff')->name('hospital-staff.')->group(function () {
            Route::get('/data', 'HospitalsStaffController@data')->name('data');
            Route::post('/login-status', 'HospitalsStaffController@loginStatus')->name('loginStatus');
        });
        Route::resource('hospital-staff', 'HospitalsStaffController');

        // Cost Center Management
        Route::prefix('costcenter')->name('costcenter.')->group(function () {
            Route::get('/data', 'CostCenterController@data')->name('data');
            Route::post('/active', 'CostCenterController@changeActive')->name('changeActive');
        });
        Route::resource('costcenter', 'CostCenterController');

        // Inventory Management
        Route::prefix('inventory')->name('inventory.')->group(function () {
            Route::get('/data', 'InventoryController@data')->name('data');
            Route::get('/assets', 'InventoryController@assets')->name('assets');
            Route::get('/item-add/{inventory}', 'InventoryController@showAddItem')->name('item-add');
            Route::get('/items-data/{id}', 'InventoryController@getInventoryItems')->name('items-data');
            Route::get('/items-list/{id}/{item}', 'InventoryController@listInventoryItems')->name('items-list');
            Route::post('/item-store', 'InventoryController@storeNewItems')->name('item-store');
            Route::post('/item-update', 'InventoryController@updateItemBalance')->name('item-update');
        });
        Route::resource('inventory', 'InventoryController');

        // Todo Items
        Route::prefix('todo-items')->name('todo-items.')->group(function () {
            Route::post('/update-todo-item', 'AdminTodoItemController@updateTodoItem')->name('updateTodoItem');
        });
        Route::resource('todo-items', 'AdminTodoItemController');

        // Settings Routes
        Route::group(['prefix' => 'settings'], function () {
            // Company Settings
            Route::resource('settings', 'CompanySettingsController', ['only' => ['index', 'edit', 'update']]);

            // Role & Permission Management
            Route::prefix('role-permission')->name('role-permission.')->group(function () {
                Route::post('/assignAllPermission', 'ManageRolePermissionController@assignAllPermission')->name('assignAllPermission');
                Route::post('/removeAllPermission', 'ManageRolePermissionController@removeAllPermission')->name('removeAllPermission');
                Route::post('/assignRole', 'ManageRolePermissionController@assignRole')->name('assignRole');
                Route::post('/detachRole', 'ManageRolePermissionController@detachRole')->name('detachRole');
                Route::post('/storeRole', 'ManageRolePermissionController@storeRole')->name('storeRole');
                Route::post('/deleteRole', 'ManageRolePermissionController@deleteRole')->name('deleteRole');
                Route::get('/showMembers/{id}', 'ManageRolePermissionController@showMembers')->name('showMembers');
            });
            Route::resource('role-permission', 'ManageRolePermissionController');

            // Language Settings
            Route::prefix('language-settings')->name('language-settings.')->group(function () {
                Route::get('/change-language', 'LanguageSettingsController@changeLanguage')->name('change-language');
                Route::put('/change-language-status/{id}', 'LanguageSettingsController@changeStatus')->name('changeStatus');
            });
            Route::resource('language-settings', 'LanguageSettingsController');

            // Theme Settings
            Route::resource('theme-settings', 'AdminThemeSettingsController');

            // SMTP Settings
            Route::prefix('smtp-settings')->name('smtp-settings.')->group(function () {
                Route::get('/sent-test-email', 'AdminSmtpSettingController@sendTestEmail')->name('sendTestEmail');
            });
            Route::resource('smtp-settings', 'AdminSmtpSettingController');

            // SMS Settings
            Route::resource('sms-settings', 'AdminSmsSettingsController', ['only' => ['index', 'update']]);

            // Footer Settings
            Route::prefix('footer-settings')->name('footer-settings.')->group(function () {
                Route::get('/data', 'FooterSettingController@data')->name('data');
            });
            Route::resource('footer-settings', 'FooterSettingController');
        });

        // Profile Management
        Route::resource('profile', 'AdminProfileController');

        // Team Management
        Route::prefix('team')->name('team.')->group(function () {
            Route::get('/data', 'AdminTeamController@data')->name('data');
            Route::post('/change-role', 'AdminTeamController@changeRole')->name('changeRole');
        });
        Route::resource('team', 'AdminTeamController');

        // Company Management
        Route::prefix('company')->name('company.')->group(function () {
            Route::get('/data', 'AdminCompanyController@data')->name('data');
            Route::get('/fetch', 'AdminCompanyController@fetchAjax')->name('fetch');
        });
        Route::resource('company', 'AdminCompanyController');

        // Sticky Notes
        Route::resource('sticky-note', 'AdminStickyNotesController');

        // Departments
        Route::resource('departments', 'AdminDepartmentController');

        // Designations
        Route::resource('designations', 'AdminDesignationController');

        // Documents
        Route::prefix('documents')->name('documents.')->group(function () {
            Route::get('/data', 'AdminDocumentController@data')->name('data');
            Route::get('/download-document/{document}', 'AdminDocumentController@downloadDoc')->name('downloadDoc');
        });
        Route::resource('documents', 'AdminDocumentController');
    });
});
