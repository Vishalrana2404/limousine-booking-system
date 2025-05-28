<?php

namespace App\Services;

use App\Models\BillingAgreement;
use App\Models\CorporateFairBilling;
use App\Models\Hotel;
use App\Repositories\Interfaces\BillingAgreementInterface;
use App\Repositories\Interfaces\CorporateFairBillingInterface;
use App\Repositories\Interfaces\HotelInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * HotelService class
 * 
 */
class HotelService
{
    /**
     * Constructor for the HotelService class.
     *
     * 
     */
    public function __construct(
        private HotelInterface $hotelRepository,
        private Auth $auth,
        private ActivityLogService $activityLogService,
        private BillingAgreementInterface $billingAgreementRepository,
        private CorporateFairBillingInterface $corporateFairBillingRepository,
    ) {
    }
    /**
     * Retrieve hotel data based on the provided criteria.
     *
     * Retrieves hotel data based on the provided request data or default values.
     * If successful, returns paginated hotel data.
     * If an error occurs during the process, throws an exception with an error message.
     *
     * @param array $requestData An associative array containing criteria for retrieving hotel data (optional). Possible keys: 'page', 'search', 'sortField', 'sortDirection'.
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator Returns paginated hotel data.
     *
     * @throws \Exception If an error occurs during the process.
     */
    public function getHotelData(array $requestData = [])
    {
        try {
            // Retrieve the logged-in user
            $loggedUser = Auth::user();

            // Extract parameters from the request data or use default values
            $page = $requestData['page'] ?? 1;
            $search = $requestData['search'] ?? '';
            $sortField = $requestData['sortField'] ?? 'id';
            $sortDirection = $requestData['sortDirection'] ?? 'asc';
            // Get paginated hotel data using the hotel repository
            return $this->hotelRepository->gethotels($loggedUser, $search, $page, $sortField, $sortDirection);
        } catch (\Exception $e) {
            // Throw an exception with the error message if an error occurs
            throw new \Exception($e->getMessage());
        }
    }
    /**
     * Create a new hotel with the provided data.
     *
     * Creates a new hotel using the provided request data.
     * If successful, commits the transaction and returns the created hotel.
     * If an error occurs during the process, rolls back the transaction and throws an exception with an error message.
     *
     * @param array $requestData An associative array containing data for creating the hotel.
     *                           Required keys: 'name', 'status'.
     *
     * @return \App\Models\Hotel Returns the created hotel model instance.
     *
     * @throws \Exception If an error occurs during the process.
     */
    public function createHotel(array $requestData, $log_headers)
    {
        DB::beginTransaction();
        try {
            // Get the logged-in user's ID
            $loggedUserId = Auth::user()->id;

            // Prepare the hotel data
            $hotelData = [
                'name' => $requestData['name'],
                'status' => $requestData['status'],
                'term_conditions' => $requestData['term_conditions'],
                'created_by_id' => $loggedUserId,
            ];
            // Add the hotel using the hotel repository
            $hotel = $this->hotelRepository->addHotel($hotelData);
            // Prepare billing agreement data
            $billingAgreementData['hotel_id'] =  $hotel->id;
            // $billingAgreementData['per_trip_arr'] = $requestData['per_trip_arr'];
            // $billingAgreementData['per_trip_dep'] = $requestData['per_trip_dep'];
            // $billingAgreementData['per_trip_transfer'] = $requestData['per_trip_transfer'];
            $billingAgreementData['per_trip_delivery'] = $requestData['per_trip_delivery'];
            // $billingAgreementData['per_hour_rate'] = $requestData['per_hour_rate'];
            $billingAgreementData['peak_period_surcharge'] = $requestData['peak_period_surcharge'];
            $billingAgreementData['fixed_multiplier_midnight_surcharge_23_seats'] = $requestData['fixed_multiplier_midnight_surcharge_23_seats'];
            $billingAgreementData['mid_night_surcharge_23_seats'] = $requestData['mid_night_surcharge_23_seats'];
            $billingAgreementData['fixed_multiplier_midnight_surcharge_greater_then_23_seats'] = $requestData['fixed_multiplier_midnight_surcharge_greater_then_23_seats'];
            $billingAgreementData['midnight_surcharge_greater_then_23_seats'] = $requestData['midnight_surcharge_greater_then_23_seats'];
            $billingAgreementData['fixed_multiplier_arrivel_waiting_time'] = $requestData['fixed_multiplier_arrivel_waiting_time'];
            $billingAgreementData['arrivel_waiting_time'] = $requestData['arrivel_waiting_time'];
            $billingAgreementData['fixed_multiplier_departure_and_transfer_waiting'] = $requestData['fixed_multiplier_departure_and_transfer_waiting'];
            $billingAgreementData['departure_and_transfer_waiting'] = $requestData['departure_and_transfer_waiting'];
            $billingAgreementData['fixed_multiplier_last_min_request_23_seats'] = $requestData['fixed_multiplier_last_min_request_23_seats'];
            $billingAgreementData['last_min_request_23_seats'] = $requestData['last_min_request_23_seats'];
            $billingAgreementData['fixed_multiplier_last_min_request_greater_then_23_seats'] = $requestData['fixed_multiplier_last_min_request_greater_then_23_seats'];
            $billingAgreementData['last_min_request_greater_then_23_seats'] = $requestData['last_min_request_greater_then_23_seats'];
            $billingAgreementData['fixed_multiplier_outside_city_surcharge_23_seats'] = $requestData['fixed_multiplier_outside_city_surcharge_23_seats'];
            $billingAgreementData['outside_city_surcharge_23_seats'] = $requestData['outside_city_surcharge_23_seats'];
            $billingAgreementData['fixed_multiplier_outside_city_surcharge_greater_then_23_seats'] = $requestData['fixed_multiplier_outside_city_surcharge_greater_then_23_seats'];
            $billingAgreementData['outside_city_surcharge_greater_then_23_seats'] = $requestData['outside_city_surcharge_greater_then_23_seats'];
            $billingAgreementData['fixed_multiplier_additional_stop'] = $requestData['fixed_multiplier_additional_stop'];
            $billingAgreementData['additional_stop'] = $requestData['additional_stop'];
            $billingAgreementData['fixed_multiplier_misc_charges'] = $requestData['fixed_multiplier_misc_charges'];
            $billingAgreementData['misc_charges'] = $requestData['misc_charges'];
            $billingAgreement = $this->billingAgreementRepository->addBillingAgreement($billingAgreementData);

            $this->activityLogService->addActivityLog('create', Hotel::class, json_encode([]), json_encode($hotelData), $log_headers['headers']['Origin'], $log_headers['headers']['User-Agent']);
            $this->activityLogService->addActivityLog('create', BillingAgreement::class, json_encode([]), json_encode($billingAgreementData), $log_headers['headers']['Origin'], $log_headers['headers']['User-Agent']);
            
            $billingTypes = [
                'per_trip_arr_' => 'Arrival',
                'per_trip_dep_' => 'Departure',
                'per_trip_transfer_' => 'Transfer',
                'per_hour_rate_' => 'Hour'
            ];
            
            foreach ($billingTypes as $prefix => $billingType) {
                $rates = array_filter($requestData, function ($key) use ($prefix) {
                    return preg_match('/^' . preg_quote($prefix) . '(\d+)$/', $key);
                }, ARRAY_FILTER_USE_KEY);
            
                foreach ($rates as $key => $value) {
                    if (preg_match('/^' . preg_quote($prefix) . '(\d+)$/', $key, $matches)) {
                        $corporateFairBillingData = [
                            'hotel_id' => $hotel->id,
                            'vehicle_class_id' => $matches[1],
                            'billing_type' => $billingType,
                            'amount' => $value
                        ];
            
                        $corporateFairBilling = $this->corporateFairBillingRepository->addCorporateFairBilling($corporateFairBillingData);
            
                        $this->activityLogService->addActivityLog(
                            'create',
                            CorporateFairBilling::class,
                            json_encode([]),
                            json_encode($corporateFairBillingData),
                            $log_headers['headers']['Origin'],
                            $log_headers['headers']['User-Agent']
                        );
                    }
                }
            }

            DB::commit();
            return $hotel;
        } catch (\Exception $e) {
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
    }
    /**
     * Update an existing hotel with the provided data.
     *
     * Updates an existing hotel using the provided request data.
     * If successful, commits the transaction and returns the updated hotel.
     * If an error occurs during the process, rolls back the transaction and throws an exception with an error message.
     *
     * @param array $requestData An associative array containing data for updating the hotel.
     *                           Required keys: 'name', 'status'.
     * @param \App\Models\Hotel $hotel The hotel model instance to be updated.
     *
     * @return \App\Models\Hotel Returns the updated hotel model instance.
     *
     * @throws \Exception If an error occurs during the process.
     */
    public function updateHotel(array $requestData, Hotel $hotel, $log_headers)
    {
        DB::beginTransaction();
        try {
            // Get the logged-in user's ID
            $loggedUserId = Auth::user()->id;
            $billingAgreement = $this->billingAgreementRepository->getBillingAgreementByHotelId($hotel->id);
            $corporateFairBilling = $this->corporateFairBillingRepository->getCorporateFairBillingByHotelId($hotel->id);
            // Prepare the updated hotel data
            $hotelData = [
                'name' => $requestData['name'],
                'term_conditions' => $requestData['term_conditions'],
                'status' => $requestData['status'],
                'updated_by_id' => $loggedUserId,
            ];
            $oldData = json_encode($hotel);
            // Update the hotel using the hotel repository
            $this->hotelRepository->updateHotel($hotel, $hotelData);
            $billingAgreementData['hotel_id'] =  $hotel->id;
            // $billingAgreementData['per_trip_arr'] = $requestData['per_trip_arr'];
            // $billingAgreementData['per_trip_dep'] = $requestData['per_trip_dep'];
            // $billingAgreementData['per_trip_transfer'] = $requestData['per_trip_transfer'];
            $billingAgreementData['per_trip_delivery'] = $requestData['per_trip_delivery'];
            // $billingAgreementData['per_hour_rate'] = $requestData['per_hour_rate'];
            $billingAgreementData['peak_period_surcharge'] = $requestData['peak_period_surcharge'];
            $billingAgreementData['fixed_multiplier_midnight_surcharge_23_seats'] = $requestData['fixed_multiplier_midnight_surcharge_23_seats'];
            $billingAgreementData['mid_night_surcharge_23_seats'] = $requestData['mid_night_surcharge_23_seats'];
            $billingAgreementData['fixed_multiplier_midnight_surcharge_greater_then_23_seats'] = $requestData['fixed_multiplier_midnight_surcharge_greater_then_23_seats'];
            $billingAgreementData['midnight_surcharge_greater_then_23_seats'] = $requestData['midnight_surcharge_greater_then_23_seats'];
            $billingAgreementData['fixed_multiplier_arrivel_waiting_time'] = $requestData['fixed_multiplier_arrivel_waiting_time'];
            $billingAgreementData['arrivel_waiting_time'] = $requestData['arrivel_waiting_time'];
            $billingAgreementData['fixed_multiplier_departure_and_transfer_waiting'] = $requestData['fixed_multiplier_departure_and_transfer_waiting'];
            $billingAgreementData['departure_and_transfer_waiting'] = $requestData['departure_and_transfer_waiting'];
            $billingAgreementData['fixed_multiplier_last_min_request_23_seats'] = $requestData['fixed_multiplier_last_min_request_23_seats'];
            $billingAgreementData['last_min_request_23_seats'] = $requestData['last_min_request_23_seats'];
            $billingAgreementData['fixed_multiplier_last_min_request_greater_then_23_seats'] = $requestData['fixed_multiplier_last_min_request_greater_then_23_seats'];
            $billingAgreementData['last_min_request_greater_then_23_seats'] = $requestData['last_min_request_greater_then_23_seats'];
            $billingAgreementData['fixed_multiplier_outside_city_surcharge_23_seats'] = $requestData['fixed_multiplier_outside_city_surcharge_23_seats'];
            $billingAgreementData['outside_city_surcharge_23_seats'] = $requestData['outside_city_surcharge_23_seats'];
            $billingAgreementData['fixed_multiplier_outside_city_surcharge_greater_then_23_seats'] = $requestData['fixed_multiplier_outside_city_surcharge_greater_then_23_seats'];
            $billingAgreementData['outside_city_surcharge_greater_then_23_seats'] = $requestData['outside_city_surcharge_greater_then_23_seats'];
            $billingAgreementData['fixed_multiplier_additional_stop'] = $requestData['fixed_multiplier_additional_stop'];
            $billingAgreementData['additional_stop'] = $requestData['additional_stop'];
            $billingAgreementData['fixed_multiplier_misc_charges'] = $requestData['fixed_multiplier_misc_charges'];
            $billingAgreementData['misc_charges'] = $requestData['misc_charges'];
            $oldBillingData = json_encode($billingAgreement);

            if($billingAgreement)
            {
                $this->billingAgreementRepository->updateBillingAgreement($billingAgreement, $billingAgreementData);
            }

            $this->activityLogService->addActivityLog('update', Hotel::class, $oldData, json_encode($hotelData), $log_headers['headers']['Origin'], $log_headers['headers']['User-Agent']);
            $this->activityLogService->addActivityLog('create', BillingAgreement::class, $oldBillingData, json_encode($billingAgreementData), $log_headers['headers']['Origin'], $log_headers['headers']['User-Agent']);

            $oldCorporateFairBilling = json_encode($corporateFairBilling);
            $deleteCorporateFairBilling = $this->corporateFairBillingRepository->deleteCorporateFairBillingByHotelId($hotel->id);
            $this->activityLogService->addActivityLog('delete', CorporateFairBilling::class, $oldCorporateFairBilling, json_encode([]), $log_headers['headers']['Origin'], $log_headers['headers']['User-Agent']);

            $billingTypes = [
                'per_trip_arr_' => 'Arrival',
                'per_trip_dep_' => 'Departure',
                'per_trip_transfer_' => 'Transfer',
                'per_hour_rate_' => 'Hour'
            ];
            
            foreach ($billingTypes as $prefix => $billingType) {
                $rates = array_filter($requestData, function ($key) use ($prefix) {
                    return preg_match('/^' . preg_quote($prefix) . '(\d+)$/', $key);
                }, ARRAY_FILTER_USE_KEY);
            
                foreach ($rates as $key => $value) {
                    if (preg_match('/^' . preg_quote($prefix) . '(\d+)$/', $key, $matches)) {
                        $corporateFairBillingData = [
                            'hotel_id' => $hotel->id,
                            'vehicle_class_id' => $matches[1],
                            'billing_type' => $billingType,
                            'amount' => $value
                        ];
            
                        $corporateFairBilling = $this->corporateFairBillingRepository->addCorporateFairBilling($corporateFairBillingData);
            
                        $this->activityLogService->addActivityLog(
                            'create',
                            CorporateFairBilling::class,
                            json_encode([]),
                            json_encode($corporateFairBillingData),
                            $log_headers['headers']['Origin'],
                            $log_headers['headers']['User-Agent']
                        );
                    }
                }
            }

            DB::commit();
            return $hotel;
        } catch (\Exception $e) {
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
    }
    /**
     * Delete one or more hotels based on the provided data.
     *
     * Deletes one or more hotels from the database based on the provided request data.
     * If successful, commits the transaction and returns true.
     * If an error occurs during the process, rolls back the transaction and throws an exception with an error message.
     *
     * @param array $requestData An associative array containing data for deleting hotels.
     *                           Required key: 'hotel_ids' (an array of hotel IDs to be deleted).
     *
     * @return bool Returns true if the hotels are successfully deleted.
     *
     * @throws \Exception If an error occurs during the process.
     */
    public function deleteHotels($requestData, $log_headers)
    {
        DB::beginTransaction();
        try {
            $oldData = $this->hotelRepository->getHotelByIds($requestData['hotel_ids']);
            // Delete hotel(s) from the database
            $this->hotelRepository->deleteHotel($requestData['hotel_ids']);
            $this->activityLogService->addActivityLog('delete', Hotel::class, json_encode($oldData), json_encode([]), $log_headers['headers']['Origin'], $log_headers['headers']['User-Agent']);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            // Rollback the transaction if an error occurs
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
    }
    /**
     * Update the status of multiple hotels in bulk based on the provided data.
     *
     * Updates the status of multiple hotels in bulk based on the provided request data.
     * If successful, commits the transaction and returns the updated user model instance.
     * If an error occurs during the process, rolls back the transaction and throws an exception with an error message.
     *
     * @param array $requestData An associative array containing data for updating hotel statuses in bulk.
     *                           Required keys: 'hotel_ids' (an array of hotel IDs to be updated),
     *                           'status' (the new status to be set for the hotels).
     *
     * @return \App\Models\Hotel Returns the updated hotel model instance.
     *
     * @throws \Exception If an error occurs during the process.
     */
    public function updateBulkStatus($requestData, $log_headers)
    {
        DB::beginTransaction();
        try {
            $loggedUserId = Auth::user()->id;
            $hotelIds = $requestData['hotel_ids'];
            $status = $requestData['status'];
            $oldData = $this->hotelRepository->getHotelByIds($hotelIds, $status);
            $user = $this->hotelRepository->updateBulkStatus($hotelIds, $status, $loggedUserId);
            $this->activityLogService->addActivityLog('updateBulkStatus', Hotel::class, json_encode($oldData), json_encode($requestData), $log_headers['headers']['Origin'], $log_headers['headers']['User-Agent']);
            DB::commit();
            return $user;
        } catch (\Exception $e) {
            // Rollback the transaction if an error occurs
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
    }
    /**
     * Retrieve hotel data.
     *
     * Retrieves hotel data from the hotel repository.
     * If successful, returns the hotel data.
     * If an error occurs during the process, throws an exception with an error message.
     *
     * @return mixed Returns the retrieved hotel data.
     *
     * @throws \Exception If an error occurs during the process.
     */
    public function getHotels()
    {
        try {
            $user = $this->hotelRepository->getHotelData();
            return $user;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
    public function getClientAdmins()
    {
        try {
            return  $this->hotelRepository->getHotelClientAdmins();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function getActiveHotels()
    {
        try {
            // Retrieve all hotels from the repository
            return $this->hotelRepository->getActiveHotelsData();
        } catch (\Exception $e) {
            // Throw an exception with the error message if an error occurs
            throw new \Exception($e->getMessage());
        }
    }
}
