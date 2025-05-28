<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserType;
use App\Repositories\Interfaces\UserInterface;
use App\Repositories\Interfaces\UserTypeInterface;
use App\Repositories\Interfaces\ClientInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

/**
 * Class UserService
 * 
 * @package App\Services
 */
class UserService
{
    /**
     * Constructor for the UserService class.
     *
     * @param UserInterface $userRepository The user repository instance.
     * @param UserTypeInterface $userTypeRepository The user type repository instance.
     * @param ClientInterface $clientRepository The client repository instance.
     * @param UploadService $uploadService The upload service instance.
     * @param ActivityLogService $activityLogService The activity log service instance.
     * @param Auth $auth The authentication service instance.
     */
    public function __construct(
        private UserInterface $userRepository,
        private UserTypeInterface $userTypeRepository,
        private ClientInterface $clientRepository,
        private UploadService $uploadService,
        private ActivityLogService $activityLogService,
        private Auth $auth,
    ) {
    }


    /**
     * Get data for multiple users.
     *
     * @param mixed $requestData The request data (if needed).
     * @return mixed The users data.
     * @throws \Exception If an error occurs.
     */
    public function getUsersData(array $requestData = [])
    {
        try {
            // Retrieve the logged-in user
            $loggedUser =  Auth::user();
            $page = $requestData['page'] ?? 1;
            $search = $requestData['search'] ?? '';
            $filterByUserType = $requestData['filterByUserType'] ?? null;
            $sortField = $requestData['sortField'] ?? 'id';
            $sortDirection = $requestData['sortDirection'] ?? 'asc';
            return $this->userRepository->getUsers($loggedUser, $search, $filterByUserType, $page, $sortField, $sortDirection);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Get user data by ID.
     *
     * @param int $id The ID of the user.
     * @return User|null The user data if found, otherwise null.
     * @throws \Exception If an error occurs while retrieving user data.
     */
    public function getUserData(int $id)
    {
        try {
            // Return user data by ID from the repository
            return $this->userRepository->getUserById($id);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Create a new user.
     *
     * @param array $requestData The data for creating the user.
     * @return User The newly created user.
     * @throws \Exception If an error occurs while creating the user.
     */
    public function createUser(array $requestData, $log_headers)
    {
        DB::beginTransaction();
        try {
            $loggedUserId = Auth::user()->id;
            $userType = Auth::user()->userType->type ?? null;
            $userData = [];

            // Extract user data from request
            $userData['first_name'] = $requestData['first_name'];
            $userData['last_name'] = $requestData['last_name'];
            $userData['user_type_id'] = $requestData['user_type'];
            $userData['department'] = $requestData['department'];
            $userData['status'] = $requestData['status'];
            $userData['country_code'] = $requestData['country_code'];
            $userData['phone'] = $requestData['phone'];
            $userData['email'] = $requestData['email'];
            $userData['created_by_id'] = $loggedUserId;

            // Set a random password if not provided
            $userData['password'] = $requestData['password'] ?? Str::random(8);

            // Add user using repository
            $user = $this->userRepository->addUser($userData);

            if ($userType === UserType::CLIENT) {
                $this->createClient($user);
            }

            // Send password email if provided
            if (isset($requestData['password'])) {
                $this->sendPasswordEmail($user, $userData['password']);
            }
            $this->activityLogService->addActivityLog('create', User::class, json_encode([]), json_encode($userData), $log_headers['headers']['Origin'], $log_headers['headers']['User-Agent']);
            DB::commit();
            return $user;
        } catch (\Exception $e) {
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
    }


    /**
     * Send a password email to the user.
     *
     * @param User $user The user to whom the email will be sent.
     * @param string $password The password generated for the user.
     * @return void
     */
    public function sendPasswordEmail($user, $password)
    {
        // Send email using PasswordGenerated Mailable
        \Mail::to($user->email)->send(new \App\Mail\PasswordGenerated($user, $password));
    }

    /**
     * Update a user's information.
     *
     * @param array $requestData The updated user data.
     * @param User $user The user object to be updated.
     * @return User The updated user object.
     * @throws \Exception If an error occurs while updating the user.
     */
    public function updateUser(array $requestData, User $user, $log_headers)
    {
        DB::beginTransaction();
        try {
            $loggedUserId = Auth::user()->id;
            // Check if password is provided and not empty, then remove it from the request data
            if (!isset($requestData['password']) || empty($requestData['password'])) {
                unset($requestData['password']);
            }
            $requestData['updated_by_id'] = $loggedUserId;
            $requestData['user_type_id'] = $requestData['user_type'];
            $oldData = json_encode($user);
            // Update user using repository
            $user = $this->userRepository->updateUser($user, $requestData);
            $this->activityLogService->addActivityLog('update', User::class, $oldData, json_encode($requestData), $log_headers['headers']['Origin'], $log_headers['headers']['User-Agent']);
            DB::commit();
            return $user;
        } catch (\Exception $e) {
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Delete user(s) based on the provided request data.
     *
     * @param array $requestData The request object containing user IDs to be deleted.
     * @return bool True if the user(s) are deleted successfully, false otherwise.
     * @throws \Exception If an error occurs while deleting the user(s).
     */
    public function deleteUser($requestData, $log_headers)
    {
        DB::beginTransaction();
        try {
            // Delete user using repository
            $oldData = $this->userRepository->getUserByIds($requestData['user_ids']);
            $clientIds =  $this->clientRepository->getClientByUserIds($requestData['user_ids']);
            $user = $this->userRepository->deleteUser($requestData['user_ids']);
            if ($clientIds) {
                $this->clientRepository->deleteClient($clientIds);
            }
            $this->activityLogService->addActivityLog('delete', User::class, json_encode($oldData), json_encode([]), $log_headers['headers']['Origin'], $log_headers['headers']['User-Agent']);
            DB::commit();
            return $user;
        } catch (\Exception $e) {
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
    }



    /**
     * Update the status of multiple users based on the provided request data.
     *
     * @param array $requestData The request object containing user IDs and status to be updated.
     * @return bool True if the status of users is updated successfully, false otherwise.
     * @throws \Exception If an error occurs while updating the status of users.
     */
    public function updateBulkStatus($requestData, $log_headers)
    {
        DB::beginTransaction();
        try {
            $loggedUserId = Auth::user()->id;
            $userIds = $requestData['user_ids'];
            $status = $requestData['status'];
            $oldData = $this->userRepository->getUserByIds($userIds, $status);
            // Update user status using repository
            $user = $this->userRepository->updateBulkStatus($userIds, $status, $loggedUserId);
            $this->activityLogService->addActivityLog('updateBulkStatus', User::class, json_encode($oldData), json_encode($requestData), $log_headers['headers']['Origin'], $log_headers['headers']['User-Agent']);
            // Commit transaction
            DB::commit();
            return $user;
        } catch (\Exception $e) {
            // Rollback transaction and throw exception
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
    }



    /**
     * Change the profile image of the logged-in user.
     *
     * @param Illuminate\Http\UploadedFile $profileImage The uploaded profile image file.
     * @return App\Models\User The updated user object with the new profile image.
     * @throws \Exception If an error occurs while changing the profile image.
     */
    public function changeProfileImage($profileImage)
    {
        DB::beginTransaction();
        try {
            // Retrieve the logged-in user
            $loggedUser = Auth::user();
            // Define the folder name for storing profile images
            $folderName = 'profile_image/' . $loggedUser->id;
            // Set the path and create directory for storing profile images
            $this->uploadService->setPath($folderName);
            $this->uploadService->createDirectory();
            // Generate a unique file name for the profile image
            $fileName = time() . '.' . $profileImage->extension();
            // Delete the old profile image if it exists
            if ($loggedUser->profile_image) {
                $oldImagePath = $loggedUser->profile_image;
                Storage::delete($oldImagePath);
            }
            // Upload the new profile image and update user data
            $userData['profile_image'] = $this->uploadService->upload($profileImage, $fileName);
            // Update the user with the new profile image
            $user = $this->userRepository->updateUser($loggedUser, $userData);
            // Commit the transaction
            DB::commit();
            return $user;
        } catch (\Exception $e) {
            // Rollback the transaction and throw an exception
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Change the password of the logged-in user.
     *
     * @param string $password The new password for the user.
     * @return App\Models\User The updated user object with the new password.
     * @throws \Exception If an error occurs while changing the password.
     */
    public function changePassword($newPassword)
    {
        DB::beginTransaction();
        try {
            // Retrieve the logged-in user
            $loggedUser = Auth::user();
            // Prepare the updated user data with the new password
            $userData['password'] = $newPassword;
            // Update the user with the new password
            $user = $this->userRepository->updateUser($loggedUser, $userData);
            // Commit the transaction
            DB::commit();
            Auth::logoutOtherDevices($newPassword);
            return $user;
        } catch (\Exception $e) {
            // Rollback the transaction and throw an exception
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
    }



    /**
     * Check if the provided email is unique in the system.
     *
     * @param string $email The email to be checked for uniqueness.
     * @param int|null $userId The ID of the user to exclude from the uniqueness check.
     * @return bool True if the email is unique, false otherwise.
     * @throws \Exception If an error occurs while checking the email uniqueness.
     */
    public function checkUniqueEmail(string $email, int $userId = null)
    {
        try {
            // Call the UserRepository to check the uniqueness of the email
            $user = $this->userRepository->checkUniqueEmail($email, $userId);
            // Return true if the email is unique, false otherwise
            return $user ? false : true;
        } catch (\Exception $e) {
            // If an exception occurs, rollback and throw an exception
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Removes the profile image of the logged-in user.
     *
     * This method removes the profile image associated with the logged-in user.
     * If the user has a profile image, it deletes the image file from storage and updates the user record.
     *
     * @return bool Returns true if the profile image is successfully removed, otherwise throws an exception.
     *
     * @throws \Exception If an error occurs during the process.
     */
    public function removeProfileImage()
    {
        DB::beginTransaction();
        try {
            // Retrieve the logged-in user
            $loggedUser = Auth::user();
            $oldProfileImage = $loggedUser['profile_image'];

            // Update the user with the new profile image
            if ($oldProfileImage) {
                // Define the folder name for storing profile images
                $folderName = 'profile_image/' . $loggedUser->id;
                // Set the path for the upload service
                $this->uploadService->setPath($folderName);
                // Get the full path of the old profile image
                $oldImagePath = storage_path('app/public/' . $oldProfileImage);
                // Delete the old profile image from storage
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
                // Update user data to remove the profile image path
                $userData['profile_image'] = null;
                $this->userRepository->updateUser($loggedUser, $userData);
            }
            // Commit the transaction
            DB::commit();

            return true;
        } catch (\Exception $e) {
            // If an exception occurs, rollback and throw an exception
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Create a new client associated with the given user.
     * 
     * This method begins a database transaction, copies client-related data from the logged-in user's client,
     * and creates a new client entry with this data. If the operation is successful, the transaction is committed.
     * If any exception occurs, the transaction is rolled back, and the exception is rethrown.
     * 
     * @param User $user The user for whom the new client is being created.
     * @return Client The newly created client instance.
     * @throws \Exception If any error occurs during the client creation process.
     */
    private function createClient(User $user)
    {
        DB::beginTransaction();
        try {
            $loggedUserClient = Auth::user()->client;
            $clientData['user_id'] = $user->id;
            $clientData['hotel_id'] = $loggedUserClient->hotel_id;
            $clientData['event'] = $loggedUserClient->event;
            $clientData['invoice'] = $loggedUserClient->invoice;
            $clientData['status'] = $loggedUserClient->status;
            $clientData['entity'] = $loggedUserClient->entity;
            $clientData['created_by_id'] = Auth::user()->id;
            $client = $this->clientRepository->addClient($clientData);
            DB::commit();
            return $client;
        } catch (\Exception $e) {
            // Rollback the transaction if an error occurs
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
    }
    public function getAllusers()
    {
        try {
            return $this->userRepository->getAllusers();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
