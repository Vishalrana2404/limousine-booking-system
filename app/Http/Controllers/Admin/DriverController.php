<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\CustomHelper;
use App\Http\Requests\AddDriverRequest;
use App\Http\Requests\DeleteDriverRequest;
use App\Http\Requests\EditDriverRequest;
use App\Models\Driver;
use App\Services\DriverService;
use App\Services\VehicleService;

/**
 * Class DriverController
 * 
 * @package  App\Http\Controllers\Admin
 */
class DriverController extends Controller
{
    public function __construct(
        private VehicleService $vehicleService,
        private DriverService $driverService,
        private CustomHelper $helper
    ) {
    }

    /**
     * Display a listing of the drivers.
     *
     * @param \Illuminate\Http\Request $request The HTTP request object containing query parameters.
     * @return \Illuminate\View\View The view displaying the drivers data.
     */
    public function index(Request $request)
    {
        try {
            // Retrieve drivers data from the DriverService
            $driverData = $this->driverService->getDriverData($request->query());
            return view('admin.drivers.drivers', compact('driverData'));
        } catch (\Exception $e) {
            // Display an alert message for the user
            $this->helper->alertResponse(__('messages.something_went_wrong'), 'error');

            // Handle any exceptions that occur
            $this->handleException($e);

            // Generate and return a response indicating the error that occurred
            return $this->handleResponse([], $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR, []);
        }
    }

    /**
     * Show the form for creating a new driver.
     *
     * @param \Illuminate\Http\Request $request The request containing any data needed for creating the driver.
     * @return \Illuminate\View\View The view for creating a new driver.
     */
    public function create(Request $request)
    {
        // Get the race options from the configuration
        $raceArr = config('constants.race');

        // Get vehicles data from the VehicleService based on the request data
        $vehiclesData = $this->vehicleService->getVehicles();

        // Return the view for creating a new driver, passing necessary data
        return view('admin.drivers.create-driver', compact('vehiclesData', 'raceArr'));
    }

    /**
     * Save a new driver.
     *
     * @param \App\Http\Requests\AddDriverRequest $request The request containing the driver data.
     * @return \Illuminate\Http\RedirectResponse Redirects to the drivers page after saving.
     */
    public function save(AddDriverRequest $request)
    {
        try {
            $log_headers = $this->getHttpData($request);
            // Create a new driver using the provided request data
            $this->driverService->createDriver($request->all(), $log_headers);

            // Display a success message and redirect to the drivers page
            $this->helper->alertResponse(__('message.driver_created'), 'success');
            return redirect('drivers');
        } catch (\Exception $e) {
            // Handle any exceptions that occur
            $this->helper->handleException($e);

            // Display an error message and redirect back to the previous page
            $this->helper->alertResponse(__('messages.something_went_wrong'), 'error');
            return redirect()->back();
        }
    }

    /**
     * Show the form for editing a driver.
     *
     * @param \App\Models\Driver $driver The driver to be edited.
     * @return \Illuminate\View\View The view for editing a driver.
     */
    public function edit(Driver $driver)
    {
        // Get the race options from the configuration
        $raceArr = config('constants.race');

        // Get vehicles data from the VehicleService
        $vehiclesData = $this->vehicleService->getVehicles();

        // Return the view for editing a driver, passing necessary data
        return view('admin.drivers.edit-driver', compact('vehiclesData', 'driver', 'raceArr'));
    }

    /**
     * Update an existing driver.
     *
     * @param \App\Http\Requests\EditDriverRequest $request The request containing the updated driver data.
     * @param \App\Models\Driver $driver The driver instance to be updated.
     * @return \Illuminate\Http\RedirectResponse Redirects to the drivers page after updating.
     */
    public function update(EditDriverRequest $request, Driver $driver)
    {
        try {
            $log_headers = $this->getHttpData($request);
            // Update the driver using the provided request data and driver instance
            $this->driverService->updateDriver($request->all(), $driver, $log_headers);

            // Display a success message and redirect to the drivers page
            $this->helper->alertResponse(__('message.driver_updated'), 'success');
            return redirect('drivers');
        } catch (\Exception $e) {
            // Handle any exceptions that occur
            $this->helper->handleException($e);

            // Display an error message and redirect back to the previous page
            $this->helper->alertResponse(__('messages.something_went_wrong'), 'error');
            return redirect()->back();
        }
    }

    /**
     * Delete a driver.
     *
     * @param \App\Http\Requests\DeleteDriverRequest $request The request containing the data for driver deletion.
     * @return \Illuminate\Http\JsonResponse A JSON response indicating the result of the deletion operation.
     */
    public function delete(DeleteDriverRequest $request)
    {
        try {
            $log_headers = $this->getHttpData($request);
            // Delete the driver using the provided request data
            $this->driverService->deleteDriver($request->all(), $log_headers);

            // Generate and return a successful response with the result of the driver deletion operation
            return $this->handleResponse([], __("message.driver_deleted"), Response::HTTP_OK);
        } catch (\Exception $e) {
            // Handle any exceptions that occur
            $this->helper->handleException($e);

            // Generate and return a response indicating the error that occurred
            return $this->handleResponse([], $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR, []);
        }
    }

    /**
     * Filter and retrieve drivers based on criteria.
     *
     * @param \Illuminate\Http\Request $request The request containing filter criteria.
     * @return \Illuminate\Http\JsonResponse A JSON response with the filtered driver data.
     */
    public function filterDrivers(Request $request)
    {
        try {
            // Retrieve driver data from the DriverService based on the provided criteria
            $driverData = $this->driverService->getDriverData($request->query());

            // Render the HTML for the driver listing view
            $data = ['html' => view('admin.drivers.partials.drivers-listing', compact('driverData'))->render()];

            // Generate and return a successful response with the filtered driver data
            return $this->handleResponse($data, __("message.driver_filtered"), Response::HTTP_OK);
        } catch (\Exception $e) {
            // Handle any exceptions that occur
            $this->helper->handleException($e);

            // Generate and return a response indicating the error that occurred
            return $this->handleResponse([], $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR, []);
        }
    }
}
