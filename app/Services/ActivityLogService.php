<?php

namespace App\Services;

use App\Repositories\Interfaces\ActivityLogInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Class ActivityLogService
 * 
 * @package App\Services
 */
class ActivityLogService
{
    /**
     * ActivityLogService constructor.
     *
     * @param ActivityLogInterface $activityLogRepository The Activity log repository instance.
     */
    public function __construct(
        private ActivityLogInterface $activityLogRepository,
        private Auth $auth
    ) {
    }
    public function addActivityLog(string $functionName,  string $tableName, string $oldData, string $newData,  string $ipAddress, string $userAgent)
    {
        DB::beginTransaction();
        try {
            $loggedUserId = Auth::user()->id;
            // Create an array to hold the activity data
            $activityData = [
                'user_id' => $loggedUserId,
                'function_name' => $functionName,
                'model_name' => $tableName,
                'old_data' => $oldData,
                'new_data' => $newData,
                'ip_address' => $ipAddress,
                'user_device' => $userAgent,
            ];

            // Create the activity log record
            $result =  $this->activityLogRepository->addActivityLog($activityData);

            DB::commit();
            return $result;
        } catch (\Exception $e) {
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
    }
}
