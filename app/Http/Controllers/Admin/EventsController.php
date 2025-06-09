<?php

namespace App\Http\Controllers\Admin;

use App\CustomHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\AddEditEventRequest;
use App\Http\Requests\BulkEventStatusUpdateRequest;
use App\Http\Requests\DeleteEventRequest;
use App\Models\Events;
use App\Services\EventService;
use App\Services\HotelService;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use App\Models\UserType;
use App\Services\UserService;
use App\Services\UserTypeService;
use Auth;

class EventsController extends Controller
{
    /**
     * Initialize controller dependencies.
     */
    public function __construct(
        private EventService $eventService,
        private HotelService $hotelService,
        private UserService $userService,
        private UserTypeService $userTypeService,
        private CustomHelper $helper
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            $userTypeSlug = $user->userType->slug ?? null;
            
            $user->load('client');
            
            if(in_array($userTypeSlug, ['client-admin', 'client-staff']))
            {
                $eventData = $this->eventService->getEventsDataForClient($request->query(), $user->client->hotel_id);
            }else{
                $eventData = $this->eventService->getEventData($request->query());
            }
            // Retrieve events data from the eventService
            return view('admin.events.index', compact('eventData'));

        } catch (\Exception $e) {
            // Display an alert message for the user
            $this->helper->alertResponse(__('messages.something_went_wrong'), 'error');

            // Handle any exceptions that occur
            $this->helper->handleException($e);

            // Generate and return a response indicating the error that occurred
            return $this->handleResponse([], $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR, []);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $hotels = $this->hotelService->getHotels();

        $loggedUser = Auth::user();
        $multipleCorporatesHotelData = NULL;

        if(!empty($loggedUser->userType))
        {
            if($loggedUser->userType->slug === 'client-staff' || $loggedUser->userType->slug === 'client-admin')
            {
                $loggedUser->load('client');
                $loggedUser->client->load(['hotel', 'multiCorporates.hotel']);

                $loggedInUserHotelDetails = $loggedUser->client->hotel;
                $client_id = $loggedUser->client->id;
                $multiCorporates = $loggedUser->client->multiCorporates;

                $multipleCorporatesHotelData = $multiCorporates->pluck('hotel');

                if (!$multipleCorporatesHotelData->isEmpty() && !$multipleCorporatesHotelData->contains('id', $loggedInUserHotelDetails->id)) {
                    $multipleCorporatesHotelData->push($loggedInUserHotelDetails);
                }
            }
        }
        return view('admin.events.create-event', compact('hotels', 'multipleCorporatesHotelData'));
    }

    /**
     * Store a newly created event resource in storage.
     */
    public function save(AddEditEventRequest $request)
    {
        try {
            // Extract HTTP headers for logging
            $log_headers = $this->getHttpData($request);
        
            // Create a new event using eventService
            $this->eventService->createEvent($request->all(), $log_headers, 'not from booking');
    
            // Display success message and redirect to events listing
            $this->helper->alertResponse(__('message.event_created'), 'success');
            return redirect('events');
        } catch (\Exception $e) {
            // Handle any exceptions that occur
            $this->helper->handleException($e);
            $this->helper->alertResponse(__('messages.something_went_wrong'), 'error');
            return redirect()->back();
        }
    }

    /**
     * Show the form for editing the specified event resource.
     */
    public function edit(Events $event)
    {
        $hotels = $this->hotelService->getHotels();

        $loggedUser = Auth::user();
        $multipleCorporatesHotelData = NULL;

        if(!empty($loggedUser->userType))
        {
            if($loggedUser->userType->slug === 'client-staff' || $loggedUser->userType->slug === 'client-admin')
            {
                $loggedUser->load('client');
                $loggedUser->client->load(['hotel', 'multiCorporates.hotel']);

                $loggedInUserHotelDetails = $loggedUser->client->hotel;
                $client_id = $loggedUser->client->id;
                $multiCorporates = $loggedUser->client->multiCorporates;

                $multipleCorporatesHotelData = $multiCorporates->pluck('hotel');

                if (!$multipleCorporatesHotelData->isEmpty() && !$multipleCorporatesHotelData->contains('id', $loggedInUserHotelDetails->id)) {
                    $multipleCorporatesHotelData->push($loggedInUserHotelDetails);
                }
            }
        }
        
        return view('admin.events.update-event', compact('event', 'hotels', 'multipleCorporatesHotelData'));
    }

