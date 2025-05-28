<?php

namespace App\Repositories\Interfaces;

use App\Models\Events;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Interface EventInterface
 *
 * This interface defines the contract for interacting with event data.
 * It abstracts the data layer for events, providing a clear API for event management.
 */
interface EventInterface
{
    /**
     * Create a new event.
     *
     * @param array $data The event data to be added.
     * @return Events The created event model instance.
     */
    public function addEvent(array $data): Events;

    /**
     * Update an existing event.
     *
     * @param Events $event The event model instance to update.
     * @param array $data The updated event data.
     * @return bool True on success, false otherwise.
     */
    public function updateEvent(Events $event, array $data): bool;

    /**
     * Delete multiple events by their IDs.
     *
     * @param array $eventIds The IDs of the events to delete.
     * @return bool True on success, false otherwise.
     */
    public function deleteEvent(array $eventIds): bool;

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
    public function getEvents(User $loggedUser, string $search = '', int $page = 1, string $sortField = 'id', string $sortDirection = 'asc'): LengthAwarePaginator;

    /**
     * Update the status of multiple events in bulk.
     *
     * @param array $eventIds The IDs of the events to update.
     * @param string $status The new status to apply.
     * @param int $loggedUserId The ID of the user performing the update.
     * @return bool True on success, false otherwise.
     */
    public function updateBulkStatus(array $eventIds, string $status, int $loggedUserId): bool;

    /**
     * Get all event data without pagination.
     *
     * @return Collection A collection of all event records.
     */
    public function getEventData(): Collection;

    /**
     * Retrieve events by their IDs with optional status filtering.
     *
     * @param array $eventIds The IDs of the events to retrieve.
     * @param string|null $status Optional status filter.
     * @return Collection A collection of the matching events.
     */
    public function getEventByIds(array $eventIds, string $status = null): Collection;
}
