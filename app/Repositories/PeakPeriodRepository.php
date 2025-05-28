<?php

namespace App\Repositories;

use App\Models\PeakPeriod;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\Interfaces\PeakPeriodInterface;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Class PeakPeriodRepository
 * 
 * @package App\Repositories
 */
class PeakPeriodRepository implements PeakPeriodInterface
{
    /**
     * PeakPeriodRepository constructor.
     *
     * @param PeakPeriod $model The PeakPeriod model instance.
     */
    public function __construct(
        protected PeakPeriod $model
    ) {
    }

    /**
     * Retrieve and paginate peak periods based on search, sorting, and pagination parameters.
     *
     * @param string $search A search query to filter peak periods (default is an empty string).
     * @param int $page The page number for pagination (default is 1).
     * @param string $sortField The field to sort the peak periods by (default is 'id').
     * @param string $sortDirection The direction to sort the peak periods ('asc' for ascending, 'desc' for descending; default is 'asc').
     * @return LengthAwarePaginator A paginator instance containing the filtered, sorted, and paginated peak periods.
     */
    public function getPeakPeriods(string $search = '', int $page = 1, string $sortField = 'id', string $sortDirection = 'asc'): LengthAwarePaginator
    {
        // Filter peak period based on the provided parameters
        $peakPeriodQuery = $this->filterPeakPeriodResult($search);

        // Sort the peak period based on the specified field and direction
        $sortedCollection = $this->sortPeakPeriod($peakPeriodQuery->get(), $sortField, $sortDirection);

        // Set the page size for pagination
        $pageSize = config('constants.paginationSize');

        // Paginate the sorted collection
        return $this->paginateResults($sortedCollection, $pageSize, $page);
    }

    /**
     * Filter peak period query result based on specified parameters.
     *
     * Builds and returns a query builder instance for filtering peak period and search criteria.
     *
     * @param string $search The search criteria for filtering peak period (optional).
     * @return \Illuminate\Database\Eloquent\Builder The query builder instance for filtering peak period.
     */
    private function filterPeakPeriodResult(string $search = '')
    {
        $query = $this->model->query();
        // Apply search query filters
        if (!empty($search)) {
            $search = strtolower($search);
            $query->where(function ($query) use ($search) {
                // Search for keywords in event field
                $query->whereRaw('LOWER(`event`) like ?', ['%' . $search . '%'])
                    // Search for keywords in start_date field
                    ->orWhereRaw('start_date like ?', ['%' . $search . '%'])
                    // Search for keywords in end_date field
                    ->orWhereRaw('end_date like ?', ['%' . $search . '%'])
                    // Search for keywords in status field
                    ->orWhereRaw('LOWER(`status`) like ?', ['%' . $search . '%']);
            });
        }
        return $query;
    }

    /**
     * Sort peak period collection based on specified field and direction.
     *
     * Sorts the provided collection of peak period based on the specified field and direction.
     *
     * @param Collection $PeakPeriodQuery The collection of peak period to be sorted.
     * @param string $sortField The field to sort peak period by (optional, default is 'id').
     * @param string $sortDirection The direction for sorting peak period ('asc' or 'desc', optional, default is 'asc').
     * @return Collection The sorted collection of peak period.
     */
    private function sortPeakPeriod(Collection $peakPeriodQuery, string $sortField = 'id', string $sortDirection = 'asc')
    {
        $sortFunction = $sortDirection == 'asc' ? 'sortBy' : 'sortByDesc';
        return $peakPeriodQuery->$sortFunction(function ($innerQuery) use ($sortField) {
            switch ($sortField) {
                case 'sortEvent':
                    $value = strtolower($innerQuery->event ?? 'zzzz');
                    break;
                case 'sortStartDate':
                    $value = $innerQuery->start_date ?? 'zzzz';
                    break;
                case 'sortEndDate':
                    $value = $innerQuery->end_date ?? 'zzzz';
                    break;
                case 'sortStatus':
                    $value = strtolower($innerQuery->status ?? '');
                    break;
                default:
                    $value = $innerQuery->id;
                    break;
            }
            return $value;
        });
    }

