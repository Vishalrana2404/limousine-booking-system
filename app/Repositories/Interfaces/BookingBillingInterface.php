<?php

namespace App\Repositories\Interfaces;

use App\Models\BookingBilling;

/**
 * Interface BookingBillingInterface
 *
 * Represents an interface for managing clients.
 */
interface BookingBillingInterface
{
   
    public function addBookingBilling(array $data): BookingBilling;
    public function createOrUpdateBookingBillingByBookingId(int $bookingId, array $data,int $loggedUserId):BookingBilling;
}