<?php

namespace App\Services;

use App\CustomHelper;
use App\Models\Booking;
use App\Models\Driver;
use App\Models\Location;
use App\Models\ServiceType;
use App\Models\User;
use App\Models\Client;
use App\Models\UserType;
use App\Models\Hotel;
use App\Models\Vehicle;
use App\Models\Events;
use App\Models\VehicleClass;
use App\Repositories\Interfaces\BookingLogInterface;
use App\Repositories\Interfaces\UserInterface;
use Carbon\Carbon;

/**
 *
 * BookingLogService class
 * 
 */
class BookingLogService
{
    /**
     * ClientService constructor.
     *
 
     */
    public function __construct(
        private CustomHelper $helper,
        private BookingLogInterface $bookingLogRepository,
        private UserInterface $userRepository,
        private NotificationService $notificationService,
        private TelegramService $telegramService,
    ) {
    }

    public function addLogMessages(array $requestData, Booking $booking, User $loggedUser, array $linkedClients = null, array $additionalStopsLogs = null)
    {
        try {
            $logMessages = [];
            // Loop through each field in the request data and log changes
            foreach ($requestData as $field => $newValue) {
                if (array_key_exists($field, $booking->getAttributes())) {
                    $oldValue = $booking->{$field};
                    if ($oldValue != $newValue) {
                        switch ($field) {
                            case "client_id":
                                if ($oldValue === null) {
                                    $name = Client::find($newValue)->hotel->name;
                                    $logMessages[] = "Added Corporate: {$name}";
                                } else {
                                    $oldName = Client::find($oldValue)->hotel->name;
                                    $newName = Client::find($newValue)->hotel->name;
                                    $logMessages[] = "Changed Corporate from {$oldName} to {$newName}";
                                }
                                break;
                            case "service_type_id":
                                if ($oldValue === null) {
                                    $name = ServiceType::find($newValue)->name;
                                    $logMessages[] = "Added service type: {$name}";
                                } else {
                                    $oldName = ServiceType::find($oldValue)->name;
                                    $newName = ServiceType::find($newValue)->name;
                                    $logMessages[] = "Changed Service Type from {$oldName} to {$newName}";
                                }
                                break;
                            case "pick_up_location_id":
                                if ($oldValue === null) {
                                    $name = Location::find($newValue)->name;
                                    $logMessages[] = "Added pickup location: {$name}";
                                } else {
                                    $oldName = Location::find($oldValue)->name;
                                    $newName = Location::find($newValue)->name;
                                    $logMessages[] = "Changed pickup location from {$oldName} to {$newName}";
                                }
                                break;
                            case "drop_off_location_id":
                                if ($oldValue === null) {
                                    $name = Location::find($newValue)->name;
                                    $logMessages[] = "Added drop off location: {$name}";
                                } else {
                                    $oldName = Location::find($oldValue)->name;
                                    $newName = Location::find($newValue)->name;
                                    $logMessages[] = "Changed drop off location from {$oldName} to {$newName}";
                                }
                                break;
                            case "driver_id":
                                if ($oldValue === null) {
                                    $name = Driver::find($newValue)->name;
                                    $logMessages[] = "Added driver: {$name}";
                                } else {
                                    $oldName = Driver::find($oldValue)->name;
                                    if(!empty($newValue))
                                    {
                                        $newName = Driver::find($newValue)->name;
                                        $logMessages[] = "Changed driver from {$oldName} to {$newName}";
                                    }else{
                                        $logMessages[] = "Removed driver {$oldName}";
                                    }
                                }
                                break;
                            case "vehicle_id":
                                if ($oldValue === null) {
                                    $name = Vehicle::find($newValue)->vehicle_number ?? null;
                                    $logMessages[] = "Added vehicle: {$name}";
                                } else {
                                    $oldName = Vehicle::find($oldValue)->vehicle_number ?? null;
                                    $newName = Vehicle::find($newValue)->vehicle_number ?? null;
                                    $logMessages[] = "Changed vehicle from {$oldName} to {$newName}";
                                }
                                break;
                            case "vehicle_type_id":
                                if ($oldValue === null) {
                                    $name = VehicleClass::find($newValue)->name;
                                    $logMessages[] = "Added vehicle type: {$name}";
                                } else {
                                    $oldName = VehicleClass::find($oldValue)->name;
                                    $newName = VehicleClass::find($newValue)->name;
                                    $logMessages[] = "Changed vehicle type from {$oldName} to {$newName}";
                                }
                                break;
                            case "pick_up_location":
                                if ($oldValue === null) {
                                    $logMessages[] = "Added pickup location: {$newValue}";
                                } else {
                                    $logMessages[] = "Changed pickup location from {$oldValue} to {$newValue}";
                                }
                                break;
                            case "drop_of_location":
                                if ($oldValue === null) {
                                    $logMessages[] = "Added drop off location: {$newValue}";
                                } else {
                                    $logMessages[] = "Changed drop off location from {$oldValue} to {$newValue}";
                                }
                                break;
                            case "pickup_date":
                                if ($oldValue === null) {
                                    $logMessages[] = "Added pickup date: {$newValue}";
                                } else {
                                    $logMessages[] = "Changed pickup date from {$this->helper->formatDate($oldValue)} to {$this->helper->formatDate($newValue)}";
                                }
                                break;
                            case "pickup_time":
                                if ($oldValue === null) {
                                    $logMessages[] = "Added pickup time: {$newValue}";
                                } else {
                                    $logMessages[] = "Changed pickup time from {$this->helper->formatTime($oldValue)} to {$this->helper->formatTime($newValue)}";
                                }
                                break;
                            case "departure_time":
                                if ($oldValue === null) {
                                    $logMessages[] = "Added departure time: {$newValue}";
                                } else {
                                    $logMessages[] = "Changed departure time from {$this->helper->formatDateTime($oldValue)} to {$this->helper->formatDateTime($newValue)}";
                                }
                                break;
                            case "flight_detail":
                                if ($oldValue === null) {
                                    $logMessages[] = "Added flight detail: {$newValue}";
                                } else {
                                    $logMessages[] = "Changed flight detail from {$oldValue} to {$newValue}";
                                }
                                break;
                            case "no_of_hours":
                                if ($oldValue === null) {
                                    $logMessages[] = "Added no. of hours: {$newValue}";
                                } else {
                                    $logMessages[] = "Changed no. of hours from {$oldValue} to {$newValue}";
                                }
                                break;
                            case "country_code":
                                if ($oldValue === null) {
                                    $logMessages[] = "Added country code: {$newValue}";
                                } else {
                                    $logMessages[] = "Changed country code from {$oldValue} to {$newValue}";
                                }
                                break;
                            case "phone":
                                if ($oldValue === null) {
                                    $logMessages[] = "Added phone: {$newValue}";
                                } else {
                                    $logMessages[] = "Changed phone from {$oldValue} to {$newValue}";
                                }
                                break;
                            case "total_pax":
                                if ($oldValue === null) {
                                    $logMessages[] = "Added total passenger: {$newValue}";
                                } else {
                                    $logMessages[] = "Changed total passenger from {$oldValue} to {$newValue}";
                                }
                                break;
                            case "total_luggage":
                                if ($oldValue === null) {
                                    $logMessages[] = "Added total luggage: {$newValue}";
                                } else {
                                    $logMessages[] = "Changed total luggage from {$oldValue} to {$newValue}";
                                }
                                break;
                            case "client_instructions":
                                if ($oldValue === null) {
                                    $logMessages[] = "Added client instructions: {$newValue}";
                                } else {
                                    $logMessages[] = "Changed client instructions from {$oldValue} to {$newValue}";
                                }
                                break;
                            case "guest_name":
                                if ($oldValue === null) {
                                    $logMessages[] = "Added guest name: {$newValue}";
                                } else {
                                    $logMessages[] = "Changed guest name from {$oldValue} to {$newValue}";
                                }
                                break;
                            case "is_cross_border":
                                $yesNoNew = $newValue  === 1 ? "yes" : "no";
                                $yesNoOld = $oldValue  === 1 ? "yes" : "no";
                                if ($oldValue === null) {
                                    $logMessages[] = "Added cross border service: {$yesNoNew}";
                                } else {
                                    $logMessages[] = "Changed cross border service from {$yesNoOld} to {$yesNoNew}";
                                }
                                break;
                            case "is_driver_acknowledge":
                                $yesNoNew = $newValue  === 1 ? "yes" : "no";
                                $yesNoOld = $oldValue  === 1 ? "yes" : "no";
                                if ($oldValue === null) {
                                    $logMessages[] = "Added driver acknowledge: {$yesNoNew}";
                                } else {
                                    $logMessages[] = "Changed driver acknowledge from {$yesNoOld} to {$yesNoNew}";
                                }
                                break;
                            case "is_driver_notified":
                                $yesNoNew = $newValue  === 1 ? "yes" : "no";
                                $yesNoOld = $oldValue  === 1 ? "yes" : "no";
                                if ($oldValue === null) {
                                    $logMessages[] = "Added driver notified: {$yesNoNew}";
                                } else {
                                    $logMessages[] = "Changed driver notified from {$yesNoOld} to {$yesNoNew}";
                                }
                                break;
                            case "attachment":
                                if ($oldValue === null) {
                                    $logMessages[] = "Added attachment: {$newValue}";
                                } else {
                                    $logMessages[] = "Changed attachment from {$oldValue} to {$newValue}";
                                }
                                break;
                            case "status":
                                $statusNew = ucfirst(strtolower($newValue));
                                if ($oldValue === null) {
                                    $logMessages[] = "Added status: {$statusNew}";
                                } else {
                                    $statusOld = ucfirst(strtolower($oldValue));
                                    $logMessages[] = "Changed status from {$statusOld} to {$statusNew}";
                                }
                                break;
                            case "trip_ended":
                                if ($oldValue === null) {
                                    $logMessages[] = "Added trip ended: {$newValue}";
                                } else {
                                    $logMessages[] = "Changed trip ended from {$this->helper->formatDateTime($oldValue)} to {$this->helper->formatDateTime($newValue)}";
                                }
                                break;
                            case "driver_contact":
                                if ($oldValue === null) {
                                    $logMessages[] = "Added driver contact: {$newValue}";
                                } else {
                                    $logMessages[] = "Changed driver contact from {$oldValue} to {$newValue}";
                                }
                                break;
                            case "driver_remark":
                                if ($oldValue === null) {
                                    $logMessages[] = "Added instructions: {$newValue}";
                                } else {
                                    $logMessages[] = "Changed instructions from {$oldValue} to {$newValue}";
                                }
                                break;
                            case "event_id":
                                if ($oldValue === null) {
                                    $name = Events::find($newValue)->name;
                                    $logMessages[] = "Added event: {$name}";
                                } else {
                                    $oldName = Events::find($oldValue)->name;
                                    $newName = Events::find($newValue)->name;
                                    $logMessages[] = "Changed event from {$oldName} to {$newName}";
                                }
                                break;
                            // case "additional_stops":
                            //     if ($oldValue === null) {
                            //         $logMessages[] = "Added additional stops: {$newValue}";
                            //     } else {
                            //         $logMessages[] = "Changed additional stops from {$oldValue} to {$newValue}";
                            //     }
                            //     break;
                            case "child_seat_required":
                                if ($oldValue === null) {
                                    $logMessages[] = "Added child seats: {$newValue}";
                                } else {
                                    $logMessages[] = "Changed child seats from {$oldValue} to {$newValue}";
                                }
                                break;
                            case "no_of_seats_required":
                                if ($oldValue === null) {
                                    $logMessages[] = "Added no of child seats: {$newValue}";
                                } else {
                                    $logMessages[] = "Changed no of child seats from {$oldValue} to {$newValue}";
                                }
                                break;
                            case "client_asked_to_cancel":
                                if($oldValue == 'no' && $newValue == 'yes')
                                {
                                    $logMessages[] = "Requested For Cancel.";

                                    $this->sendEmailToAdminForCancel($booking, $loggedUser);
                                }
                                break;
                            case "linked_clients":
                                if ($oldValue === null || $oldValue === '') {
                                    $all_user_ids = explode(',', $newValue);
    
                                    $names = User::whereIn('id', $all_user_ids)->get(['first_name', 'last_name'])->map(fn($user) => "{$user->first_name} {$user->last_name}")->implode(', ');
    
                                    $logMessages[] = "Added linked clients: {$names}";
                                } else {
    
                                    $all_old_user_ids = explode(',', $oldValue);
                                    $old_names = User::whereIn('id', $all_old_user_ids)->get(['first_name', 'last_name'])->map(fn($user) => "{$user->first_name} {$user->last_name}")->implode(', ');
    
                                    $all_new_user_ids = explode(',', $newValue);
                                    $new_names = User::whereIn('id', $all_new_user_ids)->get(['first_name', 'last_name'])->map(fn($user) => "{$user->first_name} {$user->last_name}")->implode(', ');
    
                                    if($new_names == '')
                                    {
                                        $logMessages[] = "Removed linked clients : {$old_names}";
                                    }else{
                                        $logMessages[] = "Changed linked clients from {$old_names} to {$new_names}";
                                    }
                                }
                                break;
                            case "meet_and_greet":
                                if (strtoupper($oldValue) === 'NO') {
                                    if(strtoupper($newValue) !== 'NO')
                                    {
                                        $logMessages[] = "Added Meet and Greet";
                                    }
                                } else {
                                    if(strtoupper($newValue) !== 'YES')
                                    {
                                        $logMessages[] = "Removed Meet and Greet";
                                    }
                                }
                                break;
                            case "latest_comment":
                                if(!empty($newValue))
                                {
                                    $logMessages[] = "Added a comment : {$newValue}";
                                }
                                break;
                            case "latest_admin_comment":
                                if(!empty($newValue))
                                {
                                    $logMessages[] = "Added a comment : {$newValue}";
                                }
                                break;
                            case "additional_stops_required":
                                if($oldValue == 'yes')
                                {
                                    $logMessages[] = "Additional stops marked not-required.";
                                }else{
                                    $logMessages[] = "Additional stops marked required.";
                                }
                                break;
                            default:
                                break;
                        }
                    }
                }
            }

            if(!empty($additionalStopsLogs) && $additionalStopsLogs !== null)
            {
                $logMessages = array_merge($logMessages, $additionalStopsLogs);
            }

            $userType =  $loggedUser->userType->type ?? null;
            if ($userType === null || $userType === UserType::ADMIN) {
                $notifyUsers = collect([$this->userRepository->getUserById($booking->created_by_id)]);
            } else {
                $notifyUsers =  $this->userRepository->getAdmins();
            }
            $loggedUserFullName = $this->helper->getFullName($loggedUser->first_name, $loggedUser->last_name);
            // if ($userType === null || $userType === UserType::ADMIN) {
            //     $this->sendEmailToClientAdmin($loggedUserFullName, $logMessages, $booking);
            // }
    
            // if ($userType === null || $userType === UserType::ADMIN) {
            // }
            $this->sendEmailToCreator($loggedUserFullName, $logMessages, $booking);
            $this->sendEmailToPOCandHeadOffice($loggedUserFullName, $logMessages, $booking);
    
            if(!empty($linkedClients))
            {
                $updateDelinkClients = $this->updateBookingNotification($loggedUser, $logMessages, $booking, $linkedClients);
            }
            
            if ($userType === UserType::CLIENT) {
                $this->sendTelegramNotificationToOpsTeam($loggedUserFullName, $logMessages, $booking);
            }
            $this->sendTelegramNotificationToDriver($loggedUserFullName, $logMessages, $booking);
            foreach ($logMessages as $message) {
                $logData = ["message" => $message, "booking_id" => $booking->id, "user_id" => $loggedUser->id];
                $this->bookingLogRepository->addLogs($logData);
    
                $notificationType = 'booking';
                $subject = __("message.booking_notification_subject");
                $template = 'emails.send_notification';
                $message = $loggedUserFullName . " " . $message;
                $notificationData = [
                    'booking' => $booking,
                    'message' => $message,
                    'from_user_name' => $loggedUserFullName,
                ];
                $this->notificationService->sendNotification($notificationData, $loggedUser, $notificationType, $subject, $notifyUsers, $message, $template);
            }
            return true;
        } catch (\Exception $e) {
            return;
        }
    }

