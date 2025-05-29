<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class HotelLinkageLogs extends Model
{     
    use SoftDeletes;

    protected $table = 'hotel_linkage_logs';

    protected $fillable = ['user_id', 'message', 'hotel_id', 'log_type', 'client_id', 'head_office_id', 'status', 'created_by_id', 'updated_by_id'];

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
        return $this->belongsTo(User::class, 'user_id');
    }

    public function hotel()
    {
        return $this->belongsTo(Hotel::class, 'hotel_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function headOffice()
    {
        return $this->belongsTo(Hotel::class, 'head_office_id', 'id');
    }
}
