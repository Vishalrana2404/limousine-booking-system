<?php

namespace App\Repositories\Interfaces;

use App\Models\VehicleClass;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Interface VehicleClassInterface
 * 
 * @package App\Repositories\Interfaces
 */
interface VehicleClassInterface
{
    /**
     * Get data for vehicle class.
     *
     */
    public function getVehicleClass(string $search = '', int $page = 1, string $sortField = 'id', string $sortDirection = 'asc'): LengthAwarePaginator;

    /**
     * Get data for vehicle class.
     *
     * @return Collection The collection of vehicle class.
     */
    public function getVehicleClasses(): Collection;

    /**
     * Get data for a specific vehicle class by ID.
     *
     * @param int $vehicleClassId The ID of the vehicle class.
     * @return VehicleClass|null The vehicle class instance or null if not found.
     */
    public function getVehicleClassById(int $vehicleClassId): ?VehicleClass;

    /**
     * Add a new vehicle class.
     *
     * @param array $data The data for creating the vehicle class.
     * @return VehicleClass The newly created vehicle class.
     */
    public function addVehicleClass(array $data): VehicleClass;

    /**
     * Update an existing vehicle class.
     *
     * @param int $vehicleClassId The ID of the vehicle class to update.
     * @param array $data The data for updating the vehicle class.
     * @return VehicleClass The updated vehicle class.
     */
    public function updateVehicleClass(VehicleClass $vehicleClass, array $data): bool;

    /**
     * Delete bulk vehicle class.
     *
     * @param array $vehicleClassIds The ID of the vehicle class to delete.
     * @return bool True if the vehicle class is deleted successfully, false otherwise.
     */
    public function deleteVehicleClass(array $vehicleClassIds): bool;

    /**
     * update bulk vehicle class status.
     *
     * @param array $vehicleClassIds The ID of the vehicle class to update status.
     * @param string $status The status of the vehicle class to update status.
     * @param int $loggedUserId The id of logged user.
     * @return bool True if the vehicle is updated successfully, false otherwise.
     */
    public function updateBulkStatus(array $vehicleClassIds, string $status, int $loggedUserId): bool;

    /**
     * Retrieve a vehicle class by its ID.
     *
     * This method fetches a vehicle class object from the database based on the provided vehicle class ID.
     *
     * @param int $vehicleClassId The ID of the vehicle class to retrieve.
     * @return object The retrieved vehicle class object.
     */
    public function viewVehicleClass(int $vehicleClassId): object;
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
    public function getVehicleClassByIds(array $vehicleClassIds, string $status = null): Collection;
}
