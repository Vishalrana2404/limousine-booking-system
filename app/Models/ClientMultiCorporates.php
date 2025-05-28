<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientMultiCorporates extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = [
        'client_id', 'hotel_id', 'status', 'created_by_id', 'updated_by_id'
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by_id');
    }

    public function hotel()
    {
        return $this->belongsTo(Hotel::class, 'hotel_id');
    }
    
    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }
}
