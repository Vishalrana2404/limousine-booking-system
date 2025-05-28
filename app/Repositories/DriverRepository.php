<?php

namespace App\Repositories;

use App\Models\Driver;
use App\Models\User;
use App\Repositories\Interfaces\DriverInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Class DriverRepository
 * 
 * @package App\Repositories
 */
class DriverRepository implements DriverInterface
{
    /**
     * DriverRepository constructor.
     *
     * @param Driver $model The User model instance.
     */
    public function __construct(
        protected Driver $model
    ) {
    }

    /**
     * Add a new driver to the database.
     *
     * @param array $data The data to create the driver.
     * @return Driver The newly created driver object.
     */
    public function addDriver(array $data): Driver
    {
        return $this->model->create($data);
    }

    /**
     * Update an existing driver in the database.
     *
     * @param Driver $driver The driver instance to update.
     * @param array $data The data to update the driver with.
     * @return bool True if the update was successful, false otherwise.
     */
    public function updateDriver(Driver $driver, array $data): bool
    {
        return $driver->update($data);
    }

    /**
     * Delete multiple drivers from the database.
     *
     * @param array $driverIds The IDs of the drivers to delete.
     * @return bool True if the deletion was successful, false otherwise.
     */
    public function deleteDrivers(array $driverIds): bool
    {
        return $this->model->whereIn('id', $driverIds)->delete();
    }

    /**
     * Get data for drivers.
     *
     */
    public function getDriverData(User $loggedUser,  string $search = '', int $page = 1, string $sortField = 'id', string $sortDirection = 'asc'): LengthAwarePaginator
    {
        // Filter drivers based on the provided parameters
        $drivers = $this->filterDriverResult($loggedUser,  $search)->get();

        // Sort the drivers based on the specified field and direction
        $sortedCollection = $this->sortDrivers($drivers, $sortField, $sortDirection);

        // Set the page size for pagination
        $pageSize = config('constants.paginationSize');

        // Paginate the sorted collection
        return $this->paginateResults($sortedCollection, $pageSize, $page);
    }

    /**
     * Get data for inhouse drivers.
     *
     * @return Collection The collection of inhouse drivers.
     */
    public function getInhouseData(): Collection
    {
        return $this->model->with(['driverOffDay'])->where('driver_type', 'INHOUSE')->get();
    }

    /**
     * Get data for active drivers.
     *
     * @return Collection The collection of active drivers.
     */
    public function getActiveDriverData(): Collection
    {
        return $this->model->where('status', 'ACTIVE')->get();
    }

    /**
     * Filter drivers query result based on specified parameters.
     *
     * Builds and returns a query builder instance for filtering drivers based on the provided logged-in user and search criteria.
     *
     * @param User $loggedUser The logged-in user instance.
     * @param string $search The search criteria for filtering drivers (optional).
     * @return \Illuminate\Database\Eloquent\Builder The query builder instance for filtering drivers.
     */
    private function filterDriverResult(User $loggedUser, string $search = '')
    {
        $loggedUserId = $loggedUser->id;

        // Start building the query with eager loading relationships
        $query = $this->model->with(['vehicle.vehicleClass']);

        // Apply search query filters
        if (!empty($search)) {
            $search = strtolower($search);
            $query->where(function ($query) use ($search) {
                $query->whereRaw('LOWER(`name`) like ?', ['%' . $search . '%'])
                    ->orWhereRaw('LOWER(`phone`) like ?', ['%' . $search . '%'])
                    ->orWhereRaw('LOWER(`chat_id`) like ?', ['%' . $search . '%'])
                    ->orWhereRaw('LOWER(`driver_type`) like ?', ['%' . $search . '%']);
            })->orWhere(function ($query) use ($search) {
                $query->whereHas('vehicle', function ($query) use ($search) {
                    $query->whereRaw('LOWER(`vehicle_number`) like ?', ['%' . $search . '%'])
                        ->orWhereRaw('LOWER(`brand`) like ?', ['%' . $search . '%']);
                });
            })->orWhereHas('vehicle.vehicleClass', function ($query) use ($search) {
                $query->whereRaw('LOWER(`name`) like ?', ['%' . $search . '%']);
            });
        }


        return $query;
    }

    /**
     * Sort drivers collection based on specified field and direction.
     *
     * Sorts the provided collection of drivers based on the specified field and direction.
     *
     * @param Collection $drivers The collection of drivers to be sorted.
     * @param string $sortField The field to sort drivers by (optional, default is 'id').
     * @param string $sortDirection The direction for sorting drivers ('asc' or 'desc', optional, default is 'asc').
     * @return Collection The sorted collection of drivers.
     */
    private function sortDrivers(Collection $drivers, string $sortField = 'id', string $sortDirection = 'asc')
    {
        // Determine the sorting function based on the sort direction
        $sortFunction = $sortDirection == 'asc' ? 'sortBy' : 'sortByDesc';
        // Sort the dirvers collection based on the specified field and direction
        return $drivers->$sortFunction(function ($innerQuery) use ($sortField) {
            switch ($sortField) {
                case 'sortName':
                    $value = strtolower($innerQuery->name ?? 'zzzz');
                    break;
                case 'sortPhone':
                    $value = strtolower($innerQuery->phone ?? 'zzzz');
                    break;
                case 'sortVehicle':
                    $value = strtolower($innerQuery->vehicle->vehicle_number ?? 'zzzz');
                    break;
                case 'sortClass':
                    $value = strtolower($innerQuery->vehicle->vehicleClass->name ?? 'zzzz');
                    break;
                case 'sortRace':
                    $value = strtolower($innerQuery->race ?? 'zzzz');
                    break;
                case 'sortDriverType':
                    $value = strtolower($innerQuery->driver_type ?? 'zzzz');
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
     * Get the count of drivers by vehicle IDs.
     *
     * This method retrieves the count of drivers associated with the specified vehicle IDs.
     *
     * @param array $vehicleIds An array of vehicle IDs.
     * @return int|null The count of drivers or null if no matching records are found.
     */
    public function getDriverCountByVehicleIds(array $vehicleIds): ?int
    {
        return $this->model->whereIn('vehicle_id', $vehicleIds)->count();
    }

    /**
     * Retrieves a collection of drivers based on the given array of driver IDs.
     *
     * This function queries the database to fetch all drivers whose IDs are 
     * in the provided array and returns the result as a collection.
     *
     * @param array $driverIds An array of driver IDs to retrieve.
     * 
     * @return \Illuminate\Support\Collection A collection of drivers that match the given IDs.
     *
     */
    public function getDriverByIds(array $driverIds): Collection
    {
        return $this->model->whereIn('id', $driverIds)->get();
    }

    public function getDrivers(): Collection
    {
        return $this->model->get();
    }
    public function getDriverById(int $driverId): Driver
    {
        return $this->model->where('id', $driverId)->first();
    }
}
