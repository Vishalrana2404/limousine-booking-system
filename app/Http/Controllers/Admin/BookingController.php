<?php

namespace App\Http\Controllers\Admin;

use App\CustomHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\AddBookingRequest;
use App\Http\Requests\AddMultipleBookingRequest;
use App\Http\Requests\DeleteBookingRequest;
use App\Http\Requests\EditBookingRequest;
use App\Http\Requests\UpdateInlineTableBooking;
use App\Models\Booking;
use App\Models\PeakPeriod;
use App\Services\BookingLogService;
use App\Services\BookingService;
use App\Services\CitySurchargeService;
use App\Services\ClientService;
use App\Services\DriverOffDayService;
use App\Services\DriverService;
use App\Services\HotelService;
use App\Services\EventService;
use App\Services\LocationService;
use App\Services\PeakPeriodService;
use App\Services\ServiceTypeService;
use App\Services\VehicleClassService;
use App\Services\VehicleService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\Repositories\CorporateFairBillingRepository;
use App\Services\EmailTemplatesService;
use DateTime;
/**
 * Class BookingController
 * 
 * @package  App\Http\Controllers\Admin
 */
class BookingController extends Controller
{
    /**
     * Constructor for the BookingController class.
     *
     * Initializes the BookingController with necessary services and dependencies.
     *
     * @param ServiceTypeService   $serviceTypeService    Service for handling service type operations.
     * @param LocationService $locationService Service for handling location operations.
     * @param VehicleClassService   $vehicleClassService   Service for handling vehicle class operations.
     * @param BookingService        $bookingService        Service for handling booking operations.
     * @param CustomHelper          $helper                Helper instance providing additional utility functions.
     */
    public function __construct(
        private ServiceTypeService $serviceTypeService,
        private LocationService $locationService,
        private VehicleClassService $vehicleClassService,
        private BookingService $bookingService,
        private DriverService $driverService,
        private VehicleService $vehicleService,
        private HotelService $hotelService,
        private EventService $eventService,
        private PeakPeriodService $peakPeriodService,
        private DriverOffDayService $driverOffDayService,
        private BookingLogService $bookingLogService,
        private ClientService $clientService,
        private CorporateFairBillingRepository $corporateFairBillingRepository,
        private EmailTemplatesService $emailTemplateService,
        private CustomHelper $helper
    ) {
    }
    /**
     * Display a listing of the bookings.
     *
     * Retrieves service types, locations, and booking data to display in the booking index view.
     *
     * @param Request $request The current HTTP request instance.
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse The view displaying the booking index or a redirect response in case of an error.
     */
    public function index(Request $request)
    {
        try {
            $serviceTypes = $this->serviceTypeService->getServiceTypes();
            $drivers = $this->driverService->getDrivers()->sortBy('name')->values();
            $locations = $this->locationService->getLocations();
            $hotels = $this->hotelService->getHotels();
            $vehicleTypes = $this->vehicleClassService->getVehicleClass()->sortBy('name')->values();
            $vehicles = $this->vehicleService->getvehicles()->sortBy('vehicle_number')->values();
            $driverOffDays = $this->driverOffDayService->getSavedDates();
            $hotelClients = $this->hotelService->getClientAdmins();
            $bookingData = $this->bookingService->getBookingData($request->query());
            return view('admin.bookings.index', compact('serviceTypes', 'locations', 'driverOffDays', 'bookingData', 'hotels', 'vehicleTypes', 'drivers', 'vehicles', 'hotelClients'));
        } catch (\Exception $e) {
            $this->helper->alertResponse(__('messages.something_went_wrong'), 'error');
            // Handle any exceptions that occur
            $this->helper->handleException($e);
            return redirect()->back();
        }
    }
    /**
     * Show the form for creating a new booking.
     *
     * Retrieves necessary data such as service types, locations, and vehicle types to populate the booking creation form.
     *
     * @param Request $request The current HTTP request instance.
     * @return \Illuminate\View\View The view displaying the booking creation form.
     */
    public function create(Request $request)
    {
        $loggedUser = Auth::user();

        $events = NULL;
        $multipleCorporatesHotelData = NULL;

        if(!empty($loggedUser->userType))
        {
            if($loggedUser->userType->slug === 'client-staff' || $loggedUser->userType->slug === 'client-admin')
            {
                $loggedUser->load('client');
                $loggedUser->client->load(['hotel', 'multiCorporates.hotel']);

                $loggedInUserHotelDetails = $loggedUser->client->hotel;
                $hotel_id = $loggedUser->client->hotel_id;

                $multiCorporates = $loggedUser->client->multiCorporates;

                $multipleCorporatesHotelData = $multiCorporates->pluck('hotel');

                if (!$multipleCorporatesHotelData->isEmpty() && !$multipleCorporatesHotelData->contains('id', $loggedInUserHotelDetails->id)) {
                    $multipleCorporatesHotelData->push($loggedInUserHotelDetails);
                }

                if(!empty($multipleCorporatesHotelData) && count($multipleCorporatesHotelData) > 1)
                {
                    $events = NULL;
                }else{
                    $events = $this->eventService->getEventDataByHotel($hotel_id);
                }
            }
        }

        $hotelClients = $this->hotelService->getClientAdmins();
        $peakPeriods = $this->peakPeriodService->getAllPeakPeriod();
        $locations = $this->locationService->getLocations();
        $serviceTypes = $this->serviceTypeService->getServiceTypes(true);
        $vehicleTypes = $this->vehicleClassService->getVehicleClass();
        return view('admin.bookings.create-booking', compact('serviceTypes', 'peakPeriods', 'hotelClients', 'locations', 'vehicleTypes', 'events', 'multipleCorporatesHotelData'));
    }
    /**
     * Save a newly created booking.
     *
     * Validates the incoming request data using the AddBookingRequest class,
     * then attempts to create a new booking using the BookingService.
     * If successful, redirects to the bookings index page with a success message.
     * If an exception occurs, handles the exception, alerts the user, and redirects back.
     *
     * @param AddBookingRequest $request The incoming request instance containing the booking data.
     * @return \Illuminate\Http\RedirectResponse A redirect response to the bookings index page.
     */
    public function save(AddBookingRequest $request)
    {
        try {
            $file = $request->file('attachment');
            $this->bookingService->createBooking($request->all(), $file);
            $this->helper->alertResponse(__('message.booking_created'), 'success');
            return redirect('bookings');
        } catch (\Exception $e) {
            $this->helper->handleException($e);
            $this->helper->alertResponse(__('messages.something_went_wrong'), 'error');
            return redirect()->back();
        }
    }
    /**
     * Save multiple newly created bookings.
     *
     * Validates the incoming request data using the AddMultipleBookingRequest class,
     * then attempts to create multiple new bookings using the BookingService.
     * If successful, redirects to the bookings index page with a success message.
     * If an exception occurs, handles the exception, alerts the user, and redirects back.
     *
     * @param AddMultipleBookingRequest $request The incoming request instance containing the booking data.
     * @return \Illuminate\Http\RedirectResponse A redirect response to the bookings index page.
     */
    public function saveMultipleBooking(AddMultipleBookingRequest $request)
    {
        try {
            $files = $request->file('multiple_attachment');
            $this->bookingService->createMultipleBooking($request->all(), $files);
            $this->helper->alertResponse(__('message.booking_created'), 'success');
            return redirect('bookings');
        } catch (\Exception $e) {
            $this->helper->handleException($e);
            $this->helper->alertResponse(__('messages.something_went_wrong'), 'error');
            return redirect()->back();
        }
    }
    /**
     * Filter bookings based on the provided request parameters.
     *
     * @param Request $request The HTTP request containing filter criteria.
     * @return JsonResponse JSON response containing filtered booking listing HTML or error message.
     */
    public function filterBookings(Request $request)
    {
        try {
            // Retrieve booking data based on the filter criteria
            $bookingData = $this->bookingService->getBookingData($request->query());
            // Render the booking listing partial view with the filtered data
            $data = ['html' => view('admin.bookings.partials.booking-listing', compact('bookingData'))->render()];
            // Return a JSON response with the updated booking listing HTML
            return $this->handleResponse($data, __("message.booking_filtered"), Response::HTTP_OK);
        } catch (\Exception $e) {
            // Handle the exception and return an error response
            $this->helper->handleException($e);
            return $this->handleResponse([], $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR, []);
        }
    }

