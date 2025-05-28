<?php

namespace App\Repositories;

use App\Models\VehicleClass;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\Interfaces\VehicleClassInterface;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Class VehicleClassRepository
 * 
 * @package App\Repositories
 */
class VehicleClassRepository implements VehicleClassInterface
{
    /**
     * VehicleClassRepository constructor.
     *
     * @param VehicleClass $model The VehicleClass model instance.
     */
    public function __construct(
        protected VehicleClass $model
    ) {
    }
    /**
     * Get data for vehicles class.
     *
     */
    public function getVehicleClass(string $search = '', int $page = 1, string $sortField = 'id', string $sortDirection = 'asc'): LengthAwarePaginator
    {
        // Filter vehicle class based on the provided parameters
        $vehicleClassQuery = $this->filterVehicleClassResult($search);

        // Sort the vehicle class based on the specified field and direction
        $sortedCollection = $this->sortVehicleClass($vehicleClassQuery->get(), $sortField, $sortDirection);

        // Set the page size for pagination
        $pageSize = config('constants.paginationSize');

        // Paginate the sorted collection
        return $this->paginateResults($sortedCollection, $pageSize, $page);
    }

    /**
     * Filter vehicle class query result based on specified parameters.
     *
     * Builds and returns a query builder instance for filtering vehicle class and search criteria.
     *
     * @param string $search The search criteria for filtering vehicle class (optional).
     * @return \Illuminate\Database\Eloquent\Builder The query builder instance for filtering vehicle class.
     */
    private function filterVehicleClassResult(string $search = '')
    {
        $query = $this->model->query();
        // Apply search query filters
        if (!empty($search)) {
            $search = strtolower($search);
            $query->where(function ($query) use ($search) {
                // Search for keywords in name field
                $query->whereRaw('LOWER(`name`) like ?', ['%' . $search . '%'])
                    // Search for keywords in seating_capacity field
                    ->orWhereRaw('LOWER(`seating_capacity`) like ?', ['%' . $search . '%'])
                    // Search for keywords in total_luggage field
                    ->orWhereRaw('LOWER(`total_luggage`) like ?', ['%' . $search . '%'])
                    // Search for keywords in total_pax field
                    ->orWhereRaw('LOWER(`total_pax`) like ?', ['%' . $search . '%'])
                    // Search for keywords in status field
                    ->orWhereRaw('LOWER(`status`) like ?', ['%' . $search . '%']);
            });
        }
        return $query;
    }

    /**
     * Sort vehicle class collection based on specified field and direction.
     *
     * Sorts the provided collection of vehicle class based on the specified field and direction.
     *
     * @param Collection $vehicleClassQuery The collection of vehicle class to be sorted.
     * @param string $sortField The field to sort vehicle class by (optional, default is 'id').
     * @param string $sortDirection The direction for sorting vehicle class ('asc' or 'desc', optional, default is 'asc').
     * @return Collection The sorted collection of vehicle class.
     */
    private function sortVehicleClass(Collection $vehicleClassQuery, string $sortField = 'id', string $sortDirection = 'asc')
    {
        $sortFunction = $sortDirection == 'asc' ? 'sortBy' : 'sortByDesc';
        return $vehicleClassQuery->$sortFunction(function ($innerQuery) use ($sortField) {
            switch ($sortField) {
                case 'sortClass':
                    $value = strtolower($innerQuery->name ?? '');
                    break;
                case 'sortSeating':
                    $value = strtolower($innerQuery->seating_capacity ?? '');
                    break;
                case 'sortPax':
                    $value = strtolower($innerQuery->total_pax ?? '');
                    break;
                case 'sortLuggages':
                    $value = strtolower($innerQuery->total_luggage ?? '');
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
     * Get data for vehicle class.
     *
     * @return Collection The collection of vehicle class.
     */
    public function getVehicleClasses(): Collection
    {
        return $this->model->where('status', 'Active')->get();
    }

    /**
     * Get data for a specific vehicle class by ID.
     *
     * @param int $vehicleClassId The ID of the vehicle class.
     * @return VehicleClass|null The vehicle class instance or null if not found.
     */
    public function getVehicleClassById(int $vehicleClassId): ?VehicleClass
    {
        return $this->model->find($vehicleClassId);
    }


    /**
     * Add a new vehicle class.
     *
     * @param array $data The data for creating the vehicle class.
     * @return VehicleClass The newly created vehicle class.
     */
    public function addVehicleClass(array $data): VehicleClass
    {
        return $this->model->create($data);
    }

    /**
     * Update an existing vehicle class.
     *
     * @param int $vehicleClassId The ID of the vehicle class to update.
     * @param array $data The data for updating the vehicle class.
     * @return VehicleClass The updated vehicle class.
     */
    public function updateVehicleClass(VehicleClass $vehicleClass, array $data): bool
    {
        return $vehicleClass->update($data);
    }

    /**
     * Delete bulk vehicle class.
     *
     * @param array $vehicleClassIds The ID of the vehicle class to delete.
     * @return bool True if the vehicle class is deleted successfully, false otherwise.
     */
    public function deleteVehicleClass(array $vehicleClassIds): bool
    {
        return $this->model->whereIn('id', $vehicleClassIds)->delete();
    }

    /**
     * Retrieve a vehicle class object by its ID.
     *
     * @param int $vehicleClassId The ID of the vehicle class to retrieve.
     */
    public function viewVehicleClass(int $vehicleClassId): object
    {
        // Use Eloquent to fetch the vehicle class by ID from the database
        return $this->model->where('id', $vehicleClassId)->first();
    }

    /**
     * update bulk vehicle class status.
     *
     * @param array $vehicleClassIds The ID of the vehicle class to update status.
     * @param string $status The status of the vehicle class to update status.
     * @param int $loggedUserId The id of logged user.
     * @return bool True if the vehicle class is updated successfully, false otherwise.
     */
    public function updateBulkStatus(array $vehicleClassIds, string $status, int $loggedUserId): bool
    {

        return $this->model->whereIn('id', $vehicleClassIds)->update(['status' => $status, 'updated_by_id' => $loggedUserId]);
    }
    /**
     * Retrieves a collection of vehicle classes based on the given array of vehicle class IDs and an optional status.
     *
     * This function queries the database to fetch all vehicle classes whose IDs are in the provided array.
     * Optionally, it filters the vehicle classes by their status if a status is provided.
     *
     * @param array $vehicleClassIds An array of vehicle class IDs to retrieve.
     * @param string|null $status An optional status to filter the vehicle classes by. Defaults to null.
     * 
     * @return \Illuminate\Support\Collection A collection of vehicle classes that match the given IDs and status.
     *
     */
    public function getVehicleClassByIds(array $vehicleClassIds, string $status = null): Collection
    {
        // Start the query with the base condition
        $query = $this->model->whereIn('id', $vehicleClassIds);
        // Add the status condition if it's provided
        if (!empty($status)) {
            $query->where('status', $status);
        }
        // Execute the query and return the result
        return $query->get();
    }
}
