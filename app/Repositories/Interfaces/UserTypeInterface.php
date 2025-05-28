<?php

namespace App\Repositories\Interfaces;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

/**
 * Interface UserInterface
 * 
 * @package App\Repositories\Interfaces
 */
interface UserTypeInterface
{
    /**
     * Get data for user types.
     *
     * @return Collection The collection of users.
     */
    public function getUserTypes(string $type = ''): Collection;
}