    private function sendTelegramNotificationToDriver($loggedUserFullName, $logs, $booking)
    {
        try {
            $chatId = $booking->driver->chat_id ?? null;
            $driverName = $booking->driver->name ?? null;
            $driverType = $booking->driver->driver_type ?? null;
            $logCount = count($logs);
            $message = null;

            if ($logCount > 0 && $chatId) {
                $message = "Hi " . $driverName . ",\n" . $loggedUserFullName . " has made some changes for booking #" . $booking->id . ". Below are the details:\n";
                $validLogCount = 0;
                foreach ($logs as $index => $log) {
                    $isPhone = str_contains(strtolower($log), 'phone');
                    if ($driverType !== "OUTSOURCE" || !$isPhone) {
                        $message .= $log . ".";
                        $validLogCount++;
                        if ($index < $logCount - 1) {
                            $message .= "\n";
                        }
                    }
                }
                if ($validLogCount == 0) {
                    $message = null; // No valid logs, reset the message
                }
            }
            if ($message) {
                $isDriverChanged = str_contains(strtolower($message), 'changed driver');
                $isDriverAdded = str_contains(strtolower($message), 'added driver');
                if (!$isDriverChanged && !$isDriverAdded) {
                    $this->telegramService->sendMessage($chatId, $message);
                }
            }
        } catch (\Exception $e) {
            return;
        }
    }

