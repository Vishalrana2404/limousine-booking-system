<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\CustomHelper;

class Admin
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
    // Admin Middleware
    public function handle(Request $request, Closure $next)
    {

        $user = Auth::user();
        $userTypeSlug = $user->userType->slug ?? null;
        if ($userTypeSlug === null || $userTypeSlug === 'admin') {
            return $next($request);
        }
        $this->helper->alertResponse(__('message.permission_denied'), 'error');
        return redirect()->route('dashboard');
    }
}
