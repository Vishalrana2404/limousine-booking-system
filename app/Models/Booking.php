<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use SoftDeletes;

    const PENDING = 'PENDING';
    const ACCEPTED = 'ACCEPTED';
    const COMPLETED = 'COMPLETED';
    const CANCELLED = 'CANCELLED';
    const SCHEDULED = 'SCHEDULED';


    protected $fillable = [
        'id',
        'client_id',
        'linked_clients',
        'event_id',
        'service_type_id',
        'pick_up_location_id',
        'drop_off_location_id',
        'driver_id',
        'vehicle_id',
        'vehicle_type_id',
        'pick_up_location',
        'drop_of_location',
        'pickup_date',
        'pickup_time',
        'to_be_advised_status',
        'to_be_advised_time',
        'departure_time',
        'flight_detail',
        'no_of_hours',
        'country_code',
        'phone',
        'total_pax',
        'total_luggage',
        'client_instructions',
        'guest_name',
        'is_cross_border',
        'attachment',
        'status',
        'trip_ended',
        'driver_contact',
        'created_by_id',
        'updated_by_id',
        'deleted_at',
        'completely_deleted',
        'client_asked_to_cancel',
        'is_driver_notified',
        'is_driver_acknowledge',
        'driver_remark',
        'internal_remark',
        'child_seat_required',
        'no_of_seats_required',
        'child_1_age',
        'child_2_age',
        'meet_and_greet',
        'additional_stops',
        'latest_comment'
    ];

    public function serviceType()
    {
        return $this->belongsTo(ServiceType::class);
    }

    public function pickUpLocation()
    {
        return $this->belongsTo(Location::class, 'pick_up_location_id');
    }
    public function dropOffLocation()
    {
        return $this->belongsTo(Location::class, 'drop_off_location_id');
    }
    public function vehicleType()
    {
        return $this->belongsTo(VehicleClass::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by_id');
    }
    public function bookingBilling()
    {
        return $this->hasOne(BookingBilling::class);
    }
    public function bookingLogs(): HasMany
    {
        return $this->hasMany(BookingLog::class);
    }

    public function event()
    {
        return $this->belongsTo(Events::class, 'event_id');
    }

    public function linkedClients($linkedClients)
    {
        return User::whereIn('id', $linkedClients)->get();
    }

    public function bookings_comment_log()
    {
        return $this->hasMany(BookingsCommentLog::class)->orderBy('id', 'desc');
    }
    public function latestComment()
    {
        return $this->hasOne(BookingsCommentLog::class)->latest('id');
    }


}