    private function sendTelegramNotificationToOpsTeam($loggedUserFullName, $logs, $booking)
    {
        try {
            $chatId = config('app.telegram_group_chat_id');
            $logCount = count($logs);
            if ($logCount > 0 && $chatId) {
                $message = "Hi Team,\n" . $loggedUserFullName . " has made some changes for booking #" . $booking->id . ". Below are the details:\n";
                foreach ($logs as $index => $log) {
                    $message .= $log . ".";
                    if ($index < $logCount - 1) {
                        $message .= "\n";
                    }
                }
                $this->telegramService->sendMessage($chatId, $message);
            }
        } catch (\Exception $e) {
            return;
        }
    }

    private function sendEmailToClientAdmin($loggedUserFullName, $logs, $booking)
    {
        try {
            $filterStr = ['added driver acknowledge', 'changed driver acknowledge', 'changed driver notified', 'added driver notified'];
            $filteredLogs = array_filter($logs, function($log) use ($filterStr) {
                foreach ($filterStr as $filter) {
                    if (strpos(strtolower($log), $filter) !== false) {
                        return false; // Exclude log if any filter string is found
                    }
                }
                return true; // Include log if none of the filter strings are found
            });
            $hotelId =  $booking->client->hotel->id ?? null;
            if ($hotelId && count($filteredLogs) > 0) {
                $hotelAdmins =    $this->userRepository->getHotelAdminByHotelId($hotelId);
                $bookingId = $booking->id;
                $subject = "Updated Booking #" . $bookingId;
                foreach ($hotelAdmins as $hotelAdmin) {
                    $mailData   = [
                        'subject' =>  $subject,
                        'template' =>  'send-email',
                        'name'    => $this->helper->getFullName($hotelAdmin->first_name, $hotelAdmin->last_name),
                        'logs' => $filteredLogs,
                        'changedBy' => $loggedUserFullName,
                        'bookingId' => $bookingId,
                    ];
             
                    // if (!$isDriverChanged && !$isDriverAdded) {
                    $this->helper->sendEmail($hotelAdmin->email, $mailData);
                    // }
                }
            }
        } catch (\Exception $e) {
            return;
        }
    }

