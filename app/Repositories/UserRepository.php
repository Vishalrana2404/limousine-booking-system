<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\UserType;
use App\Repositories\Interfaces\UserInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Class UserRepository
 * 
 * @package App\Repositories
 */
class UserRepository implements UserInterface
{
    /**
     * UserRepository constructor.
     *
     * @param User $model The User model instance.
     */
    public function __construct(
        protected User $model
    ) {
    }

    /**
     * Get paginated list of users based on specified parameters.
     *
     * Retrieves a paginated list of users based on the provided logged-in user, search criteria,
     * user type filter, pagination, sorting field, and sorting direction.
     *
     * @param User $loggedUser The logged-in user instance.
     * @param string $search The search criteria for filtering users (optional).
     * @param int|null $filterByUserType The user type ID to filter users by (optional, null for all types).
     * @param int $page The page number for pagination (optional, default is 1).
     * @param string $sortField The field to sort users by (optional, default is 'id').
     * @param string $sortDirection The direction for sorting users ('asc' or 'desc', optional, default is 'asc').
     * @return LengthAwarePaginator A paginator for the retrieved users.
     */
    public function getUsers(User $loggedUser, string $search = '', int $filterByUserType = null, int $page = 1, string $sortField = 'id', string $sortDirection = 'asc'): LengthAwarePaginator
    {
        // Filter users based on the provided parameters
        $users = $this->filterUserResult($loggedUser, $search, $filterByUserType)->get();

        // Sort the users based on the specified field and direction
        $sortedCollection = $this->sortUsers($users, $sortField, $sortDirection);

        // Set the page size for pagination
        $pageSize = config('constants.paginationSize');

        // Paginate the sorted collection
        return $this->paginateResults($sortedCollection, $pageSize, $page);
    }

    /**
     * Filter user query result based on specified parameters.
     *
     * Builds and returns a query builder instance for filtering users based on the provided logged-in user,
     * search criteria, and optional user type filter.
     *
     * @param User $loggedUser The logged-in user instance.
     * @param string $search The search criteria for filtering users (optional).
     * @param int|null $filterByUserType The user type ID to filter users by (optional, null for all types).
     * @return \Illuminate\Database\Eloquent\Builder The query builder instance for filtering users.
     */
    private function filterUserResult(User $loggedUser, string $search = '', int $filterByUserType = null)
    {
        $loggedUserId = $loggedUser->id;
        $loggedUserType = $loggedUser->userType->type ?? null;
        $loggedUserHotel = $loggedUser->client->hotel_id ?? null;
        // Start building the query with eager loading relationships
        $query = $this->model->with(['userType', 'client']);
        if ($loggedUserType === null || $loggedUserType === UserType::ADMIN) {
            $query->whereHas('userType', function ($query) {
                $query->where('type', UserType::ADMIN);
            });
        } elseif ($loggedUserType === UserType::CLIENT) {
            $query->where('created_by_id', $loggedUserId)
                ->orWhereHas('client', function ($query) use ($loggedUserHotel) {
                    $query->where('hotel_id', $loggedUserHotel);
                });
        } else {
            $query->where('created_by_id', $loggedUserId);
        }
        // Apply user type filter if provided
        if ($filterByUserType) {
            $query->where(function ($query) use ($filterByUserType) {
                $query->where('user_type_id', $filterByUserType);
            });
        }
        // Apply search query filters
        if (!empty($search)) {
            $search = strtolower($search);
            $query->where(function ($query) use ($search) {
                // Search for keywords in first name, last name, department, phone, and email fields
                $query->whereRaw('LOWER(`first_name`) like ?', ['%' . $search . '%'])
                    ->orWhereRaw('LOWER(`last_name`) like ?', ['%' . $search . '%'])
                    ->orWhereRaw('LOWER(`department`) like ?', ['%' . $search . '%'])
                    ->orWhereRaw('LOWER(`phone`) like ?', ['%' . $search . '%'])
                    ->orWhereRaw('LOWER(`email`) like ?', ['%' . $search . '%'])
                    ->orWhereHas('userType', function ($query) use ($search) {
                        $query->whereRaw('LOWER(`name`) like ?', ['%' . $search . '%']);
                    });
            });
        }
        return $query;
    }