    public function edit(Booking $booking)
    {
        $bookedByHotelId = $booking->client->hotel_id ?? null;
        $loggedUserHotelId = Auth::user()->client->hotel_id ?? null;
        $loggedUserClientId = Auth::user()->client->id ?? null;
        $user = Auth::user();
        $userTypeSlug = $user->userType->slug ?? null;
        // if ($loggedUserHotelId !== null && $bookedByHotelId !== $loggedUserHotelId) {
        //     $this->helper->alertResponse(__('message.permission_denied'), 'error');
        //     return redirect()->route('dashboard');
        // } else if ($userTypeSlug && in_array($userTypeSlug, ['client-admin', 'client-staff']) && ($booking->status !== 'PENDING' || $booking->status !== 'ACCEPTED')) {            
        //     $this->helper->alertResponse(__('message.permission_denied'), 'error');
        //     return redirect()->route('dashboard');
        // }
        $pickUpTime = 'N/A';
        $pickup = $booking->pickup_time ? CustomHelper::formatTime($booking->pickup_time) : null;
        if ($booking->service_type_id === 4) {
            if ($pickup && $booking->no_of_hours) {
                $pickupDateTime = new DateTime($pickup);
                $pickupDateTime->modify('+' . $booking->no_of_hours . ' hours');
                $endTime = $pickupDateTime->format('H:i');
                $pickUpTime = $pickup . '</br>(' . $endTime . ')';
            } else {
                $pickUpTime = 'N/A';
            }
        } else {
            $pickUpTime = $pickup ? $pickup : 'N/A';
        }

        $pickupDate = $booking->pickup_date;
        $pickupDateTime = $pickupDate . ' ' . $pickUpTime;
        $pickupDateTimeStamp = strtotime($pickupDateTime);
        $currentTimeStamp = strtotime(date('Y-m-d H:i'));

        $hoursDifference = ($pickupDateTimeStamp - $currentTimeStamp) / 3600;
        
        $hotelIdsFromLinkedCorporates = NULL;

        if (in_array($userTypeSlug, ['client-admin', 'client-staff']) && $loggedUserHotelId !== null) {
            $client = $user->load('client');
            $hotel = $user->client->load('hotel');
            
            $multiCorporatesData = $user->client->load('multiCorporates');
            $hotelIdsFromLinkedCorporates = $multiCorporatesData->multiCorporates->pluck('hotel_id'); 
            
            if(!empty($hotelIdsFromLinkedCorporates))
            {
                if(!empty($booking->createdBy->client) && ($booking->createdBy->client->hotel_id == $loggedUserHotelId || $booking->client->hotel_id == $loggedUserHotelId))
                {
                }else{
                    if($hotel->hotel->is_head_office == 1 && (!empty($booking->createdBy->client) && $booking->createdBy->client->hotel->linked_head_office == $loggedUserHotelId || $booking->client->hotel->id == $loggedUserHotelId || $booking->client->hotel->linked_head_office == $loggedUserHotelId))
                    {

                    }else{
                        if (!in_array($bookedByHotelId, $hotelIdsFromLinkedCorporates->toArray())) {
                            $this->helper->alertResponse(__('message.permission_denied'), 'error');
                            return redirect()->route('dashboard');
                        }
                    }
                }
            }else{
                if($loggedUserHotelId !== null && $bookedByHotelId !== $loggedUserHotelId){
                    $this->helper->alertResponse(__('message.permission_denied'), 'error');
                    return redirect()->route('dashboard');
                }
            }
            
        } else if ($userTypeSlug === null || in_array($userTypeSlug, ['admin', 'admin-staff']) || (in_array($userTypeSlug, ['client-admin', 'client-staff']) && ($booking->status === 'PENDING' || ($booking->status === 'ACCEPTED' && $hoursDifference > 24)))) {
        
        }else if($userTypeSlug && in_array($userTypeSlug, ['client-admin', 'client-staff']) && $booking->status !== 'PENDING')
        {
            $this->helper->alertResponse(__('message.permission_denied'), 'error');
            return redirect()->route('dashboard');
        }

        $locations = $this->locationService->getLocations();
        $serviceTypes = $this->serviceTypeService->getServiceTypes();
        $vehicleTypes = $this->vehicleClassService->getVehicleClass();
        $drivers = $this->driverService->getDrivers();
        $vehicles = $this->vehicleService->getvehicles();
        $peakPeriods = $this->peakPeriodService->getAllPeakPeriod();
        $driverOffDays = $this->driverOffDayService->getSavedDates();

        if ($userTypeSlug === null || in_array($userTypeSlug, ['admin', 'admin-staff']))
        {
            $events = $this->eventService->getEventDataByHotel($booking->client_id);
        }else{
            $events = $this->eventService->getEventDataByHotel($booking->client->hotel_id);
        }
        
        // if(in_array($userTypeSlug, ['client-admin', 'client-staff']) && $loggedUserClientId !== null)
        // {
        //     $clients = $this->clientService->getClientsByHotel($loggedUserClientId);
        // }else{
        // }
        $clientsFromBookingCorporate = $this->clientService->getClientsByHotel($booking->client_id);        
        $clientsFromLinkedCorporates = $this->clientService->getClientsByLinkedHotel($booking->client_id)->map(function ($client) {
            return [
                'id' => $client->client->id,
                'user_id' => $client->client->user_id,
                'hotel_id' => $client->client->hotel_id,
                'invoice' => $client->client->invoice,
                'status' => $client->client->status,
                'created_by_id' => $client->client->created_by_id,
                'updated_by_id' => $client->client->updated_by_id,
                'created_at' => $client->client->created_at,
                'updated_at' => $client->client->updated_at,
                'deleted_at' => $client->client->deleted_at,
                'entity' => $client->client->entity,
                'user' => [
                    'id' => $client->client->user->id,
                    'email' => $client->client->user->email,
                    'email_verified_at' => $client->client->user->email_verified_at,
                    'created_at' => $client->client->user->created_at,
                    'updated_at' => $client->client->user->updated_at,
                    'deleted_at' => $client->client->user->deleted_at,
                    'first_name' => $client->client->user->first_name,
                    'last_name' => $client->client->user->last_name,
                    'user_type_id' => $client->client->user->user_type_id,
                    'department' => $client->client->user->department,
                    'status' => $client->client->user->status,
                    'phone' => $client->client->user->phone,
                    'profile_image' => $client->client->user->profile_image,
                    'gender' => $client->client->user->gender,
                    'created_by_id' => $client->client->user->created_by_id,
                    'updated_by_id' => $client->client->user->updated_by_id,
                    'country_code' => $client->client->user->country_code
                ]
            ];
        });

        
        $clientsFromBookingCorporate->load('user');
        $clientsFromBookingCorporate = collect($clientsFromBookingCorporate);
        $clientsFromLinkedCorporates = collect($clientsFromLinkedCorporates);
        $clients = $clientsFromBookingCorporate->merge($clientsFromLinkedCorporates)->unique('user.id');

        $logs =  $this->bookingLogService->getBookingLogs(["searchByBookingId" => $booking->id, 'isNoDateRange' => true]);

        if(!empty($booking->service_type_id))
        {
            $service = 'Arrival';
    
            if($booking->service_type_id == 1)
            {
                $service = 'Arrival';
            }elseif($booking->service_type_id == 2)
            {
                $service = 'Transfer';
            }elseif($booking->service_type_id == 3)
            {
                $service = 'Departure';
            }
        }

        $vehicleClassId = !empty($booking->vehicle_type_id) ? $booking->vehicle_type_id : '';

        $corporateFairBillingDetailsService = null;
        $corporateFairBillingDetailsPerHour = null;
        if(!empty($vehicleClassId) && !empty($service) && !empty($booking->client->hotel_id))
        {
            $corporateFairBillingDetailsService = $this->corporateFairBillingRepository->getCorporateFairBillingByHotelIdVehicleClassTripType($booking->client->hotel_id, $vehicleClassId, $service);
            $corporateFairBillingDetailsPerHour = $this->corporateFairBillingRepository->getCorporateFairBillingByHotelIdVehicleClassTripType($booking->client->hotel_id, $vehicleClassId, 'Hour');
        }
        $hotelClients = $this->hotelService->getClientAdmins();
        // return explode(',', $booking->linked_clients);

        $emailTemplates = $this->emailTemplateService->getAllTemplates();

        return view('admin.bookings.edit-booking', compact('serviceTypes', 'driverOffDays', 'logs', 'vehicles', 'drivers', 'booking', 'locations', 'peakPeriods', 'vehicleTypes', 'events', 'corporateFairBillingDetailsService', 'corporateFairBillingDetailsPerHour', 'clients', 'hotelIdsFromLinkedCorporates', 'hotelClients', 'emailTemplates'));
    }

