<?php

namespace App\Http\Controllers;

use Yajra\DataTables\Facades\DataTables;
use App\Helper\Reply;
use Illuminate\Http\Request;

/**
 * DEMO SKELETON: Electronic Catalog Controller
 * 
 * PURPOSE & WORKFLOW:
 * This controller manages the electronic parts catalog system for medical equipment.
 * 
 * Original Functionality:
 * - Browse and search medical equipment parts catalog
 * - Integration with external PartsSource API for parts lookup
 * - Integration with Hillrom API for autocomplete suggestions
 * - Local parts inventory management with quantity tracking
 * - Parts pricing with discount calculations (iMedical pricing tiers)
 * - Export catalog products to Excel
 * - Create catalog orders and submit to CRM system
 * - Filter by category, manufacturer, asset type
 * - Display parts with images, pricing, availability
 * 
 * Key Features (Original):
 * 1. Catalog Browsing: View equipment by category/manufacturer with filters
 * 2. Parts Search: Search PartsSource database via external API
 * 3. Inventory Management: Track local parts inventory quantities
 * 4. Pricing: Calculate discounted prices for different customer tiers
 * 5. Order Management: Create and submit catalog orders to CRM
 * 
 * For demo purposes, all external API integrations and database queries have been removed.
 * The controller now returns dummy data matching the expected structure for portfolio presentation.
 */
class eCatalogController extends Admin\AdminBaseController {

    public function __construct() {
        parent::__construct();
        $this->pageTitle = lang('eCatalog');
        $this->pageIcon = 'fa fa-list';
    }

