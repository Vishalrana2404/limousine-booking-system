<?php

namespace App\Services;

use App\Models\Events;
use App\Models\Client;
use App\Repositories\Interfaces\EventInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EventService
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private EventInterface $eventRepository,
        private Auth $auth,
        private ActivityLogService $activityLogService,
    )
    {
    }

    /**
     * Get paginated event data.
     */
    public function getEventData(array $requestData = [])
    {
        try {
            // Retrieve the logged-in user
            $loggedUser = Auth::user();

            // Extract parameters from the request data or use default values
            $page = $requestData['page'] ?? 1;
            $search = $requestData['search'] ?? '';
            $sortField = $requestData['sortField'] ?? 'id';
            $sortDirection = $requestData['sortDirection'] ?? 'asc';

            // Get paginated event data using the event repository
            return $this->eventRepository->getEvents($loggedUser, $search, $page, $sortField, $sortDirection);
        } catch (\Exception $e) {
            // Throw an exception with the error message if an error occurs
            throw new \Exception($e->getMessage());
        }
    }

    public function getEventsDataForClient(array $requestData = [], int $hotel_id)
    {
        try {
            // Retrieve the logged-in user
            $loggedUser = Auth::user();

            // Extract parameters from the request data or use default values
            $page = $requestData['page'] ?? 1;
            $search = $requestData['search'] ?? '';
            $sortField = $requestData['sortField'] ?? 'id';
            $sortDirection = $requestData['sortDirection'] ?? 'asc';

            // Get paginated event data using the event repository
            return $this->eventRepository->getEventsForClient($loggedUser, $search, $page, $sortField, $sortDirection, $hotel_id);
        } catch (\Exception $e) {
            // Throw an exception with the error message if an error occurs
            throw new \Exception($e->getMessage());
        }
    }

    public function getEventDataByHotel($client_id)
    {
        try {
            // Retrieve the logged-in user
            $loggedUser = Auth::user();

            // Get paginated event data using the event repository
            return $this->eventRepository->getEventsByHotel($loggedUser, $client_id);
        } catch (\Exception $e) {
            // Throw an exception with the error message if an error occurs
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Create a new event.
     */
    public function createEvent(array $requestData, $log_headers, $fromBooking)
    {
        DB::beginTransaction();
        try {
            // Get the logged-in user's ID
            $loggedUser = Auth::user();
            $loggedUserId = $loggedUser->id;
            $loggedUserTypeSlug = $loggedUser->userType->slug ?? null;

            $loggedUser->load('client');            

            if ($loggedUserTypeSlug === null || in_array($loggedUserTypeSlug, ['admin', 'admin-staff']))
            {
                if($fromBooking == 'from booking')
                {
                    $hotelId = Client::where('id', $requestData['hotel_id'])->select('hotel_id')->first();
                    $hotel_id = $hotelId->hotel_id;
                }else{
                    $hotel_id = $requestData['hotel_id'];
                }
            }else{
                $multipleCorporatesHotelData = NULL;
            
                $loggedUser->client->load(['hotel', 'multiCorporates.hotel']);

                $loggedInUserHotelDetails = $loggedUser->client->hotel;
                $hotel_id = $loggedUser->client->hotel_id;

                $multiCorporates = $loggedUser->client->multiCorporates;

                $multipleCorporatesHotelData = $multiCorporates->pluck('hotel');

                if (!$multipleCorporatesHotelData->isEmpty() && !$multipleCorporatesHotelData->contains('id', $loggedInUserHotelDetails->id)) {
                    $multipleCorporatesHotelData->push($loggedInUserHotelDetails);
                }

                if(!empty($multipleCorporatesHotelData) && count($multipleCorporatesHotelData) > 1)
                {
                    $hotel_id = $requestData['hotel_id'];
                }else{
                    $hotel_id = $loggedUser->client->hotel_id;
                }
            }

            // Prepare the event data
            $eventData = [
                'hotel_id' => $hotel_id,
                'name' => $requestData['name'],
                'status' => $requestData['status'],
                'created_by_id' => $loggedUserId,
            ];

            // Add the event using the event repository
            $event = $this->eventRepository->addEvent($eventData);

            // Log the event creation activity
            $this->activityLogService->addActivityLog('create', Events::class, json_encode([]), json_encode($eventData), $log_headers['headers']['Origin'], $log_headers['headers']['User-Agent']);

            DB::commit();
            return $event;
        } catch (\Exception $e) {
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Update an existing event.
     */
    public function updateEvent(array $requestData, Events $event, $log_headers)
    {
        DB::beginTransaction();
        try {
            // Get the logged-in user's ID
            $loggedUser = Auth::user();
            $loggedUserId = $loggedUser->id;
            $loggedUserTypeSlug = $loggedUser->userType->slug ?? null;

            $loggedUser->load('client');

            if ($loggedUserTypeSlug === null || in_array($loggedUserTypeSlug, ['admin', 'admin-staff']))
            {
                $hotel_id = $requestData['hotel_id'];
            }else{
                $hotel_id = $loggedUser->client->hotel_id;
            }

            // Prepare the updated event data
            $eventData = [
                'hotel_id' => $hotel_id,
                'name' => $requestData['name'],
                'status' => $requestData['status'],
                'updated_by_id' => $loggedUserId,
            ];
            $oldData = json_encode($event);

            // Update the event using the event repository
            $this->eventRepository->updateEvent($event, $eventData);

            // Log the event update activity
            $this->activityLogService->addActivityLog('update', Events::class, $oldData, json_encode($eventData), $log_headers['headers']['Origin'], $log_headers['headers']['User-Agent']);

            DB::commit();
            return $event;
        } catch (\Exception $e) {
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Delete event(s) from the database.
     */
    public function deleteEvents($requestData, $log_headers)
    {
        DB::beginTransaction();
        try {
            // Get old event data before deletion
            $oldData = $this->eventRepository->getEventByIds($requestData['event_ids']);

            // Delete event(s) from the database
            $this->eventRepository->deleteEvent($requestData['event_ids']);

            // Log the event deletion activity
            $this->activityLogService->addActivityLog('delete', Events::class, json_encode($oldData), json_encode([]), $log_headers['headers']['Origin'], $log_headers['headers']['User-Agent']);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Update the status of multiple events in bulk.
     */
    public function updateBulkStatus($requestData, $log_headers)
    {
        DB::beginTransaction();
        try {
            // Get the logged-in user's ID
            $loggedUserId = Auth::user()->id;

            // Extract event IDs and status from request data
            $eventIds = $requestData['event_ids'];
            $status = $requestData['status'];

            // Get old event data before bulk update
            $oldData = $this->eventRepository->getEventByIds($eventIds, $status);

            // Update the event status in bulk
            $user = $this->eventRepository->updateBulkStatus($eventIds, $status, $loggedUserId);

            // Log the bulk status update activity
            $this->activityLogService->addActivityLog('updateBulkStatus', Events::class, json_encode($oldData), json_encode($requestData), $log_headers['headers']['Origin'], $log_headers['headers']['User-Agent']);

            DB::commit();
            return $user;
        } catch (\Exception $e) {
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Get event data.
     */
    public function getEvents()
    {
        try {
            return $this->eventRepository->getEventData();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function getActiveEvents()
    {
        try {
            // Retrieve all hotels from the repository
            return $this->eventRepository->getActiveEventsData();
        } catch (\Exception $e) {
            // Throw an exception with the error message if an error occurs
            throw new \Exception($e->getMessage());
        }
    }
}
