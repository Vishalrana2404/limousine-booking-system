<?php

namespace App\Repositories\Interfaces;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Interface BookingInterface
 *
 * Represents an interface for managing booking.
 */
interface BookingInterface
{
    /**
     * Add a new booking.
     *
     * Adds a new booking to the database.
     *
     * @param array $data The data of the booking to be added.
     * @return \App\Models\Booking The newly created booking instance.
     */
    public function addBooking(array $data): Booking;

    /**
     * Delete bookings by their IDs.
     *
     * Deletes bookings from the database based on their IDs.
     *
     * @param array $bookingIds The IDs of the bookings to be deleted.
     * @return bool True if the deletion was successful, false otherwise.
     */
    public function deleteBooking(array $bookingIds): bool;

    /**
     * Update a booking.
     *
     * Updates the data of an existing booking in the database.
     *
     * @param \App\Models\Booking $user The booking instance to be updated.
     * @param array               $data The updated data for the booking.
     * @return bool True if the update was successful, false otherwise.
     */
    public function updateBooking(Booking $booking, array $data): bool;

    /**
     * Retrieve a booking by its ID.
     *
     * Retrieves a booking from the database based on its ID.
     *
     * @param int $bookingId The ID of the booking to be retrieved.
     * @return \App\Models\Booking|null The retrieved booking instance, or null if not found.
     */
    public function getBookingById(int $bookingId): ?Booking;

    
    public function getBookingByIdToRestore(int $bookingId): ?Booking;

    /**
     * Retrieve paginated and sorted booking data based on provided parameters.
     *
     * Retrieves paginated and sorted booking data based on the logged-in user,
     * search query, pagination parameters, and sorting criteria.
     *
     * @param \App\Models\User $loggedUser    The logged-in user instance.
     * @param string           $search        The search query to filter bookings (default: '').
     * @param int              $page          The page number for pagination (default: 1).
     * @param string           $sortField     The field to sort bookings by (default: 'id').
     * @param string           $sortDirection The direction for sorting ('asc' or 'desc') (default: 'desc').
     * @return \Illuminate\Pagination\LengthAwarePaginator A paginated list of sorted bookings.
     */
    public function getBookings(User $loggedUser,  $startDate, $endDate, string $search = '', string $searchByBookingId = '', int $page = 1, string $sortField = 'id', string $sortDirection = 'asc', $driverId = null, $noPagination = false, $isDriverSchedule = false);
    /**
     * Retrieve the last pending bookings that are older than the specified threshold.
     *
     * @param int $threshold The threshold in minutes to consider bookings as pending.
     * @return \Illuminate\Database\Eloquent\Collection The collection of last pending bookings.
     */
    public function getLastPendingBookings($threshold): Collection;

    /**
     * Retrieve booking data.
     *
     * @return mixed The booking data.
     */
    public function getBookingData();
    public function getNextDayBookings($startDate, $startTime, $endDate, $endTime): Collection;

     /**
     * Retrieves a collection of bookings based on the given array of booking IDs and an optional status.
     *
     * This function queries the database to fetch all bookings whose IDs are in the provided array.
     * Optionally, it filters the bookings by their status if a status is provided.
     *
     * @param array $bookingIds An array of booking IDs to retrieve.
     * @param string|null $status An optional status to filter the bookings by. Defaults to null.
     * 
     * @return \Illuminate\Support\Collection A collection of bookings that match the given IDs and status.
     *
     */
    public function getBookingByIds(array $bookingIds, string $status = null): Collection;
}