    private function sendEmailToCreator($loggedUserFullName, $logs, $booking)
    {
        try {
            // first check if creator is not super admin
            $createdBy = $booking->created_by_id;

            $creatorDetails = $this->userRepository->getUserById($createdBy);

            $filterStr = ['added driver acknowledge', 'changed driver acknowledge', 'changed driver notified', 'added driver notified'];
            $filteredLogs = array_filter($logs, function($log) use ($filterStr) {
                foreach ($filterStr as $filter) {
                    if (strpos(strtolower($log), $filter) !== false) {
                        return false; // Exclude log if any filter string is found
                    }
                }
                return true; // Include log if none of the filter strings are found
            });

            if($creatorDetails->email !== 'admin@yopmail.com')
            {
                $bookingId = $booking->id;
                $subject = "Updated Booking #" . $bookingId;

                $mailData   = [
                    'subject' =>  $subject,
                    'template' =>  'send-email',
                    'name'    => $this->helper->getFullName($creatorDetails->first_name, $creatorDetails->last_name),
                    'logs' => $filteredLogs,
                    'changedBy' => $loggedUserFullName,
                    'bookingId' => $bookingId,
                ];
         
                $this->helper->sendEmail($creatorDetails->email, $mailData);
            }
        } catch (\Exception $e) {
            return;
        }
    }

