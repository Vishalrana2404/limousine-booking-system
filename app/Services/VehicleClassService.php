<?php

namespace App\Services;

use App\Models\VehicleClass;
use App\Repositories\Interfaces\VehicleClassInterface;
use App\Repositories\Interfaces\VehicleInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class VehicleClassService
 * 
 * @package App\Services
 */
class VehicleClassService
{
    /**
     * VehicleClassService constructor.
     *
     * @param VehicleClassInterface $vehicleClassRepository The vehicle class repository instance.
     */
    public function __construct(
        private VehicleClassInterface $vehicleClassRepository,
        private VehicleInterface $vehicleRepository,
        private ActivityLogService $activityLogService,
    ) {
    }

    /**
     * Get data for vehicles list.
     *
     * @param mixed $requestData The request data (if needed).
     * @return mixed The vehicle data.
     * @throws \Exception If an error occurs.
     */
    public function getVehicleClassData(array $requestData = [])
    {
        try {
            $page = $requestData['page'] ?? 1;
            $search = $requestData['search'] ?? '';
            $sortField = $requestData['sortField'] ?? 'id';
            $sortDirection = $requestData['sortDirection'] ?? 'asc';
            return $this->vehicleClassRepository->getVehicleClass($search, $page, $sortField, $sortDirection);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Get all vehicle classes.
     *
     * @return \Illuminate\Database\Eloquent\Collection A collection of all vehicle classes.
     */
    public function getVehicleClass()
    {
        // Retrieve all vehicle classes from the repository
        return $this->vehicleClassRepository->getVehicleClasses();
    }

    /**
     * Create a new vehicle class.
     *
     * @param array $requestData The data for creating the vehicle class.
     * @return mixed The newly created vehicle class.
     * @throws \Exception If an error occurs.
     */
    public function createVehicleClass($requestData, $log_headers)
    {
        DB::beginTransaction();
        try {
            $loggedUserId = Auth::user()->id;
            $vehicleClassData = [];

            // Fetch the max sequence_no and add 1
            $maxSequence = VehicleClass::max('sequence_no');
            $nextSequenceNo = $maxSequence ? $maxSequence + 1 : 1;

            // Prepare vehicle class data
            $vehicleClassData['name'] = $requestData['name'];
            $vehicleClassData['seating_capacity'] = $requestData['seating_capacity'];
            $vehicleClassData['total_luggage'] = $requestData['total_luggage'];
            $vehicleClassData['total_pax'] = $requestData['total_pax'];
            $vehicleClassData['status'] = $requestData['status'];
            $vehicleClassData['sequence_no'] = $nextSequenceNo; // Assign next sequence_no
            $vehicleClassData['created_by_id'] = $loggedUserId;

            // Add vehicle class using repository
            $vehicleClass = $this->vehicleClassRepository->addVehicleClass($vehicleClassData);

            // Log activity
            $this->activityLogService->addActivityLog(
                'create',
                VehicleClass::class,
                json_encode([]),
                json_encode($vehicleClassData),
                $log_headers['headers']['Origin'],
                $log_headers['headers']['User-Agent']
            );

            DB::commit();
            return $vehicleClass;
        } catch (\Exception $e) {
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
    }


    /**
     * Update an existing vehicle class.
     *
     * @param int $vehicleClassId The ID of the vehicle class to update.
     * @param array $requestData The data for updating the vehicle class.
     * @return mixed The updated vehicle class.
     * @throws \Exception If an error occurs.
     */
    public function updateVehicleClass($requestData, $vehicleClass, $log_headers)
    {
        DB::beginTransaction();
        try {
            $loggedUserId = Auth::user()->id;
            $vehicleClassData = [];
            // Extract vehicleClass data from request
            if (isset($requestData['name']))
                $vehicleClassData['name'] = $requestData['name'];
            if (isset($requestData['seating_capacity']))
                $vehicleClassData['seating_capacity'] = $requestData['seating_capacity'];
            if (isset($requestData['total_luggage']))
                $vehicleClassData['total_luggage'] = $requestData['total_luggage'];
            if (isset($requestData['total_pax']))
                $vehicleClassData['total_pax'] = $requestData['total_pax'];
            if (isset($requestData['status']))
                $vehicleClassData['status'] = $requestData['status'];
            $vehicleClassData['updated_by_id'] = $loggedUserId;
            $oldData = json_encode($vehicleClass);
            // Update vehicle clas using repository
            $vehicleClass = $this->vehicleClassRepository->updateVehicleClass($vehicleClass, $vehicleClassData);
            $this->activityLogService->addActivityLog('update', VehicleClass::class, $oldData, json_encode($vehicleClassData), $log_headers['headers']['Origin'], $log_headers['headers']['User-Agent']);
            DB::commit();
            return $vehicleClass;
        } catch (\Exception $e) {
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
    }
    /**
     * Delete a vehicle class.
     *
     * @param int $id The ID of the vehicle class to delete.
     * @return mixed The deleted vehicle class.
     * @throws \Exception If an error occurs.
     */
    public function deleteVehicleClass($requestData, $log_headers)
    {
        DB::beginTransaction();
        try {
            $count = $this->vehicleRepository->getVehicleCountByMultipleVehicleClassId($requestData['vehicle_class_ids']);
            if ($count) {
                return  false;
            }

            $oldData = $this->vehicleClassRepository->getVehicleClassByIds($requestData['vehicle_class_ids']);
            // Delete vehicle class using repository
            $this->vehicleClassRepository->deleteVehicleClass($requestData['vehicle_class_ids']);
            $this->activityLogService->addActivityLog('delete', VehicleClass::class, json_encode($oldData), json_encode([]), $log_headers['headers']['Origin'], $log_headers['headers']['User-Agent']);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * View details of a specific vehicle class.
     *
     * @param array $vehicleClassData The data containing the vehicle class ID.
     * @return mixed The result of viewing the vehicle class, typically retrieved from the repository.
     * @throws \Exception If an error occurs while trying to view the vehicle class.
     */
    public function viewVehicle($vehicleClassData)
    {
        try {
            // Extract the vehicle class ID from the provided data
            $vehicle_class_id = $vehicleClassData['id'];

            // View the vehicle class using the vehicle class repository
            return $this->vehicleClassRepository->viewVehicleClass($vehicle_class_id);
        } catch (\Exception $e) {
            // If an error occurs, re-throw the exception with the same message
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Update status for multiple vehicle classes in bulk.
     *
     * @param array $requestData The data containing vehicle class IDs, status, and user ID.
     * @return mixed The result of updating the vehicle class statuses.
     * @throws \Exception If an error occurs during the update process.
     */
    public function updateBulkStatus($requestData, $log_headers)
    {
        DB::beginTransaction();
        try {
            // Get the logged-in user's ID
            $loggedUserId = Auth::user()->id;

            // Extract vehicle class IDs and status from the request data
            $vehicleClassIds = $requestData['vehicle_class_ids'];
            $status = $requestData['status'];
            $oldData = $this->vehicleClassRepository->getVehicleClassByIds($vehicleClassIds, $status);
            // Update vehicle classes using the repository
            $vehicleClass = $this->vehicleClassRepository->updateBulkStatus($vehicleClassIds, $status, $loggedUserId);
            $this->activityLogService->addActivityLog('updateBulkStatus',  VehicleClass::class, json_encode($oldData), json_encode($requestData), $log_headers['headers']['Origin'], $log_headers['headers']['User-Agent']);
            DB::commit();
            return $vehicleClass;
        } catch (\Exception $e) {
            // Rollback the transaction if an error occurs
            DB::rollback();

            // Throw an exception with the error message
            throw new \Exception($e->getMessage());
        }
    }

    public function updateSequence(array $requestData = [])
    {
        try {
            if (!is_array($requestData)) {
                return false;
            }

            foreach ($requestData as $item) {
                $loggedUserId = Auth::user()->id;
                $this->vehicleClassRepository->updateVehicleClassSequence($item['id'], $item['sequence_no'], $loggedUserId);
            }
            return true;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
