<?php

namespace App\Broadcasting;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class Notification
{
    /**
     * Authenticate the user's access to the channel.
     */
    public function join(User $user): array|bool
    {
        return Auth::check() && (int) $user->id == Auth::id();
    }
}
