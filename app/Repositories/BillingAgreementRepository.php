<?php

namespace App\Repositories;

use App\Models\BillingAgreement;
use App\Repositories\Interfaces\BillingAgreementInterface;

/**
 * Class BillingAgreementRepository
 * 
 * This class implements the BillingAgreementInterface and provides methods to interact with billing agreements.
 */
class BillingAgreementRepository implements BillingAgreementInterface
{
    /**
     * Create a new instance of the BillingAgreementRepository.
     *
     * @param BillingAgreement $model The model instance for billing agreements.
     */
    public function __construct(
        protected BillingAgreement $model
    ) {
    }

    /**
     * Add a new billing agreement.
     *
     * @param array $data The data for creating the billing agreement.
     * @return BillingAgreement The newly created billing agreement.
     */
    public function addBillingAgreement(array $data): BillingAgreement
    {
        return $this->model->create($data);
    }

    /**
     * Update an existing billing agreement.
     *
     * @param BillingAgreement $billingAgreement The billing agreement to update.
     * @param array $data The data for updating the billing agreement.
     * @return bool True if the billing agreement is successfully updated, false otherwise.
     */
    public function updateBillingAgreement(BillingAgreement $billingAgreement, array $data): bool
    {
        return $billingAgreement->update($data);
    }

    /**
     * Get a billing agreement by hotel ID.
     *
     * @param int $hotelId The ID of the hotel associated with the billing agreement.
     * @return BillingAgreement|null The billing agreement associated with the hotel ID, or null if not found.
     */
    public function getBillingAgreementByHotelId(int $hotelId): ?BillingAgreement
    {
        return $this->model->where('hotel_id', $hotelId)->first();
    }
}
