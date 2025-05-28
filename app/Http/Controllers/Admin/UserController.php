<?php

namespace App\Http\Controllers\Admin;

use App\CustomHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\AddUserRequest;
use App\Http\Requests\BulkUserStatusUpdateRequest;
use App\Http\Requests\DeleteUserRequest;
use App\Http\Requests\EditUserRequest;
use App\Models\User;
use App\Models\UserType;
use App\Services\UserService;
use App\Services\UserTypeService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

/**
 * Class UserController
 * 
 * @package  App\Http\Controllers\Admin
 */
class UserController extends Controller
{
    /**
     * Constructor for the UserController class.
     *
     * Initializes UserController with necessary services and dependencies.
     *
     * @param UserService    $userService     The UserService instance to handle user-related operations.
     * @param UserTypeService $userTypeService The UserTypeService instance to handle user type-related operations.
     * @param CustomHelper    $helper          The CustomHelper instance to provide additional utility functions.
     */
    public function __construct(
        private UserService $userService,
        private UserTypeService $userTypeService,
        private CustomHelper $helper
    ) {
    }
    /**
     * Display the list of users.
     *
     * Retrieves user data and user type data, then renders the users view.
     *
     * @param Request $request The HTTP request object containing optional filters.
     * 
     * @return \Illuminate\Contracts\View\View The rendered view for the list of users.
     */
    public function index(Request $request)
    {
        try {
            // Retrieve user data from the UserService based on optional filters
            $userData = $this->userService->getUsersData($request->query());
            $usertype = Auth::user()->userType->type ?? null;
            $type = ($usertype === null || $usertype === UserType::ADMIN) ? UserType::ADMIN : UserType::CLIENT;
            $userTypeData = $this->userTypeService->getUserType($type);
            // Return the view for the list of users with the retrieved data
            return view('admin.user.users', compact('userData', 'userTypeData'));
        } catch (\Exception $e) {
            // Display an error alert and handle any exceptions that occur
            $this->helper->alertResponse(__('messages.something_went_wrong'), 'error');
            $this->helper->handleException($e);
            // Redirect back with an error message
            return redirect()->back();
        }
    }


    /**
     * Display the form for creating a new user.
     *
     * Retrieves user type data for admin users and renders the create user view.
     *
     * @param Request $request The HTTP request object.
     * 
     * @return \Illuminate\Contracts\View\View The rendered view for creating a new user.
     */
    public function create(Request $request)
    {
        // Retrieve departments  data from constatnts
        $departments = config('constants.departments');
        $usertype = Auth::user()->userType->type ?? null;
        $type = ($usertype === null || $usertype === UserType::ADMIN) ? UserType::ADMIN : UserType::CLIENT;
        $userTypeData = $this->userTypeService->getUserType($type);
        // Return the view for creating a new user with the retrieved data
        return view('admin.user.create-user', compact('userTypeData', 'departments'));
    }

    /**
     * Save a new user.
     *
     * Attempts to create a new user using the provided request data.
     *
     * @param AddUserRequest $request The HTTP request object containing the user data.
     * 
     * @return \Illuminate\Http\RedirectResponse A redirect response after successful user creation.
     */
    public function save(AddUserRequest $request)
    {
        try {
            $log_headers = $this->getHttpData($request);
            // Create a new user using the provided request data
            $this->userService->createUser($request->all(), $log_headers);
            // Display a success message and redirect to the users page
            $this->helper->alertResponse(__('message.user_created_successfully'), 'success');
            return redirect('users');
        } catch (\Exception $e) {
            // Handle any exceptions that occur
            $this->helper->handleException($e);
            // Display an error message and redirect back to the previous page
            $this->helper->alertResponse(__('messages.something_went_wrong'), 'error');
            return redirect()->back();
        }
    }


    /**
     * Display the form for editing a user.
     *
     * Retrieves user type data for admin users and renders the update user view with the specified user data.
     *
     * @param User $user The user instance to be edited.
     * 
     * @return \Illuminate\Contracts\View\View The rendered view for updating a user.
     */
    public function edit(User $user)
    {
        $userHotelId = $user->client->hotel_id ?? null;
        $loggedUserHotelId = Auth::user()->client->hotel_id ?? null;
        if ($loggedUserHotelId !== null && $userHotelId !== $loggedUserHotelId) {
            $this->helper->alertResponse(__('message.permission_denied'), 'error');
            return redirect()->route('dashboard');
        }        
        // Retrieve departments  data from constatnts
        $departments = config('constants.departments');
        $usertype = Auth::user()->userType->type ?? null;
        $type = ($usertype === null || $usertype === UserType::ADMIN) ? UserType::ADMIN : UserType::CLIENT;
        $userTypeData = $this->userTypeService->getUserType($type);
        // Return the view for updating a user with the specified user data and user type data
        return view('admin.user.update-user', compact('userTypeData', 'user', 'departments'));
    }


