<?php

namespace App\Services;

use App\CustomHelper;
use App\Repositories\DriverRepository;
use App\Models\Booking;
use App\Repositories\Interfaces\BookingInterface;
use App\Repositories\Interfaces\DriverInterface;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\Storage;

class ReportsService
{
    protected $chatIds;
    /**
     * Create a new class instance.
     */
    public function __construct(
        private BookingInterface $bookingRepository,
        private ActivityLogService $activityLogService,
        private ExportService $exportService,
        private DriverInterface $driverRepository,
        private TelegramService $telegramService,
        private Booking $bookingModal,
        private CustomHelper $helper,
        private ConvertFileService $convertFileService,

    ) {
    }

    public function getReportsBookingData(array $requestData = [])
    {
        try {
            // Retrieve the logged-in user
            $loggedUser = Auth::user();
            // Extract parameters from the request data or use default values
            $page = $requestData['page'] ?? 1;
            $search = $requestData['search'] ?? '';
            $searchByBookingId = $requestData['searchByBookingId'] ?? '';
            $sortField = $requestData['sortField'] ?? 'id';
            $pickupDateRange = $requestData['pickupDateRange'] ?? null;
            $driverId = $requestData['driverId'] ?? null;
            $driverType = $requestData['driverType'] ?? null;
            $hotelId = $requestData['hotelId'] ?? null;
            $eventId = $requestData['eventId'] ?? null;
            $userId = $requestData['userId'] ?? null;
            $export = $requestData['format'] ?? null;
            $noPagination = $requestData['noPagination'] ?? false;
            if ($export) {
                $sortDirection = $requestData['sortDirection'] ?? 'asc';
            } else {
                $sortDirection = $requestData['sortDirection'] ?? 'desc';
            }
            $currentDate = Carbon::now()->toDateString(); // Get current date in MySQL format
            if ($pickupDateRange && $pickupDateRange !== 'Select A Range') {
                $dates = explode("-", $pickupDateRange);
                $startDate = Carbon::createFromFormat('d/m/Y H:i', trim($dates[0]))->format('Y-m-d H:i:s');
                $endDate = Carbon::createFromFormat('d/m/Y H:i', trim($dates[1]))->format('Y-m-d H:i:s');
            } else {
                // $startDate = $currentDate;
                // $endDate = Carbon::now()->addDay()->startOfDay()->addHours(4)->toDateTimeString(); // Set end date to tomorrow at 4 AM
                $startDate = null;
                $endDate = null;
            }
            if(!empty($searchByBookingId))
            {
                $startDate = null;
                $endDate = null;
            }
            return $this->bookingRepository->getBookingsForReports($loggedUser, $startDate, $endDate, $search, $searchByBookingId, $page, $sortField, $sortDirection, $driverId, $driverType, $hotelId, $eventId, $userId, $noPagination, true);
        } catch (\Exception $e) {
            // Throw an exception with the error message if an error occurs
            throw new \Exception($e->getMessage());
        }
    }

    public function getSavedScheduleData()
    {
        return $this->bookingRepository->getBookingData();
    }

    public function exportData($requestData)
    {
        try {
            $requestData['noPagination'] = true;
            $data = $this->getReportsBookingData($requestData);
            if (!$data->isEmpty()) {
                $isDisplayContact = $requestData['isDisplayContact'];
                $isDisplayPickup = $requestData['isDisplayPickup'];
                $isDisplayDropOff = $requestData['isDisplayDropOff'];
                $isDisplayAdditionalStops = $requestData['isDisplayAdditionalStops'];
                $isDisplayGuest = $requestData['isDisplayGuest'];
                $isDisplayEvent = $requestData['isDisplayEvent'];
                $filePath = null;
                if ($requestData['format'] === 'pdf') {
                    $data = [
                        "title" => " reports " . $requestData["pickupDateRange"],
                        "driversBooking" => $data,
                        "isDisplayContact" => $isDisplayContact,
                        "isDisplayPickup" => $isDisplayPickup,
                        "isDisplayDropOff" => $isDisplayDropOff,
                        "isDisplayAdditionalStops" => $isDisplayAdditionalStops,
                        "isDisplayGuest" => $isDisplayGuest,
                        "isDisplayEvent" => $isDisplayEvent

                    ];
                    $fileName = time() . "_reports.pdf";
                    $filePath = 'exports/pdf/' . $fileName;
                    $this->exportService->setPath($filePath);
                    $this->exportService->exportToPDF('admin.driver-schedule.export', $data);
                    $pdfPath = Storage::disk('public')->path($filePath);
                    $imagePath =  $this->convertFileService->convert($pdfPath);
                    $relativePath = str_replace(storage_path('app/public/'), '', $imagePath);
                    return $relativePath;
                }
                if ($requestData['format'] === 'excel') {
                    $fileName = time() . "_reports.xlsx";
                    $filePath = 'exports/excel/';
                    $this->exportService->setPath($filePath);
                    $columns = ['Booking', 'Time', 'Type', 'Driver', 'Vehicle', 'Status', 'Booked By', 'Access Given Clients', 'Booking Date'];

                    $position = 3;

                    $dynamicFields = [];

                    if ($isDisplayPickup === "true") {
                        $dynamicFields[] = 'Pick-up';
                    }
                    if ($isDisplayDropOff === "true") {
                        $dynamicFields[] = 'Drop-off';
                    }
                    if ($isDisplayAdditionalStops === "true") {
                        $dynamicFields[] = 'Additional Stops';
                    }
                    if ($isDisplayGuest === "true") {
                        $dynamicFields[] = 'Guest Name';
                    }
                    $dynamicFields[] = 'Corporate';

                    if ($isDisplayEvent === "true") {
                        $dynamicFields[] = 'Event';
                    }
                    if ($isDisplayContact === "true") {
                        $dynamicFields[] = 'Contact';
                    }

                    // Insert all dynamic fields at the correct position before 'Corporate'
                    array_splice($columns, $position, 0, $dynamicFields);
                    $excelData =  $this->retriveExcelData($data, $isDisplayContact, $isDisplayPickup, $isDisplayDropOff, $isDisplayAdditionalStops, $isDisplayGuest, $isDisplayEvent);
                    $this->exportService->exportToExcel($excelData, $columns, $fileName);
                    $filePath = $filePath . $fileName;
                    return  $filePath;
                }
            }
        } catch (\Exception $e) {
            // Throw an exception with the error message if an error occurs
            throw new \Exception($e->getMessage());
        }
    }

