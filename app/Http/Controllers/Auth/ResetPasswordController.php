<?php

namespace App\Http\Controllers\Auth;

use App\CustomHelper;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class ResetPasswordController extends Controller
{
    use ResetsPasswords;
    public function __construct(
        private UserService $userService,
        private CustomHelper $helper
    ) {
    }
    protected function redirectTo()
    {
        $this->helper->alertResponse(__('message.password_reset_successfully'), 'success');
        return route('dashboard');
    }

    /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @param  string  $password
     * @return void
     */
    protected function resetPassword($user, $password)
    {
        $user->password = Hash::make($password);

        $user->setRememberToken(Str::random(60));

        $user->save();

        // Logout from other devices
        Auth::logoutOtherDevices($password);

        $this->guard()->login($user);
    }

    /**
     * Reset the password for the given email.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changePassword(Request $request)
    {
        try {
            // Get the email from the request
            $email = $request->input('email');
            // Find the user by email
            $user = User::where('email', $email)->first();
            // If user not found, return 404 response
            if (!$user) {
                return $this->handleResponse([], __("message.client_not_found"), Response::HTTP_NOT_FOUND);
            }
            // Generate a random password
            $password = Str::random(8);
            // Set the user's password to the generated password
            $user->password = $password;
            // Save the user
            $user->save();
            // Send password reset email
            $this->userService->sendPasswordEmail($user, $password);
            // Return success response
            return $this->handleResponse([], __("message.password_reset_successfully"), Response::HTTP_OK);
        } catch (\Exception $e) {
            // Handle any exceptions that occur
            $this->helper->handleException($e);
            // Return error response
            return $this->handleResponse([], $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR, []);
        }
    }
}