    private function sendEmailToPOCandHeadOffice($loggedUserFullName, $logs, $booking)
    {
        try {
            // first check if creator is not super admin

            $filterStr = ['added driver acknowledge', 'changed driver acknowledge', 'changed driver notified', 'added driver notified'];
            $filteredLogs = array_filter($logs, function($log) use ($filterStr) {
                foreach ($filterStr as $filter) {
                    if (strpos(strtolower($log), $filter) !== false) {
                        return false; // Exclude log if any filter string is found
                    }
                }
                return true; // Include log if none of the filter strings are found
            });

            $bookingId = $booking->id;
            $subject = "Updated Booking #" . $bookingId;

            // to poc of logged in user's hotel
            $hotelId = $loggedUser->client->hotel_id;

            // Step 1: Get all client IDs from hotel_poc table
            $allPocClientIds = DB::table('hotels_poc')
                ->where('hotel_id', $hotelId)
                ->pluck('client_id')
                ->toArray();

            // Step 2: Get all user IDs from clients table using those client IDs
            $clientUserIds = DB::table('clients')
                ->whereIn('id', $allPocClientIds)
                ->pluck('user_id')
                ->toArray();

            // Step 3: Get user details from users table
            $users = DB::table('users')
                ->whereIn('id', $clientUserIds)
                ->select('first_name', 'last_name', 'email')
                ->get();

            if(!empty($users))
            {
                foreach($users as $user)
                {

                    $mailData   = [
                        'subject' =>  $subject,
                        'template' =>  'send-email',
                        'name'    => $this->helper->getFullName($user->first_name, $user->last_name),
                        'logs' => $filteredLogs,
                        'changedBy' => $loggedUserFullName,
                        'bookingId' => $bookingId,
                    ];
                    $this->helper->sendEmail($user->email, $mailData);
                }
            }


            // check if logged in user belongs to the same corporate, for whom the booking is created
            if($booking->client->hotel_id == $loggedUser->client->hotel_id)
            {
                // check if logged in user's hotel is head office
                if($loggedUser->client->hotel->is_head_office == 1 || ($loggedUser->client->hotel->is_head_office == 0 && $loggedUser->client->hotel->linked_head_office == NULL))
                {
                    
                }else
                {   
                    // to head office
                    $hotelId = $loggedUser->client->hotel->linked_head_office;

                    // Step 1: Get all client IDs from hotel_poc table
                    $allPocClientIds = DB::table('hotels_poc')
                        ->where('hotel_id', $hotelId)
                        ->pluck('client_id')
                        ->toArray();

                    // Step 2: Get all user IDs from clients table using those client IDs
                    $clientUserIds = DB::table('clients')
                        ->whereIn('id', $allPocClientIds)
                        ->pluck('user_id')
                        ->toArray();

                    // Step 3: Get user details from users table
                    $users = DB::table('users')
                        ->whereIn('id', $clientUserIds)
                        ->select('first_name', 'last_name', 'email')
                        ->get();

                    if(!empty($users))
                    {
                        foreach($users as $user)
                        {                        
                            $mailData   = [
                                'subject' =>  $subject,
                                'template' =>  'send-email',
                                'name'    => $this->helper->getFullName($user->first_name, $user->last_name),
                                'logs' => $filteredLogs,
                                'changedBy' => $loggedUserFullName,
                                'bookingId' => $bookingId,
                            ];
                        }
                    }
                }
            }else{
                // to poc of booking's hotel
                $hotelId = $booking->client->hotel_id;

                // Step 1: Get all client IDs from hotel_poc table
                $allPocClientIds = DB::table('hotels_poc')
                    ->where('hotel_id', $hotelId)
                    ->pluck('client_id')
                    ->toArray();

                // Step 2: Get all user IDs from clients table using those client IDs
                $clientUserIds = DB::table('clients')
                    ->whereIn('id', $allPocClientIds)
                    ->pluck('user_id')
                    ->toArray();

                // Step 3: Get user details from users table
                $users = DB::table('users')
                    ->whereIn('id', $clientUserIds)
                    ->select('first_name', 'last_name', 'email')
                    ->get();

                if(!empty($users))
                {
                    foreach($users as $user)
                    {
                        $mailData   = [
                            'subject' =>  $subject,
                            'template' =>  'send-email',
                            'name'    => $this->helper->getFullName($user->first_name, $user->last_name),
                            'logs' => $filteredLogs,
                            'changedBy' => $loggedUserFullName,
                            'bookingId' => $bookingId,
                        ];
                    }
                }

                // check if booking's hotel is head office
                if($booking->client->hotel->is_head_office == 1 || ($loggedUser->client->hotel->is_head_office == 0 && $loggedUser->client->hotel->linked_head_office == NULL))
                {
                    
                }else
                {   
                    // to head office
                    $hotelId = $booking->client->hotel->linked_head_office;

                    // Step 1: Get all client IDs from hotel_poc table
                    $allPocClientIds = DB::table('hotels_poc')
                        ->where('hotel_id', $hotelId)
                        ->pluck('client_id')
                        ->toArray();

                    // Step 2: Get all user IDs from clients table using those client IDs
                    $clientUserIds = DB::table('clients')
                        ->whereIn('id', $allPocClientIds)
                        ->pluck('user_id')
                        ->toArray();

                    // Step 3: Get user details from users table
                    $users = DB::table('users')
                        ->whereIn('id', $clientUserIds)
                        ->select('first_name', 'last_name', 'email')
                        ->get();

                    if(!empty($users))
                    {
                        foreach($users as $user)
                        {
                            $mailData   = [
                                'subject' =>  $subject,
                                'template' =>  'send-email',
                                'name'    => $this->helper->getFullName($user->first_name, $user->last_name),
                                'logs' => $filteredLogs,
                                'changedBy' => $loggedUserFullName,
                                'bookingId' => $bookingId,
                            ];
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            return;
        }
    }

    private function sendEmailToAdminForCancel($booking, $loggedUser)
    {
        try {
            $bookingId = $booking->id;
            $subject = "Requested For Cancel #" . $bookingId;
            $loggedUserFullName = $this->helper->getFullName($loggedUser->first_name, $loggedUser->last_name);
            $message = "Requested For Cancel For Booking Number #" . $bookingId;
    
            $mailData   = [
                'subject' =>  $subject,
                'template' =>  'requested-for-cancel-booking',
                'name'    => 'Limousine Team',
                'logs' => $message,
                'changedBy' => $loggedUserFullName . ' from ' . $loggedUser->client->hotel->name,
                'bookingId' => $bookingId,
            ];
    
            $this->helper->sendEmail('limousine@e1asia.com.sg', $mailData);
        } catch (\Exception $e) {
            return;
        }
    }

    private function updateBookingNotification($loggedUser, $logs, $booking, $deLinkClients)
    {
        try {
            $filterStr = ['added driver acknowledge', 'changed driver acknowledge', 'changed driver notified', 'added driver notified'];
            $filteredLogs = array_filter($logs, function($log) use ($filterStr) {
                foreach ($filterStr as $filter) {
                    if (strpos(strtolower($log), $filter) !== false) {
                        return false; // Exclude log if any filter string is found
                    }
                }
                return true; // Include log if none of the filter strings are found
            });
    
            $subject = "Updated Booking #" . $booking->id;
            $loggedUserFullName = $this->helper->getFullName($loggedUser->first_name, $loggedUser->last_name);
            if(!empty($deLinkClients))
            {
                foreach($deLinkClients as $deLinkClient)
                {
                    $userDetail = $this->userRepository->getUserById((int) $deLinkClient);
                    if(!empty($userDetail) && !empty($userDetail->email) && !empty($userDetail->first_name))
                    {
                        $mailData   = [
                            'subject' =>  $subject,
                            'template' =>  'send-email',
                            'name'    => $this->helper->getFullName($userDetail->first_name, $userDetail->last_name),
                            'logs' => $filteredLogs,
                            'changedBy' => $loggedUserFullName,
                            'bookingId' => $booking->id,
                        ];
                        $this->helper->sendEmail($userDetail->email, $mailData);
                    }
                }
            }
            return true;
        } catch (\Exception $e) {
            return;
        }
    }

    public function getBookingLogs(array $requestData = [])
    {
        try {
            $dateRange = $requestData['dateRange'] ?? null;
            $userId = $requestData['userId'] ?? null;
            $isNoDateRange = $requestData['isNoDateRange'] ?? false;
            $bookingId = $requestData['searchByBookingId'] ?? null;
            if (!$isNoDateRange && !$bookingId) {
                // Set default to current date if dateRange is not provided
                $startDate = Carbon::now()->startOfDay()->format('Y-m-d H:i:s');
                $endDate = Carbon::now()->endOfDay()->format('Y-m-d H:i:s');
                if ($dateRange) {
                    $dates = explode("-", $dateRange);
                    $startDate = Carbon::createFromFormat('d/m/Y', trim($dates[0]))->startOfDay()->format('Y-m-d H:i:s');
                    $endDate = Carbon::createFromFormat('d/m/Y', trim($dates[1]))->endOfDay()->format('Y-m-d H:i:s');
                }
            } else {
                $startDate = "";
                $endDate = "";
            }
            return $this->bookingLogRepository->getLogs($userId, $startDate, $endDate, $bookingId);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
