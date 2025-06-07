<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookingsAdditionalStops extends Model
{
    use SoftDeletes;

    protected $table = 'bookings_additional_stops';
    
    protected $fillable = [
        'id',
        'booking_id',
        'destination_sequence',
        'additional_stop_address',
        'destination_type',
        'status',
        'created_by_id',
        'updated_by_id',
        'deleted_at',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by_id');
    }
}
