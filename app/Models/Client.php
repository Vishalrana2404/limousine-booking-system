<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = [
        'user_id', 'hotel_id', 'invoice', 'status','entity', 'created_by_id', 'updated_by_id'
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
    public function hotel()
    {
        return $this->belongsTo(Hotel::class, 'hotel_id');
    }

    public function multiCorporates(): HasMany
    {
        return $this->hasMany(ClientMultiCorporates::class);
    }
}
