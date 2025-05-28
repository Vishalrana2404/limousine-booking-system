<?php

namespace App\Repositories\Interfaces;

use App\Models\ActivityLog;

/**
 * Interface ActivityLogInterface
 * 
 * @package App\Repositories\Interfaces
 */
interface ActivityLogInterface
{

    /**
     * Add a new Activity log.
     *
     * @param array $data The data for creating the Activity log.
     * @return ActivityLog The newly created Activity log.
     */
    public function addActivityLog(array $data): ActivityLog;
}