    /**
     * Sort a collection of users based on specified field and direction.
     *
     * Sorts the provided collection of users based on the specified field and direction.
     *
     * @param Collection $users The collection of users to be sorted.
     * @param string $sortField The field to sort users by (optional, default is 'id').
     * @param string $sortDirection The direction for sorting users ('asc' or 'desc', optional, default is 'asc').
     * @return Collection The sorted collection of users.
     */
    private function sortUsers(Collection $users, string $sortField = 'id', string $sortDirection = 'asc')
    {
        $sortFunction = $sortDirection == 'asc' ? 'sortBy' : 'sortByDesc';
        return $users->$sortFunction(function ($innerQuery) use ($sortField) {
            switch ($sortField) {
                case 'sortName':
                    $value = strtolower($innerQuery->first_name . ' ' . $innerQuery->last_name ?? 'zzzz');
                    break;
                case 'sortPhone':
                    $value = strtolower($innerQuery->phone ?? 'zzzz');
                    break;
                case 'sortEmail':
                    $value = strtolower($innerQuery->email ?? 'zzzz');
                    break;
                case 'sortDepartment':
                    $value = strtolower($innerQuery->department ?? 'zzzz');
                    break;
                case 'sortUserType':
                    $userType = $innerQuery->userType;
                    if ($userType && $userType->type === 'client') {
                        $value = strtolower($userType->type . ' ' . ($userType->name ?? 'zzzz'));
                    } else {
                        $value = strtolower($userType->name ?? 'zzzz');
                    }
                    // $value = strtolower($innerQuery->userType->type === 'client' ? ucfirst($innerQuery->userType->type) : '' . $innerQuery->userType->name ?? 'zzzz');
                    break;
                case 'sortStatus':
                    $value = strtolower($innerQuery->status ?? 'zzzz');
                    break;
                default:
                    $value = $innerQuery->id;
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
     * Get data for a specific user by ID.
     *
     * @param int $userId The ID of the user.
     * @return User|null The user instance or null if not found.
     */
    public function getUserById(int $userId): ?User
    {
        return $this->model->find($userId);
    }
    /**
     * Add a new user.
     *
     * @param array $data The data for creating the user.
     * @return User The newly created user.
     */
    public function addUser(array $data): User
    {
        return $this->model->create($data);
    }

    /**
     * Update a user with new data.
     *
     * Updates the provided user instance with the specified data.
     *
     * @param User $user The user instance to update.
     * @param array $data The new data for updating the user.
     * @return bool True if the user is successfully updated, false otherwise.
     */
    public function updateUser(User $user, array $data): bool
    {
        return $user->update($data);
    }

    /**
     * Delete bulk user.
     *
     * @param array $userIds The ID of the users to delete.
     * @return bool True if the user is deleted successfully, false otherwise.
     */
    public function deleteUser(array $userIds): bool
    {
        return $this->model->whereIn('id', $userIds)->delete();
    }
    /**
     * update bulk user status.
     *
     * @param array $userIds The ID of the users to update status.
     * @param string $status The status of the users to update status.
     * @param int $loggedUserId The id of logged user.
     * @return bool True if the user is updated successfully, false otherwise.
     */
    public function updateBulkStatus(array $userIds, string $status, int $loggedUserId): bool
    {
        return $this->model->whereIn('id', $userIds)->update(['status' => $status, 'updated_by_id' => $loggedUserId]);
    }

    /**
     * Check if an email is unique in the database.
     *
     * Checks whether the provided email address is unique in the database.
     * Optionally, it excludes the user with the given userId from the check.
     *
     * @param string $email The email address to check for uniqueness.
     * @param int|null $userId The ID of the user to exclude from the check (optional).
     * @return User|null The user instance with the specified email if found, null otherwise.
     */
    public function checkUniqueEmail(string $email, int $userId = null): ?User
    {
        // Check if a userId is provided
        if ($userId) {
            // If userId is provided, check for unique email excluding the current user's email
            return $this->model->where('email', $email)->where('id', '!=', $userId)->first();
        } else {
            // If userId is not provided, simply check for unique email
            return $this->model->where('email', $email)->first();
        }
    }
    /**
     * Retrieves a collection of users based on the given array of user IDs and an optional status.
     *
     * This function queries the database to fetch all users whose IDs are in the provided array.
     * Optionally, it filters the users by their status if a status is provided.
     *
     * @param array $userIds An array of user IDs to retrieve.
     * @param string|null $status An optional status to filter the users by. Defaults to null.
     * 
     * @return \Illuminate\Support\Collection A collection of users that match the given IDs and status.
     *
     */
    public function getUserByIds(array $userIds, string $status = null): Collection
    {
        // Start the query with the base condition
        $query = $this->model->whereIn('id', $userIds);
        // Add the status condition if it's provided
        if (!empty($status)) {
            $query->where('status', $status);
        }
        // Execute the query and return the result
        return $query->get();
    }

    /**
     * Retrieve a collection of super admin users.
     *
     * This method fetches users from the database who have a user type ID of 1 or 2, 
     * or no user type ID at all (null), and whose status is 'ACTIVE'.
     *
     * @return \Illuminate\Database\Eloquent\Collection The collection of super admin users.
     */
    public function getAdmins(): Collection
    {
        return $this->model->where(function ($query) {
            $query->whereIn("user_type_id", [1, 2])
                ->orWhereNull("user_type_id");
        })
            ->where("status", "ACTIVE")
            ->get();
    }

    public function getAllUsers(): Collection
    {
        return $this->model->get();
    }

    public function getHotelAdminByHotelId(int $hotelId): Collection
    {
        return $this->model->where('user_type_id', 3)
            ->whereHas('client', function ($query) use ($hotelId) {
                $query->where('hotel_id', $hotelId);
            })
            ->get();
    }
}
