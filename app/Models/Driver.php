<?php

namespace App\Models;

use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Driver extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'vehicle_id',
        'name',
        'country_code',
        'phone',
        'email',
        'driver_type',
        'race',
        'status',
        'gender',
        'chat_id',
        'created_by_id',
        'updated_by_id',
    ];

    protected $dates = ['deleted_at'];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by_id');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function driverOffDay(){
        return $this->hasMany(DriverOffDay::class);
    }
}
