<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'vehicles';
    protected $fillable = [
        'vehicle_class_id',
        'vehicle_number',
        'image',
        'brand',
        'model',
        'status'
    ];
    public function vehicleClass()
    {
        return $this->belongsTo(VehicleClass::class);
    }
}
