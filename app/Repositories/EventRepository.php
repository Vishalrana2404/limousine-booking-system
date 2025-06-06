<?php

namespace App\Repositories;

use App\Models\Events;
use App\Models\User;
use App\Models\Client;
use App\Repositories\Interfaces\EventInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Class EventRepository
 * 
 * This class implements the EventInterface and provides methods to manage event data.
 * It acts as a bridge between the database and the application logic.
 */
class EventRepository implements EventInterface
{
    /**
     * Create a new class instance.
     * 
     * @param Events $model The event model instance.
     */
    public function __construct(protected Events $model)
    {
    }

    /**
     * Create a new event.
     * 
     * @param array $data The event data to be added.
     * @return Events The created event model instance.
     */
    public function addEvent(array $data): Events
    {
        return $this->model->create($data);
    }

    /**
     * Update an existing event.
     * 
     * @param Events $event The event model instance to update.
     * @param array $data The updated event data.
     * @return bool True on success, false otherwise.
     */
    public function updateEvent(Events $event, array $data): bool
    {
        return $event->update($data);
    }

    /**
     * Delete multiple events by their IDs.
     * 
     * @param array $eventIds The IDs of the events to delete.
     * @return bool True on success, false otherwise.
     */
    public function deleteEvent(array $eventIds): bool
    {
        return $this->model->whereIn('id', $eventIds)->delete();
    }

    /**
     * Retrieve paginated events with optional search and sorting.
     * 
     * @param User $loggedUser The currently authenticated user.
     * @param string $search Optional search query.
     * @param int $page The page number for pagination.
     * @param string $sortField The field to sort by.
     * @param string $sortDirection The direction of sorting ('asc' or 'desc').
     * @return LengthAwarePaginator Paginated event results.
     */
    public function getEvents(User $loggedUser, string $search = '', int $page = 1, string $sortField = 'id', string $sortDirection = 'asc'): LengthAwarePaginator
    {
        $events = $this->filterEventResult($loggedUser, $search)->get();
        $sortedCollection = $this->sortEvents($events, $sortField, $sortDirection);
        $pageSize = config('constants.paginationSize');
        return $this->paginateResults($sortedCollection, $pageSize, $page);
    }

    public function getEventsForClient(User $loggedUser, string $search = '', int $page = 1, string $sortField = 'id', string $sortDirection = 'asc', int $hotel_id): LengthAwarePaginator
    {
        $events = $this->filterEventResultForClient($loggedUser, $search, $hotel_id)->get();
        $sortedCollection = $this->sortEventsForClient($events, $sortField, $sortDirection);
        $pageSize = config('constants.paginationSize');
        return $this->paginateResults($sortedCollection, $pageSize, $page);
    }

    /**
     * Filter events based on the provided search query.
     * 
     * @param User $loggedUser The currently authenticated user.
     * @param string $search Optional search query.
     * @return \Illuminate\Database\Eloquent\Builder The query builder instance.
     */
    private function filterEventResult(User $loggedUser, string $search = '')
    {
        $query = $this->model->with('hotel');
        if (!empty($search)) {
            $search = strtolower($search);
            $query->where(function ($query) use ($search) {
                $query->whereRaw('LOWER(`name`) like ?', ['%' . $search . '%'])
                      ->orWhereRaw('LOWER(`status`) like ?', ['%' . $search . '%'])
                      ->orWhereHas('hotel', function ($query) use ($search) {
                        $query->whereRaw('LOWER(`name`) like ?', ['%' . $search . '%']);
                    });
            });
        }
        return $query;
    }
    private function filterEventResultForClient(User $loggedUser, string $search = '', int $hotel_id)
    {
        
        $query = $this->model->with(['hotel.multiClients']);

        $hotel_ids = $loggedUser->client->multiCorporates->pluck('hotel')->pluck('id');

        if (!empty($search)) {
            $search = strtolower($search);
            $query->where(function ($query) use ($search) {
                $query->whereRaw('LOWER(`name`) like ?', ['%' . $search . '%'])
                      ->orWhereRaw('LOWER(`status`) like ?', ['%' . $search . '%'])
                      ->orWhereHas('hotel', function ($query) use ($search) {
                        $query->whereRaw('LOWER(`name`) like ?', ['%' . $search . '%']);
                    })
                    ->orWhereHas('hotel.multiClients', function ($query) use ($search) {
                        $query->whereHas('hotel', function ($subQuery) use ($search) {
                            $subQuery->whereRaw('LOWER(`name`) like ?', ["%{$search}%"]);
                        });
                    });
            });
        }

        if (!empty($hotel_id)) {
            $query->where('hotel_id', $hotel_id);
        }

        if (!empty($hotel_ids)) {
            $query->orWhereIn('hotel_id', $hotel_ids);
        }        
        return $query;
    }