    public function update(EditBookingRequest $request, Booking $booking)
    {
        try {
            $bookedByHotelId = $booking->client->hotel_id ?? null;
            $loggedUserHotelId = Auth::user()->client->hotel_id ?? null;
            $user = Auth::user();
            $userTypeSlug = $user->userType->slug ?? null;
    
            $pickUpTime = 'N/A';
            $pickup = $booking->pickup_time ? CustomHelper::formatTime($booking->pickup_time) : null;
            if ($booking->service_type_id === 4) {
                if ($pickup && $booking->no_of_hours) {
                    $pickupDateTime = new DateTime($pickup);
                    $pickupDateTime->modify('+' . $booking->no_of_hours . ' hours');
                    $endTime = $pickupDateTime->format('H:i');
                    $pickUpTime = $pickup . '</br>(' . $endTime . ')';
                } else {
                    $pickUpTime = 'N/A';
                }
            } else {
                $pickUpTime = $pickup ? $pickup : 'N/A';
            }
            $pickupDate = $booking->pickup_date;
            $pickupDateTime = $pickupDate . ' ' . $pickUpTime;
            $pickupDateTimeStamp = strtotime($pickupDateTime);
            $currentTimeStamp = strtotime(date('Y-m-d H:i'));
            $hoursDifference = ($pickupDateTimeStamp - $currentTimeStamp) / 3600;
    
            if (in_array($userTypeSlug, ['client-admin', 'client-staff']) && $loggedUserHotelId !== null) 
            {
                 $client = $user->load('client');
                $hotel = $user->client->load('hotel');
                
                $multiCorporatesData = $user->client->load('multiCorporates');
                $hotelIdsFromLinkedCorporates = $multiCorporatesData->multiCorporates->pluck('hotel_id');            
                
                if(!empty($hotelIdsFromLinkedCorporates))
                {
                    if($booking->createdBy->client->hotel_id == $loggedUserHotelId)
                    {
                        
                    }else{
                        if($hotel->hotel->is_head_office == 1 && ($booking->createdBy->client->hotel->linked_head_office == $loggedUserHotelId || $booking->client->hotel->id == $loggedUserHotelId || $booking->client->hotel->linked_head_office == $loggedUserHotelId))
                        {

                        }else{
                            if (!in_array($bookedByHotelId, $hotelIdsFromLinkedCorporates->toArray())) {
                                $this->helper->alertResponse(__('message.permission_denied'), 'error');
                                return redirect()->route('dashboard');
                            }
                        }
                    }
                }else{
                    if($loggedUserHotelId !== null && $bookedByHotelId !== $loggedUserHotelId){
                        $this->helper->alertResponse(__('message.permission_denied'), 'error');
                        return redirect()->route('dashboard');
                    }
                }
                
            } else if ($userTypeSlug === null || in_array($userTypeSlug, ['admin', 'admin-staff']) || (in_array($userTypeSlug, ['client-admin', 'client-staff']) && ($booking->status === 'PENDING' || ($booking->status === 'ACCEPTED' && $hoursDifference > 24)))) 
            {                
            }else if($userTypeSlug && in_array($userTypeSlug, ['client-admin', 'client-staff']) && $booking->status !== 'PENDING')
            {
                $this->helper->alertResponse(__('message.permission_denied'), 'error');
                return redirect()->route('dashboard');
            }
            
            $logHeaders = $this->getHttpData($request);
            $file = $request->file('attachment');
    
            $this->bookingService->updateBooking($request->all(), $booking,  $file, $logHeaders);
            $this->helper->alertResponse(__('message.booking_updated'), 'success');
            return redirect('bookings');
        } catch (\Exception $e) {
            // Handle any exceptions that occur
            $this->helper->handleException($e);
            // Display an error message and redirect back to the previous page
            $this->helper->alertResponse(__('messages.something_went_wrong'), 'error');
            return redirect()->back();
        }
    }
    public function updateDispatch(Request $request)
    {
        try {
            $logHeaders = $this->getHttpData($request);
            $booking = $this->bookingService->updateDispatch($request->all(), $logHeaders);
            $data = ['html' => view('admin.bookings.partials.booking-row', compact('booking'))->render()];
            return $this->handleResponse($data, __("message.booking_updated"), Response::HTTP_OK);
        } catch (\Exception $e) {
            // Handle any exceptions that occur
            $this->helper->handleException($e);
            // Generate and return a response indicating the error that occurred
            return $this->handleResponse([], $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR, []);
        }
    }

