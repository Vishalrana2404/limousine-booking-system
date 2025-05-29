<?php

namespace App\Repositories;

use App\Models\Hotel;
use App\Models\User;
use App\Models\HotelPOC;
use App\Models\Client;
use App\Models\HotelLinkageLogs;
use App\Repositories\Interfaces\HotelInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

/**
 * Class HotelRepository
 * 
 * @package App\Repositories
 */
class HotelRepository implements HotelInterface
{
    /**
     * HotelRepository constructor.
     *
     * @param Hotel $model The User model instance.
     */
    public function __construct(
        protected Hotel $model,
        protected HotelPOC $hotelPocModel,
        protected Client $clientModel,
        protected HotelLinkageLogs $hotelLogModel
    ) {
    }
    /**
     * Add a new hotel with the provided data.
     *
     * Creates a new hotel record in the database with the provided data.
     *
     * @param array $data An associative array containing data for creating the hotel.
     *                    Required keys: 'name', 'status'.
     *
     * @return \App\Models\Hotel Returns the created hotel model instance.
     */
    public function addHotel(array $data): Hotel
    {
        return $this->model->create($data);
    }
    /**
     * Update an existing hotel with the provided data.
     *
     * @param \App\Models\Hotel $hotel The hotel model instance to be updated.
     * @param array $data An associative array containing data for updating the hotel.
     *                    Required keys: 'name', 'status'.
     *
     * @return bool Returns true if the hotel is successfully updated, false otherwise.
     */
    public function updateHotel(Hotel $hotel, array $data): bool
    {
        return $hotel->update($data);
    }

    /**
     * Delete one or more hotels based on the provided hotel IDs.
     *
     * @param array $hotelIds An array of hotel IDs to be deleted.
     *
     * @return bool Returns true if the hotels are successfully deleted, false otherwise.
     */
    public function deleteHotel(array $hotelIds): bool
    {
        return $this->model->whereIn('id', $hotelIds)->delete();
    }
    /**
     * Retrieve paginated hotel data based on provided criteria.
     *
     * @param \App\Models\User $loggedUser The logged-in user.
     * @param string $search The search query (default: '').
     * @param int $page The page number for pagination (default: 1).
     * @param string $sortField The field to sort by (default: 'id').
     * @param string $sortDirection The sort direction ('asc' or 'desc', default: 'asc').
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator Returns paginated hotel data.
     */
    public function gethotels(User $loggedUser,  string $search = '', int $page = 1, string $sortField = 'id', string $sortDirection = 'asc'): LengthAwarePaginator
    {
        // Filter hotels based on the provided parameters
        $hotels = $this->filterHotelResult($loggedUser,  $search)->get();

        // Sort the hotel based on the specified field and direction
        $sortedCollection = $this->sortHotels($hotels, $sortField, $sortDirection);

        // Set the page size for pagination
        $pageSize = config('constants.paginationSize');

        // Paginate the sorted collection
        return $this->paginateResults($sortedCollection, $pageSize, $page);
    }

    /**
     * Filter hotel query results based on the provided search criteria.
     *
     * Filters hotel query results based on the provided search criteria, such as name or status.
     *
     * @param \App\Models\User $loggedUser The logged-in user.
     * @param string $search The search query to filter hotel results (default: '').
     *
     * @return \Illuminate\Database\Eloquent\Builder Returns the query builder instance with applied filters.
     */
    private function filterHotelResult(User $loggedUser, string $search = '')
    {
        $loggedUserId = $loggedUser->id;
        // Start building the query with eager loading relationships
        $query = $this->model->query();
        // Apply search query filters
        if (!empty($search)) {
            $search = strtolower($search);
            $query->where(function ($query) use ($search) {
                $query->whereRaw('LOWER(`name`) like ?', ['%' . $search . '%'])
                    ->orWhereRaw('LOWER(`status`) like ?', ['%' . $search . '%']);
            });
        }
        return $query;
    }

