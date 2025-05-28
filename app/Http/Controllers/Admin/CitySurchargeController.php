<?php

namespace App\Http\Controllers\Admin;

use App\CustomHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\CitySurchargeService;

/**
 * Class CitySurchargeController
 * 
 * @package  App\Http\Controllers\Admin
 */
class CitySurchargeController extends Controller
{
    /**
     * Constructor for the MyClass class.
     *
     * @param CitySurchargeService $citySurchargeService The CitySurchargeService instance.
     * @param CustomHelper $helper The CustomHelper instance.
     */
    public function __construct(
        private CitySurchargeService $citySurchargeService,
        private CustomHelper $helper
    ) {
    }

    /**
     * Display the index page for the City Surcharge module.
     *
     * @param Request $request The HTTP request.
     * @return \Illuminate\Contracts\View\View The view for the index page.
     */
    public function index(Request $request)
    {
        try {
            $savedCities = $this->citySurchargeService->getSavedCitySurcharges();            
            return view('admin.city-surcharge.index', compact('savedCities'));
        } catch (\Exception $e) {
            $this->helper->alertResponse(__('messages.something_went_wrong'), 'error');
            // Handle the exception and return an error response
            $this->handleException($e);
            return $this->handleResponse([], $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR, []);
        }
    }

    /**
     * Save a new city surcharge.
     *
     * @param Request $request The HTTP request containing the data to save.
     * @return \Illuminate\Http\RedirectResponse The response indicating the result of the save operation.
     */
    public function save(Request $request)
    {
        try {
            $logHeaders = $this->getHttpData($request);
            // Create a new city surcharge using the CitySurchargeService
            $data = $this->citySurchargeService->createUpdateCitySurcharge($request->all(), $logHeaders);
            return $this->handleResponse($data, __("message.city_surcharge_created_successfully"), Response::HTTP_OK);
        } catch (\Exception $e) {
            // Handle the exception and return an error response
            $this->helper->handleException($e);
            $this->helper->alertResponse(__('messages.something_went_wrong'), 'error');
            return redirect()->back();
        }
    }

    public function delete(Request $request)
    {
        try {
            $logHeaders = $this->getHttpData($request);
            $this->citySurchargeService->deleteCoordinates($request->input('id'), $logHeaders);
            return $this->handleResponse([], __("message.city_deleted"), Response::HTTP_OK);
        } catch (\Exception $e) {
            // Handle any exceptions that occur
            $this->helper->handleException($e);
            // Generate and return a response indicating the error that occurred
            return $this->handleResponse([], $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR, []);
        }
    }
}
