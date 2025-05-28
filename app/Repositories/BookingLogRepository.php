<?php

namespace App\Repositories;

use App\Models\BookingLog;
use App\Repositories\Interfaces\BookingLogInterface;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class BookingLogRepository
 * 
 * This class implements the BookingBillingInterface and provides methods to interact with BookingBilling.
 */
class BookingLogRepository implements BookingLogInterface
{
    /**
     * Create a new instance of the BookingBillingRepository.
     *
     * @param BookingBilling $model The model instance for BookingBilling.
     */
    public function __construct(
        protected BookingLog $model
    ) {
    }

    public function getLogs(int $userId = null,  string $startDate = "", string $endDate = "", $bookingId = null): Collection
    {
        $query = $this->model->with(["user", "booking"]);
        if ($userId) {
            $query->where('user_id', $userId);
        }
        if ($bookingId) {
            $query->where('booking_id', $bookingId);
        }
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }
        return  $query->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function ($log) {
                return $log->created_at->format('Y-m-d');
            });
    }


    public function addLogs(array $data): BookingLog
    {
        return $this->model->create($data);
    }
}
