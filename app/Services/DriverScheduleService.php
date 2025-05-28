<?php

namespace App\Services;

use App\CustomHelper;
use App\Repositories\DriverRepository;
use App\Repositories\Interfaces\BookingInterface;
use App\Repositories\Interfaces\DriverInterface;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\Storage;

/**
 * Class DriverScheduleService
 * 
 * @package App\Services
 */
class DriverScheduleService
{
    protected $chatIds;
    /**
     * Constructs a new instance of the class.
     *
     * @param BookingInterface   $bookingRepository  The booking repository service used for accessing booking data.
     * @param ActivityLogService $activityLogService The activity log service used for logging activities related to bookings.
     * @param ExportService      $exportService      The export service used for exporting booking data.
     */
    public function __construct(
        private BookingInterface $bookingRepository,
        private ActivityLogService $activityLogService,
        private ExportService $exportService,
        private DriverInterface $driverRepository,
        private TelegramService $telegramService,
        private CustomHelper $helper,
        private ConvertFileService $convertFileService,

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
     *                           - 'sortField': The field to sort bookings by.
     *                           - 'sortDirection': The direction for sorting ('asc' or 'desc').
     * @return \Illuminate\Pagination\LengthAwarePaginator A paginated list of bookings.
     * @throws \Exception If an error occurs during the retrieval process.
     */
    public function getDriversBookingData(array $requestData = [])
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
            return $this->bookingRepository->getBookings($loggedUser, $startDate, $endDate, $search, $searchByBookingId, $page, $sortField, $sortDirection, $driverId, $noPagination, true);
        } catch (\Exception $e) {
            // Throw an exception with the error message if an error occurs
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Retrieves saved schedule data from the booking repository.
     *
     * This method fetches saved schedule data from the booking repository.
     *
     * @return array An array containing saved schedule data.
     */
    public function getSavedScheduleData()
    {
        return $this->bookingRepository->getBookingData();
    }

    /**
     * Exports data based on the provided format.
     *
     * This method exports data to either PDF or Excel format based on the format specified in the request data.
     *
     * @param array $requestData An array containing request data, including the export format.
     * @param mixed $data The data to be exported.
     * @param array $columns An array containing column names for the exported data.
     */
    public function exportData($requestData)
    {
        try {
            $requestData['noPagination'] = true;
            $data = $this->getDriversBookingData($requestData);
            if (!$data->isEmpty()) {
                $isDisplayContact = $requestData['isDisplayContact'];
                $filePath = null;
                if ($requestData['format'] === 'pdf') {
                    $data = [
                        "title" => " schedule " . $requestData["pickupDateRange"],
                        "driversBooking" => $data,
                        "isDisplayContact" => $isDisplayContact

                    ];
                    $fileName = time() . "_schedule.pdf";
                    $filePath = 'exports/pdf/' . $fileName;
                    $this->exportService->setPath($filePath);
                    $this->exportService->exportToPDF('admin.driver-schedule.export', $data);
                    $pdfPath = Storage::disk('public')->path($filePath);
                    $imagePath =  $this->convertFileService->convert($pdfPath);
                    $relativePath = str_replace(storage_path('app/public/'), '', $imagePath);
                    return $relativePath;
                }
                if ($requestData['format'] === 'excel') {
                    $fileName = time() . "_schedule.xlsx";
                    $filePath = 'exports/excel/';
                    $this->exportService->setPath($filePath);
                    $columns = ['Booking', 'Time', 'Type', 'Pick-up', 'Drop-off', 'Guest Name', 'Client', 'Driver Remarks', 'Driver', 'Client Instructions', 'Vehicle'];
                    if ($isDisplayContact === "true") {
                        array_splice($columns, 7, 0, 'Contact'); // Add 'Contact' before 'Driver Remarks'
                    }
                    $excelData =  $this->retriveExcelData($data, $isDisplayContact);
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
    public function sendDriverSchedule(array $requestData = [])
    {
        try {
            $requestData['noPagination'] = true;
            $data = $this->getDriversBookingData($requestData);
            $driver = $this->driverRepository->getDriverById($requestData['driverId']);
            $driverChatId = $driver->chat_id ?? null;
            $isDisplayContact = $requestData['isDisplayContact'] ?? "true";
            if ($driverChatId) {
                $data = [
                    "title" => "schedule " . $requestData["pickupDateRange"],
                    "driversBooking" => $data,
                    "isDisplayContact" => $isDisplayContact,
                ];
                $fileName = time() . "_schedule.pdf";
                $filePath = 'exports/pdf/' . $fileName;
                $this->exportService->setPath($filePath);
                $this->exportService->exportToPDF('admin.driver-schedule.export', $data);
                $message = "Hi " . $driver->name . ",\nThis is your schedule for the date of " . $requestData["pickupDateRange"] . " Please find attached document.";
                $pdfPath = Storage::disk('public')->path($filePath);
                $imagePath = $this->convertFileService->convert($pdfPath);
                $this->telegramService->sendMessage($driverChatId, $message, $imagePath);
            }
        } catch (\Exception $e) {
            // Throw an exception with the error message if an error occurs
            throw new \Exception($e->getMessage());
        }
    }

    private function retriveExcelData($scheduleData, $isDisplayContact)
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
            $vehicleClassName = $schedule->vehicle->vehicleClass->name ?? null;
            $vehicleNumber = $schedule->vehicle->vehicle_number ?? null;
            $vehicle = null;
            if ($vehicleClassName && $vehicleNumber) {
                $vehicle = $vehicleClassName . ' (' . $vehicleNumber . ')';
            }
            $contact = $schedule->country_code ? "+(" .  $schedule->country_code . ")" . $schedule->phone : $schedule->phone;

            $booking = [
                $schedule->id,
                $pickUpTime ?? null,
                $schedule->serviceType->name ?? null,
                $pickUpLocation,
                $dropOffLocation,
                $guestNames,
                $hotelValue,
                $schedule->driver_remarks ?? null,
                $schedule->driver->name ?? null,
                $schedule->client_instructions ?? null,
                $vehicle
            ];

            if ($isDisplayContact === "true") {
                array_splice($booking, 7, 0, $contact);
            }

            // Add this booking data to the array of all booking data
            $bookingData[] = $booking;
        }
        return  $bookingData;
    }
}