    /**
     * Update the specified event resource in storage.
     */
    public function update(AddEditEventRequest $request, Events $event)
    {
        try {
            // Extract HTTP headers for logging
            $log_headers = $this->getHttpData($request);

            // Update the event using eventService
            $this->eventService->updateEvent($request->all(), $event, $log_headers);

            // Display success message and redirect to events listing
            $this->helper->alertResponse(__('message.event_updated'), 'success');
            return redirect('events');
        } catch (\Exception $e) {
            // Handle any exceptions that occur
            $this->helper->handleException($e);
            $this->helper->alertResponse(__('messages.something_went_wrong'), 'error');
            return redirect()->back();
        }
    }

    /**
     * Filter events based on request data.
     */
    public function filterEvents(Request $request)
    {
        try {
            // Retrieve filtered events data from the eventService
            $eventData = $this->eventService->getEventData($request->query());

            // Render the filtered events partial view
            $data = ['html' => view('admin.events.partials.event-listing', compact('eventData'))->render()];

            // Return successful response with event listing HTML
            return $this->handleResponse($data, __("message.event_filtered"), Response::HTTP_OK);
        } catch (\Exception $e) {
            // Handle any exceptions that occur
            $this->helper->handleException($e);
            return $this->handleResponse([], $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR, []);
        }
    }

    public function filterEventsForClient(Request $request)
    {
        try {

            $user = Auth::user();
            $userTypeSlug = $user->userType->slug ?? null;
            
            $user->load('client');
            
            if(in_array($userTypeSlug, ['client-admin', 'client-staff']))
            {
                // Retrieve filtered events data from the eventService
                $eventData = $this->eventService->getEventsDataForClient($request->query(), $user->client->hotel_id);
            }else{
                // Retrieve filtered events data from the eventService
                $eventData = $this->eventService->getEventsDataForClient($request->query());
            }

            // Render the filtered events partial view
            $data = ['html' => view('admin.events.partials.event-listing', compact('eventData'))->render()];

            // Return successful response with event listing HTML
            return $this->handleResponse($data, __("message.event_filtered"), Response::HTTP_OK);
        } catch (\Exception $e) {
            // Handle any exceptions that occur
            $this->helper->handleException($e);
            return $this->handleResponse([], $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR, []);
        }
    }

    /**
     * Delete the specified event resource from storage.
     */
    public function delete(DeleteEventRequest $request)
    {
        try {
            // Extract HTTP headers for logging
            $log_headers = $this->getHttpData($request);

            // Delete event(s) using eventService
            $this->eventService->deleteEvents($request->all(), $log_headers);

            // Return successful response indicating the event was deleted
            return $this->handleResponse([], __("message.event_deleted"), Response::HTTP_OK);
        } catch (\Exception $e) {
            // Handle any exceptions that occur
            $this->helper->handleException($e);
            return $this->handleResponse([], $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR, []);
        }
    }

    /**
     * Update the status of multiple events in bulk.
     */
    public function updateBulkStatus(BulkEventStatusUpdateRequest $request)
    {
        try {
            // Extract HTTP headers for logging
            $log_headers = $this->getHttpData($request);

            // Update the status of multiple events
            $userData = $this->eventService->updateBulkStatus($request->all(), $log_headers);

            // Return successful response with the updated event data
            return $this->handleResponse($userData, __("message.event_status_updated"), Response::HTTP_OK);
        } catch (\Exception $e) {
            // Handle any exceptions that occur
            $this->helper->handleException($e);
            return $this->handleResponse([], $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR, []);
        }
    }

    public function getHotelEvents(Request $request)
    {
        $client_id = $request->hotel_id;
        $eventData = $this->eventService->getEventDataByHotel($client_id);

        if(!empty($eventData))
        {
            return $this->handleResponse($eventData, __("message.event_status_updated"), Response::HTTP_OK);
        }else{
            $this->helper->handleException($e);
            return $this->handleResponse([], $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR, []);
        }
    }

    public function createEventFromBooking(Request $request)
    {
        try {
            // Extract HTTP headers for logging
            $log_headers = $this->getHttpData($request);
        
            // Create a new event using eventService
            $this->eventService->createEvent($request->all(), $log_headers, 'from booking');

            return $this->handleResponse([], __("message.event_created"), Response::HTTP_OK);
        } catch (\Exception $e) {
            $this->helper->handleException($e);
            return $this->handleResponse([], $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR, []);
        }
    }
}
