<?php

namespace App\Repositories;

use App\Models\CorporateFairBilling;
use App\Repositories\Interfaces\CorporateFairBillingInterface;

/**
 * Class CorporateFairBillingRepository
 * 
 * This class implements the CorporateFairBillingInterface and provides
 * methods to interact with the CorporateFairBilling model.
 * 
 * @package App\Repositories
 */
class CorporateFairBillingRepository implements CorporateFairBillingInterface
{
    /**
     * Create a new class instance.
     * 
     * @param CorporateFairBilling $model The CorporateFairBilling model instance.
     */
    public function __construct(
        protected CorporateFairBilling $model
    ) {
    }

    /**
     * Add a new corporate fair billing record.
     *
     * @param array $data The data to create a new corporate fair billing entry.
     * @return CorporateFairBilling The newly created corporate fair billing model instance.
     */
    public function addCorporateFairBilling(array $data): CorporateFairBilling
    {
        return $this->model->create($data);
    }

    /**
     * Update an existing corporate fair billing record.
     *
     * @param CorporateFairBilling $corporateFairBilling The corporate fair billing instance to update.
     * @param array $data The data to update the corporate fair billing entry.
     * @return bool True on success, false on failure.
     */
    public function updateCorporateFairBilling(CorporateFairBilling $corporateFairBilling, array $data): bool
    {
        return $corporateFairBilling->update($data);
    }

    /**
     * Retrieve a corporate fair billing record by hotel ID.
     *
     * @param int $hotelId The ID of the hotel to find the corporate fair billing record for.
     * @return CorporateFairBilling|null The corporate fair billing record, or null if not found.
     */
    public function getCorporateFairBillingByHotelId(int $hotelId): ?CorporateFairBilling
    {
        return $this->model->where('hotel_id', $hotelId)->first();
    }
    public function getCorporateFairBillingByHotelIdVehicleClassTripType(int $hotelId, string $vehicleClass, string $tripType): ?CorporateFairBilling
    {
        return $this->model->where('hotel_id', $hotelId)->where('vehicle_class_id', $vehicleClass)->where('billing_type', $tripType)->first();
    }

    public function deleteCorporateFairBillingByHotelId(int $hotelId): bool
    {
        return $this->model->where('hotel_id', $hotelId)->delete();
    }
}
