<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\NotificationService;
use App\CustomHelper;

class NotificationController extends Controller
{
    /**
     * NotificationController constructor.
     * 
     * @param NotificationService $notificationService The notification service instance.
     */
    public function __construct(
        private NotificationService $notificationService,
        private CustomHelper $helper,
    ) {
    }

    /**
     * Display list of notifications.
     *
     * @param Request $request The HTTP request instance.
     * @return Response The HTTP response instance.
     */
    public function index(Request $request)
    {
        try {
            // Retrieve notification data from the NotificationService
            $notifications = $this->notificationService->getNotifications($request->query());
            return view('components.notifications', compact('notifications'));
        } catch (\Exception $e) {
            $this->helper->alertResponse(__('messages.something_went_wrong'), 'error');
            // Handle any exceptions that occur
            $this->handleException($e);
            // Generate and return a response indicating the error that occurred
            return $this->handleResponse([], $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR, []);
        }
    }

    public function filterNotification(Request $request)
    {
        try {
            // Retrieve booking data based on the filter criteria
            $notifications = $this->notificationService->getNotifications($request->query());
            // Render the booking listing partial view with the filtered data
            $data = ['html' => view('components.partials.notification-listing', compact('notifications'))->render()];
            // Return a JSON response with the updated booking listing HTML
            return $this->handleResponse($data, __("message.booking_filtered"), Response::HTTP_OK);
        } catch (\Exception $e) {
            // Handle the exception and return an error response
            $this->helper->handleException($e);
            return $this->handleResponse([], $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR, []);
        }
    }

    public function markAsRead(Request $request)
    {
        try {
            $data =  $this->notificationService->markAsRead($request->all());
            // Return a JSON response with the notification read status
            return $this->handleResponse($data, __("message.notification_marked_as_read"), Response::HTTP_OK);
        } catch (\Exception $e) {
            // Handle the exception and return an error response
            $this->helper->handleException($e);
            return $this->handleResponse([], $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR, []);
        }
    }
}
