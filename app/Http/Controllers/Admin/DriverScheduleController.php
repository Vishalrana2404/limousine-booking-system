<?php

namespace App\Http\Controllers\Admin;

use App\CustomHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\DriverScheduleService;
use App\Services\DriverService;
use App\Services\ExportService;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Class DriverScheduleController
 * 
 * @package  App\Http\Controllers\Admin
 */
class DriverScheduleController extends Controller
{
    /**
     * DriverScheduleController constructor.
     * 
     * @param DriverScheduleService $DriverScheduleService The driverSchedule service instance.
     */
    public function __construct(
        private DriverScheduleService $driverScheduleService,
        private DriverService $driverService,
        private ExportService $exportService,
        private CustomHelper $helper
    ) {
    }

    /**
     * Display list of drivers schedule.
     *
     * @param Request $request The HTTP request instance.
     * @return Response The HTTP response instance.
     */
    public function index(Request $request)
    {
        try {
            $driverData = $this->driverService->getActiveDrivers($request->query());
            $driversBooking = $this->driverScheduleService->getDriversBookingData($request->query());
            return view('admin.driver-schedule.index', compact('driverData', 'driversBooking'));
        } catch (\Exception $e) {
            $this->helper->alertResponse(__('messages.something_went_wrong'), 'error');
            // Handle any exceptions that occur
            $this->handleException($e);
            // Generate and return a response indicating the error that occurred
            return $this->handleResponse([], $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR, []);
        }
    }

    /**
     * Filter bookings based on the provided request parameters.
     *
     * @param Request $request The HTTP request containing filter criteria.
     * @return JsonResponse JSON response containing filtered booking listing HTML or error message.
     */
    public function filterDriversBookings(Request $request)
    {
        try {
            // Retrieve booking data based on the filter criteria
            $driversBooking = $this->driverScheduleService->getDriversBookingData($request->query());
            // Render the driver schedule listing partial view with the filtered data
            $data = [
                'html' => view('admin.driver-schedule.partials.driver-schedule-listing', compact('driversBooking'))->render(),
                'pagination' => $driversBooking->links('pagination::bootstrap-5')->render(), // Include pagination HTML
                'total' => $driversBooking->total(), // Use total() for paginated collection
            ];
            // Return a JSON response with the updated booking listing HTML
            return $this->handleResponse($data, __("message.driver_schedule_filtered"), Response::HTTP_OK);
        } catch (\Exception $e) {
            // Handle the exception and return an error response
            $this->helper->handleException($e);
            return $this->handleResponse([], $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR, []);
        }
    }

    /**
     * Export driver booking data in specified format (PDF or Excel).
     *
     * @param Request $request The HTTP request object containing input data.
     * @return \Illuminate\Http\JsonResponse|string Returns the file path for download if the format is PDF or Excel,otherwise returns a JSON response with the rendered HTML.
     */
    public function export(Request $request)
    {
        try {
            $filePath = $this->driverScheduleService->exportData($request->all());
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
    public function sendDriverSchedule(Request $request)
    {
        try {
            $this->driverScheduleService->sendDriverSchedule($request->all());
            return $this->handleResponse([], __("message.driver_schedule_sent"), Response::HTTP_OK);
        } catch (\Exception $e) {
            // Handle exceptions and return an error response
            $this->helper->handleException($e);
            return $this->handleResponse([], $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR, []);
        }
    }
}
