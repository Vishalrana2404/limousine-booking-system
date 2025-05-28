<?php

namespace App\Repositories;

use App\Models\ServiceType;
use App\Models\UserType;
use App\Repositories\Interfaces\ServiceTypeInterface;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class ServiceTypeRepository
 * 
 * This class implements the ServiceTypeInterface and provides methods to interact with service type.
 */
class ServiceTypeRepository implements ServiceTypeInterface
{
    /**
     * Create a new instance of the ServiceTypeRepository.
     *
     * @param ServiceType $model The model instance for service types.
     */
    public function __construct(
        protected ServiceType $model
    ) {
    }
    /**
     * Add a new service type.
     *
     * Adds a new service type to the database.
     *
     * @param array $data The data of the service type to be added.
     * @return \App\Models\ServiceType The newly created service type instance.
     */
    public function addServiceType(array $data): ServiceType
    {
        return $this->model->create($data);
    }

    /**
     * Delete service types by their IDs.
     *
     * Deletes service types from the database based on their IDs.
     *
     * @param array $serviceTypeIds The IDs of the service types to be deleted.
     * @return bool True if the deletion was successful, false otherwise.
     */
    public function deleteServiceType(array $serviceTypeIds): bool
    {
        return $this->model->whereIn('id', $serviceTypeIds)->delete();
    }

    /**
     * Update a service type.
     *
     * Updates the data of an existing service type in the database.
     *
     * @param \App\Models\ServiceType $serviceType The service type instance to be updated.
     * @param array                   $data        The updated data for the service type.
     * @return bool True if the update was successful, false otherwise.
     */
    public function updateServiceType(ServiceType $serviceType, array $data): bool
    {
        return $serviceType->update($data);
    }

    /**
     * Retrieve a service type by its ID.
     *
     * Retrieves a service type from the database based on its ID.
     *
     * @param int $serviceTypeId The ID of the service type to be retrieved.
     * @return \App\Models\ServiceType|null The retrieved service type instance, or null if not found.
     */
    public function getServiceTypeById(int $serviceTypeId): ?ServiceType
    {
        return $this->model->find($serviceTypeId);
    }

    /**
     * Retrieve all service types.
     *
     * Retrieves all service types from the database.
     *
     * @return \Illuminate\Support\Collection|null A collection of service type instances, or null if no service types are found.
     */
    public function getServiceTypes(string $loggedUserType = null, bool $isCreatePage = false): ?Collection
    {
        if ($loggedUserType === UserType::CLIENT || $isCreatePage) {
            return $this->model->whereNotIn('id', [6, 7])->get();
        } else {
            return $this->model->get();
        }
    }
}
