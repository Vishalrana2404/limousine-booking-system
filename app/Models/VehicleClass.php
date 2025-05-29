<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VehicleClass extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'vehicle_classes';
    protected $fillable = [
        'name',
        'seating_capacity',
        'total_luggage',
        'total_pax',
        'sequence_no',
        'status'
    ];

    public function fairBilling(): HasMany
    {
        return $this->hasMany(CorporateFairBilling::class);
    }  
}
