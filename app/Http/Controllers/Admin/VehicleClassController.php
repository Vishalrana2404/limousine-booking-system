<?php

namespace App\Http\Controllers\Admin;

use App\CustomHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\VehicleClassService;
use App\Http\Requests\AddVehicleClassRequest;
use App\Http\Requests\EditVehicleClassRequest;
use App\Http\Requests\DeleteVehicleClassRequest;
use App\Http\Requests\BulkVehicleClassStatusUpdateRequest;
use App\Models\VehicleClass;

/**
 * Class VehicleClassController
 * 
 * @package  App\Http\Controllers\Admin
 */
class VehicleClassController extends Controller
{
    /**
     * VehicleClassController constructor.
     * 
     * @param VehicleClassService $VehicleClassService The vehicleClass service instance.
     */
    public function __construct(
        private VehicleClassService $vehicleClassService,
        private CustomHelper $helper
    ) {
    }
    /**
     * Display list of vehicleClass.
     *
     * @param Request $request The HTTP request instance.
     * @return Response The HTTP response instance.
     */
    public function index(Request $request)
    {
        try {
            // Retrieve vehicleClass data from the VehicleClassService
            $vehicleClassData = $this->vehicleClassService->getVehicleClassData($request->query());
            return view('admin.vehicle-class.index', compact('vehicleClassData'));
        } catch (\Exception $e) {
            $this->helper->alertResponse(__('messages.something_went_wrong'), 'error');
            // Handle any exceptions that occur
            $this->handleException($e);
            // Generate and return a response indicating the error that occurred
            return $this->handleResponse([], $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR, []);
        }
    }

