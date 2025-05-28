<?php

namespace App\Services;

use App\Models\DriverOffDay;
use App\Repositories\Interfaces\DriverOffDayInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Class DriverOffDayService
 * 
 * @package App\Services
 */
class DriverOffDayService
{

    public function __construct(
        private DriverOffDayInterface $driverOffDayRepository,
        private Auth $auth,
        private ActivityLogService $activityLogService,
    ) {
    }

    /**
     * Save driver's off days to the database based on the provided request data.
     *
     * @param array $requestData The array containing the request data including driver ID and selected dates.
     * @return mixed The result of saving the driver's off days to the database.
     * @throws \Exception If an error occurs during the transaction rollback.
     */
    public function saveDriverDayOff(array $requestData, $log_headers)
    {
        DB::beginTransaction();
        try {
            // Get the logged-in user's ID
            $loggedUserId = Auth::user()->id;

            // Prepare the driver data
            $dayOffData = [
                'driver_id' => $requestData['driver_id'],
                'off_date' => $requestData['off_date'],
                'created_by_id' => $loggedUserId,
            ];

            // Add the driver's off days using the driver repository
            $dayOffs = $this->driverOffDayRepository->saveDayOffs($dayOffData);
            $this->activityLogService->addActivityLog('create', DriverOffDay::class, json_encode([]), json_encode($dayOffData), $log_headers['headers']['Origin'], $log_headers['headers']['User-Agent']);
            DB::commit();
            return $dayOffs;
        } catch (\Exception $e) {
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Retrieve saved dates for a specific driver.
     *
     * This method retrieves the saved dates for a specific driver from the repository.
     */
    public function getSavedDates()
    {

        return $this->driverOffDayRepository->getSavedDates();
    }

    /**
     * Delete a driver's day off.
     *
     * This method deletes a driver's day off based on the provided date.
     * It updates the 'deleted_at' property in the database to mark the day off as deleted.
     * @param array $requestData The data containing the driver ID and the date of the day off.
     * @throws \Exception If an error occurs during the deletion process, an exception is thrown.
     */
    public function deleteDriverDayOff(array $requestData, $log_headers)
    {
        DB::beginTransaction();
        try {
            $oldData =   $this->driverOffDayRepository->getSavedDates($requestData);
            // Logic to update 'deleted_at' property based on unchecked date
            $this->driverOffDayRepository->deleteDayOff($requestData['driver_id'], $requestData['off_date']);
            $this->activityLogService->addActivityLog('delete', DriverOffDay::class, json_encode($oldData), json_encode([]), $log_headers['headers']['Origin'], $log_headers['headers']['User-Agent']);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
    }
}
