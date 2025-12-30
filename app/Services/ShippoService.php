<?php

namespace App\Services;

/**
 * DEMO SKELETON: ShippoService Interface
 * 
 * This service was originally responsible for:
 * - Integration with Shippo shipping API
 * - Creating shipments and retrieving shipping rates
 * - Managing shipping labels and tracking
 * - Address validation
 * 
 * For demo purposes, all business logic has been removed.
 * In production, this would interface with the Shippo API.
 */
interface ShippoServiceInterface
{
    /**
     * Get available carriers
     * Original: Fetched carrier accounts from Shippo API
     * 
     * @return array|null
     */
    public function getCarriers();

    /**
     * Get specific carrier information
     * Original: Fetched carrier details from Shippo API
     * 
     * @param string $carrier
     * @return array|null
     */
    public function getCarrier($carrier);

    /**
     * Create a shipment
     * Original: Created shipment via Shippo API and saved shipment ID
     * 
     * @param mixed $order
     * @return array|null
     */
    public function createShipment($order);

    /**
     * Get available shipping methods
     * Original: Fetched shipping rates from Shippo API based on order and parcel details
     * 
     * @param mixed $order
     * @param array $parcel
     * @return array|null
     */
    public function getAvailableShippingMethods($order, $parcel);

    /**
     * Create a transaction (purchase label)
     * Original: Created shipping label transaction via Shippo API
     * 
     * @param string $rateObjectId
     * @param array $metaData
     * @return array|null
     */
    public function createTransaction($rateObjectId, $metaData);

    /**
     * Request label refund
     * Original: Requested refund for shipping label via Shippo API
     * 
     * @param string $transactionId
     * @return array|string|null
     */
    public function requestLabelRefund($transactionId);

    /**
     * Retrieve tracking information
     * Original: Fetched tracking details from Shippo API
     * 
     * @param string $carrier
     * @param string $trackingNumber
     * @return array|null
     */
    public function retrieveTracking($carrier, $trackingNumber);

    /**
     * Validate address
     * Original: Validated address via Shippo API
     * 
     * @param array $payload
     * @return array
     */
    public function validateAddress($payload);
}
