<?php

namespace App\Repositories\Interfaces;

use App\Models\ServiceType;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Interface ServiceTypeInterface
 *
 * Represents an interface for managing service type.
 */
interface ServiceTypeInterface
{
    /**
     * Add a new service type.
     *
     * Adds a new service type to the database.
     *
     * @param array $data The data of the service type to be added.
     * @return \App\Models\ServiceType The newly created service type instance.
     */
    public function addServiceType(array $data): ServiceType;

    /**
     * Delete service types by their IDs.
     *
     * Deletes service types from the database based on their IDs.
     *
     * @param array $serviceTypeIds The IDs of the service types to be deleted.
     * @return bool True if the deletion was successful, false otherwise.
     */
    public function deleteServiceType(array $serviceTypeIds): bool;

    /**
     * Update a service type.
     *
     * Updates the data of an existing service type in the database.
     *
     * @param \App\Models\ServiceType $user The service type instance to be updated.
     * @param array                   $data The updated data for the service type.
     * @return bool True if the update was successful, false otherwise.
     */
    public function updateServiceType(ServiceType $user, array $data): bool;

    /**
     * Retrieve a service type by its ID.
     *
     * Retrieves a service type from the database based on its ID.
     *
     * @param int $serviceTypeId The ID of the service type to be retrieved.
     * @return \App\Models\ServiceType|null The retrieved service type instance, or null if not found.
     */
    public function getServiceTypeById(int $serviceTypeId): ?ServiceType;

    /**
     * Retrieve all service types.
     *
     * Retrieves all service types from the database.
     *
     * @return \Illuminate\Support\Collection|null A collection of service type instances, or null if no service types are found.
     */
    public function getServiceTypes(string $loggedUserType = null, bool $isCreatePage): ?Collection;
}
