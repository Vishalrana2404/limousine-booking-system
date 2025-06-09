<?php

namespace App\Models;

use App\Models\User;
use App\Models\Hotel;
use App\Models\Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Events extends Model
{
    use SoftDeletes;

    protected $fillable = ['name','hotel_id', 'status', 'created_by_id', 'updated_by_id'];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by_id');
    }

    public function hotel(){
        return $this->belongsTo(Hotel::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(Bookings::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoices::class);
    }
}
