<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CorporateFairBilling extends Model
{
    use SoftDeletes;

    protected $fillable = ['hotel_id','vehicle_class_id', 'billing_type', 'amount', 'status', 'created_by_id', 'updated_by_id'];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by_id');
    }

    public function hotel(){
        return $this->hasOne(Hotel::class);
    }

    public function vehicle_class(){
        return $this->hasOne(VehicleClass::class);
    }
}
