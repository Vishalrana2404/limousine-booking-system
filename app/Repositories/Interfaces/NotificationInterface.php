<?php

namespace App\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\User;

/**
 * Interface NotificationInterface
 * 
 * @package App\Repositories\Interfaces
 */
interface NotificationInterface
{
    /**
     * Get data for notification class.
     *
     */
    public function getNotifications(User $loggedUser,  $startDate, $endDate, int $page = 1, string $sortField = 'id', string $sortDirection = 'asc'): LengthAwarePaginator;

    public function markAsRead(User $loggedUser, $notificationId = null);
    public function getNotificationById($id);
}