    public function updateInline(UpdateInlineTableBooking $request)
    {
        try {
            $logHeaders = $this->getHttpData($request);
            $booking = $this->bookingService->updateInline($request->all(), $logHeaders);
            $data = ['html' => view('admin.bookings.partials.booking-row', compact('booking'))->render()];
            return $this->handleResponse($data, __("message.booking_updated"), Response::HTTP_OK);
        } catch (\Exception $e) {
            // Handle any exceptions that occur
            $this->helper->handleException($e);
            // Generate and return a response indicating the error that occurred
            return $this->handleResponse([], $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR, []);
        }
    }
      /**
     * Delete booking based on the provided criteria.
     *
     * Deletes booking using data from the submitted request parameters.
     * If successful, returns a response with a success message.
     * If an error occurs during the process, handles the exception and returns a response with an error message.
     *
     * @param \App\Http\Requests\DeleteBookingRequest $request The validated request object containing criteria for deleting booking.
     *
     * @return \Illuminate\Http\JsonResponse Returns a JSON response containing a success message or an error message.
     */
    public function delete(DeleteBookingRequest $request)
    {
        try {
            $log_headers = $this->getHttpData($request);
            $this->bookingService->deleteBookings($request->all(), $log_headers);
            return $this->handleResponse([], __("message.booking_deleted"), Response::HTTP_OK);
        } catch (\Exception $e) {
            // Handle any exceptions that occur
            $this->helper->handleException($e);
            // Generate and return a response indicating the error that occurred
            return $this->handleResponse([], $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR, []);
        }
    }

