<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Repositories\Interfaces\BookingInterface;
use App\Services\ConvertFileService;
use App\Services\ExportService;
use App\Services\TelegramService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class SendDriverSchedule extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-driver-schedule';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Driver Schedule everday at 6 pm';
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(
        private BookingInterface $bookingRepository,
        private ExportService $exportService,
        private TelegramService $telegramService,
        private ConvertFileService $convertFileService,
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $date = Carbon::now();
        // Calculate the start and end dates and times
        $startDate = $date->copy()->addDay()->format('Y-m-d');
        $startTime = '04:00:00';
        $endDate = $date->copy()->addDays(2)->format('Y-m-d'); // Adding 2 days for the day after tomorrow
        $endTime = '04:00:00';
        $bookingData =   $this->bookingRepository->getNextDayBookings($startDate, $startTime, $endDate, $endTime);
        if ($bookingData->isNotEmpty()) {
            $data = [
                "title" => "Schedule " . $startDate,
                "driversBooking" => $bookingData,
                "isDisplayContact" => "true",
            ];
            $fileName = "schedule.pdf";
            $filePath = 'exports/pdf/' . $fileName;
            $this->exportService->setPath($filePath);
            $this->exportService->exportToPDF('admin.driver-schedule.export', $data);
            $message = "This is your schedule for the date of " . $startDate . " Please find attached document.";
            $chatId = config('app.telegram_group_chat_id');
            $pdfPath = Storage::disk('public')->path($filePath);
            $imagePath = $this->convertFileService->convert($pdfPath);
            $this->telegramService->sendMessage($chatId, $message, $imagePath);
            $fullPathToJpg = str_replace('.pdf', '.jpg', $pdfPath);
            unlink($fullPathToJpg);
            $driverBookingData =  $bookingData->groupBy('driver_id');
            foreach ($driverBookingData as $data) {
                $driverChatId = $data[0]->driver->chat_id ?? null;
                $drivername =  $data[0]->driver->name ?? null;
                if ($driverChatId &&  $drivername) {
                    $data = [
                        "title" => "Schedule " . $startDate,
                        "driversBooking" => $data,
                        "isDisplayContact" => "true",
                    ];
                    $fileName ="schedule.pdf";
                    $filePath = 'exports/pdf/' . $fileName;
                    $this->exportService->setPath($filePath);
                    $this->exportService->exportToPDF('admin.driver-schedule.export', $data);
                    $message = "Hi " .  $drivername . ",\nThis is your schedule for the date of " . $startDate . " Please find attached document.";
                    $pdfPath = Storage::disk('public')->path($filePath);
                    $imagePath = $this->convertFileService->convert($pdfPath);
                    $this->telegramService->sendMessage($driverChatId, $message, $imagePath);
                    $fullPathToJpg = str_replace('.pdf', '.jpg', $pdfPath);
                    unlink($fullPathToJpg);
                }
            }

            $this->info('Sent Driver Schedule Every Day 6PM.');
        } else {
            $this->info('No Schedule found.');
        }
    }
}
