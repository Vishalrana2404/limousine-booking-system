<?php

namespace App\Services;

use App\Models\OutsideCitySurcharge;
use App\Repositories\Interfaces\CitySurchargeInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Class CitySurchargeService
 * 
 * @package App\Services
 */
class CitySurchargeService
{
    /**
     * Constructor for the MyClass class.
     *
     * @param CitySurchargeInterface $citySurchargeRepository The repository for city surcharges.
     * @param ActivityLogService $activityLogService The service for activity logging.
     */
    public function __construct(
        private CitySurchargeInterface $citySurchargeRepository,
        private ActivityLogService $activityLogService,
    ) {
    }

    /**
     * Create a new city surcharge.
     *
     * @param array $requestData The request data containing the city surcharge details.
     * @param array $logHeaders The headers from the HTTP request for logging purposes.
     * @return mixed The created city surcharge.
     * @throws \Exception If an error occurs during the creation process.
     */
    public function createUpdateCitySurcharge($requestData, $logHeaders)
    {
        DB::beginTransaction();
        try {
            $loggedUserId = Auth::user()->id;
            $citySurchargeData = [];
            $citySurchargeData['coordinates'] = $requestData['coordinates'];
            $id =  (int)$requestData['id'];
            if ($id) {
                $citySurchargeData['updated_by_id'] = $loggedUserId;
                $cordinates = $this->citySurchargeRepository->getCordinatesById($id);
                $this->citySurchargeRepository->updateCordinates($cordinates, $citySurchargeData);
            } else {
                $citySurchargeData['created_by_id'] = $loggedUserId;
                $cordinates =  $this->citySurchargeRepository->addCitySurcharge($citySurchargeData);
            }
            $this->activityLogService->addActivityLog('create', OutsideCitySurcharge::class, json_encode([]), json_encode($citySurchargeData), $logHeaders['headers']['Origin'], $logHeaders['headers']['User-Agent']);
            DB::commit();
            return $cordinates;
        } catch (\Exception $e) {
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Retrieve the saved city surcharges from the repository.
     *
     * @return mixed The saved city surcharges.
     */
    public function getSavedCitySurcharges()
    {
        try {
            return $this->citySurchargeRepository->getSavedCitiesSurcharges();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function deleteCoordinates(int $id, $logHeaders)
    {
        try {
            if ($id) {
                return $this->citySurchargeRepository->delete($id);
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
