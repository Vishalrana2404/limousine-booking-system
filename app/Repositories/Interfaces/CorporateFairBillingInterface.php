<?php

namespace App\Repositories\Interfaces;

use App\Models\CorporateFairBilling;

/**
 * Interface CorporateFairBillingInterface
 * 
 * This interface defines the contract for interacting with the Corporate Fair Billing model.
 * It provides methods for creating, updating, and retrieving corporate fair billing data.
 *
 * @package App\Repositories\Interfaces
 */
interface CorporateFairBillingInterface
{
    /**
     * Add a new corporate fair billing record.
     *
     * @param array $data The data to create a new corporate fair billing entry.
     * @return CorporateFairBilling The newly created corporate fair billing model instance.
     */
    public function addCorporateFairBilling(array $data): CorporateFairBilling;

    /**
     * Update an existing corporate fair billing record.
     *
     * @param CorporateFairBilling $corporateFairBilling The corporate fair billing instance to update.
     * @param array $data The data to update the corporate fair billing entry.
     * @return bool True on success, false on failure.
     */
    public function updateCorporateFairBilling(CorporateFairBilling $corporateFairBilling, array $data): bool;

    /**
     * Retrieve a corporate fair billing record by hotel ID.
     *
     * @param int $hotelId The ID of the hotel to find the corporate fair billing record for.
     * @return CorporateFairBilling|null The corporate fair billing record, or null if not found.
     */
    public function getCorporateFairBillingByHotelId(int $hotelId): ?CorporateFairBilling;

    public function getCorporateFairBillingByHotelIdVehicleClassTripType(int $hotelId, string $vehicleClass, string $tripType): ?CorporateFairBilling;

    public function deleteCorporateFairBillingByHotelId(int $hotelId): bool;
}