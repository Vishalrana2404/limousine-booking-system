<?php

namespace App\Services;

use App\Models\Vehicle;
use App\Repositories\Interfaces\DriverInterface;
use App\Repositories\Interfaces\VehicleInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Class VehicleService
 * 
 * @package App\Services
 */
class VehicleService
{
    /**
     * VehicleService constructor.
     *
     * @param VehicleInterface $vehicleRepository The vehicle repository instance.
     */
    public function __construct(
        private VehicleInterface $vehicleRepository,
        private DriverInterface $driverRepository,
        private ActivityLogService $activityLogService,
        private UploadService $uploadService,
    ) {
    }

    /**
     * Get data for vehicles list.
     *
     * @param mixed $requestData The request data (if needed).
     * @return mixed The vehicle data.
     * @throws \Exception If an error occurs.
     */
    public function getVehicleData(array $requestData = [])
    {
        try {
            $page = $requestData['page'] ?? 1;
            $search = $requestData['search'] ?? '';
            $sortField = $requestData['sortField'] ?? 'id';
            $sortDirection = $requestData['sortDirection'] ?? 'asc';
            return $this->vehicleRepository->getVehicle($search, $page, $sortField, $sortDirection);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Create a new vehicle.
     *
     * @param array $requestData The data for creating the vehicle.
     * @return mixed The newly created vehicle.
     * @throws \Exception If an error occurs.
     */
    public function createVehicle(array $requestData, $vehicleImage, $log_headers)
    {
        DB::beginTransaction();
        try {
            $loggedUserId = Auth::user()->id;
            $vehicleData = [];
            // Extract vehicle data from request
            $vehicleData['vehicle_class_id'] = $requestData['vehicle_class'];
            $vehicleData['vehicle_number'] = $requestData['vehicle_number'];
            $vehicleData['brand'] = $requestData['brand'];
            $vehicleData['model'] = $requestData['model'];
            $vehicleData['status'] = $requestData['status'];
            $vehicleData['created_by_id'] = $loggedUserId;


            // Handle image upload
            if ($vehicleImage) {

                $folderName = 'image';

                $this->uploadService->setPath($folderName);
                $this->uploadService->createDirectory();

                $fileName = time() . '.' . $vehicleImage->extension();


                $vehicleData['image'] = $this->uploadService->upload($vehicleImage, $fileName);
            }

            // Add vehicle using repository
            $vehicleClass = $this->vehicleRepository->addVehicle($vehicleData);
            $this->activityLogService->addActivityLog('create', Vehicle::class, json_encode([]), json_encode($vehicleData), $log_headers['headers']['Origin'], $log_headers['headers']['User-Agent']);
            DB::commit();
            return $vehicleClass;
        } catch (\Exception $e) {
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Update an existing vehicle.
     *
     * @param int $vehicleId The ID of the vehicle to update.
     * @param array $requestData The data for updating the vehicle.
     * @return mixed The updated vehicle.
     * @throws \Exception If an error occurs.
     */
    public function updateVehicle(array $requestData, Vehicle $vehicle, $vehicleImage, $log_headers)
    {
        DB::beginTransaction();
        try {
            $loggedUserId = Auth::user()->id;
            $vehicleData = [];
            // Extract vehicle data from request
            if (isset($requestData['vehicle_class']))
                $vehicleData['vehicle_class_id'] = $requestData['vehicle_class'];
            if (isset($requestData['vehicle_number']))
                $vehicleData['vehicle_number'] = $requestData['vehicle_number'];
            if (isset($requestData['brand']))
                $vehicleData['brand'] = $requestData['brand'];
            if (isset($requestData['model']))
                $vehicleData['model'] = $requestData['model'];
            if (isset($requestData['status']))
                $vehicleData['status'] = $requestData['status'];
            $vehicleData['updated_by_id'] = $loggedUserId;

            $vehicleData['image'] = $vehicle['image'];

            // Handle image upload
            if (!empty($vehicleImage)) {

                $folderName = 'image';

                $this->uploadService->setPath($folderName);
                $this->uploadService->createDirectory();

                $fileName = time() . '.' . $vehicleImage->extension();


                $vehicleData['image'] = $this->uploadService->upload($vehicleImage, $fileName);
            }
            $oldData = json_encode($vehicle);
            // Update vehicle using repository
            $vehicleClass = $this->vehicleRepository->updateVehicle($vehicle, $vehicleData);
            $this->activityLogService->addActivityLog('update', Vehicle::class, $oldData, json_encode($vehicleData), $log_headers['headers']['Origin'], $log_headers['headers']['User-Agent']);
            DB::commit();
            return $vehicleClass;
        } catch (\Exception $e) {
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Delete client(s) from the database.
     *
     * @param array $requestData The request data containing the IDs of the vehicles to delete.
     * @return bool True if the client(s) are successfully deleted.
     * @throws \Exception If an error occurs during the deletion process.
     */
    public function deleteVehicle($requestData, $log_headers)
    {
        DB::beginTransaction();
        try {
            $count =   $this->driverRepository->getDriverCountByVehicleIds($requestData['vehicle_ids']);
            if ($count) {
                return  false;
            }
            $oldData =  $this->vehicleRepository->getVehicleByIds($requestData['vehicle_ids']);
            // Delete vehicle(s) from the database
            $this->vehicleRepository->deleteVehicle($requestData['vehicle_ids']);
            $this->activityLogService->addActivityLog('delete', Vehicle::class, json_encode($oldData), json_encode([]), $log_headers['headers']['Origin'], $log_headers['headers']['User-Agent']);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            // Rollback the transaction if an error occurs
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * View details of a specific vehicle.
     *
     * @param array $vehicleData The data containing the vehicle ID.
     * @return mixed The result of viewing the vehicle, typically retrieved from the repository.
     * @throws \Exception If an error occurs while trying to view the vehicle.
     */
    public function viewVehicle($vehicleData)
    {
        try {
            // Extract the vehicle ID from the provided data
            $vehicle_id = $vehicleData['id'];

            // View the vehicle using the vehicle repository
            return $this->vehicleRepository->viewVehicle($vehicle_id);
        } catch (\Exception $e) {
            // If an error occurs, re-throw the exception with the same message
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Update status for multiple vehicles in bulk.
     *
     * @param array $requestData The data containing vehicle IDs, status, and user ID.
     * @return mixed The result of updating the vehicle statuses.
     * @throws \Exception If an error occurs during the update process.
     */
    public function updateBulkStatus($requestData, $log_headers)
    {
        DB::beginTransaction();
        try {
            // Get the logged-in user's ID
            $loggedUserId = Auth::user()->id;

            // Extract vehicle IDs and status from the request data
            $vehicleIds = $requestData['vehicle_ids'];
            $status = $requestData['status'];
            $oldData =  $this->vehicleRepository->getVehicleByIds($vehicleIds, $status);
            // Update vehicles using the repository
            $vehicleClass = $this->vehicleRepository->updateBulkStatus($vehicleIds, $status, $loggedUserId);
            $this->activityLogService->addActivityLog('updateBulkStatus', Vehicle::class, json_encode($oldData), json_encode($requestData), $log_headers['headers']['Origin'], $log_headers['headers']['User-Agent']);
            DB::commit();
            return $vehicleClass;
        } catch (\Exception $e) {
            // Rollback the transaction if an error occurs
            DB::rollback();

            // Throw an exception with the error message
            throw new \Exception($e->getMessage());
        }
    }
    public function getVehicles()
    {
        try {
            return $this->vehicleRepository->getVehicles();
        } catch (\Exception $e) {
            // Throw an exception with the error message
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Check if the provided vehicle number is unique in the system.
     *
     * @param string $vehicleNumber The vehicle number to be checked for uniqueness.
     * @param int|null $vehicleId The ID of the vehicle to exclude from the uniqueness check.
     * @return bool True if the vehicle number is unique, false otherwise.
     * @throws \Exception If an error occurs while checking the vehicle number uniqueness.
     */
    public function checkUniqueVehicleNumber(string $vehicleNumber, int $vehicleId = null): bool
    {
        try {
            // Call the VehicleRepository to check the uniqueness of the vehicle number
            $vehicle = $this->vehicleRepository->checkUniqueVehicleNumber($vehicleNumber, $vehicleId);
            // Return true if the vehicle number is unique, false otherwise
            return $vehicle ? false : true;
        } catch (\Exception $e) {
            // If an exception occurs, throw an exception
            throw new \Exception($e->getMessage());
        }
    }
}
