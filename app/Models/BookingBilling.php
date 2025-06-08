<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookingBilling extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'booking_id',
        'arrival_charge',
        'transfer_charge',
        'departure_charge',
        'disposal_charge',
        'delivery_charge',
        'is_peak_period_surcharge',
        'peak_period_surcharge',
        'is_fixed_midnight_surcharge',
        'mid_night_surcharge',
        'is_fixed_arrival_waiting_surcharge',
        'arrivel_waiting_time_surcharge',
        'is_fixed_outside_city_surcharge',
        'outside_city_surcharge',
        'is_fixed_last_minute_surcharge',
        'last_minute_surcharge',
        'is_fixed_additional_stop_surcharge',
        'additional_stop_surcharge',
        'is_fixed_misc_surcharge',
        'misc_surcharge',
        'is_mid_night_surcharge',
        'is_arr_waiting_time_surcharge',
        'is_outside_city_surcharge',
        'is_last_minute_surcharge',
        'is_additional_stop_surcharge',
        'is_misc_surcharge',
        'sub_total_charge',
        'total_charge',
        'arrival_charge_description',
        'transfer_charge_description',
        'departure_charge_description',
        'disposal_charge_description',
        'delivery_charge_description',
        'peak_period_charge_description',
        'mid_night_charge_description',
        'arrivel_waiting_charge_description',
        'outside_city_charge_description',
        'last_minute_charge_description',
        'additional_charge_description',
        'misc_charge_description',
        'is_discount',
        'discount_type',
        'discount_value',
        'created_by_id',
        'updated_by_id',
    ];

    /**
     * Get the booking that owns the billing.
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    /**
     * Get the user who created the billing.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    /**
     * Get the user who updated the billing.
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by_id');
    }
}
