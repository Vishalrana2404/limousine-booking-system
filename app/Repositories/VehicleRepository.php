<?php

namespace App\Repositories;

use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\Interfaces\VehicleInterface;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Class VehicleRepository
 * 
 * @package App\Repositories
 */
class VehicleRepository implements VehicleInterface
{
    /**
     * VehicleRepository constructor.
     *
     * @param Vehicle $model The Vehicle model instance.
     */
    public function __construct(
        protected Vehicle $model
    ) {
    }
    /**
     * Get data for vehicles.
     *
     */
    public function getVehicle(string $search = '', int $page = 1, string $sortField = 'id', string $sortDirection = 'asc'): LengthAwarePaginator
    {
        // Filter vehicle based on the provided parameters
        $vehicleQuery = $this->filterVehicleResult($search);

        // Sort the vehicle based on the specified field and direction
        $sortedCollection = $this->sortVehicle($vehicleQuery->get(), $sortField, $sortDirection);

        // Set the page size for pagination
        $pageSize = config('constants.paginationSize');

        // Paginate the sorted collection
        return $this->paginateResults($sortedCollection, $pageSize, $page);
    }

    /**
     * Filter vehicles query result based on specified parameters.
     *
     * Builds and returns a query builder instance for filtering vehicles and search criteria.
     *
     * @param string $search The search criteria for filtering vehicles (optional).
     * @return \Illuminate\Database\Eloquent\Builder The query builder instance for filtering vehicles.
     */
    private function filterVehicleResult(string $search = '')
    {
        $query = $this->model->with(['vehicleClass']);
        // Apply search query filters
        if (!empty($search)) {
            $search = strtolower($search);
            $query->where(function ($query) use ($search) {
                // Search for keywords in name field
                $query->whereRaw('LOWER(`vehicle_number`) like ?', ['%' . $search . '%'])
                    ->orWhereRaw('LOWER(`brand`) like ?', ['%' . $search . '%'])
                    ->orWhereRaw('LOWER(`model`) like ?', ['%' . $search . '%'])
                    ->orWhereRaw('LOWER(`status`) like ?', ['%' . $search . '%']);
            })->orWhereHas('vehicleClass', function ($query) use ($search) {
                $query->whereRaw('LOWER(`name`) like ?', ['%' . $search . '%']);
            });
        }

        return $query;
    }

