<?php

namespace App\Repositories\Interfaces;

use App\Models\BookingLog;
use Illuminate\Database\Eloquent\Collection;

/**
 * Interface BookingInterface
 *
 * Represents an interface for managing booking.
 */
interface BookingLogInterface
{
    public function getLogs(int $userId, string $startDate, string $endDate, $bookingId = null): Collection;
    public function addLogs(array $data): BookingLog;
}
