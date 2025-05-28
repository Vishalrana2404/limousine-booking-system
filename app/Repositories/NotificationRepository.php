<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Collection;
use App\Repositories\Interfaces\NotificationInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Notifications\DatabaseNotification;

/**
 * Class NotificationRepository
 * 
 * @package App\Repositories
 */
class NotificationRepository implements NotificationInterface
{
    /**
     * NotificationRepository constructor.
     *
     */
    public function __construct()
    {
    }
    /**
     * Get notifications.
     *
     */
    public function getNotifications(User $loggedUser,  $startDate, $endDate, int $page = 1, string $sortField = 'created_at', string $sortDirection = 'desc'): LengthAwarePaginator
    {
        // Filter notification based on the provided parameters
        $notificationQuery = $this->filterNotificationsResult($loggedUser, $startDate, $endDate)->get();

        // Sort the notification based on the specified field and direction
        $sortedCollection = $this->sortNotifications($notificationQuery, $sortField, $sortDirection);

        // Set the page size for pagination
        $pageSize = config('constants.paginationSize');

        // Paginate the sorted collection
        return $this->paginateResults($sortedCollection, $pageSize, $page);
    }

    private function filterNotificationsResult(User $loggedUser, $startDate, $endDate)
    {
        $loggedUserId = $loggedUser->id;
        $query = $loggedUser->notifications();

        if (!empty($startDate) && !empty($endDate)) {
            $query->whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endDate]);
        }

        return $query;
    }

    private function sortNotifications(Collection $notificationQuery, string $sortField = 'created_at', string $sortDirection = 'desc')
    {
        $sortFunction = $sortDirection == 'asc' ? 'sortBy' : 'sortByDesc';
        return $notificationQuery->$sortFunction(function ($innerQuery) use ($sortField) {
            $dateTimeObj = Carbon::parse($innerQuery->created_at);
            $date = $dateTimeObj->format('Y-m-d');
            $time = $dateTimeObj->format('H:i:s');
            switch ($sortField) {
                case 'sortDate':
                    $value = $date;
                    break;
                case 'sortTime':
                    $value = $time;
                    break;
                default:
                    $value = $innerQuery->created_at;
                    break;
            }
            return $value;
        });
    }

    private function paginateResults($collection, $pageSize, $page = 1): LengthAwarePaginator
    {
        return new LengthAwarePaginator(
            $collection->values()->forPage($page, $pageSize), // Paginate the collection for the specified page and page size
            $collection->count(), // Total count of items in the collection
            $pageSize, // Number of items per page
            $page, // Current page number
            ['path' => LengthAwarePaginator::resolveCurrentPath()] // Path for generating pagination links
        );
    }

    public function markAsRead(User $loggedUser, $notificationId = null)
    {
        if (empty($notificationId)) {
            $unreadNotifications = $loggedUser->unreadNotifications;

            if ($unreadNotifications->count() > 0) {
                $result = $unreadNotifications->markAsRead();
            }
        } else {
            // Fetch the specific notification by its ID
            $unreadNotifications = $loggedUser->notifications()->find($notificationId);
            // Check if the notification exists and mark it as read
            if ($unreadNotifications) {
                $unreadNotifications->markAsRead();
            }
        }
        return true;
    }

    public function getNotificationById($id)
    {
        return DatabaseNotification::find($id);
    }
}
