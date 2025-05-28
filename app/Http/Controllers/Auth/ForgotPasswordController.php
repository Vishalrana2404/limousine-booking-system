<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use App\CustomHelper;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    use SendsPasswordResetEmails;
    public function __construct(
        private CustomHelper $helper
    ) {
    }
    /**
     * Get the response for a successful password reset link.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function sendResetLinkResponse(Request $request, $response)
    {
        $this->helper->alertResponse(__('message.password_rest_link_sent'), 'success');
        return redirect()->route('login');
    }
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        // Get the user associated with the email
        $user = \App\Models\User::where('email', $request->email)->first();

        if (!$user) {
            return $this->sendResetLinkFailedResponse($request, Password::INVALID_USER);
        }

        // Create a password reset token
        $token = app('auth.password.broker')->createToken($user);

        // Send the password reset email using the custom notification
        $user->notify(new \App\Notifications\ResetPasswordNotification($token, $user));

        return $this->sendResetLinkResponse($request, Password::RESET_LINK_SENT);
    }




    /**
     * Get the response for a failed password reset link.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function sendResetLinkFailedResponse(Request $request, $response)
    {
        $this->helper->alertResponse(__('message.something_went_wrong'), 'error');
        return redirect()->back();
    }
}