    public function getCorporateFareCharges(Request $request)
    {
        $fareData = $this->corporateFairBillingRepository->getCorporateFairBillingByHotelIdVehicleClassTripType($request->hotelId, $request->vehicleTypeId, $request->serviceTypeId);

        if(!empty($fareData))
        {
            return response()->json([
                'status' => 200,
                'message' => 'Fare fetched.',
                'data' => $fareData
            ]);
        }else{
            return response()->json([
                'status' => 200,
                'message' => 'No Fare Found',
            ]);
        }
    }

    public function bookingsArchives(Request $request)
    {
        try {
            $serviceTypes = $this->serviceTypeService->getServiceTypes();
            $locations = $this->locationService->getLocations();
            $hotels = $this->hotelService->getHotels();
            $vehicleTypes = $this->vehicleClassService->getVehicleClass();
            $drivers = $this->driverService->getDrivers();
            $vehicles = $this->vehicleService->getvehicles();
            $driverOffDays = $this->driverOffDayService->getSavedDates();
            $hotelClients = $this->hotelService->getClientAdmins();
            $bookingData = $this->bookingService->getBookingArchiveData($request->query());
            return view('admin.bookings-archives.index', compact('serviceTypes', 'locations', 'driverOffDays', 'bookingData', 'hotels', 'vehicleTypes', 'drivers', 'vehicles', 'hotelClients'));
        } catch (\Exception $e) {
            $this->helper->alertResponse(__('messages.something_went_wrong'), 'error');
            // Handle any exceptions that occur
            $this->helper->handleException($e);
            return redirect()->back();
        }
    }

