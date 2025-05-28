<?php

namespace App\Repositories\Interfaces;

use App\Models\PeakPeriod;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Interface PeakPeriodInterface
 * 
 * @package App\Repositories\Interfaces
 */
interface PeakPeriodInterface
{
    /**
     * Retrieve and paginate peak periods based on search, sorting, and pagination parameters.
     *
     * @param string $search A search query to filter peak periods (default is an empty string).
     * @param int $page The page number for pagination (default is 1).
     * @param string $sortField The field to sort the peak periods by (default is 'id').
     * @param string $sortDirection The direction to sort the peak periods ('asc' for ascending, 'desc' for descending; default is 'asc').
     * @return LengthAwarePaginator A paginator instance containing the filtered, sorted, and paginated peak periods.
     */
    public function getPeakPeriods(string $search = '', int $page = 1, string $sortField = 'id', string $sortDirection = 'asc'): LengthAwarePaginator;

    /**
     * Get data for peak period.
     *
     * @return Collection The collection of peak period.
     */
    public function getAllPeakPeriodes(): Collection;

    /**
     * Get data for a specific peak period by ID.
     *
     * @param int $PeakPeriodId The ID of the peak period.
     * @return PeakPeriod|null The peak period instance or null if not found.
     */
    public function getPeakPeriodById(int $PeakPeriodId): ?PeakPeriod;

    /**
     * Add a new peak period.
     *
     * @param array $data The data for creating the peak period.
     * @return PeakPeriod The newly created peak period.
     */
    public function addPeakPeriod(array $data): PeakPeriod;

    /**
     * Update an existing peak period.
     *
     * @param int $PeakPeriodId The ID of the peak period to update.
     * @param array $data The data for updating the peak period.
     * @return PeakPeriod The updated peak period.
     */
    public function updatePeakPeriod(PeakPeriod $peakPeriod, array $data): bool;

    /**
     * Delete bulk peak period.
     *
     * @param array $PeakPeriodIds The ID of the peak period to delete.
     * @return bool True if the peak period is deleted successfully, false otherwise.
     */
    public function deletePeakPeriod(array $PeakPeriodIds): bool;

    /**
     * update bulk peak period status.
     *
     * @param array $PeakPeriodIds The ID of the peak period to update status.
     * @param string $status The status of the peak period to update status.
     * @param int $loggedUserId The id of logged user.
     * @return bool True if the peak period is updated successfully, false otherwise.
     */
    public function updateBulkStatus(array $PeakPeriodIds, string $status, int $loggedUserId): bool;

    /**
     * Retrieves a collection of peak period based on the given array of peak period IDs and an optional status.
     *
     * This function queries the database to fetch all peak periods whose IDs are in the provided array.
     * Optionally, it filters the peak periods by their status if a status is provided.
     *
     * @param array $PeakPeriodIds An array of peak period IDs to retrieve.
     * @param string|null $status An optional status to filter the peak periods by. Defaults to null.
     * 
     * @return \Illuminate\Support\Collection A collection of peak periods that match the given IDs and status.
     *
     */
    public function getPeakPeriodByIds(array $PeakPeriodIds, string $status = null): Collection;
}
