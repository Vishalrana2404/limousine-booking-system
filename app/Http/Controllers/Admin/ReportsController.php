<?php

namespace App\Http\Controllers\Admin;

use App\CustomHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\ReportsService;
use App\Services\DriverService;
use App\Services\ExportService;
use App\Services\HotelService;
use App\Services\ClientService;
use App\Services\EventService;
use App\Services\UserService;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportsController extends Controller
{
    public function __construct(
        private ReportsService $reportsService,
        private DriverService $driverService,
        private ExportService $exportService,
        private HotelService $hotelService,
        private ClientService $clientService,
        private EventService $eventService,
        private UserService $userService,
        private CustomHelper $helper
    ) {
    }

    public function index(Request $request)
    {
        try {
            $driverData = $this->driverService->getActiveDrivers($request->query())->sortBy(fn($driver) => strtolower($driver->name));
            $hotelsData = $this->hotelService->getActiveHotels($request->query())->sortBy(fn($hotel) => strtolower($hotel->name));
            $usersData = $this->userService->getAllUsers($request->query())->sortBy(function ($user) {
                $fullName = strtolower(trim($user->first_name . ' ' . ($user->last_name ?? '')));
                return $fullName;
            });            
            $eventsData = $this->eventService->getActiveEvents($request->query())->sortBy(fn($event) => strtolower($event->name));
            $driversBooking = $this->reportsService->getReportsBookingData($request->query());
            return view('admin.reports.index', compact('driverData', 'driversBooking', 'hotelsData', 'eventsData', 'usersData'));
        } catch (\Exception $e) {
            $this->helper->alertResponse(__('messages.something_went_wrong'), 'error');
            // Handle any exceptions that occur
            $this->handleException($e);
            // Generate and return a response indicating the error that occurred
            return $this->handleResponse([], $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR, []);
        }
    }

    public function filterReports(Request $request)
    {
        try {
            // Retrieve booking data based on the filter criteria
            $driversBooking = $this->reportsService->getReportsBookingData($request->query());
            // Render the driver schedule listing partial view with the filtered data
            $data = [
                'html' => view('admin.reports.partials.reports-listing', compact('driversBooking'))->render(),
                'pagination' => $driversBooking->links('pagination::bootstrap-5')->render(), // Include pagination HTML
                'total' => $driversBooking->total(), // Use total() for paginated collection
            ];
            // Return a JSON response with the updated booking listing HTML
            return $this->handleResponse($data, __("message.reports_filtered"), Response::HTTP_OK);
        } catch (\Exception $e) {
            // Handle the exception and return an error response
            $this->helper->handleException($e);
            return $this->handleResponse([], $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR, []);
        }
    }

    public function export(Request $request)
    {
        try {
            $filePath = $this->reportsService->exportData($request->all());
            $format = $request->input('format');
            // Determine appropriate file extension and content type
            $contentType = ($format === 'excel') ? 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' : 'image/jpeg';
            ini_set('memory_limit', '512M');
            ini_set('max_execution_time', 300);
            // Set headers
            $headers = [
                'Content-Type' => $contentType,
                'Content-Disposition' => 'attachment; filename="' . basename($filePath) . '"',
            ];
            $response = new StreamedResponse(function () use ($filePath) {
                // Stream the file
                $stream = Storage::disk('public')->readStream($filePath);
                fpassthru($stream);
                fclose($stream);
            }, 200, $headers);
        
            // Delete the file after the response has been sent
            $response->send();
        
            // Now delete the file
            Storage::disk('public')->delete($filePath);        
            return $response;
        } catch (\Exception $e) {
            // Handle exceptions and return an error response
            $this->helper->handleException($e);
            return $this->handleResponse([], $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR, []);
        }
    }
}
