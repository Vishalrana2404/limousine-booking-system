<?php

namespace App\Repositories\Interfaces;

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * Interface UserInterface
 *
 * Represents an interface for managing users.
 */
interface UserInterface
{
    /**
     * Get users based on various criteria.
     *
     * @param User $loggedUser The logged-in user.
     * @param string $search The search keyword to filter users (optional, default is an empty string).
     * @param int|null $filterByUserType The ID of the user type to filter users by (optional, default is null).
     * @param int $page The page number for pagination (optional, default is 1).
     * @param string $sortField The field to sort users by (optional, default is 'id').
     * @param string $sortDirection The direction for sorting users ('asc' or 'desc', optional, default is 'asc').
     * @return LengthAwarePaginator A paginated collection of users.
     */
    public function getUsers(User $loggedUser, string $search = '', int $filterByUserType = null, int $page = 1, string $sortField = 'id', string $sortDirection = 'asc'): LengthAwarePaginator;

    /**
     * Get a user by its ID.
     *
     * @param int $userId The ID of the user to retrieve.
     * @return User|null The user instance associated with the specified ID, or null if not found.
     */
    public function getUserById(int $userId): ?User;

    /**
     * Add a new user.
     *
     * @param array $data The data for creating the user.
     * @return User The newly created user instance.
     */
    public function addUser(array $data): User;

    /**
     * Update a user's information.
     *
     * @param User $user The user instance to update.
     * @param array $data The data for updating the user.
     * @return bool True if the user is successfully updated, false otherwise.
     */
    public function updateUser(User $user, array $data): bool;

    /**
     * Delete multiple users.
     *
     * @param array $userIds The IDs of the users to delete.
     * @return bool True if the users are successfully deleted, false otherwise.
     */
    public function deleteUser(array $userIds): bool;

    /**
     * Update the status of multiple users.
     *
     * @param array $userIds The IDs of the users to update.
     * @param string $status The new status for the users.
     * @param int $loggedUserId The ID of the logged-in user performing the action.
     * @return bool True if the bulk status update is successful, false otherwise.
     */
    public function updateBulkStatus(array $userIds, string $status, int $loggedUserId): bool;

    /**
     * Check if an email is unique.
     *
     * @param string $email The email to check for uniqueness.
     * @param int|null $userId The ID of the user to exclude from the uniqueness check (optional, default is null).
     * @return User|null The user instance with the specified email, or null if not found.
     */
    public function checkUniqueEmail(string $email, int $userId = null): ?User;

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
    public function getUserByIds(array $userIds, string $status = null): Collection;

    public function getAdmins(): Collection;
    public function getAllUsers(): Collection;

    public function getHotelAdminByHotelId(int $hotelId): Collection;
}
