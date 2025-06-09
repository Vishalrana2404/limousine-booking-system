<?php

namespace App\Services;

use App\CustomHelper;
use App\Models\Booking;
use App\Models\UserType;
use App\Repositories\Interfaces\BookingBillingInterface;
use App\Repositories\Interfaces\BookingInterface;
use App\Repositories\Interfaces\BookingLogInterface;
use App\Repositories\Interfaces\ClientInterface;
use App\Repositories\Interfaces\DriverInterface;
use App\Repositories\Interfaces\VehicleClassInterface;
use App\Repositories\Interfaces\LocationInterface;
use App\Repositories\Interfaces\UserInterface;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 *
 * BookingService class
 * 
 */
class BookingService
{

    protected $chatIds;
    /**
     * BookingService constructor.
     *
     * @param BookingInterface $bookingRepository The user repository instance.
     * @param Auth $auth The authentication instance.
     */
    public function __construct(
        private BookingInterface $bookingRepository,
        private BookingBillingInterface $bookingBillingRepository,
        private VehicleClassInterface $vehicleClassRepository,
        private ClientInterface $clientRepository,
        private UploadService $uploadService,
        private LocationInterface $locationRepository,
        private DriverInterface $driverRepository,
        private TelegramService $telegramService,
        private BookingLogService $bookingLogService,
        private NotificationService $notificationService,
        private UserInterface $userRepository,
        private CustomHelper $helper,
        private ActivityLogService $activityLogService,
        private BookingLogInterface $bookingLogRepository,
        private Auth $auth,
    ) {
    }