    /**
     * Sort vehicles collection based on specified field and direction.
     *
     * Sorts the provided collection of vehicles based on the specified field and direction.
     *
     * @param Collection $vehicleQuery The collection of vehicles to be sorted.
     * @param string $sortField The field to sort vehicles by (optional, default is 'id').
     * @param string $sortDirection The direction for sorting vehicles ('asc' or 'desc', optional, default is 'asc').
     * @return Collection The sorted collection of vehicles.
     */
    private function sortVehicle(Collection $vehicleQuery, string $sortField = 'id', string $sortDirection = 'asc')
    {
        $sortFunction = $sortDirection == 'asc' ? 'sortBy' : 'sortByDesc';
        return $vehicleQuery->$sortFunction(function ($innerQuery) use ($sortField) {
            switch ($sortField) {
                case 'sortClass':
                    $value = strtolower($innerQuery->vehicleClass->name ?? '');
                    break;
                case 'sortNumber':
                    $value = strtolower($innerQuery->vehicle_number ?? '');
                    break;
                case 'sortBrand':
                    $value = strtolower($innerQuery->brand ?? '');
                    break;
                case 'sortModel':
                    $value = strtolower($innerQuery->model ?? '');
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
     * Get data for a specific vehicle by ID.
     *
     * @param int $vehicleId The ID of the vehicle.
     * @return Vehicle|null The vehicle instance or null if not found.
     */
    public function getVehicleById(int $vehicleId): ?Vehicle
    {
        return $this->model->find($vehicleId);
    }

    /**
     * Add a new vehicle.
     *
     * @param array $data The data for creating the vehicle.
     * @return Vehicle The newly created vehicle.
     */
    public function addVehicle(array $data): Vehicle
    {
        return $this->model->create($data);
    }

    /**
     * Update an existing vehicle.
     *
     * @param int $vehicleId The ID of the vehicle to update.
     * @param array $data The data for updating the vehicle.
     * @return Vehicle The updated vehicle.
     */
    public function updateVehicle(Vehicle $vehicle, array $data): bool
    {
        return $vehicle->update($data);
    }

    /**
     * Delete multiple vehicles.
     *
     * Deletes vehicles with the provided IDs.
     *
     * @param array $vehicleIds The IDs of the vehicles to delete.
     * @return bool True if the vehicles are successfully deleted, false otherwise.
     */
    public function deleteVehicle(array $vehicleIds): bool
    {
        return $this->model->whereIn('id', $vehicleIds)->delete();
    }

    /**
     * Retrieve a vehicle object by its ID.
     *
     * @param int $vehicleId The ID of the vehicle to retrieve.
     */
    public function viewVehicle(int $vehicleId): object
    {
        // Use Eloquent to fetch the vehicle by ID from the database
        return $this->model->where('id', $vehicleId)->first();
    }

    /**
     * update bulk vehicle status.
     *
     * @param array $vehicleIds The ID of the vehicle to update status.
     * @param string $status The status of the vehicle to update status.
     * @param int $loggedUserId The id of logged user.
     * @return bool True if the vehicle is updated successfully, false otherwise.
     */
    public function updateBulkStatus(array $vehicleIds, string $status, int $loggedUserId): bool
    {
        return $this->model->whereIn('id', $vehicleIds)->update(['status' => $status, 'updated_by_id' => $loggedUserId]);
    }

    /**
     * Get the count of vehicles by multiple vehicle class IDs.
     *
     * This method queries the database to count the number of vehicles that belong to
     * the specified vehicle class IDs.
     *
     * @param array $vehicleIds An array of vehicle class IDs.
     * @return int|null The count of vehicles or null if no matching records are found.
     */
    public function getVehicleCountByMultipleVehicleClassId(array $vehicleIds): ?int
    {
        return $this->model->whereIn('vehicle_class_id', $vehicleIds)->count();
    }

    public function getVehicles(): ?Collection
    {
        return $this->model->where('status', 'ACTIVE')->get();
    }

    /**
     * Check if a vehicle number is unique in the database.
     *
     * Checks whether the provided vehicle number is unique in the database.
     * Optionally, it excludes the vehicle with the given vehicleId from the check.
     *
     * @param string $vehicleNumber The vehicle number to check for uniqueness.
     * @param int|null $vehicleId The ID of the vehicle to exclude from the check (optional).
     * @return Vehicle|null The vehicle instance with the specified vehicle number if found, null otherwise.
     */
    public function checkUniqueVehicleNumber(string $vehicleNumber, int $vehicleId = null): ?Vehicle
    {
        // Check if a vehicleId is provided
        if ($vehicleId) {
            // If vehicleId is provided, check for unique vehicle number excluding the current vehicle's number
            return $this->model->where('vehicle_number', $vehicleNumber)->where('id', '!=', $vehicleId)->first();
        } else {
            // If vehicleId is not provided, simply check for unique vehicle number
            return $this->model->where('vehicle_number', $vehicleNumber)->first();
        }
    }
    /**
     * Retrieves a collection of vehicle based on the given array of vehicle IDs and an optional status.
     *
     * This function queries the database to fetch all vehicle whose IDs are in the provided array.
     * Optionally, it filters the vehicle by their status if a status is provided.
     *
     * @param array $vehicleIds An array of vehicle  IDs to retrieve.
     * @param string|null $status An optional status to filter the vehicle by. Defaults to null.
     * 
     * @return \Illuminate\Support\Collection A collection of vehicle that match the given IDs and status.
     *
     */
    public function getVehicleByIds(array $vehicleIds, string $status = null): Collection
    {
        // Start the query with the base condition
        $query = $this->model->whereIn('id', $vehicleIds);
        // Add the status condition if it's provided
        if (!empty($status)) {
            $query->where('status', $status);
        }
        // Execute the query and return the result
        return $query->get();
    }
}
