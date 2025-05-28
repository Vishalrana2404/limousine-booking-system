<?php

namespace App\Services;

use App\Repositories\Interfaces\UserTypeInterface;

/**
 * Class UserTypeService
 * 
 * @package App\Services
 */
class UserTypeService
{
    /**
     * UserTypeService constructor.
     *
     * @param UserTypeInterface $userTypeRepository The user repository instance.
     */
    public function __construct(
        private UserTypeInterface $userTypeRepository
    ) {
    }

    /**
     * Retrieve user types based on the provided type.
     *
     * @param string $type The type of user types to retrieve.
     * @return \Illuminate\Support\Collection A collection of user types matching the provided type.
     * @throws \Exception If an error occurs while retrieving user types.
     */
    public function getUserType(string $type = '')
    {
        try {
            // Call the UserTypeRepository to retrieve user types based on the provided type
            return $this->userTypeRepository->getUserTypes($type);
        } catch (\Exception $e) {
            // If an exception occurs, throw an exception with the error message
            throw new \Exception($e->getMessage());
        }
    }
}
