<?php

namespace App\Repositories;

use App\Models\UserType;
use App\Repositories\Interfaces\UserTypeInterface;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class UserTypeRepository
 * 
 * @package App\Repositories
 */
class UserTypeRepository implements UserTypeInterface
{
    /**
     * UserTypeRepository constructor.
     *
     * @param UserType $model The User Type model instance.
     */
    public function __construct(
        private UserType $model
    ) {
    }

    /**
     * Get user types by type.
     *
     * Retrieves a collection of user types based on the specified type.
     *
     * @param string $type The type of user types to retrieve.
     * @return Collection A collection of user types matching the specified type.
     */
    public function getUserTypes(string $type = ''): Collection
    {
        if ($type) {
            return $this->model->where('type', $type)->get();
        } else {
            return $this->model->get();
        }
    }
}
