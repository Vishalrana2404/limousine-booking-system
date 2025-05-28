<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\UserTypeService;
use App\CustomHelper;

/**
 * Class UserTypeController
 * 
 * @package App\Http\Controllers\Admin
 */
class UserTypeController extends Controller
{
    /**
     * UserTypeController constructor.
     * 
     * @param UserTypeService $userTypeService The user type service instance.
     * @param CustomHelper    $helper          The custom helper instance.
     */
    public function __construct(
        private UserTypeService $userTypeService,
        private CustomHelper $helper
    ) {
    }
}
