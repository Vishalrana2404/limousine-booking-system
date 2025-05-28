<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Repositories\Interfaces\BookingInterface;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdatePendingBookings extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-pending-bookings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update pending bookings older than 24 hours to accepted';
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(
        private BookingInterface $bookingRepository,
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get the current date and time minus 24 hours
        $threshold = Carbon::now()->subHours(24);
        $bookingData =   $this->bookingRepository->getLastPendingBookings($threshold);
        foreach ($bookingData as $booking) {
            $vehicleType = $booking->vehicleType;
            $totalLuggage = $booking->total_luggage ?? null;
            $totalPax = $booking->total_pax ?? null;
            if (($totalLuggage === null || $totalLuggage < $vehicleType->total_luggage)  &&  ($totalPax === null || $totalPax < $vehicleType->total_pax)) {
                $booking->status = Booking::ACCEPTED;
                $booking->save();
            }
        }
        $this->info('Pending bookings older than 24 hours have been updated to accepted.');
    }
}
