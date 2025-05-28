<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use App\Services\UserTypeService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\CustomHelper;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\ChangeProfileImageRequest;
use App\Services\SettingsService;

/**
 * Class SettingController
 * 
 * @package  App\Http\Controllers\Admin
 */
class SettingController extends Controller
{
    /**
     * Constructor for the SettingController class.
     *
     * Initializes SettingController with necessary services and dependencies.
     *
     * @param UserService    $userService        The UserService instance to handle user-related operations.
     * @param UserTypeService $userTypeService    The UserTypeService instance to handle user type-related operations.
     * @param SettingsService $settingsService    The SettingsService instance to manage application settings.
     * @param CustomHelper    $helper             The CustomHelper instance to provide additional utility functions.
     */
    public function __construct(
        private UserService $userService,
        private UserTypeService $userTypeService,
        private SettingsService $settingsService,
        private CustomHelper $helper
    ) {
    }
    /**
     * Display the settings page.
     *
     * Retrieves user profile data and renders the settings view.
     *
     * @return \Illuminate\Contracts\View\View The rendered view for the settings page.
     */
    public function index()
    {
        // Retrieve user profile data for the currently authenticated user
        $profileData = $this->userService->getUserData(Auth::user()->id);
        // Return the view for the settings page with the user profile data
        return view('admin.settings', compact('profileData'));
    }

    /**
     * Change the profile image of the authenticated user.
     *
     * Updates the profile image of the authenticated user based on the provided request data.
     *
     * @param ChangeProfileImageRequest $request The HTTP request object containing the new profile image data.
     * 
     * @return \Illuminate\Http\JsonResponse A JSON response containing the updated user data.
     */
    public function changeProfileImage(ChangeProfileImageRequest $request)
    {
        try {
            // Check if a new profile image is provided in the request
            $profileImage = $request->has('profile_image') ? $request->file('profile_image') : false;
            // Update the user's profile image using the UserService
            $userData = $this->userService->changeProfileImage($profileImage);
            // Generate and return a successful response with the updated user data
            return $this->handleResponse($userData, __("message.profile_image_updated_successfully"), Response::HTTP_OK);
        } catch (\Exception $e) {
            // Handle any exceptions that occur
            $this->helper->handleException($e);
            // Generate and return a response indicating the error that occurred
            return $this->handleResponse([], $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR, []);
        }
    }

    /**
     * Update the profile of the authenticated user.
     *
     * Updates the profile of the authenticated user based on the provided request data.
     *
     * @param Request $request The HTTP request object containing the updated profile data.
     * 
     * @return \Illuminate\Http\JsonResponse A JSON response containing the updated user data.
     */
    public function updateProfile(Request $request)
    {
        try {
            // Update the user's profile using the provided request data
            $userData = $this->settingsService->updateProfile($request->all());
            // Generate and return a successful response with the updated user data
            return $this->handleResponse($userData, __("message.profile_updated_successfully"), Response::HTTP_OK);
        } catch (\Exception $e) {
            // Handle any exceptions that occur
            $this->helper->handleException($e);
            // Generate and return a response indicating the error that occurred
            return $this->handleResponse([], $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR, []);
        }
    }

    /**
     * Check if the provided current password matches the authenticated user's password.
     *
     * Checks if the provided current password matches the password of the authenticated user.
     *
     * @param Request $request The HTTP request object containing the current password.
     * 
     * @return \Illuminate\Http\JsonResponse A JSON response indicating whether the current password is valid.
     */
    public function checkCurrentPassword(Request $request)
    {
        try {
            // Retrieve the authenticated user
            $user = Auth::user();
            // Check if the provided current password matches the user's password
            $isValid = Hash::check($request->current_password, $user->password) ? true : false;
            // Generate and return a response indicating whether the current password is valid
            return $this->handleResponse(['isvalid' => $isValid], '', Response::HTTP_OK);
        } catch (\Exception $e) {
            // Handle any exceptions that occur
            $this->helper->handleException($e);
            // Generate and return a response indicating the error that occurred
            return $this->handleResponse(['isvalid' => false], $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR, []);
        }
    }

    /**
     * Change the current password of the authenticated user.
     *
     * Changes the password of the authenticated user to the new password provided in the request.
     *
     * @param ChangePasswordRequest $request The HTTP request object containing the new password.
     * 
     * @return \Illuminate\Http\JsonResponse A JSON response containing the updated user data.
     */
    public function changeCurrentPassword(ChangePasswordRequest $request)
    {
        try {
            // Change the password of the authenticated user to the new password
            $userData = $this->userService->changePassword($request->input('new_password'));
            // Generate and return a response indicating that the password has been changed successfully
            return $this->handleResponse($userData, __('message.password_changed'), Response::HTTP_OK);
        } catch (\Exception $e) {
            // Handle any exceptions that occur
            $this->helper->handleException($e);
            // Generate and return a response indicating the error that occurred
            return $this->handleResponse([], $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR, []);
        }
    }

    /**
     * Removes the profile image of the authenticated user.
     *
     * This function removes the profile image associated with the authenticated user.
     *
     * @param Request $request The HTTP request object.
     * @return JsonResponse Returns a JSON response indicating the result of the operation.
     *
     * @throws \Exception If an error occurs during the process.
     */
    public function removeProfileImage(Request $request)
    {
        try {
            // Change the password of the authenticated user to the new password
            $userData = $this->userService->removeProfileImage();

            // Generate and return a response indicating that the profile image has been removed successfully
            return $this->handleResponse($userData, __('message.profile_image_removed'), Response::HTTP_OK);
        } catch (\Exception $e) {
            // Handle any exceptions that occur
            $this->helper->handleException($e);

            // Generate and return a response indicating the error that occurred
            return $this->handleResponse([], $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR, []);
        }
    }
}
