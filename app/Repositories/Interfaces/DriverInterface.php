<?php

namespace App\Repositories\Interfaces;

use App\Models\Driver;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * Interface DriverInterface
 *
 * Represents an interface for managing drivers.
 */
interface DriverInterface
{
    /**
     * Add a new driver.
     *
     * @param array $data The data for creating the driver.
     * @return Driver The newly created driver.
     */
    public function addDriver(array $data): Driver;

    /**
     * Get data for drivers.
     *
     */
    public function getDriverData(User $loggedUser,  string $search = '', int $page = 1, string $sortField = 'id', string $sortDirection = 'asc'): LengthAwarePaginator;

    /**
     * Get data for inhouse drivers.
     *
     * @return Collection The collection of inhouse drivers.
     */
    public function getInhouseData(): Collection;

    /**
     * Get data for active drivers.
     *
     * @return Collection The collection of active drivers.
     */
    public function getActiveDriverData(): Collection;

    /**
     * Update an existing driver in the database.
     *
     * @param \App\Models\Driver $driver The driver instance to update.
     * @param array $data The data to update the driver with.
     * @return bool True if the update was successful, false otherwise.
     */
    public function updateDriver(Driver $driver, array $data): bool;

    /**
     * Delete multiple drivers from the database.
     *
     * @param array $userIds The IDs of the drivers to delete.
     * @return bool True if the drivers are successfully deleted, false otherwise.
     */
    public function deleteDrivers(array $userIds): bool;

    /**
     * Get the count of drivers by vehicle IDs.
     *
     * This method retrieves the count of drivers associated with the specified array of vehicle IDs.
     *
     * @param array $vehicleIds An array of vehicle IDs.
     * @return int|null The count of drivers or null if no matching records are found.
     */
    public function getDriverCountByVehicleIds(array $vehicleIds): ?int;
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
    public function getDriverByIds(array $driverIds): Collection;

    public function getDrivers(): Collection;
    
    public function getDriverById(int $driverId): Driver;
}
