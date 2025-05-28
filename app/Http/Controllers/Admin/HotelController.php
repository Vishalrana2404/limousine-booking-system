<?php

namespace App\Http\Controllers\Admin;

use App\CustomHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\AddEditHotelRequest;
use App\Http\Requests\BulkHotelStatusUpdateRequest;
use App\Http\Requests\DeleteHotelRequest;
use App\Models\Hotel;
use App\Services\HotelService;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use App\Services\VehicleClassService;

class HotelController extends Controller
{
    public function __construct(
        private VehicleClassService $vehicleClassService,
        private HotelService $hotelService,
        private CustomHelper $helper
    ) {
    }
    /**
     * Display the index page for hotels.
     *
     * Retrieves hotels data from the hotelService based on the provided request parameters,
     * then renders the index view with the retrieved data.
     *
     * @param \Illuminate\Http\Request $request The HTTP request object containing query parameters.
     *
     * @return \Illuminate\Contracts\View\View Returns the rendered view with hotels data.
     */
    public function index(Request $request)
    {
        try {
            // Retrieve hotels data from the hotelService
            $hotelData = $this->hotelService->getHotelData($request->query());
            return view('admin.hotels.index', compact('hotelData'));
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
     * Display the form for creating a new hotel.
     *
     * Renders the view for creating a new hotel.
     *
     * @param \Illuminate\Http\Request $request The HTTP request object.
     *
     * @return \Illuminate\Contracts\View\View Returns the rendered view for creating a new hotel.
     */
    public function create(Request $request)
    {
        $vehicleClasses = $this->vehicleClassService->getVehicleClass();

        return view('admin.hotels.create-hotel', compact('vehicleClasses'));
    }
    /**
     * Save a newly created hotel.
     *
     * Attempts to create a new hotel using data from the submitted form.
     * If successful, redirects to the hotels index page with a success message.
     * If an error occurs during the process, displays an error message and redirects back to the previous page.
     *
     * @param \App\Http\Requests\AddEditHotelRequest $request The validated request object containing hotel data.
     *
     * @return \Illuminate\Http\RedirectResponse Redirects to the hotels index page or back to the previous page.
     */
    public function save(AddEditHotelRequest $request)
    {
        try {
            $log_headers = $this->getHttpData($request);
            $this->hotelService->createHotel($request->all(), $log_headers);
            $this->helper->alertResponse(__('message.hotel_created'), 'success');
            return redirect('hotels');
        } catch (\Exception $e) {
            // Handle any exceptions that occur
            $this->helper->handleException($e);
            // Display an error message and redirect back to the previous page
            $this->helper->alertResponse(__('messages.something_went_wrong'), 'error');
            return redirect()->back();
        }
    }
    /**
     * Display the form for editing a specific hotel.
     *
     * Renders the view for editing a specific hotel, passing the hotel data to the view.
     *
     * @param \App\Models\Hotel $hotel The hotel model instance to be edited.
     *
     * @return \Illuminate\Contracts\View\View Returns the rendered view for editing the hotel.
     */
    public function edit(Hotel $hotel)
    {
        $hotel = $hotel->load(['billingAgreement']);
        $corporateFairBillings = $hotel->load(['fairBilling']);
        $vehicleClasses = $this->vehicleClassService->getVehicleClass();
        
        return view('admin.hotels.update-hotel', compact('hotel', 'corporateFairBillings', 'vehicleClasses'));
    }
    /**
     * Update the specified hotel.
     *
     * Attempts to update the specified hotel using data from the submitted form.
     * If successful, redirects to the hotels index page with a success message.
     * If an error occurs during the process, displays an error message and redirects back to the previous page.
     *
     * @param \App\Http\Requests\AddEditHotelRequest $request The validated request object containing updated hotel data.
     * @param \App\Models\Hotel $hotel The hotel model instance to be updated.
     *
     * @return \Illuminate\Http\RedirectResponse Redirects to the hotels index page or back to the previous page.
     */
    public function update(AddEditHotelRequest $request, Hotel $hotel)
    {
        try {
            $log_headers = $this->getHttpData($request);
            $this->hotelService->updateHotel($request->all(), $hotel, $log_headers);

            $this->helper->alertResponse(__('message.hotel_updated'), 'success');
            return redirect('hotels');
        } catch (\Exception $e) {
            // Handle any exceptions that occur
            $this->helper->handleException($e);

            // Display an error message and redirect back to the previous page
            $this->helper->alertResponse(__('messages.something_went_wrong'), 'error');
            return redirect()->back();
        }
    }
    /**
     * Filter hotels based on the provided criteria.
     *
     * Retrieves hotels data from the hotelService based on the provided request parameters.
     * Renders the HTML for the filtered hotel listing and returns it as part of the response data.
     * If successful, returns a response with the filtered hotel listing HTML and a success message.
     * If an error occurs during the process, handles the exception and returns a response with an error message.
     *
     * @param \Illuminate\Http\Request $request The HTTP request object containing query parameters for filtering hotels.
     *
     * @return \Illuminate\Http\JsonResponse Returns a JSON response containing the filtered hotel listing HTML or an error message.
     */
    public function filterHotels(Request $request)
    {
        try {
            $hotelData = $this->hotelService->getHotelData($request->query());
            $data = ['html' => view('admin.hotels.partials.hotel-listing', compact('hotelData'))->render()];
            return $this->handleResponse($data, __("message.hotel_filtered"), Response::HTTP_OK);
        } catch (\Exception $e) {
            // Handle any exceptions that occur
            $this->helper->handleException($e);
            // Generate and return a response indicating the error that occurred
            return $this->handleResponse([], $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR, []);
        }
    }
    /**
     * Delete hotels based on the provided criteria.
     *
     * Deletes hotels using data from the submitted request parameters.
     * If successful, returns a response with a success message.
     * If an error occurs during the process, handles the exception and returns a response with an error message.
     *
     * @param \App\Http\Requests\DeleteHotelRequest $request The validated request object containing criteria for deleting hotels.
     *
     * @return \Illuminate\Http\JsonResponse Returns a JSON response containing a success message or an error message.
     */
    public function delete(DeleteHotelRequest $request)
    {
        try {
            $log_headers = $this->getHttpData($request);
            $this->hotelService->deleteHotels($request->all(), $log_headers);
            return $this->handleResponse([], __("message.hotel_deleted"), Response::HTTP_OK);
        } catch (\Exception $e) {
            // Handle any exceptions that occur
            $this->helper->handleException($e);
            // Generate and return a response indicating the error that occurred
            return $this->handleResponse([], $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR, []);
        }
    }
    /**
     * Update the status of multiple hotels in bulk.
     *
     * Updates the status of multiple hotels based on the request data.
     * If successful, returns a response with the updated hotel data and a success message.
     * If an error occurs during the process, handles the exception and returns a response with an error message.
     *
     * @param \App\Http\Requests\BulkHotelStatusUpdateRequest $request The validated request object containing data for updating hotel statuses in bulk.
     *
     * @return \Illuminate\Http\JsonResponse Returns a JSON response containing the updated hotel data or an error message.
     */
    public function updateBulkStatus(BulkHotelStatusUpdateRequest $request)
    {
        try {
            $log_headers = $this->getHttpData($request);
            // Update the status of multiple users based on the request data
            $userData = $this->hotelService->updateBulkStatus($request->all(), $log_headers);
            // Generate and return a successful response with the updated user data
            return $this->handleResponse($userData, __("message.hotel_status_updated"), Response::HTTP_OK);
        } catch (\Exception $e) {
            // Handle any exceptions that occur
            $this->helper->handleException($e);
            // Generate and return a response indicating the error that occurred
            return $this->handleResponse([], $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR, []);
        }
    }
}