    /**
     * Retrieve booking data based on provided parameters.
     *
     * Retrieves booking data based on parameters such as page number, search query,
     * sorting field, and sorting direction. Uses default values if parameters are not provided.
     *
     * @param array $requestData An associative array containing optional parameters:
     *                           - 'page': The page number for pagination.
     *                           - 'search': The search query to filter bookings.
     *                           - 'sortField': The field to sort bookwings by.
     *                           - 'sortDirection': The direction for sorting ('asc' or 'desc').
     * @return \Illuminate\Pagination\LengthAwarePaginator A paginated list of bookings.
     * @throws \Exception If an error occurs during the retrieval process.
     */
    public function getBookingData(array $requestData = [])
    {
        try {
            // Retrieve the logged-in user
            $loggedUser = Auth::user();
            // Extract parameters from the request data or use default values
            $page = $requestData['page'] ?? 1;
            $search = $requestData['search'] ?? '';
            $searchByBookingId = $requestData['searchByBookingId'] ?? '';
            $sortField = $requestData['sortField'] ?? 'id';
            $sortDirection = $requestData['sortDirection'] ?? 'desc';
            $pickupDateRange = $requestData['pickupDateRange'] ?? null;
            $driverId = $requestData['driverId'] ?? null;
            $currentDate = Carbon::now()->toDateString(); // Get current date in MySQL format
            $loggedUser = Auth::user();
            $userTypeSlug = $loggedUser->userType->slug ?? null;
            if($userTypeSlug === 'client-staff' ||  $userTypeSlug === 'client-admin')
            {
                if ($pickupDateRange) {
                    $dates = explode("-", $pickupDateRange);
                    $startDate = Carbon::createFromFormat('d/m/Y H:i', trim($dates[0]))->format('Y-m-d H:i:s');
                    $endDate = Carbon::createFromFormat('d/m/Y H:i', trim($dates[1]))->format('Y-m-d H:i:s');
                } else {
                    $startDate = $currentDate;
                    $endDate = Carbon::now()->addDays(30)->startOfDay()->toDateTimeString();
                }
            }else{
                if ($pickupDateRange) {
                    $dates = explode("-", $pickupDateRange);
                    $startDate = Carbon::createFromFormat('d/m/Y H:i', trim($dates[0]))->format('Y-m-d H:i:s');
                    $endDate = Carbon::createFromFormat('d/m/Y H:i', trim($dates[1]))->format('Y-m-d H:i:s');
                } else {
                    $startDate = $currentDate;
                    $endDate = Carbon::now()->addDay()->startOfDay()->addHours(4)->toDateTimeString(); // Set end date to tomorrow at 4 AM
                }
            }

            if(!empty($searchByBookingId))
            {
                $startDate = "";
                $endDate = "";    
            }
            return $this->bookingRepository->getBookings($loggedUser, $startDate, $endDate, $search, $searchByBookingId, $page, $sortField, $sortDirection, $driverId);
        } catch (\Exception $e) {
            // Throw an exception with the error message if an error occurs
            throw new \Exception($e->getMessage());
        }
    }

    
    public function getBookingArchiveData(array $requestData = [])
    {
        try {
            // Retrieve the logged-in user
            $loggedUser = Auth::user();
            // Extract parameters from the request data or use default values
            $page = $requestData['page'] ?? 1;
            $search = $requestData['search'] ?? '';
            $sortField = $requestData['sortField'] ?? 'id';
            $sortDirection = $requestData['sortDirection'] ?? 'desc';
            $pickupDateRange = $requestData['pickupDateRange'] ?? null;
            $driverId = $requestData['driverId'] ?? null;
            $currentDate = Carbon::now()->toDateString(); // Get current date in MySQL format
            $loggedUser = Auth::user();
            $userTypeSlug = $loggedUser->userType->slug ?? null;
            if($userTypeSlug === 'client-staff' ||  $userTypeSlug === 'client-admin')
            {
                if ($pickupDateRange) {
                    $dates = explode("-", $pickupDateRange);
                    $startDate = Carbon::createFromFormat('d/m/Y H:i', trim($dates[0]))->format('Y-m-d H:i:s');
                    $endDate = Carbon::createFromFormat('d/m/Y H:i', trim($dates[1]))->format('Y-m-d H:i:s');
                } else {
                    // $startDate = $currentDate;
                    // $endDate = Carbon::now()->addDays(30)->startOfDay()->toDateTimeString();
                    $startDate = Carbon::create(2000, 1, 1, 0, 0, 0)->startOfDay()->toDateTimeString();
                    $endDate = Carbon::create(2000, 1, 1, 0, 0, 0)->addYears(100)->endOfDay()->toDateTimeString();
                }
            }else{
                if ($pickupDateRange) {
                    $dates = explode("-", $pickupDateRange);
                    $startDate = Carbon::createFromFormat('d/m/Y H:i', trim($dates[0]))->format('Y-m-d H:i:s');
                    $endDate = Carbon::createFromFormat('d/m/Y H:i', trim($dates[1]))->format('Y-m-d H:i:s');
                } else {
                    // $startDate = $currentDate;
                    // $endDate = Carbon::now()->addDay()->startOfDay()->addHours(4)->toDateTimeString(); // Set end date to tomorrow at 4 AM
                    $startDate = Carbon::create(2000, 1, 1, 0, 0, 0)->startOfDay()->toDateTimeString();
                    $endDate = Carbon::create(2000, 1, 1, 0, 0, 0)->addYears(100)->endOfDay()->toDateTimeString();
                }
            }
            return $this->bookingRepository->getBookingsArchive($loggedUser, $startDate, $endDate, $search, $page, $sortField, $sortDirection, $driverId);
        } catch (\Exception $e) {
            // Throw an exception with the error message if an error occurs
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Create a new booking.
     *
     * Creates a new booking based on the provided request data.
     * Validates user type, vehicle availability, and other conditions to determine the booking status.
     * Prepares and saves the booking data using the booking repository.
     *
     * @param array $requestData An associative array containing the booking data:
     *                           - 'service_type_id': The ID of the service type for the booking.
     *                           - 'pick_up_location_id': The ID of the pickup location.
     *                           - 'drop_off_location_id': The ID of the drop off location.     * 
     *                           - 'pick_up_location': The name of the pickup location.
     *                           - 'drop_of_location': The name of the drop-off location.
     *                           - 'vehicle_type_id': The ID of the vehicle type for the booking.
     *                           - 'pickup_date': The date of pickup.
     *                           - 'pickup_time': The time of pickup.
     *                           - 'departure_time': The departure time.
     *                           - 'flight_detail': Details of the flight (if applicable).
     *                           - 'no_of_hours': The number of hours for the booking.
     *                           - 'country_code': The country code for the phone number.
     *                           - 'phone': The phone number for contact.
     *                           - 'total_pax': The total number of passengers.
     *                           - 'total_luggage': The total number of luggage.
     *                           - 'client_instructions': Instructions provided by the client.
     *                           - 'guest_name': An array of guest names.
     * @return \App\Models\Booking The created booking instance.
     * @throws \Exception If an error occurs during the creation process.
     */
    public function createBooking(array $requestData, ?UploadedFile $file)
    {
        DB::beginTransaction();
        try {
            // Get the logged-in user's
            $loggedUser = Auth::user();
            // Prepare the booking data
            $bookingData = [];
            $clientId = null;
            if (isset($requestData['client_id']) && !empty($requestData['client_id'])) {
                $clientId = $requestData['client_id'];
            } elseif ($loggedUser->userType->slug === 'client-staff' || $loggedUser->userType->slug === 'client-admin') {
                $clientId = $loggedUser->client->id;
            }
            $eventId = null;
            if (isset($requestData['event_id']) && !empty($requestData['event_id'])) {
                $eventId = $requestData['event_id'];
            }
            $status = Booking::PENDING;

            $guestname = join(',', $requestData['guest_name']);
            $countryCode = join(',', $requestData['country_code']);
            $phone = join(',', $requestData['phone']);
            // $additional_stops = !empty($requestData['additional_stops']) 
            // ? join('||', array_filter($requestData['additional_stops'])) 
            // : '';
            $bookingData['client_id'] = $clientId;
            $bookingData['event_id'] = $eventId;
            $bookingData['service_type_id'] = $requestData['service_type_id'];
            $bookingData['pick_up_location_id'] = $requestData['pick_up_location_id'];
            $bookingData['drop_off_location_id'] = $requestData['drop_off_location_id'];

            $bookingData['pick_up_location'] = $requestData['pick_up_location'];
            $bookingData['drop_of_location'] = $requestData['drop_of_location'];
            $bookingData['vehicle_type_id'] = $requestData['vehicle_type_id'];
            $bookingData['pickup_date'] = $requestData['pickup_date'] ?  Carbon::createFromFormat('d/m/Y', $requestData['pickup_date'])->format('Y-m-d') : null;
            if(isset($requestData['pickup_time_to_be_advised']))
            {
                $bookingData['to_be_advised_status'] = 'yes';
                $bookingData['pickup_time'] = '00:00:00';
            }else{
                $bookingData['pickup_time'] = $requestData['pickup_time'] ? Carbon::createFromFormat('H:i', $requestData['pickup_time'])->format('H:i:s') : null;
            }
            $bookingData['departure_time'] = ((int)$requestData['service_type_id'] === 3) ? ($requestData['departure_time'] ? Carbon::createFromFormat('d/m/Y H:i', $requestData['departure_time'])->format('Y-m-d H:i:s') : null) : null;
            $bookingData['flight_detail'] = (isset($requestData['flight_detail']) && !empty($requestData['flight_detail'])) ? $requestData['flight_detail'] : null;
            $bookingData['no_of_hours'] = ((int)$requestData['service_type_id'] === 4)  ? ($requestData['no_of_hours'] ? $requestData['no_of_hours'] : null) : null;
            $bookingData['country_code'] = $countryCode;
            $bookingData['phone'] = $phone;
            $bookingData['total_pax'] = ((int)$requestData['service_type_id'] !== 5)  ? ($requestData['total_pax']  ?  $requestData['total_pax']  : null) : null;
            $bookingData['total_luggage'] = ((int)$requestData['service_type_id'] !== 5)  ? ($requestData['total_luggage']  ?  $requestData['total_luggage']  : 0) : 0;
            $bookingData['client_instructions'] = $requestData['client_instructions'];
            $bookingData['is_cross_border'] = (isset($requestData['is_cross_border']) && !empty($requestData['is_cross_border'])) ? 1 : 0;
            $bookingData['child_seat_required'] = (isset($requestData['child_seat_required']) && !empty($requestData['child_seat_required'])) ? $requestData['child_seat_required'] : 'no';
            if($bookingData['child_seat_required'] == 'yes')
            {
                $bookingData['no_of_seats_required'] = $requestData['no_of_seats_required'];

                if($bookingData['no_of_seats_required'] == 1)
                {
                    $bookingData['child_1_age'] = $requestData['child_1_age'];
                    $bookingData['child_2_age'] = NULL;
                }else{
                    $bookingData['child_1_age'] = $requestData['child_1_age'];
                    $bookingData['child_2_age'] = $requestData['child_2_age'];
                }
            }else{
                $bookingData['no_of_seats_required'] = NULL;
                $bookingData['child_1_age'] = NULL;
                $bookingData['child_2_age'] = NULL;
            }
            $bookingData['meet_and_greet'] = $requestData['meet_and_greet'];
            $bookingData['guest_name'] = $guestname;
            // $bookingData['additional_stops'] = $additional_stops;
            $bookingData['status'] = $status;
            $bookingData['created_by_id'] = $loggedUser->id;
            if ($file && $file->isValid()) {
                $folderName = 'bookings';
                $this->uploadService->setPath($folderName);
                $this->uploadService->createDirectory();
                $fileName = time() . '.' . $file->extension();
                // Upload the new profile image and update user data
                $bookingData['attachment'] = $this->uploadService->upload($file, $fileName);
            }

            $bookingData['additional_stops_required'] = $requestData['additional_stops_required'];
            // Add the booking using the booking repository
            $booking = $this->bookingRepository->addBooking($bookingData);

            // additional stops
            if($requestData['additional_stops_required'] == 'yes')
            {
                if(!empty($requestData['additional_stops']))
                {
                    $addAdditionalStops = $this->bookingRepository->addAdditionalStops($requestData['additional_stops'], $requestData['pickup_dropoff'], $booking->id, $loggedUser->id);
                }
            }
            $message="Created";
            $logData = ["message" => $message, "booking_id" => $booking->id, "user_id" => $loggedUser->id];
            $this->bookingLogRepository->addLogs($logData);
            DB::commit();
            $userTypeSlug = $loggedUser->userType->slug ?? null;
            if ($userTypeSlug === 'client-staff' ||  $userTypeSlug === 'client-admin') {
                $this->sendMessageToOpsTeam($booking, $loggedUser);
                $this->createBookingNotification($loggedUser, $booking, $userTypeSlug);
                $this->createBookingNotificationToPOCandHeadOffice($booking, $loggedUser);
            }
            return $booking;
        } catch (\Exception $e) {
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
    }
    /**
     * Create multiple new bookings.
     *
     * Creates multiple new bookings based on the provided request data.
     * Validates user type, vehicle availability, and other conditions to determine the booking status for each booking.
     * Prepares and saves the booking data for each booking using the booking repository.
     *
     * @param array $requestData An associative array containing the booking data for each booking:
     *                           - 'multiple_service_type_id': An array of service type IDs for each booking.
     *                           - 'multiple_pick_up_location_id': An array of pickup location IDs for each booking.
     *                           - 'multiple_pick_up_location': An array of pickup locations for each booking.
     *                           - 'multiple_drop_of_location': An array of drop-off locations for each booking.
     *                           - 'multiple_vehicle_type_id': An array of vehicle type IDs for each booking.
     *                           - 'multiple_pickup_date': An array of pickup dates for each booking.
     *                           - 'multiple_pickup_time': An array of pickup times for each booking.
     *                           - 'multiple_departure_time': An array of departure times for each booking.
     *                           - 'multiple_flight_detail': An array of flight details for each booking.
     *                           - 'multiple_no_of_hours': An array of numbers of hours for each booking.
     *                           - 'multiple_country_code': An array of country codes for each booking.
     *                           - 'multiple_phone': An array of phone numbers for each booking.
     *                           - 'multiple_total_pax': An array of total numbers of passengers for each booking.
     *                           - 'multiple_total_luggage': An array of total numbers of luggage for each booking.
     *                           - 'multiple_client_instructions': An array of client instructions for each booking.
     *                           - 'multiple_guest_name': An array of guest names for each booking.
     * @return \App\Models\Booking The last created booking instance.
     * @throws \Exception If an error occurs during the creation process.
     */
    public function createMultipleBooking(array $requestData, ?array $files = null)
    {

        DB::beginTransaction();
        try {
            // Get the logged-in user's ID
            $loggedUser = Auth::user();
            // Prepare the booking data

            foreach ($requestData['multiple_pickup_date'] as $key => $data) {
                $bookingData = [];
                $clientId = null;
                if (isset($requestData['multiple_client_id'][$key]) && !empty($requestData['multiple_client_id'][$key])) {
                    $clientId = $requestData['multiple_client_id'][$key];
                } elseif ($loggedUser->userType->slug === 'client-staff' || $loggedUser->userType->slug === 'client-admin') {
                    $clientId = $loggedUser->client->id;
                }
                $eventId = null;
                if (isset($requestData['multiple_event_id'][$key]) && !empty($requestData['multiple_event_id'][$key])) {
                    $eventId = $requestData['multiple_event_id'][$key];
                }
                $status = Booking::PENDING;
                $guestname = join(',', $requestData['multiple_guest_name'][$key]);
                $countryCodes = join(',', $requestData['multiple_country_code'][$key]);
                $phones = join(',', $requestData['multiple_phone'][$key]);

                $bookingData['country_code'] = $countryCodes;
                $bookingData['phone'] = $phones;
                // $additionalStops = !empty($requestData['multiple_additional_stops'][$key]) 
                //                         ? join('||', array_filter($requestData['multiple_additional_stops'][$key])) 
                //                         : '';
                $bookingData['client_id'] = $clientId;
                $bookingData['event_id'] = $eventId;
                $bookingData['service_type_id'] = $requestData['multiple_service_type_id'][$key] ?? null;
                $bookingData['pick_up_location_id'] =  $requestData['multiple_pick_up_location_id'][$key] ?? null;
                $bookingData['pick_up_location'] = $requestData['multiple_pick_up_location'][$key] ?? null;
                $bookingData['drop_off_location_id'] = $requestData['multiple_drop_off_location_id'][$key] ?? null;
                $bookingData['drop_of_location'] = $requestData['multiple_drop_of_location'][$key] ?? null;
                $bookingData['vehicle_type_id'] = $requestData['multiple_vehicle_type_id'][$key] ?? null;


                $bookingData['pickup_date'] = $requestData['multiple_pickup_date'][$key] ? Carbon::createFromFormat('d/m/Y', $requestData['multiple_pickup_date'][$key])->format('Y-m-d') : null;
                if(isset($requestData['multiple_pickup_time_to_be_advised'][$key]))
                {
                    $bookingData['to_be_advised_status'] = 'yes';
                    $bookingData['pickup_time'] = '00:00:00';
                }else{
                    $bookingData['pickup_time'] = $requestData['multiple_pickup_time'][$key] ? Carbon::createFromFormat('H:i', $requestData['multiple_pickup_time'][$key])->format('H:i:s') : null;
                }
                $bookingData['departure_time'] = ((int)$requestData['multiple_service_type_id'][$key] === 3) ? ($requestData['multiple_departure_time'][$key] ? Carbon::createFromFormat('d/m/Y H:i', $requestData['multiple_departure_time'][$key])->format('Y-m-d H:i:s') : null) : null;
                $bookingData['flight_detail'] = $requestData['multiple_flight_detail'][$key] ?? null;
                $bookingData['no_of_hours'] =  ((int)$requestData['multiple_service_type_id'][$key] === 4) ? ($requestData['multiple_no_of_hours'][$key] ? $requestData['multiple_no_of_hours'][$key] : null) : null;
                $bookingData['total_pax'] =  ((int)$requestData['multiple_service_type_id'][$key] !== 5) ? ($requestData['multiple_total_pax'][$key] ? $requestData['multiple_total_pax'][$key] : null) : null;

                $bookingData['total_luggage'] =  ((int)$requestData['multiple_service_type_id'][$key] !== 5) ? ($requestData['multiple_total_luggage'][$key] ? $requestData['multiple_total_luggage'][$key] : 0) : 0;
                $bookingData['client_instructions'] = $requestData['multiple_client_instructions'][$key] ?? null;
                $bookingData['is_cross_border'] = $requestData['multiple_is_cross_border'][$key] ?? 0;
                $bookingData['child_seat_required'] = $requestData['multiple_child_seat_required'][$key] ?? 'no';

                if($bookingData['child_seat_required'] == 'yes')
                {
                    $bookingData['no_of_seats_required'] = $requestData['multiple_no_of_seats_required'][$key] ?? NULL;

                    if($bookingData['no_of_seats_required'] == 1)
                    {
                        $bookingData['child_1_age'] = $requestData['multiple_child_1_age'][$key] ?? NULL;
                        $bookingData['child_2_age'] = NULL;
                    }else{                        
                        $bookingData['child_1_age'] = $requestData['multiple_child_1_age'][$key] ?? NULL;
                        $bookingData['child_2_age'] = $requestData['multiple_child_2_age'][$key] ?? NULL;;
                    }
                }else{
                    $bookingData['no_of_seats_required'] = NULL;
                    $bookingData['child_1_age'] = NULL;
                    $bookingData['child_2_age'] = NULL;
                }
                $bookingData['meet_and_greet'] = $requestData['multiple_meet_and_greet'][$key] ?? NULL;
                $bookingData['guest_name'] = $guestname;
                // $bookingData['additional_stops'] = $additionalStops;
                $bookingData['status'] = $status;
                $bookingData['created_by_id'] = $loggedUser->id;
                if (isset($files[$key]) && $files[$key]->isValid()) {
                    $folderName = 'bookings';
                    $this->uploadService->setPath($folderName);
                    $this->uploadService->createDirectory();
                    $fileName = time() . '_' . $key . '.' . $files[$key]->extension();
                    // Upload the file and update booking data
                    $bookingData['attachment'] = $this->uploadService->upload($files[$key], $fileName);
                }

                $bookingData['additional_stops_required'] = $requestData['additional_stops_required'][$key];
                // Add the booking using the booking repository
                $booking = $this->bookingRepository->addBooking($bookingData);

                // additional stops
                if($requestData['additional_stops_required'][$key] == 'yes')
                {
                    if(!empty($requestData['multiple_additional_stops'][$key]))
                    {
                        $addAdditionalStops = $this->bookingRepository->addAdditionalStops($requestData['multiple_additional_stops'][$key], $requestData['multiple_pickup_dropoff'][$key], $booking->id, $loggedUser->id);
                    }
                }
                $message="Created";
                $logData = ["message" => $message, "booking_id" => $booking->id, "user_id" => $loggedUser->id];
                $this->bookingLogRepository->addLogs($logData);
                $userTypeSlug = $loggedUser->userType->slug ?? null;
                if ($userTypeSlug === 'client-staff' ||  $userTypeSlug === 'client-admin') {
                    $this->sendMessageToOpsTeam($booking, $loggedUser);
                    $this->createBookingNotification($loggedUser, $booking, $userTypeSlug);
                    $this->createBookingNotificationToPOCandHeadOffice($booking, $loggedUser);
                }
            }
            DB::commit();
            return $booking;
        } catch (\Exception $e) {
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
    }

    public function updateBooking(array $requestData, Booking $booking, ?UploadedFile $file, array $logHeaders)
    {
        try {
            DB::beginTransaction();
            $loggedUserForNotification = Auth::user();
            $loggedUserId = Auth::user()->id;
            $loggedUserType = Auth::user()->userType->name ?? null;
            $userTypeSlug = Auth::user()->userType->slug ?? null;
    
            $clientId = null;
            if (isset($requestData['client_id']) && !empty($requestData['client_id'])) {
                $clientId = $requestData['client_id'];
            } elseif ($userTypeSlug === 'client-staff' || $userTypeSlug === 'client-admin') {
                $clientId = $loggedUserForNotification->client->id;
            }
    
            $serviceTypeId = $requestData['service_type_id'] ?? null;
            // $additional_stops = !empty($requestData['additional_stops']) 
            //                     ? join('||', array_filter($requestData['additional_stops'])) 
            //                     : '';
    
            $linkedClients = NULL;
            $linkedClients = join(',', $requestData['access_given_clients']);
    
            if (isset($requestData['access_given_clients']))
                $bookingData['linked_clients'] = $linkedClients;
            if (isset($requestData['status'])) {
                $canUpdateStatus = false;

                $isAdminOrStaff = is_null($userTypeSlug) || in_array($userTypeSlug, ['admin', 'admin-admin']);
                $isInAllowedDepartment = in_array($loggedUserForNotification->department, [null, 'Management', 'Finance']);
                $isNotCancelledWithCharges = $booking->status !== 'CANCELLED WITH CHARGES';

                if ($isAdminOrStaff && $isInAllowedDepartment) {
                    $canUpdateStatus = true;
                } elseif ($isNotCancelledWithCharges) {
                    $canUpdateStatus = true;
                }

                if ($canUpdateStatus) {
                    $bookingData['status'] = $requestData['status'];
                }
            }
            if (isset($requestData['pickup_date']))
                $bookingData['pickup_date'] =  Carbon::createFromFormat('d/m/Y', $requestData['pickup_date'])->format('Y-m-d');
            if(isset($requestData['pickup_time_to_be_advised']))
            {
                $bookingData['to_be_advised_status'] = 'yes';
                $bookingData['pickup_time'] = '00:00:00';
            }else{
                $bookingData['to_be_advised_status'] = 'no';
                if (isset($requestData['pickup_time'])){
                    $bookingData['pickup_time'] = Carbon::createFromFormat('H:i', $requestData['pickup_time'])->format('H:i:s');
                }
            }
            $eventId = null;
            if (isset($requestData['event_id']) && !empty($requestData['event_id'])) {
                $eventId = $requestData['event_id'];
            }else{
                $eventId = NULL;
            }
            $guestname = join(',', $requestData['guest_name']);
            $countryCode = join(',', $requestData['country_code']);
            $phone = join(',', $requestData['phone']);
    
            $bookingData['event_id'] = $eventId;
            if (isset($requestData['client_id']))
                $bookingData['client_id'] = $clientId;
            if (isset($requestData['service_type_id']))
                $bookingData['service_type_id'] = $serviceTypeId;
            if (isset($requestData['flight_detail'])){
                $bookingData['flight_detail'] = $requestData['flight_detail'];
            }else{
                $bookingData['flight_detail'] = null;
            }
            if (isset($requestData['departure_time']))
                $bookingData['departure_time'] = Carbon::createFromFormat('d/m/Y H:i', $requestData['departure_time'])->format('Y-m-d H:i:s');
            if (isset($requestData['trip_ended']))
                $bookingData['trip_ended'] =  Carbon::createFromFormat('d/m/Y H:i', $requestData['trip_ended'])->format('Y-m-d H:i:s');
            if (isset($requestData['is_cross_border']))
                $bookingData['is_cross_border'] = $requestData['is_cross_border'];
            if (isset($requestData['no_of_hours']))
                $bookingData['no_of_hours'] = $requestData['no_of_hours'];
            if (isset($requestData['guest_name']))
                $bookingData['guest_name'] = $guestname;
            if (isset($requestData['country_code']))
                $bookingData['country_code'] = $countryCode;
            if (isset($requestData['phone']))
                $bookingData['phone'] = $phone;
            if (isset($requestData['total_pax']))
                $bookingData['total_pax'] = $requestData['total_pax'];
            if (isset($requestData['total_luggage']))
                $bookingData['total_luggage'] = $requestData['total_luggage'];
            if (isset($requestData['pick_up_location_id']))
                $bookingData['pick_up_location_id'] = $requestData['pick_up_location_id'];
            if (isset($requestData['pick_up_location']))
                $bookingData['pick_up_location'] = $requestData['pick_up_location'];
            if (isset($requestData['drop_off_location_id']))
                $bookingData['drop_off_location_id'] = $requestData['drop_off_location_id'];
            if (isset($requestData['drop_of_location']))
                $bookingData['drop_of_location'] = $requestData['drop_of_location'];
            if (isset($requestData['vehicle_type_id']))
                $bookingData['vehicle_type_id'] = $requestData['vehicle_type_id'];
            if (isset($requestData['driver_id'])) {
                $bookingData['driver_id'] = $requestData['driver_id'];
                $driverData = $this->driverRepository->getDriverById($bookingData['driver_id']);
                if ($booking->driver_id && $booking->driver_id !== (int)$requestData['driver_id']) {
                    if ($booking->is_driver_notified) {
                        $this->removeDriverNotification($booking);
                    }
                    // $this->addDriverNotification($booking, $driverData);
                    $bookingData['is_driver_notified'] = 0;
                    $bookingData['is_driver_acknowledge'] = 0;
                    $bookingData['status'] = Booking::PENDING;
                }
                //  elseif (!$booking->driver_id) {
                //     $this->addDriverNotification($booking, $driverData);
                // }
                if (isset($requestData['vehicle_id'])){
                    $bookingData['vehicle_id'] = $requestData['vehicle_id'];
                }else{
                    $bookingData['vehicle_id'] = NULL;
                }
            }else{
                $bookingData['driver_id'] = NULL;
                $bookingData['vehicle_id'] = NULL;
            }
            if(($userTypeSlug === 'client-staff' ||  $userTypeSlug === 'client-admin'))
            {
                $bookingData['status'] = Booking::PENDING;
            }
            if (isset($requestData['driver_contact'])){
                $bookingData['driver_contact'] = $requestData['driver_contact'];
            }else{
                $bookingData['driver_contact'] = NULL;
            }
            if (isset($requestData['client_instructions']))
                $bookingData['client_instructions'] = $requestData['client_instructions'];
    
            $bookingData['child_seat_required'] = (isset($requestData['child_seat_required']) && !empty($requestData['child_seat_required'])) ? $requestData['child_seat_required'] : 'no';
            if($bookingData['child_seat_required'] == 'yes')
            {
                $bookingData['no_of_seats_required'] = $requestData['no_of_seats_required'];
    
                if($bookingData['no_of_seats_required'] == 1)
                {
                    $bookingData['child_1_age'] = $requestData['child_1_age'];
                    $bookingData['child_2_age'] = NULL;
                }else{
                    $bookingData['child_1_age'] = $requestData['child_1_age'];
                    $bookingData['child_2_age'] = $requestData['child_2_age'];
                }
            }else{
                $bookingData['no_of_seats_required'] = NULL;
                $bookingData['child_1_age'] = NULL;
                $bookingData['child_2_age'] = NULL;
            }
            $bookingData['meet_and_greet'] = $requestData['meet_and_greet'];
    
            // $bookingData['additional_stops'] = $additional_stops;
    
            
            if (isset($requestData['latest_comment']))
            {
                $bookingData['latest_comment'] = $requestData['latest_comment'];
                $this->bookingRepository->addBookingComment($bookingData['latest_comment'], $booking->id, $loggedUserId);
            }
            
            if (isset($requestData['latest_admin_comment']))
            {
                $bookingData['latest_admin_comment'] = $requestData['latest_admin_comment'];
                $this->bookingRepository->addBookingAdminComment($bookingData['latest_admin_comment'], $booking->id, $loggedUserId);
            }
    
            $bookingData['additional_stops_required'] = $requestData['additional_stops_required'];
    
            $additionalStopsLogs = [];

            // additional stops
            if($booking->additional_stops_required == 'yes')
            {
                if($requestData['additional_stops_required'] == 'no')
                {
                    $additionalStopsLogs = $this->bookingRepository->deleteAdditionalStops($booking->id, $loggedUserId);
                }else{
                    if(!empty($requestData['additional_stops']))
                    {
                        $additionalStopsLogs = $this->bookingRepository->editAdditionalStops($requestData['additional_stops'], $requestData['pickup_dropoff'], $booking, $loggedUserId);
                    }
                }
            }else{
                if($requestData['additional_stops_required'] == 'yes')
                {
                    $additionalStopsLogs = $this->bookingRepository->addAdditionalStops($requestData['additional_stops'], $requestData['pickup_dropoff'], $booking->id, $loggedUserId);
                }
            }
    
            if ($file && $file->isValid()) {
                $folderName = 'bookings';
                $this->uploadService->setPath($folderName);
                $this->uploadService->createDirectory();
                $fileName = time() . '.' . $file->extension();
                if ($booking->attachment) {
                    Storage::disk('public')->delete($booking->attachment);
                }
                // Upload the new profile image and update user data
                $bookingData['attachment'] = $this->uploadService->upload($file, $fileName);
            }

            $logMessage = $this->bookingLogService->addLogMessages($bookingData, $booking, Auth::user(), $requestData['access_given_clients'], $additionalStopsLogs);
            $bookingData['updated_by_id'] = $loggedUserId;
            
            $this->bookingRepository->updateBooking($booking, $bookingData);
    
            if ($loggedUserType === null || $loggedUserType === UserType::ADMIN) {
                $bookingBillingData['booking_id'] = $booking->id;
    
                $isPeakPeriod =  (isset($requestData['is_peak_period_surcharge']) && !empty($requestData['is_peak_period_surcharge'])) ? 1 : 0;
                $isMidNight =  (isset($requestData['is_mid_night_surcharge']) && !empty($requestData['is_mid_night_surcharge'])) ? 1 : 0;
                $isWaitingTime =  (isset($requestData['is_arr_waiting_time_surcharge']) && !empty($requestData['is_arr_waiting_time_surcharge'])) ? 1 : 0;
                $isOutsideCity = (isset($requestData['is_outside_city_surcharge']) && !empty($requestData['is_outside_city_surcharge'])) ? 1 : 0;
                $isLastminute = (isset($requestData['is_last_minute_surcharge']) && !empty($requestData['is_last_minute_surcharge'])) ? 1 : 0;
                $isAdditionalStop = (isset($requestData['is_additional_stop_surcharge']) && !empty($requestData['is_additional_stop_surcharge'])) ? 1 : 0;
                $isMisc =  (isset($requestData['is_misc_surcharge'])  && !empty($requestData['is_misc_surcharge'])) ? 1 : 0;
                if (isset($requestData['arrival_charge']) && $serviceTypeId == 1)
                    $bookingBillingData['arrival_charge'] = $requestData['arrival_charge'];
                if (isset($requestData['transfer_charge']) && $serviceTypeId == 2)
                    $bookingBillingData['transfer_charge'] = $requestData['transfer_charge'];
                if (isset($requestData['departure_charge']) && $serviceTypeId == 3)
                    $bookingBillingData['departure_charge'] = $requestData['departure_charge'];
                if (isset($requestData['disposal_charge']) && $serviceTypeId == 4)
                    $bookingBillingData['disposal_charge'] = $requestData['disposal_charge'];
                if (isset($requestData['delivery_charge']) && $serviceTypeId == 5)
                    $bookingBillingData['delivery_charge'] = $requestData['delivery_charge'];
                if (isset($requestData['peak_period_surcharge']) && $isPeakPeriod)
                    $bookingBillingData['peak_period_surcharge'] = $requestData['peak_period_surcharge'];
                if (isset($requestData['is_fixed_midnight_surcharge']) &&  $isMidNight)
                    $bookingBillingData['is_fixed_midnight_surcharge'] = $requestData['is_fixed_midnight_surcharge'] === 'x' ? 0 : 1;
                if (isset($requestData['mid_night_surcharge']) && $isMidNight)
                    $bookingBillingData['mid_night_surcharge'] = $requestData['mid_night_surcharge'];
                if (isset($requestData['is_fixed_arrival_waiting_surcharge']) && $isWaitingTime)
                    $bookingBillingData['is_fixed_arrival_waiting_surcharge'] = $requestData['is_fixed_arrival_waiting_surcharge'] === 'x' ? 0 : 1;
                if (isset($requestData['arrivel_waiting_time_surcharge']) && $isWaitingTime)
                    $bookingBillingData['arrivel_waiting_time_surcharge'] = $requestData['arrivel_waiting_time_surcharge'];
                if (isset($requestData['is_fixed_outside_city_surcharge']) && $isOutsideCity)
                    $bookingBillingData['is_fixed_outside_city_surcharge'] = $requestData['is_fixed_outside_city_surcharge'] === 'x' ? 0 : 1;
                if (isset($requestData['outside_city_surcharge']) && $isOutsideCity)
                    $bookingBillingData['outside_city_surcharge'] = $requestData['outside_city_surcharge'];
                if (isset($requestData['is_fixed_last_minute_surcharge']) && $isLastminute)
                    $bookingBillingData['is_fixed_last_minute_surcharge'] = $requestData['is_fixed_last_minute_surcharge'] === 'x' ? 0 : 1;
                if (isset($requestData['last_minute_surcharge']) && $isLastminute)
                    $bookingBillingData['last_minute_surcharge'] = $requestData['last_minute_surcharge'];
                if (isset($requestData['is_fixed_additional_stop_surcharge']) && $isAdditionalStop)
                    $bookingBillingData['is_fixed_additional_stop_surcharge'] = $requestData['is_fixed_additional_stop_surcharge'] === 'x' ? 0 : 1;
                if (isset($requestData['additional_stop_surcharge']) && $isAdditionalStop)
                    $bookingBillingData['additional_stop_surcharge'] = $requestData['additional_stop_surcharge'];
                if (isset($requestData['is_fixed_misc_surcharge']) && $isMisc)
                    $bookingBillingData['is_fixed_misc_surcharge'] = $requestData['is_fixed_misc_surcharge'] === 'x' ? 1 : 0;
                if (isset($requestData['misc_surcharge']) && $isMisc)
                    $bookingBillingData['misc_surcharge'] = $requestData['misc_surcharge'];
                if (isset($requestData['total_charge']))
                    $bookingBillingData['total_charge'] = $requestData['total_charge'];
                if (isset($requestData['arrival_charge_description']) && !empty($requestData['arrival_charge_description']))
                    $bookingBillingData['arrival_charge_description'] = $requestData['arrival_charge_description'];
                if (isset($requestData['transfer_charge_description']) && !empty($requestData['transfer_charge_description']))
                    $bookingBillingData['transfer_charge_description'] = $requestData['transfer_charge_description'];
                if (isset($requestData['departure_charge_description']) && !empty($requestData['departure_charge_description']))
                    $bookingBillingData['departure_charge_description'] = $requestData['departure_charge_description'];
                if (isset($requestData['disposal_charge_description']) && !empty($requestData['disposal_charge_description']))
                    $bookingBillingData['disposal_charge_description'] = $requestData['disposal_charge_description'];
                if (isset($requestData['delivery_charge_description']) && !empty($requestData['delivery_charge_description']))
                    $bookingBillingData['delivery_charge_description'] = $requestData['delivery_charge_description'];
                if (isset($requestData['peak_period_charge_description']) && !empty($requestData['peak_period_charge_description']))
                    $bookingBillingData['peak_period_charge_description'] = $requestData['peak_period_charge_description'];
                if (isset($requestData['mid_night_charge_description']) && !empty($requestData['mid_night_charge_description']))
                    $bookingBillingData['mid_night_charge_description'] = $requestData['mid_night_charge_description'];
                if (isset($requestData['arrivel_waiting_charge_description']) && !empty($requestData['arrivel_waiting_charge_description']))
                    $bookingBillingData['arrivel_waiting_charge_description'] = $requestData['arrivel_waiting_charge_description'];
                if (isset($requestData['outside_city_charge_description']) && !empty($requestData['outside_city_charge_description']))
                    $bookingBillingData['outside_city_charge_description'] = $requestData['outside_city_charge_description'];
                if (isset($requestData['last_minute_charge_description']) && !empty($requestData['last_minute_charge_description']))
                    $bookingBillingData['last_minute_charge_description'] = $requestData['last_minute_charge_description'];
                if (isset($requestData['additional_charge_description']) && !empty($requestData['additional_charge_description']))
                    $bookingBillingData['additional_charge_description'] = $requestData['additional_charge_description'];
                if (isset($requestData['misc_charge_description']) && !empty($requestData['misc_charge_description']))
                    $bookingBillingData['misc_charge_description'] = $requestData['misc_charge_description'];

                    
                $bookingBillingData['is_discount'] = !empty($requestData['is_discount']) ? '1' : '0';
                if (isset($requestData['discount_type']) && !empty($requestData['discount_type']))
                {
                    $bookingBillingData['discount_type'] = $requestData['discount_type'];
                }
                $bookingBillingData['discount_value'] = !empty($requestData['discount_value']) ? $requestData['discount_value'] : null;

                if (isset($requestData['sub_total_charge']) && !empty($requestData['sub_total_charge']))
                {
                    $bookingBillingData['sub_total_charge'] = $requestData['sub_total_charge'];
                }
                $bookingBillingData['is_mid_night_surcharge'] = $isMidNight;
                $bookingBillingData['is_arr_waiting_time_surcharge'] = $isWaitingTime;
                $bookingBillingData['is_outside_city_surcharge'] = $isOutsideCity;
                $bookingBillingData['is_last_minute_surcharge'] = $isLastminute;
                $bookingBillingData['is_additional_stop_surcharge'] = $isAdditionalStop;
                $bookingBillingData['is_misc_surcharge'] = $isMisc;
                $bookingBillingData['is_peak_period_surcharge'] = $isPeakPeriod;
                $this->bookingBillingRepository->createOrUpdateBookingBillingByBookingId($booking->id, $bookingBillingData, $loggedUserId);
            }
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
    }

    

    public function restoreBooking(int $bookingId, Booking $booking, array $log_headers)
    {
        DB::beginTransaction();
        try {
            $loggedUserId = Auth::user()->id;
            $loggedUserType = Auth::user()->userType->name ?? null;
            $userTypeSlug = Auth::user()->userType->slug ?? null;

            
            $bookingData['deleted_at'] = NULL;
            $bookingData['completely_deleted'] = 'no';
            
            $linkedClients = !empty($booking->linked_clients) ? explode(',', $booking->linked_clients) : [];
            
            $additionalStopsLogs = [];
            $this->bookingLogService->addLogMessages($bookingData, $booking, Auth::user(), $linkedClients, $additionalStopsLogs);

            $bookingData['updated_by_id'] = $loggedUserId;

            $oldData = $this->bookingRepository->getBookingByIdToRestore($bookingId);

            $newData = $this->bookingRepository->restoreBooking($booking, $bookingData);

            $message= "Restored";
            $logData = ["message" => $message, "booking_id" => $bookingId, "user_id" => $loggedUserId];
            $this->bookingLogRepository->addLogs($logData);

            $this->activityLogService->addActivityLog('restore', Booking::class, json_encode($oldData), json_encode($newData), $log_headers['headers']['Origin'], $log_headers['headers']['User-Agent']);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
    }

    

    public function cancelBooking(int $bookingId, Booking $booking, array $log_headers)
    {
        DB::beginTransaction();
        try {
            $loggedUserId = Auth::user()->id;
            $loggedUserType = Auth::user()->userType->name ?? null;
            $userTypeSlug = Auth::user()->userType->slug ?? null;
            
            $bookingData['client_asked_to_cancel'] = 'yes';
            
            $linkedClients = !empty($booking->linked_clients) ? explode(',', $booking->linked_clients) : [];
            
            $additionalStopsLogs = [];
            $this->bookingLogService->addLogMessages($bookingData, $booking, Auth::user(), $linkedClients, $additionalStopsLogs);

            $bookingData['updated_by_id'] = $loggedUserId;

            $oldData = $this->bookingRepository->getBookingByIdToRestore($bookingId);

            $newData = $this->bookingRepository->cancelBooking($booking, $bookingData);

            $message= "Requested For Cancel";
            $logData = ["message" => $message, "booking_id" => $bookingId, "user_id" => $loggedUserId];
            $this->bookingLogRepository->addLogs($logData);

            $this->activityLogService->addActivityLog('cancel', Booking::class, json_encode($oldData), json_encode($newData), $log_headers['headers']['Origin'], $log_headers['headers']['User-Agent']);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
    }

    public function updateDispatch(array $requestData, $logHeaders)
    {
        try {
            $loggedUserId = Auth::user()->id;
            $bookingId = $requestData['booking_id'];
            if ($bookingId) {
                $booking = $this->bookingRepository->getBookingById($bookingId);
                if (isset($requestData['is_driver_notified']) && !empty($requestData['is_driver_notified'])) {
                    $bookingData['is_driver_notified'] = $requestData['is_driver_notified'] === "true" ? 1 : 0;
                    if ($requestData['is_driver_notified'] === "true" && $requestData['is_driver_acknowledge'] === "false")
                        $this->addDriverNotification($booking, $booking->driver);
                }
                if (isset($requestData['is_driver_acknowledge']) && !empty($requestData['is_driver_acknowledge']))
                    $bookingData['is_driver_acknowledge'] = $requestData['is_driver_acknowledge'] === "true" ? 1 : 0;
                if ($bookingData['is_driver_acknowledge'] && $bookingData['is_driver_notified'])
                    $bookingData['status'] = Booking::ACCEPTED;
            
                $linkedClients = !empty($booking->linked_clients) ? explode(',', $booking->linked_clients) : [];

                $additionalStopsLogs = [];
                $this->bookingLogService->addLogMessages($bookingData, $booking, Auth::user(), $linkedClients, $additionalStopsLogs);
                $bookingData['updated_by_id'] = $loggedUserId;
                $this->bookingRepository->updateBooking($booking, $bookingData);
            }
            return $this->bookingRepository->getBookingById($bookingId);
        } catch (\Exception $e) {
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
    }

    public function updateInline(array $requestData, $logHeaders)
    {
        try {
            $loggedUserId = Auth::user()->id;
            $bookingId = $requestData['booking_id'];
            if ($bookingId) {
                $booking = $this->bookingRepository->getBookingById($bookingId);
                if (isset($requestData['internal_remark']) && !empty($requestData['internal_remark']))
                    $bookingData['internal_remark'] = $requestData['internal_remark'];
                if (isset($requestData['guest_name']) && !empty($requestData['guest_name']))
                    $bookingData['guest_name'] = $requestData['guest_name'];
                if (isset($requestData['pickup_date']) && !empty($requestData['pickup_date']))
                    $bookingData['pickup_date'] =   Carbon::createFromFormat('d/m/Y', $requestData['pickup_date'])->format('Y-m-d');
                if (isset($requestData['pickup_time']) && !empty($requestData['pickup_time'])){
                    $bookingData['pickup_time'] = Carbon::createFromFormat('H:i', $requestData['pickup_time'])->format('H:i:s');
                    $bookingData['to_be_advised_status'] = 'no';
                }
                if (isset($requestData['pick_up_location']) && !empty($requestData['pick_up_location']))
                    $bookingData['pick_up_location'] = $requestData['pick_up_location'];
                if (isset($requestData['pick_up_location_id']) && !empty($requestData['pick_up_location_id']))
                    $bookingData['pick_up_location_id'] = $requestData['pick_up_location_id'];
                if (isset($requestData['flight_detail']) && !empty($requestData['flight_detail']))
                    $bookingData['flight_detail'] = $requestData['flight_detail'];
                if (isset($requestData['drop_of_location']) && !empty($requestData['drop_of_location']))
                    $bookingData['drop_of_location'] = $requestData['drop_of_location'];
                if (isset($requestData['drop_off_location_id']) && !empty($requestData['drop_off_location_id']))
                    $bookingData['drop_off_location_id'] = $requestData['drop_off_location_id'];
                if (isset($requestData['phone']) && !empty($requestData['phone']))
                    $bookingData['phone'] = $requestData['phone'];
                if (isset($requestData['driver_id']) && !empty($requestData['driver_id'])) {
                    $bookingData['driver_id'] = $requestData['driver_id'];
                    $driverData = $this->driverRepository->getDriverById($bookingData['driver_id']);
                    $bookingData['vehicle_id'] = $driverData->vehicle_id ?? null;
                    if ($booking->driver_id && $booking->driver_id !== (int)$requestData['driver_id']) {
                        if ($booking->is_driver_notified) {
                            $this->removeDriverNotification($booking);
                        }
                        // $this->addDriverNotification($booking, $driverData);
                        $bookingData['is_driver_notified'] = 0;
                        $bookingData['is_driver_acknowledge'] = 0;
                        $bookingData['status'] = Booking::PENDING;
                    }
                    // elseif (!$booking->driver_id) {
                    //     $this->addDriverNotification($booking, $driverData);
                    // }
                }else{
                    $bookingData['driver_id'] = NULL;
                    $bookingData['vehicle_id'] = NULL;
                    $bookingData['driver_remark'] = NULL;
                }
                if (isset($requestData['vehicle_id']) && !empty($requestData['vehicle_id']))
                {
                    $bookingData['vehicle_id'] = $requestData['vehicle_id'];
                }else{
                    $bookingData['vehicle_id'] = NULL;
                }
                if (isset($requestData['driver_remark']) && !empty($requestData['driver_remark']))
                {
                    $bookingData['driver_remark'] = $requestData['driver_remark'];
                }else{
                    $bookingData['driver_remark'] = NULL;
                }

                if (isset($requestData['vehicle_type_id']) && !empty($requestData['vehicle_type_id']))
                    $bookingData['vehicle_type_id'] = $requestData['vehicle_type_id'];
                if (isset($requestData['status']) && !empty($requestData['status'])){
                    $bookingData['status'] = $requestData['status'];
                    $bookingData['client_asked_to_cancel'] = 'no';
                }
                if (isset($requestData['client_instructions']) && !empty($requestData['client_instructions']))
                    $bookingData['client_instructions'] = $requestData['client_instructions'];

                    
                if (isset($requestData['latest_comment']) && !empty($requestData['latest_comment']))
                {
                    $bookingData['latest_comment'] = $requestData['latest_comment'];
                    $this->bookingRepository->addBookingComment($bookingData['latest_comment'], $booking->id, $loggedUserId);
                }
            
                if (isset($requestData['latest_admin_comment']))
                {
                    $bookingData['latest_admin_comment'] = $requestData['latest_admin_comment'];
                    $this->bookingRepository->addBookingAdminComment($bookingData['latest_admin_comment'], $booking->id, $loggedUserId);
                }
            
                $additionalStopsLogs = [];
                if(isset($requestData['latest_admin_comment']) && !empty($requestData['latest_admin_comment']))
                {
                }else{
                    $linkedClients = !empty($booking->linked_clients) ? explode(',', $booking->linked_clients) : [];
                    $this->bookingLogService->addLogMessages($bookingData, $booking, Auth::user(), $linkedClients, $additionalStopsLogs);
                }
                $bookingData['updated_by_id'] = $loggedUserId;
                $this->bookingRepository->updateBooking($booking, $bookingData);
            }
            return $this->bookingRepository->getBookingById($bookingId);
        } catch (\Exception $e) {
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
    }

    private function addDriverNotification($booking, $driver)
    {
        $driverName = $driver->name ?? null;
        $driverChatId = $driver->chat_id ?? null;
        $driverType = $driver->driver_type ?? null;
        $pickupLocationId = $booking->pick_up_location_id ?? null;
        if ($pickupLocationId && $pickupLocationId !== 8) {
            $pickUpLocation = $booking->pickUpLocation->name ??  "N/A";
        } else {
            $pickUpLocation = $booking->pick_up_location ??  "N/A";
        }
        $dropOffLocationId = $booking->drop_off_location_id ?? null;
        if ($dropOffLocationId && $dropOffLocationId !== 8) {
            $dropOffLocation = $booking->dropOffLocation->name ??  "N/A";
        } else {
            $dropOffLocation = $booking->drop_of_location ??  "N/A";
        }
        $type = $booking->serviceType->name ?? "N/A";
        $guest = $booking->guest_name ??  "N/A";
        $hotel = $booking->client->hotel->name ?? null;
        $event = $booking->client->event ?? null;
        if ($hotel) {
            $client = $hotel . $event ? ' (' . $event . ')' : '';
        } else {
            $client = "N/A";
        }
        $contact = $booking->country_code ? "+(" .  $booking->country_code . ")" . $booking->phone : $booking->phone;
        $instructions = $booking->driver_remark ?? "N/A";
        $vehicleClassName = $booking->vehicle->vehicleClass->name ?? null;
        $vehicleNumber = $booking->vehicle->vehicle_number ?? null;
        $vehicle = "N/A";
        if ($vehicleClassName && $vehicleNumber) {
            $vehicle = $vehicleClassName . ' (' . $vehicleNumber . ')';
        }

        if ($driverChatId && $driverName) {
            $message = "Hi " . $driverName . ",\nA ride has been assigned to you. Below are the details:\n"
                . "Booking ID: #" . $booking->id . ".\n"
                . "Pickup Date: " . $booking->pickup_date . ".\n"
                . "Pickup Time: " . $booking->pickup_time . ".\n"
                . "Pickup Location: " . $pickUpLocation . ".\n"
                . "Drop Off Location: " . $dropOffLocation . ".\n"
                . "Type: " . $type . ".\n"
                . "Guest: " . $guest . ".\n"
                . "Client: " . $client . ".\n";
            if ($driverType === "INHOUSE") {
                $message .= "Contact: " . $contact . ".\n";
            }
            $message .= "Instructions: " . $instructions . ".\n"
                . "Vehicle: " . $vehicle . ".";
            $this->telegramService->sendMessage($driverChatId, $message);
        }
    }

    private function removeDriverNotification($booking)
    {
        $driverName = $booking->driver->name ?? null;
        $driverType = $booking->driver->driver_type ?? null;
        $driverChatId = $booking->driver->chat_id ?? null;
        $pickupLocationId = $booking->pick_up_location_id ?? null;
        if ($pickupLocationId && $pickupLocationId !== 8) {
            $pickUpLocation = $booking->pickUpLocation->name ??  "N/A";
        } else {
            $pickUpLocation = $booking->pick_up_location ??  "N/A";
        }
        $dropOffLocationId = $booking->drop_off_location_id ?? null;
        if ($dropOffLocationId && $dropOffLocationId !== 8) {
            $dropOffLocation = $booking->dropOffLocation->name ??  "N/A";
        } else {
            $dropOffLocation = $booking->drop_of_location ??  "N/A";
        }
        $type = $booking->serviceType->name ?? "N/A";
        $guest = $booking->guest_name ??  "N/A";
        $hotel = $booking->client->hotel->name ?? null;
        $event = $booking->client->event ?? null;
        if ($hotel) {
            $client = $hotel . $event ? ' (' . $event . ')' : '';
        } else {
            $client = "N/A";
        }
        $contact = $booking->country_code ? "+(" .  $booking->country_code . ")" . $booking->phone : $booking->phone;
        $instructions = $booking->driver_remark ?? "N/A";
        $vehicleClassName = $booking->vehicle->vehicleClass->name ?? null;
        $vehicleNumber = $booking->vehicle->vehicle_number ?? null;
        $vehicle = "N/A";
        if ($vehicleClassName && $vehicleNumber) {
            $vehicle = $vehicleClassName . ' (' . $vehicleNumber . ')';
        }

        if ($driverChatId && $driverName) {
            $message = "Hi " . $driverName . ",\nA ride has been <b style='color:red;'>❌ CANCELLED</b> due to some reason. Below are the details:\n"
                . "Booking ID: #" . $booking->id . ".\n"
                . "Pickup Date: " . $booking->pickup_date . ".\n"
                . "Pickup Time: " . $booking->pickup_time . ".\n"
                . "Pickup Location: " . $pickUpLocation . ".\n"
                . "Drop Off Location: " . $dropOffLocation . ".\n"
                . "Type: " . $type . ".\n"
                . "Guest: " . $guest . ".\n"
                . "Client: " . $client . ".\n";
            if ($driverType === "INHOUSE") {
                $message .= "Contact: " . $contact . ".\n";
            }
            $message .= "Instructions: " . $instructions . ".\n"
                . "Vehicle: " . $vehicle . ".";
            $this->telegramService->sendMessage($driverChatId, $message);
        }
    }
    private function sendMessageToOpsTeam($booking, $loggedUser)
    {
        $pickupLocationId = $booking->pick_up_location_id ?? null;
        if ($pickupLocationId && $pickupLocationId !== 8) {
            $pickUpLocation = $booking->pickUpLocation->name ??  "N/A";
        } else {
            $pickUpLocation = $booking->pick_up_location ??  "N/A";
        }
        $dropOffLocationId = $booking->drop_off_location_id ?? null;
        if ($dropOffLocationId && $dropOffLocationId !== 8) {
            $dropOffLocation = $booking->dropOffLocation->name ??  "N/A";
        } else {
            $dropOffLocation = $booking->drop_of_location ??  "N/A";
        }
        $type = $booking->serviceType->name ?? "N/A";
        $guest = $booking->guest_name ??  "N/A";
        $hotel = $booking->client->hotel->name ?? "N/A";
        $client = $booking->client->user->first_name . " " . $booking->client->user->last_name;
        $contact = $booking->country_code ? "+(" .  $booking->country_code . ")" . $booking->phone : $booking->phone;
        $instructions = $booking->client_instructions ?? "N/A";
        $vehicleClassName = $booking->vehicleType->name ?? null;
        $vehicle = "N/A";
        if ($vehicleClassName) {
            $vehicle = $vehicleClassName;
        }
        $hotelName =  $loggedUser->client->hotel->name ?? null;
        $chatId = config('app.telegram_group_chat_id');
        if ($hotelName && $chatId) {
            $message = "Hi Team,\n New Booking Created\n"
                . "Corporate: " . $hotel  . "\n"
                . "Client: " .       $client  . "\n"
                . "Booking ID: " . $booking->id  . "\n"
                . "Pickup Date: " . $booking->pickup_date  . "\n"
                . "Pickup Time: " . $booking->pickup_time . "\n"
                . "Pickup Location: " . $pickUpLocation  . "\n"
                . "Drop Off Location: " . $dropOffLocation  . "\n"
                . "Type: " . $type . "\n"
                . "Guest: " . $guest . "\n"
                . "Contact: " . $contact  . "\n"
                . "Client Instructions: " .   $instructions . "\n"
                . "Vehicle: " . $vehicle . "\n";
            $this->telegramService->sendMessage($chatId, $message);
        }
    }

    private function createBookingNotification($loggedUser, $booking, $userTypeSlug)
    {
        
        try {
            $message = "added a new booking";
            $subject = "New Booking Created";
            $loggedUserFullName = $this->helper->getFullName($loggedUser->first_name, $loggedUser->last_name);

            $mailDataForAdmin = [
                'subject' => $subject,
                'template' => 'booking-created-email',
                'name' => 'Limousine Team',
                'logs' => $message,
                'changedBy' => $loggedUserFullName . ' from ' . Auth::user()->client->hotel->name,
                'bookingId' => $booking->id,
            ];
            $this->helper->sendEmail('limousine@e1asia.com.sg', $mailDataForAdmin);
            
            $notifyUsers =  $this->userRepository->getAdmins();

            $notificationType = 'booking';
            $subject = __("message.booking_notification_subject");
            $template = 'emails.send_notification';
            $message = $loggedUserFullName . " " . $message;
            $notificationData = [
                'booking' => $booking,
                'message' => $message,
                'from_user_name' => $loggedUserFullName,
            ];
            $this->notificationService->sendNotification($notificationData, $loggedUser, $notificationType, $subject, $notifyUsers, $message, $template);
        } catch (\Exception $e) {
            // Rollback the transaction if an error occurs
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
    }

    private function createBookingNotificationToPOCandHeadOffice($loggedUser, $booking)
    {
        
        try {
            $message = "added a new booking";
            $subject = "New Booking Created";


            // to poc of logged in user's hotel
            $hotelId = $loggedUser->client->hotel_id;

            // Step 1: Get all client IDs from hotel_poc table
            $allPocClientIds = DB::table('hotels_poc')
                ->where('hotel_id', $hotelId)
                ->pluck('client_id')
                ->toArray();

            // Step 2: Get all user IDs from clients table using those client IDs
            $clientUserIds = DB::table('clients')
                ->whereIn('id', $allPocClientIds)
                ->pluck('user_id')
                ->toArray();

            // Step 3: Get user details from users table
            $users = DB::table('users')
                ->whereIn('id', $clientUserIds)
                ->select('first_name', 'last_name', 'email')
                ->get();

            if(!empty($users))
            {
                foreach($users as $user)
                {
                    $loggedUserFullName = $this->helper->getFullName($loggedUser->first_name, $loggedUser->last_name);

                    $mailDataForAdmin = [
                        'subject' => $subject,
                        'template' => 'booking-created-email',
                        'name' => 'Limousine Team',
                        'logs' => $message,
                        'changedBy' => $loggedUserFullName . ' from ' . Auth::user()->client->hotel->name,
                        'bookingId' => $booking->id,
                    ];
                    $this->helper->sendEmail($user->email, $mailDataForAdmin);
                }
            }


            // check if logged in user belongs to the same corporate, for whom the booking is created
            if($booking->client->hotel_id == $loggedUser->client->hotel_id)
            {
                // check if logged in user's hotel is head office
                if($loggedUser->client->hotel->is_head_office == 1 || ($loggedUser->client->hotel->is_head_office == 0 && $loggedUser->client->hotel->linked_head_office == NULL))
                {
                    
                }else
                {   
                    // to head office
                    $hotelId = $loggedUser->client->hotel->linked_head_office;

                    // Step 1: Get all client IDs from hotel_poc table
                    $allPocClientIds = DB::table('hotels_poc')
                        ->where('hotel_id', $hotelId)
                        ->pluck('client_id')
                        ->toArray();

                    // Step 2: Get all user IDs from clients table using those client IDs
                    $clientUserIds = DB::table('clients')
                        ->whereIn('id', $allPocClientIds)
                        ->pluck('user_id')
                        ->toArray();

                    // Step 3: Get user details from users table
                    $users = DB::table('users')
                        ->whereIn('id', $clientUserIds)
                        ->select('first_name', 'last_name', 'email')
                        ->get();

                    if(!empty($users))
                    {
                        foreach($users as $user)
                        {
                            $loggedUserFullName = $this->helper->getFullName($loggedUser->first_name, $loggedUser->last_name);

                            $mailDataForAdmin = [
                                'subject' => $subject,
                                'template' => 'booking-created-email',
                                'name' => 'Limousine Team',
                                'logs' => $message,
                                'changedBy' => $loggedUserFullName . ' from ' . Auth::user()->client->hotel->name,
                                'bookingId' => $booking->id,
                            ];
                            $this->helper->sendEmail($user->email, $mailDataForAdmin);
                        }
                    }
                }
            }else{
                // to poc of booking's hotel
                $hotelId = $booking->client->hotel_id;

                // Step 1: Get all client IDs from hotel_poc table
                $allPocClientIds = DB::table('hotels_poc')
                    ->where('hotel_id', $hotelId)
                    ->pluck('client_id')
                    ->toArray();

                // Step 2: Get all user IDs from clients table using those client IDs
                $clientUserIds = DB::table('clients')
                    ->whereIn('id', $allPocClientIds)
                    ->pluck('user_id')
                    ->toArray();

                // Step 3: Get user details from users table
                $users = DB::table('users')
                    ->whereIn('id', $clientUserIds)
                    ->select('first_name', 'last_name', 'email')
                    ->get();

                if(!empty($users))
                {
                    foreach($users as $user)
                    {
                        $loggedUserFullName = $this->helper->getFullName($loggedUser->first_name, $loggedUser->last_name);

                        $mailDataForAdmin = [
                            'subject' => $subject,
                            'template' => 'booking-created-email',
                            'name' => 'Limousine Team',
                            'logs' => $message,
                            'changedBy' => $loggedUserFullName . ' from ' . Auth::user()->client->hotel->name,
                            'bookingId' => $booking->id,
                        ];
                        $this->helper->sendEmail($user->email, $mailDataForAdmin);
                    }
                }

                // check if booking's hotel is head office
                if($booking->client->hotel->is_head_office == 1 || ($loggedUser->client->hotel->is_head_office == 0 && $loggedUser->client->hotel->linked_head_office == NULL))
                {
                    
                }else
                {   
                    // to head office
                    $hotelId = $booking->client->hotel->linked_head_office;

                    // Step 1: Get all client IDs from hotel_poc table
                    $allPocClientIds = DB::table('hotels_poc')
                        ->where('hotel_id', $hotelId)
                        ->pluck('client_id')
                        ->toArray();

                    // Step 2: Get all user IDs from clients table using those client IDs
                    $clientUserIds = DB::table('clients')
                        ->whereIn('id', $allPocClientIds)
                        ->pluck('user_id')
                        ->toArray();

                    // Step 3: Get user details from users table
                    $users = DB::table('users')
                        ->whereIn('id', $clientUserIds)
                        ->select('first_name', 'last_name', 'email')
                        ->get();

                    if(!empty($users))
                    {
                        foreach($users as $user)
                        {
                            $loggedUserFullName = $this->helper->getFullName($loggedUser->first_name, $loggedUser->last_name);

                            $mailDataForAdmin = [
                                'subject' => $subject,
                                'template' => 'booking-created-email',
                                'name' => 'Limousine Team',
                                'logs' => $message,
                                'changedBy' => $loggedUserFullName . ' from ' . Auth::user()->client->hotel->name,
                                'bookingId' => $booking->id,
                            ];
                            $this->helper->sendEmail($user->email, $mailDataForAdmin);
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            // Rollback the transaction if an error occurs
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Delete one or more bookings based on the provided data.
     *
     * Deletes one or more bookings from the database based on the provided request data.
     * If successful, commits the transaction and returns true.
     * If an error occurs during the process, rolls back the transaction and throws an exception with an error message.
     *
     * @param array $requestData An associative array containing data for deleting bookings.
     *                           Required key: 'booking_ids' (an array of booking IDs to be deleted).
     *
     * @return bool Returns true if the bookings are successfully deleted.
     *
     * @throws \Exception If an error occurs during the process.
     */
    public function deleteBookings($requestData, $log_headers)
    {
        DB::beginTransaction();
        try {
            $oldData = $this->bookingRepository->getBookingByIds($requestData['booking_ids']);
            // Delete booking(s) from the database
            $this->bookingRepository->deleteBooking($requestData['booking_ids']);
            $loggedUser=Auth::user();
            foreach ($requestData['booking_ids'] as $bookingId) {
                $message= "Deleted";
                $logData = ["message" => $message, "booking_id" => $bookingId, "user_id" => $loggedUser->id];
                $this->bookingLogRepository->addLogs($logData);
            }
            $this->activityLogService->addActivityLog('delete', Booking::class, json_encode($oldData), json_encode([]), $log_headers['headers']['Origin'], $log_headers['headers']['User-Agent']);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            // Rollback the transaction if an error occurs
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
    }

    
    public function getBookingByIdToRestore(int $bookingId)
    {
        try {
            $booking = $this->bookingRepository->getBookingByIdToRestore($bookingId);

            return $booking;
        } catch (\Exception $e) {
            // Rollback the transaction if an error occurs
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
    }

    
    public function permanentDeleteBookings($requestData, $log_headers)
    {
        try {
            $oldData = $this->bookingRepository->getBookingByIdsToPermanentDelete($requestData['booking_ids']);
            
            $this->bookingRepository->permanentDeleteBooking($requestData['booking_ids']);

            $loggedUser=Auth::user();
            foreach ($requestData['booking_ids'] as $bookingId) {
                $message= "Deleted";
                $logData = ["message" => $message, "booking_id" => $bookingId, "user_id" => $loggedUser->id];
                $this->bookingLogRepository->addLogs($logData);
            }
            $this->activityLogService->addActivityLog('delete', Booking::class, json_encode($oldData), json_encode([]), $log_headers['headers']['Origin'], $log_headers['headers']['User-Agent']);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            // Rollback the transaction if an error occurs
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
    }

    public function getBookingDataForDashboardPieChart(array $requestData = [])
    {
        try {
            $startDate = $requestData['startDate'] ?? null;
            $endDate = $requestData['endDate'] ?? Carbon::now()->toDateString();
            
            $loggedUser = Auth::user();
            $userTypeSlug = $loggedUser->userType->slug ?? null;

            if (!$startDate) {
                $startDate = Booking::min('created_at');
            }
            return $this->bookingRepository->getBookingsForDashboardForPieChart($startDate, $endDate, $loggedUser);
        } catch (\Exception $e) {
            // Throw an exception with the error message if an error occurs
            throw new \Exception($e->getMessage());
        }
    }

    public function getBookingDataForDashboardForLineChart(array $requestData = [])
    {
        try {
            $startDate = $requestData['startDate'] ?? null;
            $endDate = $requestData['endDate'] ?? Carbon::now()->toDateString();
            
            $loggedUser = Auth::user();
            $userTypeSlug = $loggedUser->userType->slug ?? null;

            if (!$startDate) {
                $startDate = Booking::min('created_at');
            }

            return $this->bookingRepository->getBookingsForDashboardForLineChart($startDate, $endDate, $loggedUser);
        } catch (\Exception $e) {
            // Throw an exception with the error message if an error occurs
            throw new \Exception($e->getMessage());
        }
    }

    public function getBookingDataForDashboardForLineChartCancellation(array $requestData = [])
    {
        try {
            $startDate = $requestData['startDate'] ?? null;
            $endDate = $requestData['endDate'] ?? Carbon::now()->toDateString();
            
            $loggedUser = Auth::user();
            $userTypeSlug = $loggedUser->userType->slug ?? null;
            
            if (!$startDate) {
                $startDate = Booking::min('created_at');
            }

            return $this->bookingRepository->getBookingsForDashboardForLineChartCancellation($startDate, $endDate, $loggedUser);
        } catch (\Exception $e) {
            // Throw an exception with the error message if an error occurs
            throw new \Exception($e->getMessage());
        }
    }
}
