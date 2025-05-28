<?php

namespace App\Http\Controllers\Admin;

use App\CustomHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\VehicleService;
use App\Http\Requests\AddVehicleRequest;
use App\Http\Requests\EditVehicleRequest;
use App\Http\Requests\DeleteVehicleRequest;
use App\Http\Requests\BulkVehicleStatusUpdateRequest;
use App\Models\Vehicle;
use App\Services\VehicleClassService;

/**
 * Class VehicleController
 * 
 * @package  App\Http\Controllers\Admin
 */
class VehicleController extends Controller
{
    /**
     * VehicleController constructor.
     * 
     * @param VehicleService $VehicleService The vehicle service instance.
     */
    public function __construct(
        private VehicleService $vehicleService,
        private VehicleClassService $vehicleClassService,
        private CustomHelper $helper
    ) {
    }
    /**
     * Display list of vehicles.
     *
     * @param Request $request The HTTP request instance.
     * @return Response The HTTP response instance.
     */
    public function index(Request $request)
    {
        try {
            // Retrieve vehicle data from the VehicleService
            $vehicleData = $this->vehicleService->getVehicleData($request->query());
            return view('admin.vehicle.index', compact('vehicleData'));
        } catch (\Exception $e) {
            $this->helper->alertResponse(__('messages.something_went_wrong'), 'error');
            // Handle any exceptions that occur
            $this->handleException($e);
            // Generate and return a response indicating the error that occurred
            return $this->handleResponse([], $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR, []);
        }
    }