    /**
     * Sort hotel collection based on the specified field and direction.
     *
     * Sorts the hotel collection based on the specified field and direction.
     *
     * @param \Illuminate\Support\Collection $hotels The collection of hotels to be sorted.
     * @param string $sortField The field to sort by (default: 'id').
     * @param string $sortDirection The sort direction ('asc' or 'desc', default: 'asc').
     *
     * @return \Illuminate\Support\Collection Returns the sorted collection of hotels.
     */
    private function sortHotels(Collection $hotels, string $sortField = 'id', string $sortDirection = 'asc')
    {
        // Determine the sorting function based on the sort direction
        $sortFunction = $sortDirection == 'asc' ? 'sortBy' : 'sortByDesc';
        // Sort the dirvers collection based on the specified field and direction
        return $hotels->$sortFunction(function ($innerQuery) use ($sortField) {
            switch ($sortField) {
                case 'sortName':
                    $value = strtolower($innerQuery->name ?? 'zzzz');
                    break;
                case 'sortIsHeadOffice':
                    $value = strtolower($innerQuery->is_head_office ?? 'zzzz');
                    break;
                case 'sortHeadOffice':
                    $value = strtolower($innerQuery->linkedHeadOffice->name ?? 'zzzz');
                    break;
                case 'sortStatus':
                    $value = strtolower($innerQuery->status ?? 'zzzz');
                    break;
                default:
                    $value = strtolower($innerQuery->name ?? 'zzzz');
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
     * Update the status of multiple hotels in bulk based on the provided hotel IDs and status.
     *
     * @param array $hotelIds An array of hotel IDs to be updated.
     * @param string $status The new status to be set for the hotels.
     * @param int $loggedUserId The ID of the user performing the operation.
     *
     * @return bool Returns true if the hotel statuses are successfully updated, false otherwise.
     */
    public function updateBulkStatus(array $hotelIds, string $status, int $loggedUserId): bool
    {
        return $this->model->whereIn('id', $hotelIds)->update(['status' => $status, 'updated_by_id' => $loggedUserId]);
    }
    /**
     * Retrieve hotel data.
     *
     * @return \Illuminate\Support\Collection Returns the retrieved hotel data.
     */
    public function getHotelData(): Collection
    {
        return $this->model->where('status', 'ACTIVE')->orderBy('name', 'ASC')->get();
    }
    /**
     * Retrieves a collection of hotels based on the given array of hotel IDs and an optional status.
     *
     * This function queries the database to fetch all hotels whose IDs are in the provided array.
     * Optionally, it filters the hotels by their status if a status is provided.
     *
     * @param array $hotelIds An array of hotel IDs to retrieve.
     * @param string|null $status An optional status to filter the hotels by. Defaults to null.
     * 
     * @return \Illuminate\Support\Collection A collection of hotels that match the given IDs and status.
     *
     */
    public function getHotelByIds(array $hotelIds, string $status = null): Collection
    {
        // Start the query with the base condition
        $query = $this->model->whereIn('id', $hotelIds);
        // Add the status condition if it's provided
        if (!empty($status)) {
            $query->where('status', $status);
        }
        // Execute the query and return the result
        return $query->get();
    }

    public function getHotelClientAdmins(): ?Collection
    {
        return $this->model->with(['client.user'])->orderBy('name', 'ASC')->get();
    }

    public function getActiveHotelsData(): Collection
    {
        return $this->model->where('status', 'ACTIVE')->get();
    }

    public function getHeadOfficeHotelsData(): Collection
    {
        return $this->model->where('status', 'ACTIVE')->where('is_head_office', '1')->get();
    }

    public function updateHotelPOC(int $hotelId, int $loggedUserId, array $pocData = null): bool
    {
        $pocData = $pocData ?? [];

        // Get existing client_ids for this hotel
        $existingPOCs = $this->hotelPocModel
            ->where('hotel_id', $hotelId)
            ->pluck('client_id')
            ->toArray();

        // Determine new POCs to insert
        $toInsert = array_diff($pocData, $existingPOCs);

        // Determine POCs to delete
        $toDelete = array_diff($existingPOCs, $pocData);

        // Delete removed POCs
        if (!empty($toDelete)) {
            $this->hotelPocModel
                ->where('hotel_id', $hotelId)
                ->whereIn('client_id', $toDelete)
                ->delete();
        }

        // Insert new POCs
        foreach ($toInsert as $pocClientId) {
            $checkClient = $this->clientModel->where('id', $pocClientId)->where('hotel_id', $hotelId)->first();

            if($checkClient)
            {
                $this->hotelPocModel->create([
                    'hotel_id' => $hotelId,
                    'client_id' => $pocClientId,
                    'created_by_id' => $loggedUserId,
                ]);
            }
        }

        return true;
    }

    public function createHotelLinkageLogs($hotel, $hotelData, $point_of_contact, $loggedUserId)
    {
        $logs = [];

        // 1. Head Office status changes
        if ($hotel->is_head_office == 0 && $hotelData['is_head_office'] == 1) {
            $logs[] = [
                'user_id'        => $loggedUserId,
                'message'        => 'as Head Office',
                'hotel_id'       => $hotel->id,
                'log_type'       => 'head_office',
                'client_id'      => null,
                'head_office_id' => null,
                'created_by_id'  => $loggedUserId,
                'updated_by_id'  => null,
                'created_at'     => now(),
                'updated_at'     => now(),
            ];
        } elseif ($hotel->is_head_office == 1 && $hotelData['is_head_office'] == 0) {
            $logs[] = [
                'user_id'        => $loggedUserId,
                'message'        => 'from Head Office',
                'hotel_id'       => $hotel->id,
                'log_type'       => 'head_office',
                'client_id'      => null,
                'head_office_id' => null,
                'created_by_id'  => $loggedUserId,
                'updated_by_id'  => null,
                'created_at'     => now(),
                'updated_at'     => now(),
            ];
        }

        // 2. Linked Head Office changes
        if (is_null($hotel->linked_head_office) && !empty($hotelData['linked_head_office'])) {
            $logs[] = [
                'user_id'        => $loggedUserId,
                'message'        => 'to Head Office',
                'hotel_id'       => $hotel->id,
                'log_type'       => 'head_office',
                'client_id'      => null,
                'head_office_id' => $hotelData['linked_head_office'],
                'created_by_id'  => $loggedUserId,
                'updated_by_id'  => null,
                'created_at'     => now(),
                'updated_at'     => now(),
            ];
        } elseif (!is_null($hotel->linked_head_office) && empty($hotelData['linked_head_office'])) {
            $logs[] = [
                'user_id'        => $loggedUserId,
                'message'        => 'from Head Office',
                'hotel_id'       => $hotel->id,
                'log_type'       => 'head_office',
                'client_id'      => null,
                'head_office_id' => $hotel->linked_head_office,
                'created_by_id'  => $loggedUserId,
                'updated_by_id'  => null,
                'created_at'     => now(),
                'updated_at'     => now(),
            ];
        }

        // 3. Point of Contact (POC) changes
        $existingPOCs = $this->hotelPocModel->where('hotel_id', $hotel->id)->pluck('client_id')->toArray();
        $newPOCs = $point_of_contact ?? [];

        // Added clients
        $addedClients = array_diff($newPOCs, $existingPOCs);
        foreach ($addedClients as $clientId) {
            $logs[] = [
                'user_id'        => $loggedUserId,
                'message'        => 'as POC',
                'hotel_id'       => $hotel->id,
                'log_type'       => 'poc',
                'client_id'      => $clientId,
                'head_office_id' => null,
                'created_by_id'  => $loggedUserId,
                'updated_by_id'  => null,
                'created_at'     => now(),
                'updated_at'     => now(),
            ];
        }

        // Removed clients
        $removedClients = array_diff($existingPOCs, $newPOCs);
        foreach ($removedClients as $clientId) {
            $logs[] = [
                'user_id'        => $loggedUserId,
                'message'        => 'from POC',
                'hotel_id'       => $hotel->id,
                'log_type'       => 'poc',
                'client_id'      => $clientId,
                'head_office_id' => null,
                'created_by_id'  => $loggedUserId,
                'updated_by_id'  => null,
                'created_at'     => now(),
                'updated_at'     => now(),
            ];
        }

        // Save all logs in bulk
        if (!empty($logs)) {
            $this->hotelLogModel->insert($logs);
        }
    }

    public function getHotelLinkageLogs(int $hotelId): Collection
    {
        $logs = $this->hotelLogModel
            ->where('hotel_id', $hotelId)
            ->with(['user', 'hotel', 'client.user'])
            ->orderBy('id', 'desc')
            ->get();

        // Group by date (Y-m-d) and sort the groups in descending order
        $groupedLogs = $logs->groupBy(function ($log) {
            return date('Y-m-d', strtotime($log->created_at));
        })->sortKeysDesc();

        foreach ($groupedLogs as $date => $logsOnDate) {
            foreach ($logsOnDate as $log) {
                if (!empty($log->head_office_id)) {
                    $log->headOffice = $this->model->find($log->head_office_id);
                } else {
                    $log->headOffice = [];
                }
            }
        }

        return $groupedLogs;
    }
}
