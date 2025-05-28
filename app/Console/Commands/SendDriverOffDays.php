<?php

namespace App\Console\Commands;

use App\CustomHelper;
use App\Repositories\Interfaces\DriverOffDayInterface;
use App\Services\TelegramService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendDriverOffDays extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-driver-off-days';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Driver leave everday at 6 pm';
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(
        private DriverOffDayInterface $driverOffDayRepository,
        private TelegramService $telegramService,
        private CustomHelper $helper,
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $date = Carbon::now()->addDay()->format('Y-m-d');

        $results = $this->driverOffDayRepository->getDiverLeavesByDate($date);
        if ($results->isNotEmpty()) {
            foreach ($results as $leaveData) {
                $offDate = $leaveData->off_date ?? null;
                $drivername = $leaveData->driver->name ?? null;
                $driverChatId = $leaveData->driver->chat_id ?? null;
                if ($drivername && $driverChatId && $offDate) {                   
                    $offDate =   $this->helper->parseDateTime($offDate, 'd M, Y');
                    $message = "Hi " .  $drivername . ",\nYou are on off tomorrow " . $offDate;
                    $this->telegramService->sendMessage($driverChatId, $message);
                }
            }
            $this->info('Sent Driver Leave Every Day 6PM.');
        } else {
            $this->info('No data found.');
        }
    }
}
