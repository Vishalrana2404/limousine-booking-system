<?php

namespace App\Services;

use App\Repositories\Interfaces\ServiceTypeInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * ServiceTypeService class
 * 
 */
class ServiceTypeService
{
    /**
     * Constructor for the ServiceTypeService class.
     *
     * Initializes the ServiceTypeService with necessary dependencies.
     *
     * @param ServiceTypeInterface $serviceTypeRepository The repository for service type operations.
     * @param Auth                 $auth                  The authentication service instance.
     */
    public function __construct(
        private ServiceTypeInterface $serviceTypeRepository,
        private Auth $auth,
    ) {
    }
    /**
     * Retrieve service types.
     *
     * Retrieves service types from the service type repository.
     *
     * @return mixed The service types retrieved from the repository.
     * @throws \Exception If an error occurs during the retrieval process.
     */
    public function getServiceTypes(bool $isCreatePage=false)
    {
        try {
            $loggedUserType = Auth::user()->userType->type ?? null;
            $user = $this->serviceTypeRepository->getServiceTypes($loggedUserType,$isCreatePage);
            return $user;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