    /**
     * DEMO: Main catalog index - Browse equipment catalog
     * 
     * Original: Loaded manufacturers, categories, and inventory items from database
     * Returns catalog view with filters and DataTables for equipment browsing
     * 
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function index() {
        abort_if(!user()->can('view_catalog'), 403);

        if (!request()->ajax()) {
            // DEMO: Return dummy filter data
            $this->list = collect([
                (object)['id' => 1, 'title' => 'Demo Manufacturer A'],
                (object)['id' => 2, 'title' => 'Demo Manufacturer B'],
            ]);

            $this->categories = collect([
                (object)['id' => 1, 'title' => 'Ventilators'],
                (object)['id' => 2, 'title' => 'Hospital Beds'],
                (object)['id' => 3, 'title' => 'Infusion Pumps'],
            ]);

            return view('ecatalog.index', $this->data);
        }

        // DEMO: Return dummy DataTables data for AJAX requests
        $items = collect([
            (object)[
                'id' => 1,
                'category' => 1,
                'manufacturer' => 1,
                'model_name' => 'Vent-X2000',
                'title' => 'Ventilator',
                'mt' => 'Demo Manufacturer A',
                'product_details' => 'Advanced mechanical ventilator with modern features',
                'availability' => 'available',
                'asset_id' => 'PR-M00001',
            ],
            (object)[
                'id' => 2,
                'category' => 2,
                'manufacturer' => 2,
                'model_name' => 'Bed-Pro500',
                'title' => 'Hospital Bed',
                'mt' => 'Demo Manufacturer B',
                'product_details' => 'Electric hospital bed with safety features',
                'availability' => 'available',
                'asset_id' => 'PR-M00002',
            ],
            (object)[
                'id' => 3,
                'category' => 3,
                'manufacturer' => 1,
                'model_name' => 'Infuse-Max',
                'title' => 'Infusion Pump',
                'mt' => 'Demo Manufacturer A',
                'product_details' => 'Programmable infusion pump system',
                'availability' => 'pending sale',
                'asset_id' => 'PR-M00003',
            ],
        ]);

        return DataTables::of($items)
            ->addColumn('chk', function ($row) {
                return '';
            })
            ->addColumn('action', function ($row) {
                $action = '';
                if (user()->can('request_sales') && $row->availability == 'available') {
                    $action .= '<a href="#" data-row-id="' . $row->id . '" data-row-title="' . $row->asset_id . '" class="btn btn-info btn-sm sales_request"
                        x-data-toggle="tooltip" title="Request change">Request</a>';
                }
                if (user()->can('request_sales') && $row->availability == 'pending sale') {
                    $action .= '<a href="#" data-row-id="' . $row->id . '" data-row-title="' . $row->asset_id . '" class="btn btn-info btn-sm make_sold text-warning"
                        x-data-toggle="tooltip" title="Click to Make it Sold">Make Sold</a>';
                }
                return $action;
            })
            ->addColumn('thumb', function ($row) {
                return '<img width="96" src="/images/demo-equipment.jpg" alt="' . $row->title . '" />';
            })
            ->addColumn('name', function ($row) {
                return $row->title . ', ' . $row->mt . ', ' . $row->model_name;
            })
            ->addColumn('info', function ($row) {
                return "<table class='table'>"
                    . "<tr>"
                    . "<td><div class='breakspace'>" . ($row->product_details ?? 'N/A') . "</div></td>"
                    . "</tr>"
                    . "</table>";
            })
            ->editColumn('user_id', function ($row) {
                return 'Demo User';
            })
            ->editColumn('id', function ($row) {
                return 'PR-M' . str_pad($row->id, 5, '0', STR_PAD_LEFT);
            })
            ->editColumn('availability', function ($row) {
                return 1;
            })
            ->editColumn('asset_id', function ($row) {
                return $row->asset_id;
            })
            ->rawColumns(['info', 'thumb'])
            ->addIndexColumn()
            ->make(true);
    }

    /**
     * DEMO: Search PartsSource - Search external parts database
     * 
     * Original: Made cURL requests to PartsSource API, fetched products, saved to local database
     * Returns DataTables with parts search results including pricing and availability
     * 
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function getPartsSource() {
        $this->pageTitle = lang('PartsSource Query');

        if (request()->ajax()) {
            $query = request()->filter_search;
            
            // DEMO: Return dummy parts data instead of API call
            if ($query != '') {
                $this->products = [
                    [
                        'id' => 'PS-001',
                        'title' => 'Demo Medical Part A',
                        'partNumber' => 'MP-12345',
                        'brand' => 'Demo Brand',
                        'description' => 'High-quality medical equipment part',
                        'detailUrl' => '/parts/demo-part-a',
                        'models' => 'Model X, Model Y',
                        'thumbnailUrl' => '/images/demo-part.jpg',
                        'options' => [
                            [
                                'price' => 125.50,
                                'images' => ['/images/part1.jpg'],
                                'oemListPrice' => 150.00
                            ]
                        ]
                    ],
                    [
                        'id' => 'PS-002',
                        'title' => 'Demo Medical Part B',
                        'partNumber' => 'MP-67890',
                        'brand' => 'Demo Brand',
                        'description' => 'Replacement part for medical equipment',
                        'detailUrl' => '/parts/demo-part-b',
                        'models' => 'Model Z',
                        'thumbnailUrl' => '/images/demo-part2.jpg',
                        'options' => [
                            [
                                'price' => 89.99,
                                'images' => ['/images/part2.jpg'],
                                'oemListPrice' => 110.00
                            ]
                        ]
                    ],
                ];
            } else {
                $this->products = [];
            }

            return DataTables::of($this->products)
                ->editColumn("detailUrl", function ($row) {
                    return "<a href='https://www.partssource.com" . $row['detailUrl'] . "' target='_blank'>Open</a>";
                })
                ->editColumn("thumbnailUrl", function ($row) {
                    return "<img style='width:64px;height:64px' src='" . $row['thumbnailUrl'] . "' />";
                })
                ->addColumn("action", function ($row) {
                    return "<a class='add-to-inventory' href='javascript:;' row-data='" . $row['id'] . "' >Update Quantity</a>";
                })
                ->addColumn("price", function ($row) {
                    return '$' . ($row['options'][0]['price'] ?? '0.00');
                })
                ->addColumn("models", function ($row) {
                    return $row['models'] ?? 'N/A';
                })
                ->addColumn("qty", function ($row) {
                    return 5;
                })
                ->addColumn("price_imed", function ($row) {
                    $price = $row['options'][0]['price'] ?? 0;
                    if ($price > 0) {
                        return '$' . round($price - ($price * 15 / 100), 2);
                    }
                    return '$0.00';
                })
                ->addColumn("price_imed_ref", function ($row) {
                    $price = $row['options'][0]['price'] ?? 0;
                    if ($price > 0) {
                        return '$' . round($price - ($price * 35 / 100), 2);
                    }
                    return '$0.00';
                })
                ->rawColumns(['detailUrl', 'thumbnailUrl', 'action'])
                ->addIndexColumn()
                ->make(true);
        }
        
        return view('ecatalog.partssource', $this->data);
    }

    /**
     * Removed: getImage() - Fetched equipment images from database
     * Removed: showAddInv() - Displayed add inventory form
     * Removed: updatePS() - Updated parts source quantity and location
     * Removed: showPartsSource() - Displayed local parts inventory with DataTables
     * Removed: create() - Displayed catalog order creation form
     * Removed: postPS() - Saved parts source to database
     * Removed: getLocalPartsSource() - Queried local parts database with search
     * Removed: exportPSProducts() - Exported products to Excel via PartsSource API
     * Removed: search_ajax() - AJAX search endpoint for PartsSource API
     * Removed: getAutocompleteResults() - Autocomplete suggestions from Hillrom API
     * Removed: viewOrderCatalog() - Displayed order catalog view
     * Removed: store() - Created catalog orders and submitted to CRM via API
     */

}
