<?php

namespace App\Repositories\Interfaces;

use App\Models\BillingAgreement;

/**
 * Interface BillingAgreementInterface
 *
 * Represents an interface for managing billing agreements.
 */
interface BillingAgreementInterface
{
    /**
     * Add a new billing agreement.
     *
     * @param array $data The data for creating the billing agreement.
     * @return BillingAgreement The newly created billing agreement instance.
     */
    public function addBillingAgreement(array $data): BillingAgreement;

    /**
     * Update an existing billing agreement.
     *
     * @param BillingAgreement $billingAgreement The billing agreement to update.
     * @param array $data The data for updating the billing agreement.
     * @return bool True if the billing agreement is successfully updated, false otherwise.
     */
    public function updateBillingAgreement(BillingAgreement $billingAgreement, array $data): bool;

    /**
     * Get a billing agreement by hotel ID.
     *
     * @param int $hotelId The ID of the hotel associated with the billing agreement.
     * @return BillingAgreement|null The billing agreement associated with the specified hotel ID, or null if not found.
     */
    public function getBillingAgreementByHotelId(int $hotelId): ?BillingAgreement;
}
