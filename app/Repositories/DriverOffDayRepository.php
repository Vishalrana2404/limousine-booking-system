<?php

namespace App\Repositories;

use App\Models\DriverOffDay;
use App\Repositories\Interfaces\DriverOffDayInterface;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class DriverOffDayRepository
 * 
 * @package App\Repositories
 */
class DriverOffDayRepository implements DriverOffDayInterface
{
    /**
     * DriverOffDayRepository constructor.
     *
     * @param DriverOffDay $model The User model instance.
     */
    public function __construct(
        protected DriverOffDay $model
    ) {
    }

    /**
     * Save a new driver's day off data to the database.
     *
     * @param array $data The data for saving driver's day off.
     * @return DriverOffDay The newly saved driver's day off.
     */
    public function saveDayOffs(array $data): DriverOffDay
    {
        return $this->model->create($data);
    }

    /**
     * Retrieve saved dates for a specific driver.
     *
     * This method retrieves the saved dates for a specific driver from the database.
     */
    public function getSavedDates(): Collection
    {
        return $this->model->get();
    }

    /**
     * Delete a driver's day off.
     *
     * This method deletes a driver's day off based on the provided date.
     * It updates the 'deleted_at' property in the database to mark the day off as deleted.
     * @param int|string $driverId The ID of the driver for whom to delete the day off.
     * @param string $offDate The date of the day off to be deleted (in YYYY-MM-DD format).
     * @throws \Exception If an error occurs during the deletion process, an exception is thrown.
     */
    public function deleteDayOff($driverId, $offDate)
    {
        // Find the record by driver ID and off date and update 'deleted_at' property
        $dayOff = $this->model->where('driver_id', $driverId)->where('off_date', $offDate)->first();
        if ($dayOff) {
            $dayOff->delete();
        }
    }
    public function getDiverLeavesByDate($date): Collection
    {
        return $this->model->where('off_date', $date)->get();
    }
}