    /**
     * Sort a collection of events based on the specified field and direction.
     * 
     * @param Collection $events The collection of events to sort.
     * @param string $sortField The field to sort by.
     * @param string $sortDirection The direction of sorting ('asc' or 'desc').
     * @return Collection The sorted collection of events.
     */
    private function sortEvents(Collection $events, string $sortField = 'id', string $sortDirection = 'asc')
    {
        $sortFunction = $sortDirection == 'asc' ? 'sortBy' : 'sortByDesc';
        return $events->$sortFunction(function ($innerQuery) use ($sortField) {
            switch ($sortField) {
                case 'sortCorporate':
                    return strtolower($innerQuery->hotel->name ?? 'zzzz');
                case 'sortName':
                    return strtolower($innerQuery->name ?? 'zzzz');
                case 'sortStatus':
                    return strtolower($innerQuery->status ?? 'zzzz');
                default:
                    return strtolower($innerQuery->hotel->name ?? 'zzzz');
            }
        });
    }
    private function sortEventsForClient(Collection $events, string $sortField = 'id', string $sortDirection = 'asc')
    {
        $sortFunction = $sortDirection == 'asc' ? 'sortBy' : 'sortByDesc';
        return $events->$sortFunction(function ($innerQuery) use ($sortField) {
            switch ($sortField) {
                case 'sortCorporate':
                    return strtolower($innerQuery->hotel->name ?? 'zzzz');
                case 'sortName':
                    return strtolower($innerQuery->name ?? 'zzzz');
                case 'sortStatus':
                    return strtolower($innerQuery->status ?? 'zzzz');
                default:
                    return strtolower($innerQuery->hotel->name ?? 'zzzz');
            }
        });
    }

    /**
     * Paginate a collection of events.
     * 
     * @param Collection $collection The collection to paginate.
     * @param int $pageSize The number of items per page.
     * @param int $page The current page number.
     * @return LengthAwarePaginator The paginated collection of events.
     */
    private function paginateResults($collection, $pageSize, $page = 1): LengthAwarePaginator
    {
        return new LengthAwarePaginator(
            $collection->values()->forPage($page, $pageSize),
            $collection->count(),
            $pageSize,
            $page,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );
    }

    /**
     * Update the status of multiple events in bulk.
     * 
     * @param array $eventIds The IDs of the events to update.
     * @param string $status The new status to apply.
     * @param int $loggedUserId The ID of the user performing the update.
     * @return bool True on success, false otherwise.
     */
    public function updateBulkStatus(array $eventIds, string $status, int $loggedUserId): bool
    {
        return $this->model->whereIn('id', $eventIds)->update(['status' => $status, 'updated_by_id' => $loggedUserId]);
    }

    /**
     * Get all active events.
     * 
     * @return Collection A collection of all active event records.
     */
    public function getEventData(): Collection
    {
        return $this->model->where('status', 'ACTIVE')->get();
    }

    /**
     * Retrieve events by their IDs with optional status filtering.
     * 
     * @param array $eventIds The IDs of the events to retrieve.
     * @param string|null $status Optional status filter.
     * @return Collection A collection of the matching events.
     */
    public function getEventByIds(array $eventIds, string $status = null): Collection
    {
        $query = $this->model->whereIn('id', $eventIds);
        if (!empty($status)) {
            $query->where('status', $status);
        }
        return $query->get();
    }
    public function getEventsByHotel(User $loggedUser, int $client_id = null)
    {
        $hotelId = Client::where('id', $client_id)->select('hotel_id')->first();
        $events = $this->filterEventResultByHotel($loggedUser, $hotelId->hotel_id)->get();

        return $events;
    }

    private function filterEventResultByHotel(User $loggedUser, int $hotel_id = null)
    {
        $query = $this->model->query();
        $query->where('hotel_id', $hotel_id);

        return $query;
    }

    public function getActiveEventsData(): Collection
    {
        return $this->model->where('status', 'ACTIVE')->get();
    }
}
