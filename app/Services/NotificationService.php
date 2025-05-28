<?php

namespace App\Services;

use App\Notifications\SendNotification;
use Illuminate\Support\Facades\Notification;
use App\Repositories\Interfaces\NotificationInterface;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

/**
 *
 * NotificationService class
 * 
 */
class NotificationService
{
    /**
     * NotificationService constructor.
     *
     * @param NotificationInterface $notificationRepository The notification repository instance.
     */
    public function __construct(
        private NotificationInterface $notificationRepository,
    ) {
    }

    public function sendNotification($notificationData, $auth, $notificationType, $subject, $notifyUsers, $message, $template)
    {
        try {
            $notification = new SendNotification([
                'type' => $notificationType,
                'subject' => $subject,
                'from' => $auth->id,
                'data' => $notificationData,
                'template' => $template,
                'message' => $message,
            ]);
            foreach ($notifyUsers as $user) {
                $user->notify($notification);
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function getNotifications(array $requestData = [])
    {
        try {
            $loggedUser = Auth::user();
            $page = $requestData['page'] ?? 1;
            $sortField = $requestData['sortField'] ?? 'created_at';
            $sortDirection = $requestData['sortDirection'] ?? 'desc';
            $notificationDateRange = $requestData['notificationDateRange'] ?? null;
            $currentDate = Carbon::now()->toDateString(); // Get current date in MySQL format

            if ($notificationDateRange) {
                $dates = explode("-", $notificationDateRange);
                $startDate = Carbon::createFromFormat('d/m/Y', trim($dates[0]))->format('Y-m-d');
                $endDate = Carbon::createFromFormat('d/m/Y', trim($dates[1]))->format('Y-m-d');
            } else {
                $startDate = $currentDate;
                $endDate = $currentDate;
            }
            return $this->notificationRepository->getNotifications($loggedUser, $startDate, $endDate, $page, $sortField, $sortDirection);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function markAsRead(array $requestData)
    {
        try {
            $loggedUser = Auth::user();
            $dataType = $requestData['dataType'];
            $notificationId = $dataType === "mark-single-read" ? $requestData['dataId'] : null;
            $this->notificationRepository->markAsRead($loggedUser, $notificationId);
            $notification = $this->notificationRepository->getNotificationById($notificationId);
            $topHtml = view('components.partials.render-notifications')->render();
            if ($dataType === "mark-single-read") {
                $listHtml = view('components.partials.notification-row', compact('notification'))->render();
                $data = ['topHtml' => $topHtml, 'listHtml' => $listHtml];
            } else {
                $data = ['topHtml' => $topHtml];
            }
            return $data;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
