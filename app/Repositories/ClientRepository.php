<?php

namespace App\Repositories;

use App\Models\Client;
use App\Models\User;
use App\Models\ClientMultiCorporates;
use App\Models\UserType;
use App\Models\ClientLinkageLogs;
use App\Repositories\Interfaces\ClientInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Class ClientRepository
 * 
 * This class implements the ClientInterface and provides methods to interact with clients.
 */
class ClientRepository implements ClientInterface
{
    /**
     * Create a new instance of the ClientRepository.
     *
     * @param Client $model The model instance for clients.
     */
    public function __construct(
        protected Client $model,
        protected ClientLinkageLogs $clientLinkageLogsModel,
        protected ClientMultiCorporates $clientMultiCorporateModel
    ) {
    }

    /**
     * Add a new client.
     *
     * @param array $data The data for creating the client.
     * @return Client The newly created client.
     */
    public function addClient(array $data): Client
    {
        return $this->model->create($data);
    }

    /**
     * Get clients based on specified parameters.
     *
     * Retrieves clients based on the provided logged-in user, client ID, search criteria, pagination,
     * sorting field, and sorting direction.
     *
     * @param User $loggedUser The logged-in user instance.
     * @param int $filterByClient filter by client.
     * @param int $filterByUserType filter by client type.
     * @param string $search The search criteria for filtering clients (optional).
     * @param int $page The page number for pagination (optional, default is 1).
     * @param string $sortField The field to sort clients by (optional, default is 'id').
     * @param string $sortDirection The direction for sorting clients ('asc' or 'desc', optional, default is 'asc').
     * @return LengthAwarePaginator A paginator for the retrieved clients.
     */
    public function getClient(User $loggedUser, int $filterByClient = null, int $filterByUserType = null, string $search = '', int $page = 1, string $sortField = 'id', string $sortDirection = 'asc'): LengthAwarePaginator
    {
        // Filter clients based on the provided parameters
        $clients = $this->filterClientResult($loggedUser, $filterByClient, $filterByUserType, $search)->get();

        // Sort the clients based on the specified field and direction
        $sortedCollection = $this->sortClients($clients, $sortField, $sortDirection);

        // Set the page size for pagination
        $pageSize = config('constants.paginationSize');

        // Paginate the sorted collection
        return $this->paginateResults($sortedCollection, $pageSize, $page);
    }


