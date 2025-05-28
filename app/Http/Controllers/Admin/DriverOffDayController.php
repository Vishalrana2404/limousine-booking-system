<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\CustomHelper;
use App\Services\DriverService;
use App\Services\DriverOffDayService;
use App\Services\VehicleService;
use App\Http\Requests\AddDriverOffDayRequest;

/**
 * Class DriverOffDayController
 * 
 * @package  App\Http\Controllers\Admin
 */
class DriverOffDayController extends Controller
{
    public function __construct(
        private VehicleService $vehicleService,
        private DriverService $driverService,
        private DriverOffDayService $driverOffDayService,
        private CustomHelper $helper
    ) {
    }

    /**
     * Display the index page for drivers' off days.
     *
     * @param \Illuminate\Http\Request $request The request object containing any query parameters.
     * @return \Illuminate\View\View The view for the index page of drivers' off days.
     */
    public function index(Request $request)
    {
        try {
            // Retrieve driver data from the DriverService based on the query parameters
            $driverData = $this->driverService->getInhouseDrivers($request->query());
            // Return the view for the index page of drivers' off days, passing the driver data
            return view('admin.drivers-off-day.index', compact('driverData'));
        } catch (\Exception $e) {
            // Display an error message using alertResponse helper
            $this->helper->alertResponse(__('messages.something_went_wrong'), 'error');

            // Handle any exceptions that occur
            $this->handleException($e);

            // Generate and return a response indicating the error that occurred
            return $this->handleResponse([], $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR, []);
        }
    }

    /**
     * Save selected driver off days to the database.
     *
     * @param \Illuminate\Http\Request $request The HTTP request object containing the form data.
     * @return \Illuminate\Http\RedirectResponse Redirect to the drivers off day page on success or back to the previous page on error.
     */
    public function saveDriverOffDays(AddDriverOffDayRequest $request)
    {
        try {
            $log_headers = $this->getHttpData($request);
            // Check if the checkbox was unchecked
            if (isset($request['checked']) && $request['checked'] === "true") {
                // Save selected dates to the database using a service or repository
                $data = $this->driverOffDayService->saveDriverDayOff($request->all(), $log_headers);
                return $this->handleResponse($data, __("message.driver_off_day_saved_successfully"), Response::HTTP_OK);
            } else {
                // Logic to update the 'deleted_at' property when unchecked
                $data = $this->driverOffDayService->deleteDriverDayOff($request->all(), $log_headers);
                return $this->handleResponse($data, __("message.driver_off_day_deleted_successfully"), Response::HTTP_OK);
            }
        } catch (\Exception $e) {
            // Handle any exceptions that occur
            $this->helper->handleException($e);

            // Display an error message and redirect back to the previous page
            $this->helper->alertResponse(__('messages.something_went_wrong'), 'error');
            return redirect()->back();
        }
    }

    public function getDriverOffDays(Request $request)
    {
        try {
            // Retrieve driver data from the DriverService based on the query parameters
            $driverData = $this->driverService->getInhouseDrivers($request->query());
            return $this->handleResponse($driverData, __("message.driver_off_day_deleted_successfully"), Response::HTTP_OK);
        } catch (\Exception $e) {
            // Handle any exceptions that occur
            $this->handleException($e);

            // Generate and return a response indicating the error that occurred
            return $this->handleResponse([], $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR, []);
        }
    }
}
