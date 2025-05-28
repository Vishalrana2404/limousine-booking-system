<?php

use App\Http\Controllers\Admin\BookingController;
use App\Http\Controllers\Admin\CitySurchargeController;
use App\Http\Controllers\Admin\BookingLogController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DriverController;
use App\Http\Controllers\Admin\DriverOffDayController;
use App\Http\Controllers\Admin\DriverScheduleController;
use App\Http\Controllers\Admin\HotelController;
use App\Http\Controllers\Admin\EventsController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\PeakPeriodController;
use App\Http\Controllers\Admin\UserTypeController;
use App\Http\Controllers\Admin\ReportsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\VehicleClassController;
use App\Http\Controllers\Admin\VehicleController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Admin\NotificationController;
use \Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Support\Facades\Artisan;

Route::get('/clear-cache', function () {
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');

    return "Cache, Config, Routes, and Views Cleared!";
});


Route::get('/link-storage', function () {
    Artisan::call('storage:link');

    return "Storage Linking Successful.";
});


Route::group(['middleware' => ['guest', 'web']], function () {
    Route::get('/', [LoginController::class, 'index'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('submit-login');
    Route::post('/register', [LoginController::class, 'register'])->name('register');
    // Password Reset Routes
    Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('forgotPassword', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('forget_password.email');
    Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');
});

Route::middleware(['Auth','SanitizeInput', AuthenticateSession::class])->group(function () {

    //Dashboard Routes
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('filterDashboardData', [DashboardController::class, 'filterDashboardData'])->name('filter-dashboard-data');
    Route::get('getUserType',  [UserTypeController::class, 'index'])->name('get-user-types');

    Route::post('checkUniqueEmail', [UserController::class, 'checkUniqueEmail'])->name('check-unique-email');
    Route::post('logout', [LoginController::class, 'loggedOut'])->name('submit-logout');

    //Settings  Route
    Route::get('settings', [SettingController::class, 'index'])->name('settings');
    Route::post('changeProfileImage', [SettingController::class, 'changeProfileImage'])->name('change-profile-image');
    Route::post('updateProfile', [SettingController::class, 'updateProfile'])->name('update-profile');
    Route::post('checkCurrentPassword', [SettingController::class, 'checkCurrentPassword'])->name('current-password-check');
    Route::post('changeCurrentPassword', [SettingController::class, 'changeCurrentPassword'])->name('change-current-password');
    Route::post('removeProfileImage', [SettingController::class, 'removeProfileImage'])->name('remove-profile-image');
    //Notifactions Route
    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications');
    Route::get('filterNotification', [NotificationController::class, 'filterNotification'])->name('filter-notification');
    Route::post('notification/mark/read', [NotificationController::class, 'markAsRead'])->name('markAsRead');
    Route::post('bookings/save', [BookingController::class, 'save'])->name('save-booking');
    Route::post('bookings/add-multiple-booking', [BookingController::class, 'saveMultipleBooking'])->name('save-multiple-booking');
    Route::get('getCorporateFareCharges', [BookingController::class, 'getCorporateFareCharges'])->name('get-corporate-fare-charges');

    Route::middleware(['Admin'])->group(function () {
        //Hotel Management Routes
        Route::get('hotels', [HotelController::class, 'index'])->name('hotels');
        Route::get('hotels/create',  [HotelController::class, 'create'])->name('create-hotel');
        Route::post('hotels/save', [HotelController::class, 'save'])->name('save-hotel');
        Route::get('hotels/{hotel}/edit', [HotelController::class, 'edit'])->name('edit-hotel');
        Route::post('hotels/{hotel}', [HotelController::class, 'update'])->name('update-hotel');
        Route::post('deleteHotels', [HotelController::class, 'delete'])->name('delete-hotels');
        Route::post('updateBulkHotelStatus', [HotelController::class, 'updateBulkStatus'])->name('update-bulk-hotel-status');
        Route::get('hotels/filterHotels', [HotelController::class, 'filterHotels'])->name('filter-hotels');

        //Events Management Routes
        Route::get('events', [EventsController::class, 'index'])->name('events');
        Route::get('events/create',  [EventsController::class, 'create'])->name('create-event');
        Route::post('events/save', [EventsController::class, 'save'])->name('save-event');
        Route::get('events/{event}/edit', [EventsController::class, 'edit'])->name('edit-event');
        Route::post('events/{event}', [EventsController::class, 'update'])->name('update-event');
        Route::post('deleteEvents', [EventsController::class, 'delete'])->name('delete-events');
        Route::post('updateBulkEventStatus', [EventsController::class, 'updateBulkStatus'])->name('update-bulk-event-status');
        Route::get('events/filterEvents', [EventsController::class, 'filterEvents'])->name('filter-events');
        Route::get('events/filterEventsForClient', [EventsController::class, 'filterEventsForClient'])->name('filter-events-for-client');
        Route::get('getHotelEvents', [EventsController::class, 'getHotelEvents'])->name('get-hotel-events');
        Route::post('createEventFromBooking',  [EventsController::class, 'createEventFromBooking'])->name('create-event-by-ajax');


        //users Routes
        Route::get('users', [UserController::class, 'index'])->name('users');
        Route::get('users/create',  [UserController::class, 'create'])->name('create-user');
        Route::post('users/save', [UserController::class, 'save'])->name('save-user');

        Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('edit-user');
        Route::post('users/{user}', [UserController::class, 'update'])->name('update-user');



        Route::post('deleteUsers', [UserController::class, 'delete'])->name('delete-users');
        Route::post('updateBulkUserStatus', [UserController::class, 'updateBulkStatus'])->name('update-bulk-user-status');
        Route::get('users/filterUsers', [UserController::class, 'filterUsers'])->name('filter-users');

        // Vehicle Class Routes
        Route::get('vehicle-class', [VehicleClassController::class, 'index'])->name('vehicle-class');

        Route::any('vehicle-class/create', [VehicleClassController::class, 'create'])->name('add-vehicle-class');
        Route::post('vehicle-class/save', [VehicleClassController::class, 'save'])->name('save-vehicle-class');

        Route::any('vehicle-class/{vehicleClass}/edit', [VehicleClassController::class, 'edit'])->name('edit-vehicle-class');
        Route::post('vehicle-class/{vehicleClass}', [VehicleClassController::class, 'update'])->name('update-vehicle-class');

        Route::post('deleteVehicleClass', [VehicleClassController::class, 'delete'])->name('delete-vehicle-class');

        Route::any('vehicle-class/{vehicleClass}/view', [VehicleClassController::class, 'view'])->name('view-vehicle-class');

        Route::post('updateBulkVehicleStatus', [VehicleClassController::class, 'updateBulkStatus'])->name('update-bulk-vehicle-class-status');

        Route::get('vehicle-class/filterVehicleClass', [VehicleClassController::class, 'filterVehicleClass'])->name('filter-vehicle-class');

        // Vehicle Routes
        Route::get('vehicles', [VehicleController::class, 'index'])->name('vehicles');

        Route::any('vehicles/create', [VehicleController::class, 'create'])->name('add-vehicle');
        Route::post('vehicles/save', [VehicleController::class, 'save'])->name('save-vehicle');

        Route::any('vehicles/{vehicle}/edit', [VehicleController::class, 'edit'])->name('edit-vehicle');
        Route::post('vehicles/{vehicle}', [VehicleController::class, 'update'])->name('update-vehicle');

        Route::post('deleteVehicle', [VehicleController::class, 'delete'])->name('delete-vehicle');

        Route::any('vehicles/{vehicle}/view', [VehicleController::class, 'view'])->name('view-vehicle');

        Route::post('updateBulkStatus', [VehicleController::class, 'updateBulkStatus'])->name('update-bulk-vehicle-status');

        Route::get('vehicles/filterVehicle', [VehicleController::class, 'filterVehicle'])->name('filter-vehicle');

        Route::post('checkUniqueVehicleNumber', [VehicleController::class, 'checkUniqueVehicleNumber'])->name('check-unique-vehicle-number');

        //Client routes
        Route::get('clients', [ClientController::class, 'index'])->name('clients');
        Route::get('clients/create',  [ClientController::class, 'create'])->name('client-create');
        Route::post('clients/clients/save', [ClientController::class, 'save'])->name('client-save');
        Route::get('clients/filterClients', [ClientController::class, 'filterClients'])->name('filter-clients');
        Route::post('clients/updateBulkStatus', [ClientController::class, 'updateBulkStatus'])->name('update-bulk-client-status');
        Route::post('clients/deleteClient', [ClientController::class, 'delete'])->name('delete-client');
        Route::get('clients/{client}/edit', [ClientController::class, 'edit'])->name('edit-client');
        Route::post('clients/{client}', [ClientController::class, 'update'])->name('client-update');
        Route::post('clients/password/reset', [ResetPasswordController::class, 'changePassword'])->name('password-change');




        //drivers routes
        Route::get('drivers', [DriverController::class, 'index'])->name('drivers');

        Route::get('drivers/create',  [DriverController::class, 'create'])->name('create-driver');
        Route::post('drivers/save', [DriverController::class, 'save'])->name('save-driver');
        Route::get('drivers/{driver}/edit', [DriverController::class, 'edit'])->name('edit-driver');
        Route::post('drivers/{driver}', [DriverController::class, 'update'])->name('update-driver');
        Route::post('deleteDrivers', [DriverController::class, 'delete'])->name('delete-drivers');
        Route::get('drivers/filterDrivers', [DriverController::class, 'filterDrivers'])->name('filter-drivers');

        // Driver Off Day routes
        Route::get('drivers-off-day', [DriverOffDayController::class, 'index'])->name('drivers-off-day');
        Route::post('/save-driver-off-days', [DriverOffDayController::class, 'saveDriverOffDays'])->name('save-driver-off-days');
        Route::get('/get-driver-off-days', [DriverOffDayController::class, 'getDriverOffDays'])->name('get-driver-off-days');


        //Bookings routes

        Route::get('bookings', [BookingController::class, 'index'])->name('bookings');
        Route::get('filterBookings', [BookingController::class, 'filterBookings'])->name('filter-bookings');
        Route::get('bookings/{booking}/edit', [BookingController::class, 'edit'])->name('edit-booking');
        Route::post('bookings/{booking}', [BookingController::class, 'update'])->name('update-booking');
        Route::post('updateDispatch', [BookingController::class, 'updateDispatch'])->name('update-dispatch');
        Route::post('updateInlineBooking', [BookingController::class, 'updateInline'])->name('update-inline-booking');
        Route::get('bookings/create',  [BookingController::class, 'create'])->name('create-booking');
        Route::post('deleteBookings', [BookingController::class, 'delete'])->name('delete-bookings');
        Route::post('cancelBooking', [BookingController::class, 'cancelBooking'])->name('cancel-booking');
        Route::get('bookings-archives', [BookingController::class, 'bookingsArchives'])->name('bookings-archives');
        Route::get('filterBookingsArchives', [BookingController::class, 'filterBookingsArchives'])->name('filter-bookings-archives');
        Route::get('bookings/{booking}/restore', [BookingController::class, 'restoreBooking'])->name('restore-booking');
        Route::post('permanentDeleteBookings', [BookingController::class, 'permanentDeleteBookings'])->name('permanent-delete-bookings');
        

        // Reports Routes
        Route::get('reports', [ReportsController::class, 'index'])->name('reports');
        Route::get('/filter-reports', [ReportsController::class, 'filterReports'])->name('filter-reports');
        Route::post('/export-reports', [ReportsController::class, 'export'])->name('export-reports');
        Route::get('/get-clients-by-corporate-id', [ClientController::class, 'getClientsByCorporateId'])->name('get-clients-by-corporate-id');

        //Logs Routes
        Route::get('logs', [BookingLogController::class, 'index'])->name('logs');
        Route::get('filterLogs', [BookingLogController::class, 'filterLogs'])->name('filter-logs');

      
      
        // Peak Period
        Route::get('peak-period', [PeakPeriodController::class, 'index'])->name('peak-period');
        Route::any('peak-period/create', [PeakPeriodController::class, 'create'])->name('add-peak-period');
        Route::post('peak-period/save', [PeakPeriodController::class, 'save'])->name('save-peak-period');
        Route::any('peak-period/{peakPeriod}/edit', [PeakPeriodController::class, 'edit'])->name('edit-peak-period');
        Route::post('peak-period/{peakPeriod}', [PeakPeriodController::class, 'update'])->name('update-peak-period');
        Route::get('peak-period/filterPeakPeriod', [PeakPeriodController::class, 'filterPeakPeriod'])->name('filter-peak-period');
        Route::post('deletePeakPeriod', [PeakPeriodController::class, 'delete'])->name('delete-peak-period');
        Route::post('updateBulkPeakPeriod', [PeakPeriodController::class, 'updateBulkStatus'])->name('update-bulk-peak-period-status');

        // Outside City Surcharge
        Route::get('/city-surcharge', [CitySurchargeController::class, 'index'])->name('city-surcharge');
        Route::post('/save-city-surcharge', [CitySurchargeController::class, 'save'])->name('save-city-surcharge');
        Route::post('/delete-city-surcharge', [CitySurchargeController::class, 'delete'])->name('delete-city-surcharge');
        // Drivers Schedule
        Route::get('/driver-schedule', [DriverScheduleController::class, 'index'])->name('driver-schedule');
        Route::get('/filter-drivers-bookings', [DriverScheduleController::class, 'filterDriversBookings'])->name('filter-drivers-bookings');

        Route::post('/export', [DriverScheduleController::class, 'export'])->name('export');
        Route::post('/sendDriverSchedule', [DriverScheduleController::class, 'sendDriverSchedule'])->name('send-driver-schedule');

    });



    Route::middleware(['Staff'])->group(function () {

        // Vehicle Class Routes
        Route::get('vehicle-class', [VehicleClassController::class, 'index'])->name('vehicle-class');

        Route::any('vehicle-class/create', [VehicleClassController::class, 'create'])->name('add-vehicle-class');
        Route::post('vehicle-class/save', [VehicleClassController::class, 'save'])->name('save-vehicle-class');

        Route::any('vehicle-class/{vehicleClass}/edit', [VehicleClassController::class, 'edit'])->name('edit-vehicle-class');
        Route::post('vehicle-class/{vehicleClass}', [VehicleClassController::class, 'update'])->name('update-vehicle-class');

        Route::post('deleteVehicleClass', [VehicleClassController::class, 'delete'])->name('delete-vehicle-class');

        Route::any('vehicle-class/{vehicleClass}/view', [VehicleClassController::class, 'view'])->name('view-vehicle-class');

        Route::post('updateBulkVehicleStatus', [VehicleClassController::class, 'updateBulkStatus'])->name('update-bulk-vehicle-class-status');

        Route::get('vehicle-class/filterVehicleClass', [VehicleClassController::class, 'filterVehicleClass'])->name('filter-vehicle-class');

        // Vehicle Routes
        Route::get('vehicles', [VehicleController::class, 'index'])->name('vehicles');

        Route::any('vehicles/create', [VehicleController::class, 'create'])->name('add-vehicle');
        Route::post('vehicles/save', [VehicleController::class, 'save'])->name('save-vehicle');

        Route::any('vehicles/{vehicle}/edit', [VehicleController::class, 'edit'])->name('edit-vehicle');
        Route::post('vehicles/{vehicle}', [VehicleController::class, 'update'])->name('update-vehicle');

        Route::post('deleteVehicle', [VehicleController::class, 'delete'])->name('delete-vehicle');

        Route::any('vehicles/{vehicle}/view', [VehicleController::class, 'view'])->name('view-vehicle');

        Route::post('updateBulkStatus', [VehicleController::class, 'updateBulkStatus'])->name('update-bulk-vehicle-status');

        Route::get('vehicles/filterVehicle', [VehicleController::class, 'filterVehicle'])->name('filter-vehicle');

        Route::post('checkUniqueVehicleNumber', [VehicleController::class, 'checkUniqueVehicleNumber'])->name('check-unique-vehicle-number');

        //Client routes
        Route::get('clients', [ClientController::class, 'index'])->name('clients');
        Route::get('clients/create',  [ClientController::class, 'create'])->name('client-create');
        Route::post('clients/clients/save', [ClientController::class, 'save'])->name('client-save');
        Route::get('clients/filterClients', [ClientController::class, 'filterClients'])->name('filter-clients');
        Route::post('clients/updateBulkStatus', [ClientController::class, 'updateBulkStatus'])->name('update-bulk-client-status');
        Route::post('clients/deleteClient', [ClientController::class, 'delete'])->name('delete-client');
        Route::get('clients/{client}/edit', [ClientController::class, 'edit'])->name('edit-client');
        Route::post('clients/{client}', [ClientController::class, 'update'])->name('client-update');
        Route::post('clients/password/reset', [ResetPasswordController::class, 'changePassword'])->name('password-change');

        //Events Management Routes
        Route::get('events', [EventsController::class, 'index'])->name('events');
        Route::get('events/create',  [EventsController::class, 'create'])->name('create-event');
        Route::post('events/save', [EventsController::class, 'save'])->name('save-event');
        Route::get('events/{event}/edit', [EventsController::class, 'edit'])->name('edit-event');
        Route::post('events/{event}', [EventsController::class, 'update'])->name('update-event');
        Route::post('deleteEvents', [EventsController::class, 'delete'])->name('delete-events');
        Route::post('updateBulkEventStatus', [EventsController::class, 'updateBulkStatus'])->name('update-bulk-event-status');
        Route::get('events/filterEvents', [EventsController::class, 'filterEvents'])->name('filter-events');
        Route::get('events/filterEventsForClient', [EventsController::class, 'filterEventsForClient'])->name('filter-events-for-client');
        Route::get('getHotelEvents', [EventsController::class, 'getHotelEvents'])->name('get-hotel-events');
        Route::post('createEventFromBooking',  [EventsController::class, 'createEventFromBooking'])->name('create-event-by-ajax');


        //drivers routes
        Route::get('drivers', [DriverController::class, 'index'])->name('drivers');

        Route::get('drivers/create',  [DriverController::class, 'create'])->name('create-driver');
        Route::post('drivers/save', [DriverController::class, 'save'])->name('save-driver');
        Route::get('drivers/{driver}/edit', [DriverController::class, 'edit'])->name('edit-driver');
        Route::post('drivers/{driver}', [DriverController::class, 'update'])->name('update-driver');
        Route::post('deleteDrivers', [DriverController::class, 'delete'])->name('delete-drivers');
        Route::get('drivers/filterDrivers', [DriverController::class, 'filterDrivers'])->name('filter-drivers');

        // Driver Off Day routes
        Route::get('drivers-off-day', [DriverOffDayController::class, 'index'])->name('drivers-off-day');
        Route::post('/save-driver-off-days', [DriverOffDayController::class, 'saveDriverOffDays'])->name('save-driver-off-days');
        Route::get('/get-driver-off-days', [DriverOffDayController::class, 'getDriverOffDays'])->name('get-driver-off-days');

        Route::get('bookings', [BookingController::class, 'index'])->name('bookings');
        Route::get('filterBookings', [BookingController::class, 'filterBookings'])->name('filter-bookings');
        Route::get('bookings/{booking}/edit', [BookingController::class, 'edit'])->name('edit-booking');
        Route::post('bookings/{booking}', [BookingController::class, 'update'])->name('update-booking');
        Route::post('updateDispatch', [BookingController::class, 'updateDispatch'])->name('update-dispatch');
        Route::post('updateInlineBooking', [BookingController::class, 'updateInline'])->name('update-inline-booking');
        Route::get('bookings/create',  [BookingController::class, 'create'])->name('create-booking');
        Route::post('deleteBookings', [BookingController::class, 'delete'])->name('delete-bookings');
        Route::post('cancelBooking', [BookingController::class, 'cancelBooking'])->name('cancel-booking');
        Route::get('bookings-archives', [BookingController::class, 'bookingsArchives'])->name('bookings-archives');
        Route::get('filterBookingsArchives', [BookingController::class, 'filterBookingsArchives'])->name('filter-bookings-archives');
        Route::get('bookings/{booking}/restore', [BookingController::class, 'restoreBooking'])->name('restore-booking');
        Route::post('permanentDeleteBookings', [BookingController::class, 'permanentDeleteBookings'])->name('permanent-delete-bookings');
       
        

        // Reports Routes
        Route::get('reports', [ReportsController::class, 'index'])->name('reports');
        Route::get('/filter-reports', [ReportsController::class, 'filterReports'])->name('filter-reports');
        Route::post('/export-reports', [ReportsController::class, 'export'])->name('export-reports');
        Route::get('/get-clients-by-corporate-id', [ClientController::class, 'getClientsByCorporateId'])->name('get-clients-by-corporate-id');

        Route::get('logs', [BookingLogController::class, 'index'])->name('logs');
        Route::get('filterLogs', [BookingLogController::class, 'filterLogs'])->name('filter-logs');

        // Peak Period
        Route::get('peak-period', [PeakPeriodController::class, 'index'])->name('peak-period');
        Route::any('peak-period/create', [PeakPeriodController::class, 'create'])->name('add-peak-period');
        Route::post('peak-period/save', [PeakPeriodController::class, 'save'])->name('save-peak-period');
        Route::any('peak-period/{peakPeriod}/edit', [PeakPeriodController::class, 'edit'])->name('edit-peak-period');
        Route::post('peak-period/{peakPeriod}', [PeakPeriodController::class, 'update'])->name('update-peak-period');
        Route::get('peak-period/filterPeakPeriod', [PeakPeriodController::class, 'filterPeakPeriod'])->name('filter-peak-period');
        Route::post('deletePeakPeriod', [PeakPeriodController::class, 'delete'])->name('delete-peak-period');
        Route::post('updateBulkPeakPeriod', [PeakPeriodController::class, 'updateBulkStatus'])->name('update-bulk-peak-period-status');
         // Drivers Schedule
         Route::get('/driver-schedule', [DriverScheduleController::class, 'index'])->name('driver-schedule');
         Route::get('/filter-drivers-bookings', [DriverScheduleController::class, 'filterDriversBookings'])->name('filter-drivers-bookings');
 
         Route::post('/export', [DriverScheduleController::class, 'export'])->name('export');
         Route::post('/sendDriverSchedule', [DriverScheduleController::class, 'sendDriverSchedule'])->name('send-driver-schedule');
         Route::get('/city-surcharge', [CitySurchargeController::class, 'index'])->name('city-surcharge');
         Route::post('/save-city-surcharge', [CitySurchargeController::class, 'save'])->name('save-city-surcharge');
         Route::post('/delete-city-surcharge', [CitySurchargeController::class, 'delete'])->name('delete-city-surcharge');
    });
    Route::middleware(['ClientAdmin'])->group(function () {
        //users Routes
        Route::get('users', [UserController::class, 'index'])->name('users');
        Route::get('users/create',  [UserController::class, 'create'])->name('create-user');
        Route::post('users/save', [UserController::class, 'save'])->name('save-user');

        Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('edit-user');
        Route::post('users/{user}', [UserController::class, 'update'])->name('update-user');



        Route::post('deleteUsers', [UserController::class, 'delete'])->name('delete-users');
        Route::post('updateBulkUserStatus', [UserController::class, 'updateBulkStatus'])->name('update-bulk-user-status');
        Route::get('users/filterUsers', [UserController::class, 'filterUsers'])->name('filter-users');


        Route::get('bookings', [BookingController::class, 'index'])->name('bookings');
        Route::get('bookings/create',  [BookingController::class, 'create'])->name('create-booking');
        Route::get('bookings-archives', [BookingController::class, 'bookingsArchives'])->name('bookings-archives');
        Route::get('filterBookingsArchives', [BookingController::class, 'filterBookingsArchives'])->name('filter-bookings-archives');
        Route::get('bookings/{booking}/restore', [BookingController::class, 'restoreBooking'])->name('restore-booking');
        Route::post('permanentDeleteBookings', [BookingController::class, 'permanentDeleteBookings'])->name('permanent-delete-bookings');
        Route::post('cancelBooking', [BookingController::class, 'cancelBooking'])->name('cancel-booking');

     
        

        
        Route::get('filterBookings', [BookingController::class, 'filterBookings'])->name('filter-bookings');
        Route::get('bookings/{booking}/edit', [BookingController::class, 'edit'])->name('edit-booking');
        Route::post('bookings/{booking}', [BookingController::class, 'update'])->name('update-booking');
        Route::post('updateInlineBooking', [BookingController::class, 'updateInline'])->name('update-inline-booking');
        
        // Reports Routes
        Route::get('reports', [ReportsController::class, 'index'])->name('reports');
        Route::get('/filter-reports', [ReportsController::class, 'filterReports'])->name('filter-reports');
        Route::post('/export-reports', [ReportsController::class, 'export'])->name('export-reports');
        Route::get('/get-clients-by-corporate-id', [ClientController::class, 'getClientsByCorporateId'])->name('get-clients-by-corporate-id');
        
        Route::get('terms-and-conditions', [DashboardController::class, 'terms'])->name('dashboard.terms');

        //Events Management Routes
        Route::get('events', [EventsController::class, 'index'])->name('events');
        Route::get('events/create',  [EventsController::class, 'create'])->name('create-event');
        Route::post('events/save', [EventsController::class, 'save'])->name('save-event');
        Route::get('events/{event}/edit', [EventsController::class, 'edit'])->name('edit-event');
        Route::post('events/{event}', [EventsController::class, 'update'])->name('update-event');
        Route::post('deleteEvents', [EventsController::class, 'delete'])->name('delete-events');
        Route::post('updateBulkEventStatus', [EventsController::class, 'updateBulkStatus'])->name('update-bulk-event-status');
        Route::get('events/filterEvents', [EventsController::class, 'filterEvents'])->name('filter-events');
        Route::get('events/filterEventsForClient', [EventsController::class, 'filterEventsForClient'])->name('filter-events-for-client');
        Route::get('getHotelEvents', [EventsController::class, 'getHotelEvents'])->name('get-hotel-events');
        Route::post('createEventFromBooking',  [EventsController::class, 'createEventFromBooking'])->name('create-event-by-ajax');
        
    });

    Route::middleware(['ClientStaff'])->group(function () {
        Route::get('bookings', [BookingController::class, 'index'])->name('bookings');
        Route::get('bookings/create',  [BookingController::class, 'create'])->name('create-booking');

        Route::get('filterBookings', [BookingController::class, 'filterBookings'])->name('filter-bookings');

        Route::get('bookings/{booking}/edit', [BookingController::class, 'edit'])->name('edit-booking');
        Route::post('bookings/{booking}', [BookingController::class, 'update'])->name('update-booking');
        Route::post('updateInlineBooking', [BookingController::class, 'updateInline'])->name('update-inline-booking');
        Route::get('bookings-archives', [BookingController::class, 'bookingsArchives'])->name('bookings-archives');
        Route::get('filterBookingsArchives', [BookingController::class, 'filterBookingsArchives'])->name('filter-bookings-archives');
        Route::get('bookings/{booking}/restore', [BookingController::class, 'restoreBooking'])->name('restore-booking');
        Route::post('permanentDeleteBookings', [BookingController::class, 'permanentDeleteBookings'])->name('permanent-delete-bookings');
        Route::post('cancelBooking', [BookingController::class, 'cancelBooking'])->name('cancel-booking');
        Route::get('terms-and-conditions', [DashboardController::class, 'terms'])->name('terms-and-conditions');
        

        // Reports Routes
        Route::get('reports', [ReportsController::class, 'index'])->name('reports');
        Route::get('/filter-reports', [ReportsController::class, 'filterReports'])->name('filter-reports');
        Route::post('/export-reports', [ReportsController::class, 'export'])->name('export-reports');
        Route::get('/get-clients-by-corporate-id', [ClientController::class, 'getClientsByCorporateId'])->name('get-clients-by-corporate-id');

        //Events Management Routes
        Route::get('events', [EventsController::class, 'index'])->name('events');
        Route::get('events/create',  [EventsController::class, 'create'])->name('create-event');
        Route::post('events/save', [EventsController::class, 'save'])->name('save-event');
        Route::get('events/{event}/edit', [EventsController::class, 'edit'])->name('edit-event');
        Route::post('events/{event}', [EventsController::class, 'update'])->name('update-event');
        Route::post('deleteEvents', [EventsController::class, 'delete'])->name('delete-events');
        Route::post('updateBulkEventStatus', [EventsController::class, 'updateBulkStatus'])->name('update-bulk-event-status');
        Route::get('events/filterEvents', [EventsController::class, 'filterEvents'])->name('filter-events');
        Route::get('events/filterEventsForClient', [EventsController::class, 'filterEventsForClient'])->name('filter-events-for-client');
        Route::get('getHotelEvents', [EventsController::class, 'getHotelEvents'])->name('get-hotel-events');
        Route::post('createEventFromBooking',  [EventsController::class, 'createEventFromBooking'])->name('create-event-by-ajax');
    });
});
