<?php

namespace App\Repositories\Interfaces;

use App\Models\Hotel;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Interface HotelInterface
 *
 * Represents an interface for managing drivers.
 */
interface HotelInterface
{
    /**
     * Add a new hotel with the provided data.
     *
     * @param array $data An associative array containing data for creating the hotel.
     *                    Required keys: 'name', 'status'.
     *
     * @return \App\Models\Hotel Returns the created hotel model instance.
     */
    public function addHotel(array $data): Hotel;

    /**
     * Update an existing hotel with the provided data.
     *
     * @param \App\Models\Hotel $hotel The hotel model instance to be updated.
     * @param array $data An associative array containing data for updating the hotel.
     *                    Required keys: 'name', 'status'.
     *
     * @return bool Returns true if the hotel is successfully updated, false otherwise.
     */
    public function updateHotel(Hotel $hotel, array $data): bool;

    /**
     * Delete one or more hotels based on the provided hotel IDs.
     *
     * @param array $hotelIds An array of hotel IDs to be deleted.
     *
     * @return bool Returns true if the hotels are successfully deleted, false otherwise.
     */
    public function deleteHotel(array $hotelIds): bool;

    /**
     * Retrieve paginated hotel data based on provided criteria.
     *
     * @param \App\Models\User $loggedUser The logged-in user.
     * @param string $search The search query (default: '').
     * @param int $page The page number for pagination (default: 1).
     * @param string $sortField The field to sort by (default: 'id').
     * @param string $sortDirection The sort direction ('asc' or 'desc', default: 'asc').
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator Returns paginated hotel data.
     */
    public function gethotels(User $loggedUser, string $search = '', int $page = 1, string $sortField = 'id', string $sortDirection = 'asc'): LengthAwarePaginator;

    /**
     * Update the status of multiple hotels in bulk based on the provided hotel IDs and status.
     *
     * @param array $hotelIds An array of hotel IDs to be updated.
     * @param string $status The new status to be set for the hotels.
     * @param int $loggedUserId The ID of the user performing the operation.
     *
     * @return bool Returns true if the hotel statuses are successfully updated, false otherwise.
     */
    public function updateBulkStatus(array $hotelIds, string $status, int $loggedUserId): bool;

    /**
     * Retrieve hotel data.
     *
     * @return \Illuminate\Support\Collection Returns the retrieved hotel data.
     */
    public function getHotelData(): Collection;
    /**
     * Retrieves a collection of hotels based on the given array of hotel IDs and an optional status.
     *
     * This function queries the database to fetch all hotels whose IDs are in the provided array.
     * Optionally, it filters the hotels by their status if a status is provided.
     *
     * @param array $hotelIds An array of hotel IDs to retrieve.
     * @param string|null $status An optional status to filter the hotels by. Defaults to null.
     * 
     * @return \Illuminate\Support\Collection A collection of hotels that match the given IDs and status.
     *
     */
    public function getHotelByIds(array $hotelIds, string $status = null): Collection;


    public function getHotelClientAdmins(): ?Collection;
}