    /**
     * Update a user.
     *
     * Updates a user using the provided request data and user instance.
     *
     * @param EditUserRequest $request The HTTP request object containing the updated user data.
     * @param User $user The user instance to be updated.
     * 
     * @return \Illuminate\Http\RedirectResponse A redirect response after successful user update.
     */
    public function update(EditUserRequest $request, User $user)
    {
        try {
            $log_headers = $this->getHttpData($request);
            // Update the user using the provided request data and user instance
            $this->userService->updateUser($request->all(), $user, $log_headers);
            // Display a success message and redirect to the users page
            $this->helper->alertResponse(__('message.user_updated_successfully'), 'success');
            return redirect('users');
        } catch (\Exception $e) {
            // Handle any exceptions that occur
            $this->helper->handleException($e);
            // Display an error message and redirect back to the previous page
            $this->helper->alertResponse(__('messages.something_went_wrong'), 'error');
            return redirect()->back();
        }
    }

    /**
     * Delete a user.
     *
     * Validates input parameters, deletes the user using the UserService, and returns a response.
     *
     * @param DeleteUserRequest $request The HTTP request object containing the parameters for user deletion.
     * 
     * @return \Illuminate\Http\JsonResponse A JSON response containing the result of the user deletion operation.
     */
    public function delete(DeleteUserRequest $request)
    {
        try {
            $log_headers = $this->getHttpData($request);
            // Delete the user using the UserService
            $userData = $this->userService->deleteUser($request->all(), $log_headers);
            // Generate and return a successful response with the result of the user deletion operation
            return $this->handleResponse($userData, __("message.user_deleted_successfully"), Response::HTTP_OK);
        } catch (\Exception $e) {
            // Handle any exceptions that occur
            $this->helper->handleException($e);
            // Generate and return a response indicating the error that occurred
            return $this->handleResponse([], $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR, []);
        }
    }


    /**
     * Update status for multiple users in bulk.
     *
     * Validates input parameters, updates the status of multiple users using the UserService,
     * and returns a response.
     *
     * @param BulkUserStatusUpdateRequest $request The HTTP request object containing the parameters for updating user statuses in bulk.
     * 
     * @return \Illuminate\Http\JsonResponse A JSON response containing the result of the bulk user status update operation.
     */
    public function updateBulkStatus(BulkUserStatusUpdateRequest $request)
    {
        try {
            $log_headers = $this->getHttpData($request);
            // Update the status of multiple users using the UserService
            $userData = $this->userService->updateBulkStatus($request->all(), $log_headers);
            // Generate and return a successful response with the result of the bulk user status update operation
            return $this->handleResponse($userData, __("message.user_status_updated_successfully"), Response::HTTP_OK);
        } catch (\Exception $e) {
            // Handle any exceptions that occur
            $this->helper->handleException($e);
            // Generate and return a response indicating the error that occurred
            return $this->handleResponse([], $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR, []);
        }
    }


    /**
     * Filter users based on provided criteria.
     *
     * Retrieves user data based on the provided request criteria and returns a response.
     *
     * @param Request $request The HTTP request object containing the criteria for filtering users.
     * 
     * @return \Illuminate\Http\JsonResponse A JSON response containing the filtered user data.
     */
    public function filterUsers(Request $request)
    {
        try {
            // Retrieve user data from the UserService based on the provided criteria
            $userData = $this->userService->getUsersData($request->query());
            // Render the HTML for the user listing view
            $data = ['html' => view('admin.user.partials.user-listing', compact('userData'))->render()];
            // Generate and return a successful response with the filtered user data
            return $this->handleResponse($data, __("message.user_filtered_successfully"), Response::HTTP_OK);
        } catch (\Exception $e) {
            // Handle any exceptions that occur
            $this->helper->handleException($e);
            // Generate and return a response indicating the error that occurred
            return $this->handleResponse([], $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR, []);
        }
    }
    /**
     * Check if the provided email is unique.
     *
     * Checks if the provided email is unique among users, excluding the user with the specified user ID (if provided).
     *
     * @param Request $request The HTTP request object containing the email and optional user ID.
     * 
     * @return \Illuminate\Http\JsonResponse A JSON response indicating whether the email is unique.
     */
    public function checkUniqueEmail(Request $request)
    {
        try {
            // Retrieve user ID and email from the request
            $userId = $request->input('user_id', null);
            $email = $request->input('email');
            // Check if the email is unique among users, excluding the user with the specified user ID (if provided)
            $result = $this->userService->checkUniqueEmail($email, $userId);
            // Generate and return a response indicating whether the email is unique
            return $this->handleResponse(['isvalid' => $result], '', Response::HTTP_OK);
        } catch (\Exception $e) {
            // Handle any exceptions that occur
            $this->helper->handleException($e);
            // Generate and return a response indicating the error that occurred
            return $this->handleResponse(['isvalid' => false], $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR, []);
        }
    }
}
