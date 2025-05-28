<?php

namespace App\Repositories;

use App\Models\ActivityLog;
use App\Repositories\Interfaces\ActivityLogInterface;

/**
 * Class ActivityLogRepository
 * 
 * @package App\Repositories
 */
class ActivityLogRepository implements ActivityLogInterface
{
    /**
     * ActivityLogRepository constructor.
     *
     * @param ActivityLog $model The ActivityLog model instance.
     */
    public function __construct(
        protected ActivityLog $model
    ) {
    }
    /**
     * Add a new Activity log.
     *
     * @param array $data The data for creating the Activity log.
     * @return ActivityLog The newly created Activity log.
     */
    public function addActivityLog(array $data): ActivityLog
    {
        return $this->model->create($data);
    }
}
