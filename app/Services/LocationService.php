<?php

namespace App\Services;

use App\Repositories\Interfaces\LocationInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * LocationService class
 * 
 */
class LocationService
{
    /**
     * Constructor for the LocationService class.
     *
     * Initializes the LocationService with necessary dependencies.
     *
     * @param LocationInterface $locationRepository The repository for location operations.
     * @param Auth                    $auth                     The authentication service instance.
     */
    public function __construct(
        private LocationInterface $locationRepository,
        private Auth $auth,
    ) {
    }
    /**
     * Retrieve locations.
     *
     * Retrieves locations from the location repository.
     *
     * @return mixed The locations retrieved from the repository.
     * @throws \Exception If an error occurs during the retrieval process.
     */
    public function getLocations()
    {
        try {
            $user = $this->locationRepository->getLocations();
            return $user;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
