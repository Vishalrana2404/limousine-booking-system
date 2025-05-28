<?php

namespace App\Services;

use App\Models\PeakPeriod;
use App\Repositories\Interfaces\PeakPeriodInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Carbon\Carbon;

/**
 * Class PeakPeriodService
 * 
 * @package App\Services
 */
class PeakPeriodService
{
    /**
     * PeakPeriodService constructor.
     *
     * @param PeakPeriodInterface $peakPeriodRepository The peak period repository instance.
     */
    public function __construct(
        private PeakPeriodInterface $peakPeriodRepository,
        private ActivityLogService $activityLogService,
    ) {
    }

    /**
     * Get data for peak periods list.
     *
     * @param mixed $requestData The request data (if needed).
     * @return mixed The peak period data.
     * @throws \Exception If an error occurs.
     */
    public function getPeakPeriodData(array $requestData = [])
    {
        try {
            $page = $requestData['page'] ?? 1;
            $search = $requestData['search'] ?? '';
            $sortField = $requestData['sortField'] ?? 'id';
            $sortDirection = $requestData['sortDirection'] ?? 'asc';
            return $this->peakPeriodRepository->getPeakPeriods($search, $page, $sortField, $sortDirection);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Get all peak periods.
     *
     * @return \Illuminate\Database\Eloquent\Collection A collection of all peak periods.
     */
    public function getAllPeakPeriod()
    {
        // Retrieve all peak periods from the repository
        return $this->peakPeriodRepository->getAllPeakPeriodes();
    }

    /**
     * Create a new peak period.
     *
     * @param array $requestData The data for creating the peak period.
     * @return mixed The newly created peak period.
     * @throws \Exception If an error occurs.
     */
    public function createPeakPeriod($requestData, $log_headers)
    {

        DB::beginTransaction();
        try {
            $loggedUserId = Auth::user()->id;
            $peakPeriodData = [];
            // Extract peak period data from request
            $peakPeriodData['event'] = $requestData['event'];
            $peakPeriodData['start_date'] = $requestData['start_date'] ? Carbon::createFromFormat('d/m/Y', $requestData['start_date'])->format('Y-m-d') : null;
            $peakPeriodData['end_date'] = $requestData['end_date'] ? Carbon::createFromFormat('d/m/Y', $requestData['end_date'])->format('Y-m-d') : null;
            $peakPeriodData['status'] = $requestData['status'];
            $peakPeriodData['created_by_id'] = $loggedUserId;
            // Add peak period using repository
            $PeakPeriod = $this->peakPeriodRepository->addPeakPeriod($peakPeriodData);
            $this->activityLogService->addActivityLog('create', PeakPeriod::class, json_encode([]), json_encode($peakPeriodData), $log_headers['headers']['Origin'], $log_headers['headers']['User-Agent']);
            DB::commit();
            return $PeakPeriod;
        } catch (\Exception $e) {
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Update an existing peak period.
     *
     * @param int $PeakPeriodId The ID of the peak period to update.
     * @param array $requestData The data for updating the peak period.
     * @return mixed The updated peak period.
     * @throws \Exception If an error occurs.
     */
    public function updatePeakPeriod($requestData, $peakPeriod, $log_headers)
    {
        DB::beginTransaction();
        try {
            $loggedUserId = Auth::user()->id;
            $peakPeriodData = [];
            // Extract peakPeriod data from request
            if (isset($requestData['event']))
                $peakPeriodData['event'] = $requestData['event'];
            if (isset($requestData['start_date']))
                $peakPeriodData['start_date'] = $requestData['start_date'] ? Carbon::createFromFormat('d/m/Y', $requestData['start_date'])->format('Y-m-d') : null;
            if (isset($requestData['end_date']))
                $peakPeriodData['end_date'] = $requestData['end_date'] ? Carbon::createFromFormat('d/m/Y', $requestData['end_date'])->format('Y-m-d') : null;
            if (isset($requestData['status']))
                $peakPeriodData['status'] = $requestData['status'];
            $peakPeriodData['updated_by_id'] = $loggedUserId;
            $oldData = json_encode($peakPeriod);
            // Update peak period using repository
            $result = $this->peakPeriodRepository->updatePeakPeriod($peakPeriod, $peakPeriodData);
            $this->activityLogService->addActivityLog('update', PeakPeriod::class, $oldData, json_encode($peakPeriodData), $log_headers['headers']['Origin'], $log_headers['headers']['User-Agent']);
            DB::commit();
            return $result;
        } catch (\Exception $e) {
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Delete a peak period.
     *
     * @param int $id The ID of the peak period to delete.
     * @return mixed The deleted peak period.
     * @throws \Exception If an error occurs.
     */
    public function deletePeakPeriod($requestData, $log_headers)
    {
        DB::beginTransaction();
        try {
            $oldData = $this->peakPeriodRepository->getPeakPeriodByIds($requestData['peak_period_ids']);
            // Delete peak period using repository
            $this->peakPeriodRepository->deletePeakPeriod($requestData['peak_period_ids']);
            $this->activityLogService->addActivityLog('delete', PeakPeriod::class, json_encode($oldData), json_encode([]), $log_headers['headers']['Origin'], $log_headers['headers']['User-Agent']);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Update status for multiple peak periods in bulk.
     *
     * @param array $requestData The data containing peak period IDs, status, and user ID.
     * @return mixed The result of updating the peak period statuses.
     * @throws \Exception If an error occurs during the update process.
     */
    public function updateBulkStatus($requestData, $log_headers)
    {
        DB::beginTransaction();
        try {
            // Get the logged-in user's ID
            $loggedUserId = Auth::user()->id;

            // Extract peak period IDs and status from the request data
            $peakPeriodIds = $requestData['peak_period_ids'];
            $status = $requestData['status'];
            $oldData = $this->peakPeriodRepository->getPeakPeriodByIds($peakPeriodIds, $status);
            // Update peak periods using the repository
            $peakPeriod = $this->peakPeriodRepository->updateBulkStatus($peakPeriodIds, $status, $loggedUserId);
            $this->activityLogService->addActivityLog('updateBulkStatus',  PeakPeriod::class, json_encode($oldData), json_encode($requestData), $log_headers['headers']['Origin'], $log_headers['headers']['User-Agent']);
            DB::commit();
            return $peakPeriod;
        } catch (\Exception $e) {
            // Rollback the transaction if an error occurs
            DB::rollback();

            // Throw an exception with the error message
            throw new \Exception($e->getMessage());
        }
    }
 }