    /**
     * Paginate a collection of results.
     *
     * Paginates the provided collection based on the specified page size and page number.
     *
     * @param mixed $collection The collection of results to be paginated.
     * @param int $pageSize The number of items per page.
     * @param int $page The current page number (optional, default is 1).
     * @return LengthAwarePaginator The paginated collection of results.
     */
    private function paginateResults($collection, $pageSize, $page = 1): LengthAwarePaginator
    {
        return new LengthAwarePaginator(
            $collection->values()->forPage($page, $pageSize), // Paginate the collection for the specified page and page size
            $collection->count(), // Total count of items in the collection
            $pageSize, // Number of items per page
            $page, // Current page number
            ['path' => LengthAwarePaginator::resolveCurrentPath()] // Path for generating pagination links
        );
    }

    /**
     * Get data for peak period.
     *
     * @return Collection The collection of peak period.
     */
    public function getAllPeakPeriodes(): Collection
    {
        return $this->model->where('status', 'ACTIVE')->get();
    }

    /**
     * Get data for a specific peak period by ID.
     *
     * @param int $PeakPeriodId The ID of the peak period.
     * @return PeakPeriod|null The peak period instance or null if not found.
     */
    public function getPeakPeriodById(int $PeakPeriodId): ?PeakPeriod
    {
        return $this->model->find($PeakPeriodId);
    }


    /**
     * Add a new peak period.
     *
     * @param array $data The data for creating the peak period.
     * @return PeakPeriod The newly created peak period.
     */
    public function addPeakPeriod(array $data): PeakPeriod
    {
        return $this->model->create($data);
    }

    /**
     * Update an existing peak period.
     *
     * @param int $PeakPeriodId The ID of the peak period to update.
     * @param array $data The data for updating the peak period.
     * @return PeakPeriod The updated peak period.
     */
    public function updatePeakPeriod(PeakPeriod $peakPeriod, array $data): bool
    {
        return $peakPeriod->update($data);
    }

    /**
     * Delete bulk peak period.
     *
     * @param array $peakPeriodIds The ID of the peak period to delete.
     * @return bool True if the peak period is deleted successfully, false otherwise.
     */
    public function deletePeakPeriod(array $peakPeriodIds): bool
    {
        return $this->model->whereIn('id', $peakPeriodIds)->delete();
    }

    /**
     * update bulk peak period status.
     *
     * @param array $peakPeriodIds The ID of the peak period to update status.
     * @param string $status The status of the peak period to update status.
     * @param int $loggedUserId The id of logged user.
     * @return bool True if the peak period is updated successfully, false otherwise.
     */
    public function updateBulkStatus(array $peakPeriodIds, string $status, int $loggedUserId): bool
    {

        return $this->model->whereIn('id', $peakPeriodIds)->update(['status' => $status, 'updated_by_id' => $loggedUserId]);
    }
    /**
     * Retrieves a collection of peak periods based on the given array of peak period IDs and an optional status.
     *
     * This function queries the database to fetch all peak periods whose IDs are in the provided array.
     * Optionally, it filters the peak periods by their status if a status is provided.
     *
     * @param array $peakPeriodIds An array of peak period IDs to retrieve.
     * @param string|null $status An optional status to filter the peak periods by. Defaults to null.
     * 
     * @return \Illuminate\Support\Collection A collection of peak periods that match the given IDs and status.
     *
     */
    public function getPeakPeriodByIds(array $peakPeriodIds, string $status = null): Collection
    {
        // Start the query with the base condition
        $query = $this->model->whereIn('id', $peakPeriodIds);
        // Add the status condition if it's provided
        if (!empty($status)) {
            $query->where('status', $status);
        }
        // Execute the query and return the result
        return $query->get();
    }
}