    /**
     * Filter client query result based on specified parameters.
     *
     * Builds and returns a query builder instance for filtering clients based on the provided logged-in user,
     * client ID, and search criteria.
     *
     * @param User $loggedUser The logged-in user instance.
     * @param int $filterByClient filter by client.
     * @param int $filterByUserType filter by client type.
     * @param string $search The search criteria for filtering clients (optional).
     * @return \Illuminate\Database\Eloquent\Builder The query builder instance for filtering clients.
     */
    private function filterClientResult(User $loggedUser, int $filterByClient = null, int $filterByUserType = null, string $search = '')
    {
        $loggedUserId = $loggedUser->id;

        // Start building the query with eager loading relationships
        $query = $this->model->with(['user.userType',  'hotel']);

        if ($filterByUserType) {
            $query->whereHas('user', function ($query) use ($filterByUserType) {
                $query->where('user_type_id', $filterByUserType);
            });
        }
        if ($filterByClient) {
            $query->where('hotel_id', $filterByClient);
        }
        // Apply search query filters
        if (!empty($search)) {
            $search = strtolower($search);
            $query->where(function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->orWhereRaw('LOWER(`invoice`) like ?', ['%' . $search . '%'])
                        ->orWhereRaw('LOWER(`status`) like ?', ['%' . $search . '%']);
                })->orWhereHas('user', function ($query) use ($search) {
                    $query->whereRaw('LOWER(`first_name`) like ?', ['%' . $search . '%'])
                        ->orWhereRaw('LOWER(`last_name`) like ?', ['%' . $search . '%'])
                        ->orWhereRaw('LOWER(`phone`) like ?', ['%' . $search . '%'])
                        ->orWhereRaw('LOWER(`email`) like ?', ['%' . $search . '%']);
                })->orWhereHas('user.userType', function ($query) use ($search) {
                    $query->whereRaw('LOWER(`name`) like ?', ['%' . $search . '%']);
                })->orWhereHas('hotel', function ($query) use ($search) {
                    $query->whereRaw('LOWER(`name`) like ?', ['%' . $search . '%']);
                });
            });
        }

        return $query;
    }
    /**
     * Sort clients collection based on specified field and direction.
     *
     * Sorts the provided collection of clients based on the specified field and direction.
     *
     * @param Collection $clients The collection of clients to be sorted.
     * @param string $sortField The field to sort clients by (optional, default is 'id').
     * @param string $sortDirection The direction for sorting clients ('asc' or 'desc', optional, default is 'asc').
     * @return Collection The sorted collection of clients.
     */
    private function sortClients(Collection $clients, string $sortField = 'id', string $sortDirection = 'asc')
    {
        // Determine the sorting function based on the sort direction
        $sortFunction = $sortDirection == 'asc' ? 'sortBy' : 'sortByDesc';

        // Sort the clients collection based on the specified field and direction
        return $clients->$sortFunction(function ($innerQuery) use ($sortField) {
            switch ($sortField) {
                case 'sortClient':
                    $value = strtolower($innerQuery->hotel->name ?? 'zzzz');
                    break;
                case 'sortPhone':
                    $value = strtolower($innerQuery->user->phone ?? 'zzzz');
                    break;
                case 'sortEmail':
                    $value = strtolower($innerQuery->user->email ?? 'zzzz');
                    break;
                case 'sortContactPerson':
                    $value = strtolower($innerQuery->user->first_name . ' ' . $innerQuery->user->last_name ?? 'zzzz');
                    break;
                case 'sortInvoice':
                    $value = strtolower($innerQuery->invoice ?? 'zzzz');
                    break;
                case 'sortGroup':
                    $value = strtolower($innerQuery->group ?? 'zzzz');
                    break;
                case 'sortStatus':
                    $value = strtolower($innerQuery->status ?? 'zzzz');
                    break;
                case 'sortClientType':
                    $value = strtolower($innerQuery->user->userType->name ?? 'zzzz');
                    break;
                default:
                    $value = strtolower($innerQuery->hotel->name ?? 'zzzz');
                    break;
            }
            return $value;
        });
    }

    /**
     * Paginate a collection of results.
     *
     * Paginates the provided collection based on the specified page size and page number.
     *
     * @param mixed $collection The collection of results to be paginated.
     * @param int $pageSize The number of items per page.
     * @param int $page The current page number (optional, default is 1).
     * @return LengthAwarePaginator The paginated collection of results.
     */
    private function paginateResults($collection, $pageSize, $page = 1): LengthAwarePaginator
    {
        return new LengthAwarePaginator(
            $collection->values()->forPage($page, $pageSize), // Paginate the collection for the specified page and page size
            $collection->count(), // Total count of items in the collection
            $pageSize, // Number of items per page
            $page, // Current page number
            ['path' => LengthAwarePaginator::resolveCurrentPath()] // Path for generating pagination links
        );
    }

    /**
     * Update the status of multiple clients.
     *
     * Updates the status of clients with the provided IDs to the specified status,
     * and sets the updated by user ID.
     *
     * @param array $clientIds The IDs of the clients to update.
     * @param string $status The new status to set for the clients.
     * @param int $loggedUserId The ID of the user who initiated the update.
     * @return bool True if the bulk status update is successful, false otherwise.
     */
    public function updateBulkStatus(array $clientIds, string $status, int $loggedUserId): bool
    {
        return $this->model->whereIn('id', $clientIds)->update(['status' => $status, 'updated_by_id' => $loggedUserId]);
    }

    /**
     * Delete multiple clients.
     *
     * Deletes clients with the provided IDs.
     *
     * @param array $clientIds The IDs of the clients to delete.
     * @return bool True if the clients are successfully deleted, false otherwise.
     */
    public function deleteClient(array $clientIds): bool
    {
        return $this->model->whereIn('id', $clientIds)->delete();
    }

    /**
     * Update a client with new data.
     *
     * Updates the provided client instance with the specified data.
     *
     * @param Client $client The client instance to update.
     * @param array $data The new data for updating the client.
     * @return bool True if the client is successfully updated, false otherwise.
     */
    public function updateClient(Client $client, array $data): bool
    {
        return $client->update($data);
    }

    /**
     * Get a client by its ID.
     *
     * Retrieves the client with the specified ID.
     *
     * @param int $clientId The ID of the client to retrieve.
     * @return Client|null The client instance, or null if not found.
     */
    public function getClientById(int $clientId): ?Client
    {
        return $this->model->find($clientId);
    }
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
    public function getClientByIds(array $clientIds): Collection
    {
        return $this->model->whereIn('id', $clientIds)->get();
    }

    public function getClientByUserIds(array $userIds): array
    {
        return $this->model->whereIn('user_id', $userIds)->pluck('id')->toArray();
    }

    public function getClientsByHotel(User $loggedUser, int $client_id = null)
    {
        $clients = $this->filterClientResultByHotel($loggedUser, $client_id)->get();

        return $clients;
    }

    private function filterClientResultByHotel(User $loggedUser, int $client_id = null)
    {
        $hotelId = $this->model->where('id', $client_id)->first();        
        $query = $this->model->query();
        $query->where('hotel_id', $hotelId->hotel_id)->with('user');

        return $query;
    }

    public function getHotelClients(int $hotel_id = null)
    {
        $clients = $this->filterHotelClients($hotel_id)->get();

        return $clients;
    }

    private function filterHotelClients(int $hotel_id = null)
    {
        $query = $this->model->query();
        $query->where('hotel_id', $hotel_id)->with('user');

        return $query;
    }

    public function getClientsByLinkedHotel(int $client_id = null)
    {
        $clients = $this->filterClientResultByLinkedHotel($client_id)->get();

        return $clients;
    }

    private function filterClientResultByLinkedHotel(int $client_id = null)
    {
        $hotelId = $this->model->where('id', $client_id)->first();
        $query = $this->clientMultiCorporateModel->query();
        $query->where('hotel_id', $hotelId->hotel_id)->with('client.user');

        return $query;
    }
    public function addClientHotel(array $data): ClientMultiCorporates
    {
        $entryExists = $this->clientMultiCorporateModel->where('client_id', $data['client_id'])->where('hotel_id', $data['hotel_id'])->first();

        if(empty($entryExists))
        {
            $log = [
                'user_id'        => $data['created_by_id'],
                'client_id'      => $data['client_id'],
                'hotel_id'       => $data['hotel_id'],
                'message'        => 'linked',
                'log_type'       => 'to',
                'created_by_id'  => $data['created_by_id'],
                'updated_by_id'  => null,
            ];
            $this->clientLinkageLogsModel->create($log);
        }
        
        return $this->clientMultiCorporateModel->create($data);
    }
    public function syncClientHotels(array $hotelIds, int $clientId, int $loggedUserId, string $status): void
    {
        // Get current hotel IDs from DB
        $existingHotels = $this->clientMultiCorporateModel
            ->where('client_id', $clientId)
            ->pluck('hotel_id')
            ->toArray();

        $newHotels = $hotelIds;
        
        // Determine hotels to unlink (present in DB, but not in new list)
        $toUnlink = array_diff($existingHotels, $newHotels);
        
        // Determine hotels to link (present in new list, but not in DB)
        $toLink = array_diff($newHotels, $existingHotels);

        // Unlink old hotels and log
        foreach ($toUnlink as $hotelId) {
            $this->clientMultiCorporateModel
                ->where('client_id', $clientId)
                ->where('hotel_id', $hotelId)
                ->delete();

            $log = [
                'user_id'        => $loggedUserId,
                'client_id'      => $clientId,
                'hotel_id'       => $hotelId,
                'message'        => 'unlinked',
                'log_type'       => 'from',
                'created_by_id'  => $loggedUserId,
                'updated_by_id'  => null,
            ];
            $this->clientLinkageLogsModel->create($log);
        }

        // Link new hotels and log
        foreach ($toLink as $hotelId) {
            $data = [
                'client_id'      => $clientId,
                'hotel_id'       => $hotelId,
                'status'         => $status,
                'created_by_id'  => $loggedUserId,
            ];

            $this->clientMultiCorporateModel->create($data);

            $log = [
                'user_id'        => $loggedUserId,
                'client_id'      => $clientId,
                'hotel_id'       => $hotelId,
                'message'        => 'linked',
                'log_type'       => 'to',
                'created_by_id'  => $loggedUserId,
                'updated_by_id'  => null,
            ];
            $this->clientLinkageLogsModel->create($log);
        }
    }

    public function getActiveClientsData(): Collection
    {
        return $this->model->where('status', 'ACTIVE')->with('user')->get();
    }

    public function getClientLinkageLogs(int $clientId): Collection
    {
        $logs = $this->clientLinkageLogsModel
            ->where('client_id', $clientId)
            ->with(['user', 'hotel', 'client.user'])
            ->orderBy('id', 'desc')
            ->get();

        // Group by date (Y-m-d) and sort the groups in descending order
        $groupedLogs = $logs->groupBy(function ($log) {
            return date('Y-m-d', strtotime($log->created_at));
        })->sortKeysDesc();

        return $groupedLogs;
    }
}
