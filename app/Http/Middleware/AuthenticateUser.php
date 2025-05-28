<?php

namespace App\Http\Middleware;

use App\CustomHelper;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticateUser
{
    public function __construct(
        private CustomHelper $helper
    ) {
    }
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user && $user->status === "ACTIVE") {
                return $next($request);
            } else {
                Auth::logout(); // Logout the user
                $this->helper->alertResponse(__('message.account_not_active'), 'error');
                return redirect()->route('login');
            }
        } else {
            return redirect()->route('login');
        }
    }
}
