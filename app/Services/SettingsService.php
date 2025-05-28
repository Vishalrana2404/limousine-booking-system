<?php

namespace App\Services;

use App\Repositories\Interfaces\UserInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * SettingsService class
 * 
 */
class SettingsService
{
    /**
     * Constructor for the SettingsService class.
     *
     * @param UserInterface $userRepository The user repository instance.
     * @param Auth $auth The authentication service instance.
     */
    public function __construct(
        private UserInterface $userRepository,
        private Auth $auth,
    ) {
    }

    /**
     * Update the profile information of the currently logged-in user.
     *
     * @param array $requestData The request data containing the updated profile information.
     * @return User The updated user instance.
     * @throws \Exception If an error occurs during the update process.
     */
    public function updateProfile(array $requestData)
    {
        DB::beginTransaction();
        try {
            // Retrieve the currently logged-in user
            $loggedUser = Auth::user();

            // Prepare the profile data to update
            $profileData['first_name'] = $requestData['first_name'];
            $profileData['last_name'] = $requestData['last_name'];
            // Optionally, update other profile fields such as email

            // Update the user's profile information
            $profile =  $this->userRepository->updateUser($loggedUser, $requestData);

            // Commit the transaction
            DB::commit();

            // Return the updated user instance
            return $profile;
        } catch (\Exception $e) {
            // Rollback the transaction if an error occurs
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
    }
}