    /**
     * Show the form for creating a new vehicle class.
     *
     * @param \Illuminate\Http\Request $request The request object (optional).
     * @return \Illuminate\View\View The view for creating a new vehicle class.
     */
    public function create(Request $request)
    {
        // Return the view for creating a new vehicle class
        return view('admin.vehicle-class.create-vehicle-class');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request The HTTP request instance.
     * @return Response The HTTP response instance.
     */
    public function save(AddVehicleClassRequest $request)
    {
        try {
            $log_headers =  $this->getHttpData($request);
            // Create a new vehicle class using the VehicleClassService
            $this->vehicleClassService->createVehicleClass($request->all(), $log_headers);
            $this->helper->alertResponse(__('message.vehicle_class_created_successfully'), 'success');
            return redirect('vehicle-class');
        } catch (\Exception $e) {
            $this->helper->handleException($e);
            $this->helper->alertResponse(__('messages.something_went_wrong'), 'error');
            return redirect()->back();
        }
    }
    
    /**
     * Show the form for editing a vehicle class.
     *
     * @param \App\Models\VehicleClass $vehicleClass The vehicle class to be edited.
     * @return \Illuminate\View\View The view for editing a vehicle class.
     */
    public function edit(VehicleClass $vehicleClass)
    {
        // Return the view for editing a vehicle class, passing the vehicle class data
        return view('admin.vehicle-class.update-vehicle-class', compact('vehicleClass'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request The HTTP request instance.
     * @return Response The HTTP response instance.
     */
    public function update(EditVehicleClassRequest $request, VehicleClass $vehicleClass)
    {
        try {
            $log_headers = $this->getHttpData($request);
            // Update the vehicle class using the VehicleClassService
            $this->vehicleClassService->updateVehicleClass($request->all(), $vehicleClass, $log_headers);
            $this->helper->alertResponse(__('message.vehicle_class_updated_successfully'), 'success');
            return redirect('vehicle-class');
        } catch (\Exception $e) {
            $this->helper->handleException($e);
            $this->helper->alertResponse(__('messages.something_went_wrong'), 'error');
            return redirect()->back();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id The ID of the vehicle class to delete.
     * @return Response The HTTP response instance.
     */
    public function delete(DeleteVehicleClassRequest $request)
    {
        try {
            $log_headers = $this->getHttpData($request);
            // Delete the vehicle class using the VehicleClassService
            $vehicleClassData = $this->vehicleClassService->deleteVehicleClass($request->all(), $log_headers);
            if(!$vehicleClassData){
                return $this->handleResponse([], __("message.can_not_delete"), 422);
            }
            // Generate and return a successful response
            return $this->handleResponse([], __("message.vehicle_class_deleted_successfully"), Response::HTTP_OK);
        } catch (\Exception $e) {
            // Handle any exceptions that occur
            $this->handleException($e);
            // Generate and return a response indicating the error that occurred
            return $this->handleResponse([], $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR, []);
        }
    }

    /**
     * Update status for multiple vehicle classes in bulk.
     *
     * @param \App\Http\Requests\BulkVehicleClassStatusUpdateRequest $request The request containing data for updating vehicle class statuses.
     * @return \Illuminate\Http\JsonResponse A JSON response indicating the result of the bulk status update operation for vehicle classes.
     */
    public function updateBulkStatus(BulkVehicleClassStatusUpdateRequest $request)
    {
        try {
            $log_headers = $this->getHttpData($request);
            // Update the status of vehicle classes using the VehicleClassService
            $vehicleData = $this->vehicleClassService->updateBulkStatus($request->all(), $log_headers);

            // Generate and return a successful response
            return $this->handleResponse($vehicleData, __("message.vehicle_class_status_updated_successfully"), Response::HTTP_OK);
        } catch (\Exception $e) {
            // Handle any exceptions that occur
            $this->handleException($e);

            // Generate and return a response indicating the error that occurred
            return $this->handleResponse([], $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR, []);
        }
    }

    /**
     * Filter and retrieve vehicle class data based on criteria.
     *
     * @param \Illuminate\Http\Request $request The request object containing filter criteria.
     * @return \Illuminate\Http\JsonResponse A JSON response with the filtered vehicle class data.
     */
    public function filterVehicleClass(Request $request)
    {
        try {
            // Retrieve vehicle class data from the VehicleClassService based on the provided criteria
            $vehicleClassData = $this->vehicleClassService->getVehicleClassData($request->query());

            // Render the HTML for the vehicle class listing view
            $data = ['html' => view('admin.vehicle-class.partials.vehicle-class', compact('vehicleClassData'))->render()];

            // Generate and return a successful response with the filtered vehicle class data
            return $this->handleResponse($data, __("message.vehicle_class_filtered_successfully"), Response::HTTP_OK);
        } catch (\Exception $e) {
            // Handle any exceptions that occur
            $this->handleException($e);

            // Generate and return a response indicating the error that occurred
            return $this->handleResponse([], $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR, []);
        }
    }

    /**
     * Display details of a specific vehicle class.
     *
     * @param \App\Models\VehicleClass $vehicleClass The vehicle class to be viewed.
     * @return \Illuminate\View\View The view for viewing a vehicle class's details.
     */
    public function view(VehicleClass $vehicleClass)
    {
        // Get detailed information about the vehicle class from the VehicleClassService
        $vehicle_class = $this->vehicleClassService->viewVehicle($vehicleClass);

        // Return the view for viewing a vehicle class's details, passing necessary data
        return view('admin.vehicle-class.view-vehicle-class', compact('vehicle_class'));
    }

    public function updateSequence(Request $request)
    {
        try {
            $sortedData = $request->input('sortedData');
            $sequenceUpdate = $this->vehicleClassService->updateSequence($sortedData);
            
            if($sequenceUpdate)
            {
                return response()->json(['message' => 'Sequence updated successfully']);
            }else{
                return response()->json(['message' => 'Invalid data format'], 422);
            }
        }catch (\Exception $e) {
            // Handle any exceptions that occur
            $this->handleException($e);

            // Generate and return a response indicating the error that occurred
            return $this->handleResponse([], $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR, []);
        }
    }
}