    /**
     * Show the form for creating a new vehicle.
     *
     * @param \Illuminate\Http\Request $request The request object (optional).
     * @return \Illuminate\View\View The view for creating a new vehicle.
     */
    public function create(Request $request)
    {
        // Get vehicle class data from the VehicleClassService
        $vehicleClassData = $this->vehicleClassService->getVehicleClass();

        // Return the view for creating a new vehicle, passing necessary data
        return view('admin.vehicle.create-vehicle', compact('vehicleClassData'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request The HTTP request instance.
     * @return Response The HTTP response instance.
     */
    public function save(AddVehicleRequest $request)
    {
        try {
            $log_headers = $this->getHttpData($request);
            // Create a new vehicle using the VehicleService
            $vehicleImage = $request->has('image') ? $request->file('image') : false;
            $vehicleData = $this->vehicleService->createVehicle($request->all(),$vehicleImage, $log_headers);
            $this->helper->alertResponse(__('message.vehicle_created_successfully'), 'success');
            return redirect('vehicles');
        } catch (\Exception $e) {
            $this->helper->handleException($e);
            $this->helper->alertResponse(__('messages.something_went_wrong'), 'error');
            return redirect()->back();
        }
    }

    /**
     * Show the form for editing a vehicle.
     *
     * @param \App\Models\Vehicle $vehicle The vehicle to be edited.
     * @return \Illuminate\View\View The view for editing a vehicle.
     */
    public function edit(Vehicle $vehicle)
    {
        // Get vehicle class data from the VehicleClassService
        $vehicleClassData = $this->vehicleClassService->getVehicleClass();

        // Return the view for editing a vehicle, passing necessary data
        return view('admin.vehicle.update-vehicle', compact('vehicleClassData', 'vehicle'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request The HTTP request instance.
     * @return Response The HTTP response instance.
     */
    public function update(EditVehicleRequest $request, Vehicle $vehicle)
    {
        try {
            $log_headers = $this->getHttpData($request);
            $vehicleImage = $request->has('image') ? $request->file('image') : false;
            // Update the vehicle using the VehicleService
            $this->vehicleService->updateVehicle($request->all(), $vehicle, $vehicleImage, $log_headers);
            $this->helper->alertResponse(__('message.vehicle_updated_successfully'), 'success');
            return redirect('vehicles');
        } catch (\Exception $e) {
            $this->helper->handleException($e);
            $this->helper->alertResponse(__('messages.something_went_wrong'), 'error');
            return redirect()->back();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id The ID of the vehicle to delete.
     * @return Response The HTTP response instance.
     */
    public function delete(DeleteVehicleRequest $request)
    {
        try {
            $log_headers = $this->getHttpData($request);
            // Delete the client based on the request data
            $vehicleData = $this->vehicleService->deleteVehicle($request->all(), $log_headers);
            if(!$vehicleData){
                return $this->handleResponse([], __("message.can_not_delete"), 422);
            }
            // Generate and return a successful response with the updated vehicle data
            return $this->handleResponse($vehicleData, __("message.vehicle_deleted_successfully"), Response::HTTP_OK);
        } catch (\Exception $e) {
            // Handle any exceptions that occur
            $this->helper->handleException($e);
            // Generate and return a response indicating the error that occurred
            return $this->handleResponse([], $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR, []);
        }
    }

    /**
     * Display details of a specific vehicle.
     *
     * @param \App\Models\Vehicle $vehicle The vehicle to be viewed.
     * @return \Illuminate\View\View The view for viewing a vehicle's details.
     */
    public function view(Vehicle $vehicle)
    {
        // Get detailed information about the vehicle from the VehicleService
        $vehicle = $this->vehicleService->viewVehicle($vehicle);

        // Return the view for viewing a vehicle's details, passing necessary data
        return view('admin.vehicle.view-vehicle', compact('vehicle'));
    }

    /**
     * Update status for multiple vehicles in bulk.
     *
     * @param \App\Http\Requests\BulkVehicleStatusUpdateRequest $request The request containing data for updating vehicle statuses.
     * @return \Illuminate\Http\JsonResponse A JSON response indicating the result of the bulk status update operation.
     */
    public function updateBulkStatus(BulkVehicleStatusUpdateRequest $request)
    {
        try {
            $log_headers = $this->getHttpData($request);
            // Update the status of vehicles using the VehicleService
            $vehicleData = $this->vehicleService->updateBulkStatus($request->all(), $log_headers);

            // Generate and return a successful response
            return $this->handleResponse($vehicleData, __("message.vehicle_status_updated_successfully"), Response::HTTP_OK);
        } catch (\Exception $e) {
            // Handle any exceptions that occur
            $this->handleException($e);

            // Generate and return a response indicating the error that occurred
            return $this->handleResponse([], $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR, []);
        }
    }

    /**
     * Filter and retrieve vehicle data based on criteria.
     *
     * @param \Illuminate\Http\Request $request The request object containing filter criteria.
     * @return \Illuminate\Http\JsonResponse A JSON response with the filtered vehicle data.
     */
    public function filterVehicle(Request $request)
    {
        try {
            // Retrieve vehicle data from the VehicleService based on the provided criteria
            $vehicleData = $this->vehicleService->getVehicleData($request->query());

            // Render the HTML for the vehicle listing view
            $data = ['html' => view('admin.vehicle.partials.vehicle', compact('vehicleData'))->render()];

            // Generate and return a successful response with the filtered vehicle data
            return $this->handleResponse($data, __("message.vehicle_filtered_successfully"), Response::HTTP_OK);
        } catch (\Exception $e) {
            // Handle any exceptions that occur
            $this->handleException($e);

            // Generate and return a response indicating the error that occurred
            return $this->handleResponse([], $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR, []);
        }
    }

    /**
     * Check if the provided vehicle number is unique.
     *
     * Checks if the provided vehicle number is unique among users, excluding the user with the specified user ID (if provided).
     *
     * @param Request $request The HTTP request object containing the vehicle number.
     * 
     * @return \Illuminate\Http\JsonResponse A JSON response indicating whether the vehicle number is unique.
     */
    public function checkUniqueVehicleNumber(Request $request)
    {
        try {
            // Retrieve vehicle ID and vehicle number from the request
            $vehicleId = $request->input('vehicle_id', null); // Assuming the parameter is 'vehicle_id'
            $vehicleNumber = $request->input('vehicle_number');
            // Check if the vehicle number is unique among vehicles, excluding the vehicle with the specified ID (if provided)
            $isUnique = $this->vehicleService->checkUniqueVehicleNumber($vehicleNumber, $vehicleId);
            
            // Generate and return a response indicating whether the vehicle number is unique
            return $this->handleResponse(['isvalid' => $isUnique], '', Response::HTTP_OK);
        } catch (\Exception $e) {
            // Handle any exceptions that occur
            $this->helper->handleException($e);
            // Generate and return a response indicating the error that occurred
            return $this->handleResponse(['isvalid' => false], $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR, []);
        }
    }
}