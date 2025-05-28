<?php

namespace App\Repositories;

use App\Models\Location;
use App\Repositories\Interfaces\LocationInterface;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class LocationRepository
 * 
 * This class implements the LocationInterface and provides methods to interact with service type.
 */
class LocationRepository implements LocationInterface
{
    /**
     * Create a new instance of the LocationRepository.
     *
     * @param Location $model The model instance for service types.
     */
    public function __construct(
        protected Location $model
    ) {
    }


    /**
     * Add a new location.
     *
     * Adds a new location to the database.
     *
     * @param array $data The data of the location to be added.
     * @return \App\Models\Location The newly created location instance.
     */
    public function addLocation(array $data): Location
    {
        return $this->model->create($data);
    }

    /**
     * Delete  locations by their IDs.
     *
     * Deletes locations from the database based on their IDs.
     *
     * @param array $locationIds The IDs of the  locations to be deleted.
     * @return bool True if the deletion was successful, false otherwise.
     */
    public function deleteLocation(array $locationIds): bool
    {
        return $this->model->whereIn('id', $locationIds)->delete();
    }

    /**
     * Update a location.
     *
     * Updates the data of an existing location in the database.
     *
     * @param \App\Models\Location $location The location instance to be updated.
     * @param array                      $data           The updated data for the location.
     * @return bool True if the update was successful, false otherwise.
     */
    public function updateLocation(Location $location, array $data): bool
    {
        return $location->update($data);
    }

    /**
     * Retrieve a location by its ID.
     *
     * Retrieves a location from the database based on its ID.
     *
     * @param int $locationId The ID of the location to be retrieved.
     * @return \App\Models\Location|null The retrieved location instance, or null if not found.
     */
    public function getLocationById(int $locationId): ?Location
    {
        return $this->model->find($locationId);
    }

    /**
     * Retrieve all locations.
     *
     * Retrieves all locations from the database.
     *
     * @return \Illuminate\Support\Collection|null A collection of location instances, or null if no locations are found.
     */
    public function getLocations(): ?Collection
    {
        return $this->model->get();
    }
}