    private function retriveExcelData($scheduleData, $isDisplayContact, $isDisplayPickup, $isDisplayDropOff, $isDisplayAdditionalStops, $isDisplayGuest, $isDisplayEvent)
    {
        foreach ($scheduleData as $key => $schedule) {
            $pickUpLocation = null;
            $pickupLocationId = $schedule->pick_up_location_id ?? null;
            if ($pickupLocationId && $pickupLocationId !== 8) {
                $pickUpLocation = $schedule->pickUpLocation->name ?? null;
            } else {
                $pickUpLocation = $schedule->pick_up_location;
            }

            $dropOffLocation = null;
            $dropOffLocationId = $schedule->drop_off_location_id ?? null;
            if ($dropOffLocationId && $dropOffLocationId !== 8) {
                $dropOffLocation = $schedule->dropOffLocation->name ?? null;
            } else {
                $dropOffLocation = $schedule->drop_of_location;
            }
            $additionalStops = $schedule->additional_stops;
            $hotel = $schedule->client->hotel->name ?? null;
            $event = $schedule->client->event ?? null;
            if ($hotel) {
                $hotelValue = $hotel . ' (' . $event . ')';
            } else {
                $hotelValue = null;
            }
            $pickUpTime = null;
            $pickup = $schedule->pickup_time ? $this->helper->formatTime($schedule->pickup_time) : null;
            if ($schedule->service_type_id === 4) {
                if ($pickup && $schedule->no_of_hours) {
                    $pickupDateTime = new DateTime($pickup);
                    $pickupDateTime->modify('+' . $schedule->no_of_hours . ' hours');
                    $endTime = $pickupDateTime->format('H:i');
                    $pickUpTime = $pickup . PHP_EOL . '(' . $endTime . ')';
                } else {
                    $pickUpTime = null;
                }
            } else {
                $pickUpTime = $pickup ? $pickup : 'N/A';
            }
            $guestNames = $schedule->guest_name ?? null;
            $eventName = !empty($schedule->event) && !empty($schedule->event->name) ? $schedule->event->name : null;
            $vehicleClassName = $schedule->vehicle->vehicleClass->name ?? null;
            $vehicleNumber = $schedule->vehicle->vehicle_number ?? null;
            $vehicle = null;
            if ($vehicleClassName && $vehicleNumber) {
                $vehicle = $vehicleClassName . ' (' . $vehicleNumber . ')';
            }
            $contact = $schedule->country_code ? "+(" .  $schedule->country_code . ")" . $schedule->phone : $schedule->phone;

            $clientDetails = '';
            if(!empty($schedule->linked_clients)){
                $allClientIds = explode(',', $schedule->linked_clients);
                $linkedClients = $this->bookingModal->linkedClients($allClientIds);
                if(!empty($linkedClients))
                {
                    foreach($linkedClients as $clientKey => $client){

                        if(!empty($client))
                        {
                            if($clientDetails !== '')
                            {
                                $clientDetails .= ',';
                            }
                            $clientDetails .= (!empty($client->first_name) ? $client->first_name : '') . ' ' . (!empty($client->last_name) ? $client->last_name : '');
                        }

                    }
                }
            }

            $driverName = !empty($schedule->driver) && !empty($schedule->driver->name) ? $schedule->driver->name : '';
            $booking = [
                $schedule->id,
                $pickUpTime ?? null,
                $schedule->serviceType->name ?? null,
                $driverName,
                $vehicle,
                $schedule->status,
                ($schedule->createdBy->first_name ?? null) . ' ' . ($schedule->createdBy->last_name ?? null),
                $clientDetails,
                date('d-m-Y H:i', strtotime($schedule->created_at)) ?? null,
            ];

            $position = 3;

            $dynamicData = [];

            if ($isDisplayPickup === "true") {
                $dynamicData[] = $pickUpLocation;
            }
            if ($isDisplayDropOff === "true") {
                $dynamicData[] = $dropOffLocation;
            }
            if ($isDisplayAdditionalStops === "true") {
                $dynamicData[] = $additionalStops;
            }
            if ($isDisplayGuest === "true") {
                $dynamicData[] = $guestNames;
            }
            $dynamicData[] = $hotelValue;

            if ($isDisplayEvent === "true") {
                $dynamicData[] = $eventName;
            }
            if ($isDisplayContact === "true") {
                $dynamicData[] = $contact;
            }

            // Insert all dynamic fields at the correct position before 'Corporate'
            array_splice($booking, $position, 0, $dynamicData);

            // Add this booking data to the array of all booking data
            $bookingData[] = $booking;
        }
        return  $bookingData;
    }
}