    public function filterBookingsArchives(Request $request)
    {
        try {
            // Retrieve booking data based on the filter criteria
            $bookingData = $this->bookingService->getBookingArchiveData($request->query());
            // Render the booking listing partial view with the filtered data
            $data = ['html' => view('admin.bookings-archives.partials.bookings-archives-listing', compact('bookingData'))->render()];
            // Return a JSON response with the updated booking listing HTML
            return $this->handleResponse($data, __("message.booking_filtered"), Response::HTTP_OK);
        } catch (\Exception $e) {
            // Handle the exception and return an error response
            $this->helper->handleException($e);
            return $this->handleResponse([], $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR, []);
        }
    }
    

    public function restoreBooking(Request $request, int $bookingId)
    {
        $logHeaders = $this->getHttpData($request);

        $booking = $this->bookingService->getBookingByIdToRestore($bookingId);

        $restoreBooking = $this->bookingService->restoreBooking($bookingId, $booking, $logHeaders);

        return redirect()->route('bookings')->with('success', 'Booking Restored Successfully');
        try {
        } catch (\Exception $e) {
            // Handle the exception and return an error response
            $this->helper->handleException($e);
            return $this->handleResponse([], $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR, []);
        }        
    }

    
    public function permanentDeleteBookings(DeleteBookingRequest $request)
    {
        try {

            $logHeaders = $this->getHttpData($request);

            $this->bookingService->permanentDeleteBookings($request->all(), $logHeaders);

            return $this->handleResponse([], __("message.booking_deleted"), Response::HTTP_OK);
        } catch (\Exception $e) {
            // Handle any exceptions that occur
            $this->helper->handleException($e);
            // Generate and return a response indicating the error that occurred
            return $this->handleResponse([], $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR, []);
        }
    }

    public function cancelBooking(Request $request)
    {
        try {
            $log_headers = $this->getHttpData($request);

            $booking = $this->bookingService->getBookingByIdToRestore($request->booking_id);

            $cancelBooking = $this->bookingService->cancelBooking($request->booking_id, $booking, $log_headers);

            return $this->handleResponse([], __("message.booking_cancelled_request"), Response::HTTP_OK);
        } catch (\Exception $e) {
            // Handle any exceptions that occur
            $this->helper->handleException($e);
            // Generate and return a response indicating the error that occurred
            return $this->handleResponse([], $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR, []);
        }
    }
}
