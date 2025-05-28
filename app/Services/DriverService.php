<?php

namespace App\Services;

use App\Models\Driver;
use App\Repositories\Interfaces\DriverInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Class DriverService
 * 
 * @package App\Services
 */
class DriverService
{

    public function __construct(
        private DriverInterface $driverRepository,
        private Auth $auth,
        private ActivityLogService $activityLogService,
    ) {
    }

    /**
     * Create a new driver record.
     *
     * @param array $requestData The data containing driver information.
     * @throws \Exception If an error occurs during the creation process.
     */
    public function createDriver(array $requestData, $log_headers)
    {
        DB::beginTransaction();
        try {
            // Get the logged-in user's ID
            $loggedUserId = Auth::user()->id;

            // Prepare the driver data
            $driverData = [
                'name' => $requestData['name'],
                'driver_type' => $requestData['type'],
                'country_code' => $requestData['country_code'],
                'phone' => $requestData['phone'],
                'email' => $requestData['email'],
                'gender' => $requestData['gender'],
                'race' => $requestData['race'],
                'chat_id' => $requestData['chat_id'],
                'vehicle_id' => $requestData['vehicle'],
                'created_by_id' => $loggedUserId,
            ];

            // Add the driver using the driver repository
            $driver = $this->driverRepository->addDriver($driverData);
            $this->activityLogService->addActivityLog('create', Driver::class, json_encode([]), json_encode($driverData), $log_headers['headers']['Origin'], $log_headers['headers']['User-Agent']);
            DB::commit();
            return $driver;
        } catch (\Exception $e) {
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Get driver data based on the provided request data.
     *
     * @param array $requestData The data containing parameters for filtering and sorting.
     * @throws \Exception If an error occurs during data retrieval.
     */
    public function getDriverData($requestData)
    {
        try {
            // Retrieve the logged-in user
            $loggedUser = Auth::user();

            // Extract parameters from the request data or use default values
            $page = $requestData['page'] ?? 1;
            $search = $requestData['search'] ?? '';
            $sortField = $requestData['sortField'] ?? 'id';
            $sortDirection = $requestData['sortDirection'] ?? 'asc';

            // Get paginated driver data using the driver repository
            return $this->driverRepository->getDriverData($loggedUser, $search, $page, $sortField, $sortDirection);
        } catch (\Exception $e) {
            // Throw an exception with the error message if an error occurs
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Retrieves all inhouse drivers from the repository.
     *
     * This method fetches all drivers that are classified as 'inhouse'
     * from the driver repository. It handles any exceptions that may
     * occur during the process and returns the retrieved drivers.
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     * @throws \Exception If an error occurs while fetching the data.
     */
    public function getInhouseDrivers()
    {
        try {
            // Retrieve all inhouse drivers from the repository
            return $this->driverRepository->getInhouseData();
        } catch (\Exception $e) {
            // Throw an exception with the error message if an error occurs
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Retrieves all active drivers from the repository.
     *
     * This method fetches all drivers that are classified as 'active'
     * from the driver repository. It handles any exceptions that may
     * occur during the process and returns the retrieved drivers.
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     * @throws \Exception If an error occurs while fetching the data.
     */
    public function getActiveDrivers()
    {
        try {
            // Retrieve all inhouse drivers from the repository
            return $this->driverRepository->getActiveDriverData();
        } catch (\Exception $e) {
            // Throw an exception with the error message if an error occurs
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Update an existing driver record.
     *
     * @param array $requestData The data containing updated driver information.
     * @param Driver $driver The driver instance to update.
     * @throws \Exception If an error occurs during the update process.
     */
    public function updateDriver(array $requestData, Driver $driver, $log_headers)
    {
        DB::beginTransaction();
        try {
            // Get the logged-in user's ID
            $loggedUserId = Auth::user()->id;

            // Prepare the updated driver data
            $driverData = [
                'name' => $requestData['name'],
                'driver_type' => $requestData['type'],
                'country_code' => $requestData['country_code'],
                'phone' => $requestData['phone'],
                'email' => $requestData['email'],
                'gender' => $requestData['gender'],
                'race' => $requestData['race'],
                'chat_id' => $requestData['chat_id'],
                'vehicle_id' => $requestData['vehicle'],
                'updated_by_id' => $loggedUserId,
            ];
            $oldData = json_encode($driver);
            // Update the driver using the driver repository
            $driver = $this->driverRepository->updateDriver($driver, $driverData);
            $this->activityLogService->addActivityLog('update', Driver::class, $oldData, json_encode($driverData), $log_headers['headers']['Origin'], $log_headers['headers']['User-Agent']);
            DB::commit();
            return $driver;
        } catch (\Exception $e) {
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Delete multiple drivers.
     *
     * @param array $requestData The data containing driver IDs to delete.
     * @return bool True if deletion is successful, false otherwise.
     * @throws \Exception If an error occurs during the deletion process.
     */
    public function deleteDriver($requestData, $log_headers): bool
    {
        DB::beginTransaction();
        try {
            $oldData = $this->driverRepository->getDriverByIds($requestData['driver_ids']);
            // Delete drivers using the driver repository
            $this->driverRepository->deleteDrivers($requestData['driver_ids']);
            $this->activityLogService->addActivityLog('delete', Driver::class, json_encode($oldData), json_encode([]), $log_headers['headers']['Origin'], $log_headers['headers']['User-Agent']);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
    }


    public function getDrivers(){
        try {
           return $this->driverRepository->getDrivers();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

}
