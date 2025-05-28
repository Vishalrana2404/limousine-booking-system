<?php

namespace App\Http\Controllers\Admin;

use App\CustomHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\PeakPeriodService;
use App\Http\Requests\AddPeakPeriodRequest;
use App\Http\Requests\EditPeakPeriodRequest;
use App\Http\Requests\DeletePeakPeriodRequest;
use App\Http\Requests\BulkPeakPeriodStatusUpdateRequest;
use App\Models\PeakPeriod;

/**
 * Class PeakPeriodController
 * 
 * @package  App\Http\Controllers\Admin
 */
class PeakPeriodController extends Controller
{
    /**
     * Class Constructor
     *
     * Initializes the PeakPeriodService and CustomHelper instances.
     *
     * @param PeakPeriodService $peakPeriodService The service responsible for managing peak periods.
     * @param CustomHelper $helper A custom helper utility class.
     */
    public function __construct(
        private PeakPeriodService $peakPeriodService,
        private CustomHelper $helper
    ) {
    }
    /**
     * Display list of PeakPeriod.
     *
     * @param Request $request The HTTP request instance.
     * @return Response The HTTP response instance.
     */
    public function index(Request $request)
    {
        try {
            // Retrieve PeakPeriod data from the PeakPeriodService
            $peakPeriodData = $this->peakPeriodService->getPeakPeriodData($request->query());
            return view('admin.peak-period.index', compact('peakPeriodData'));
        } catch (\Exception $e) {
            $this->helper->alertResponse(__('messages.something_went_wrong'), 'error');
            // Handle any exceptions that occur
            $this->handleException($e);
            // Generate and return a response indicating the error that occurred
            return $this->handleResponse([], $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR, []);
        }
    }

    /**
     * Show the form for creating a new peak period..
     *
     * @param \Illuminate\Http\Request $request The request object (optional).
     * @return \Illuminate\View\View The view for creating a new peak period..
     */
    public function create(Request $request)
    {
        // Return the view for creating a new peak period.
        return view('admin.peak-period.create-peak-period');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request The HTTP request instance.
     * @return Response The HTTP response instance.
     */
    public function save(AddPeakPeriodRequest $request)
    {
        try {
            $log_headers = $this->getHttpData($request);
            // Create a new peak period using the PeakPeriodService
            $this->peakPeriodService->createPeakPeriod($request->all(), $log_headers);
            $this->helper->alertResponse(__('message.peak_period_created_successfully'), 'success');
            return redirect('peak-period');
        } catch (\Exception $e) {
            $this->helper->handleException($e);
            $this->helper->alertResponse(__('messages.something_went_wrong'), 'error');
            return redirect()->back();
        }
    }
    
    /**
     * Show the form for editing a peak period.
     *
     * @param \App\Models\PeakPeriod $PeakPeriod The peak period to be edited.
     * @return \Illuminate\View\View The view for editing a peak period.
     */
    public function edit(PeakPeriod $peakPeriod)
    {
        // Return the view for editing a peak period, passing the peak period data
        return view('admin.peak-period.update-peak-period', compact('peakPeriod'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request The HTTP request instance.
     * @return Response The HTTP response instance.
     */
    public function update(EditPeakPeriodRequest $request, PeakPeriod $peakPeriod)
    {
        try {
            $log_headers = $this->getHttpData($request);
            // Update the peak period using the PeakPeriodService
            $this->peakPeriodService->updatePeakPeriod($request->all(), $peakPeriod, $log_headers);
            $this->helper->alertResponse(__('message.peak_period_updated_successfully'), 'success');
            return redirect('peak-period');
        } catch (\Exception $e) {
            $this->helper->handleException($e);
            $this->helper->alertResponse(__('messages.something_went_wrong'), 'error');
            return redirect()->back();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id The ID of the peak period to delete.
     * @return Response The HTTP response instance.
     */
    public function delete(DeletePeakPeriodRequest $request)
    {
        try {
            $log_headers = $this->getHttpData($request);
            // Delete the peak period using the PeakPeriodService
            $PeakPeriodData = $this->peakPeriodService->deletePeakPeriod($request->all(), $log_headers);
            if(!$PeakPeriodData){
                return $this->handleResponse([], __("message.can_not_delete"), 422);
            }
            // Generate and return a successful response
            return $this->handleResponse([], __("message.peak_period_deleted_successfully"), Response::HTTP_OK);
        } catch (\Exception $e) {
            // Handle any exceptions that occur
            $this->handleException($e);
            // Generate and return a response indicating the error that occurred
            return $this->handleResponse([], $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR, []);
        }
    }

    /**
     * Filter and retrieve peak period data based on criteria.
     *
     * @param \Illuminate\Http\Request $request The request object containing filter criteria.
     * @return \Illuminate\Http\JsonResponse A JSON response with the filtered peak period data.
     */
    public function filterPeakPeriod(Request $request)
    {
        try {
            // Retrieve peak period data from the PeakPeriodService based on the provided criteria
            $peakPeriodData = $this->peakPeriodService->getPeakPeriodData($request->query());

            // Render the HTML for the peak period listing view
            $data = ['html' => view('admin.peak-period.partials.peak-period', compact('peakPeriodData'))->render()];

            // Generate and return a successful response with the filtered peak period data
            return $this->handleResponse($data, __("message.peak_period_filtered_successfully"), Response::HTTP_OK);
        } catch (\Exception $e) {
            // Handle any exceptions that occur
            $this->handleException($e);

            // Generate and return a response indicating the error that occurred
            return $this->handleResponse([], $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR, []);
        }
    }

    /**
     * Update status for multiple peak periods in bulk.
     *
     * @param \App\Http\Requests\BulkPeakPeriodStatusUpdateRequest $request The request containing data for updating peak period statuses.
     * @return \Illuminate\Http\JsonResponse A JSON response indicating the result of the bulk status update operation for peak periods.
     */
    public function updateBulkStatus(BulkPeakPeriodStatusUpdateRequest $request)
    {
        try {
            $log_headers = $this->getHttpData($request);
            // Update the status of peak periods using the PeakPeriodService
            $peakPeriodData = $this->peakPeriodService->updateBulkStatus($request->all(), $log_headers);

            // Generate and return a successful response
            return $this->handleResponse($peakPeriodData, __("message.peak_period_status_updated_successfully"), Response::HTTP_OK);
        } catch (\Exception $e) {
            // Handle any exceptions that occur
            $this->handleException($e);

            // Generate and return a response indicating the error that occurred
            return $this->handleResponse([], $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR, []);
        }
    }
 }
