<?php

namespace App\Services\ManagerOrders;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

/**
 * DEMO SKELETON: OrdersQuery Interface
 * 
 * This service was originally responsible for:
 * - Building complex database queries for orders
 * - Applying filters based on user permissions
 * - Joining multiple tables (hospitals, companies, cost centers, staff)
 * - Filtering by status, company, hospital, patient, room, equipment, dates, etc.
 * 
 * For demo purposes, all business logic has been removed.
 * In production, this would build and execute database queries.
 */
interface OrdersQueryInterface
{
    /**
     * Build the base query with common joins and scopes
     * Original: Built complex query with joins and permission-based filtering
     * 
     * @return Builder
     */
    public function baseQuery(): Builder;

    /**
     * Apply all filters to the query
     * Original: Applied multiple filters including status, company, hospital, patient, dates, etc.
     * 
     * @param Builder $query
     * @param Request $request
     * @return Builder
     */
    public function applyFilters(Builder $query, Request $request): Builder;
}

