<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\CustomHelper;
use App\Services\BookingLogService;
use App\Services\UserService;

/**
 * Class BookingLogController
 * 
 * @package  App\Http\Controllers\Admin
 */
class BookingLogController extends Controller
{
    public function __construct(
        private BookingLogService $bookingLogService,
        private UserService $userService,
        private CustomHelper $helper
    ) {
    }


    public function index(Request $request)
    {
        try {
            $logs = $this->bookingLogService->getBookingLogs();
            $users = $this->userService->getAllusers();
            return view('admin.logs.index', compact('logs','users'));
        } catch (\Exception $e) {
            // Display an alert message for the user
            $this->helper->alertResponse(__('messages.something_went_wrong'), 'error');
            $this->handleException($e);

            // Generate and return a response indicating the error that occurred
            return $this->handleResponse([], $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR, []);
        }
    }

    public function filterLogs(Request $request)
    {
        try {
            $logs = $this->bookingLogService->getBookingLogs($request->query());
            // Render the client listing partial view with the filtered data
            $data = ['html' => view('admin.logs.partials.logs', compact('logs'))->render()];
            // Return a JSON response with the updated client listing HTML
            return $this->handleResponse($data, __("message.logs_filtered"), Response::HTTP_OK);
        } catch (\Exception $e) {
            // Handle the exception and return an error response
            $this->helper->handleException($e);
            return $this->handleResponse([], $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR, []);
        }
    }
}
