<?php

namespace App\Repositories\Interfaces;

use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Interface VehicleInterface
 * 
 * @package App\Repositories\Interfaces
 */
interface VehicleInterface
{
    /**
     * Get data for vehicles.
     *
     */
    public function getVehicle(string $search = '', int $page = 1, string $sortField = 'id', string $sortDirection = 'asc'): LengthAwarePaginator;

    /**
     * Add a new vehicle.
     *
     * @param array $data The data for creating the vehicle.
     * @return Vehicle The newly created vehicle.
     */
    public function addVehicle(array $data): Vehicle;

    /**
     * Get data for a specific vehicle by ID.
     *
     * @param int $vehicleId The ID of the vehicle.
     * @return Vehicle|null The vehicle instance or null if not found.
     */
    public function getVehicleById(int $vehicleId): ?Vehicle;

    /**
     * Update an existing vehicle in the database.
     *
     * @param \App\Models\Vehicle $vehicle The vehicle instance to update.
     * @param array $data The data to update the vehicle with.
     * @return bool True if the update was successful, false otherwise.
     */
    public function updateVehicle(Vehicle $vehicle, array $data): bool;

    /**
     * Delete multiple vehicles.
     *
     * @param array $clientIds The IDs of the vehicles to delete.
     * @return bool True if the vehicles are successfully deleted, false otherwise.
     */
    public function deleteVehicle(array $vehicleIds): bool;

    /**
     * update bulk vehicle status.
     *
     * @param array $vehicleIds The ID of the vehicle to update status.
     * @param string $status The status of the vehicle to update status.
     * @param int $loggedUserId The id of logged user.
     * @return bool True if the vehicle is updated successfully, false otherwise.
     */
    public function updateBulkStatus(array $vehicleIds, string $status, int $loggedUserId): bool;

    /**
     * Retrieve a vehicle by its ID.
     *
     * This method fetches a vehicle object from the database based on the provided vehicle ID.
     *
     * @param int $vehicleId The ID of the vehicle to retrieve.
     * @return object The retrieved vehicle object.
     */
    public function viewVehicle(int $vehicleId): object;

    /**
     * Get the count of vehicles by multiple vehicle class IDs.
     *
     * This method retrieves the count of vehicles from the database based on the provided
     * array of vehicle class IDs.
     *
     * @param array $vehicleIds An array of vehicle class IDs.
     * @return int|null The count of vehicles or null if no matching records are found.
     */
    public function getVehicleCountByMultipleVehicleClassId(array $vehicleIds): ?int;
    public function getVehicles(): ?Collection;
    /**
     * Check if a vehicle number is unique.
     *
     * @param string $vehicleNumber The vehicle number to check for uniqueness.
     * @param int|null $vehicleId The ID of the vehicle to exclude from the uniqueness check (optional, default is null).
     * @return Vehicle|null The vehicle instance with the specified vehicle number, or null if not found.
     */
    public function checkUniqueVehicleNumber(string $vehicleNumber, int $vehicleId  = null): ?Vehicle;


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
    public function getVehicleByIds(array $vehicleIds, string $status = null): Collection;
}
