<?php

namespace App\Repositories\Interfaces;

use App\Models\DriverOffDay;
use Illuminate\Database\Eloquent\Collection;

/**
 * Interface DriverOffDayInterface
 *
 * Represents an interface for managing drivers off days.
 */
interface DriverOffDayInterface
{
    /**
     * Save driver's day offs to the database.
     *
     * @param array $data The data for saving driver's day offs.
     * @return DriverOffDay The newly saved driver's day offs.
     */
    public function saveDayOffs(array $data): DriverOffDay;

    /**
     * Retrieve saved dates for a specific driver.
     *
     * This method retrieves the saved dates for a specific driver from the repository.
     *
     * @return array An array of saved dates for the specified driver.
     * @throws \Exception If an error occurs during the retrieval process, an exception is thrown.
     */
    public function getSavedDates(): Collection;

    /**
     * Delete a driver's day off.
     *
     * This method deletes a driver's day off based on the provided date.
     * It updates the 'deleted_at' property in the database to mark the day off as deleted.
     * @param int|string $driverId The ID of the driver for whom to delete the day off.
     * @param string $offDate The date of the day off to be deleted (in YYYY-MM-DD format).
     * @throws \Exception If an error occurs during the deletion process, an exception is thrown.
     */
    public function deleteDayOff($driverId, $offDate);
    public function getDiverLeavesByDate($date):Collection;
}