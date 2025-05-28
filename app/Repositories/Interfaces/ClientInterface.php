<?php

namespace App\Repositories\Interfaces;

use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Interface ClientInterface
 *
 * Represents an interface for managing clients.
 */
interface ClientInterface
{
    /**
     * Add a new client.
     *
     * @param array $data The data for creating the client.
     * @return Client The newly created client instance.
     */
    public function addClient(array $data): Client;

    /**
     * Get clients based on various criteria.
     *
     * @param User $loggedUser The logged-in user.
     * @param int $filterByClient filter by client.
     * @param int $filterByUserType filter by client type.
     * @param string $search The search keyword to filter clients (optional, default is an empty string).
     * @param int $page The page number for pagination (optional, default is 1).
     * @param string $sortField The field to sort clients by (optional, default is 'id').
     * @param string $sortDirection The direction for sorting clients ('asc' or 'desc', optional, default is 'asc').
     * @return LengthAwarePaginator A paginated collection of clients.
     */
    public function getClient(User $loggedUser, int $filterByClient = null, int $filterByUserType = null, string $search = '', int $page = 1, string $sortField = 'id', string $sortDirection = 'asc'): LengthAwarePaginator;

    /**
     * Update the status of multiple clients.
     *
     * @param array $clientIds The IDs of the clients to update.
     * @param string $status The new status for the clients.
     * @param int $loggedUserId The ID of the logged-in user performing the action.
     * @return bool True if the bulk status update is successful, false otherwise.
     */
    public function updateBulkStatus(array $clientIds, string $status, int $loggedUserId): bool;

    /**
     * Delete multiple clients.
     *
     * @param array $clientIds The IDs of the clients to delete.
     * @return bool True if the clients are successfully deleted, false otherwise.
     */
    public function deleteClient(array $clientIds): bool;

    /**
     * Update a client's information.
     *
     * @param Client $user The client instance to update.
     * @param array $data The data for updating the client.
     * @return bool True if the client is successfully updated, false otherwise.
     */
    public function updateClient(Client $user, array $data): bool;

    /**
     * Get a client by its ID.
     *
     * @param int $clientId The ID of the client to retrieve.
     * @return Client|null The client instance associated with the specified ID, or null if not found.
     */
    public function getClientById(int $clientId): ?Client;

  /**
     * Retrieves a collection of clients based on the given array of client IDs.
     *
     * This function queries the database to fetch all clients whose IDs are 
     * in the provided array and returns the result as a collection.
     *
     * @param array $clientIds An array of client IDs to retrieve.
     * 
     * @return \Illuminate\Support\Collection A collection of clients that match the given IDs.
     *
     */
    public function getClientByIds(array $clientIds): ?Collection;

    public function getClientByUserIds(array $userIds): array;    
}
