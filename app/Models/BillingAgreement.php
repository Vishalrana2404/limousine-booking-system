<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BillingAgreement extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'hotel_id',
        'per_trip_arr',
        'per_trip_dep',
        'per_trip_transfer',
        'per_trip_delivery',
        'per_hour_rate',
        'peak_period_surcharge',
        'fixed_multiplier_midnight_surcharge_23_seats',
        'mid_night_surcharge_23_seats',
        'fixed_multiplier_midnight_surcharge_greater_then_23_seats',
        'midnight_surcharge_greater_then_23_seats',
        'fixed_multiplier_arrivel_waiting_time',
        'arrivel_waiting_time',
        'fixed_multiplier_departure_and_transfer_waiting',
        'departure_and_transfer_waiting',
        'fixed_multiplier_last_min_request_23_seats',
        'last_min_request_23_seats',
        'fixed_multiplier_last_min_request_greater_then_23_seats',
        'last_min_request_greater_then_23_seats',
        'fixed_multiplier_outside_city_surcharge_23_seats',
        'outside_city_surcharge_23_seats',
        'fixed_multiplier_outside_city_surcharge_greater_then_23_seats',
        'outside_city_surcharge_greater_then_23_seats',
        'fixed_multiplier_additional_stop',
        'additional_stop',
        'fixed_multiplier_misc_charges',
        'misc_charges',
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }
}
