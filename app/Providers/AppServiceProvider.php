<?php

namespace App\Providers;

use App\Repositories\ActivityLogRepository;
use App\Repositories\BillingAgreementRepository;
use App\Repositories\BookingBillingRepository;
use App\Repositories\BookingLogRepository;
use App\Repositories\BookingRepository;
use App\Repositories\CitySurchargeRepository;
use App\Repositories\ClientRepository;
use App\Repositories\DriverRepository;
use App\Repositories\DriverOffDayRepository;
use App\Repositories\HotelRepository;
use App\Repositories\EventRepository;
use App\Repositories\CorporateFairBillingRepository;
use App\Repositories\Interfaces\ActivityLogInterface;
use App\Repositories\Interfaces\BillingAgreementInterface;
use App\Repositories\Interfaces\BookingBillingInterface;
use App\Repositories\Interfaces\BookingInterface;
use App\Repositories\Interfaces\BookingLogInterface;
use App\Repositories\Interfaces\CitySurchargeInterface;
use App\Repositories\Interfaces\ClientInterface;
use App\Repositories\Interfaces\DriverInterface;
use App\Repositories\Interfaces\DriverOffDayInterface;
use App\Repositories\Interfaces\HotelInterface;
use App\Repositories\Interfaces\EventInterface;
use App\Repositories\Interfaces\CorporateFairBillingInterface;
use App\Repositories\Interfaces\LocationInterface;
use App\Repositories\Interfaces\ServiceTypeInterface;
use App\Repositories\Interfaces\UserInterface;
use App\Repositories\Interfaces\PeakPeriodInterface;
use App\Repositories\UserRepository;
use App\Repositories\Interfaces\UserTypeInterface;
use App\Repositories\Interfaces\VehicleClassInterface;
use App\Repositories\Interfaces\VehicleInterface;
use App\Repositories\Interfaces\NotificationInterface;
use App\Repositories\LocationRepository;
use App\Repositories\ServiceTypeRepository;
use App\Repositories\UserTypeRepository;
use App\Repositories\VehicleClassRepository;
use App\Repositories\PeakPeriodRepository;
use App\Repositories\VehicleRepository;
use App\Repositories\NotificationRepository;
use App\Repositories\EmailTemplatesRepository;
use App\Repositories\Interfaces\EmailTemplatesInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserInterface::class, UserRepository::class);
        $this->app->bind(UserTypeInterface::class, UserTypeRepository::class);
        $this->app->bind(ActivityLogInterface::class, ActivityLogRepository::class);
        $this->app->bind(VehicleClassInterface::class, VehicleClassRepository::class);
        $this->app->bind(VehicleInterface::class, VehicleRepository::class);
        $this->app->bind(ClientInterface::class, ClientRepository::class);
        $this->app->bind(BillingAgreementInterface::class, BillingAgreementRepository::class);
        $this->app->bind(DriverInterface::class, DriverRepository::class);
        $this->app->bind(DriverOffDayInterface::class, DriverOffDayRepository::class);
        $this->app->bind(HotelInterface::class, HotelRepository::class);
        $this->app->bind(EventInterface::class, EventRepository::class);
        $this->app->bind(CorporateFairBillingInterface::class, CorporateFairBillingRepository::class);
        $this->app->bind(ServiceTypeInterface::class, ServiceTypeRepository::class);
        $this->app->bind(BookingInterface::class, BookingRepository::class);
        $this->app->bind(LocationInterface::class, LocationRepository::class);
        $this->app->bind(NotificationInterface::class, NotificationRepository::class);
        $this->app->bind(BookingBillingInterface::class, BookingBillingRepository::class);
        $this->app->bind(BookingLogInterface::class, BookingLogRepository::class);
        $this->app->bind(PeakPeriodInterface::class, PeakPeriodRepository::class);
        $this->app->bind(CitySurchargeInterface::class, CitySurchargeRepository::class);
        $this->app->bind(EmailTemplatesInterface::class, EmailTemplatesRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
