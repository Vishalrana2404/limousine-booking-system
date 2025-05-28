<?php

namespace App\Repositories;

use App\Models\BookingBilling;
use App\Repositories\Interfaces\BookingBillingInterface;

/**
 * Class BookingBillingRepository
 * 
 * This class implements the BookingBillingInterface and provides methods to interact with BookingBilling.
 */
class BookingBillingRepository implements BookingBillingInterface
{
    /**
     * Create a new instance of the BookingBillingRepository.
     *
     * @param BookingBilling $model The model instance for BookingBilling.
     */
    public function __construct(
        protected BookingBilling $model
    ) {
    }

    /**
     * Add a new BookingBilling.
     *
     * @param array $data The data for creating the BookingBilling.
     * @return BookingBilling The newly created BookingBilling.
     */
    public function addBookingBilling(array $data): BookingBilling
    {
        return $this->model->create($data);
    }

    public function createOrUpdateBookingBillingByBookingId(int $bookingId, array $data, int $loggedUserId): BookingBilling
    {
        $bookingBilling = $this->model->where('booking_id', $bookingId)->first();
        if ($bookingBilling) {
            $data['updated_by_id'] = $loggedUserId;
            $bookingBilling->update($data);
            return $bookingBilling;
        } else {
            $data['booking_id'] = $bookingId;
            $data['created_by_id'] = $loggedUserId;
            return $this->model->create($data);
        }
    }
}
