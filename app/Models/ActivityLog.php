<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActivityLog extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = [
        'user_id',
        'function_name',
        'model_name',
        'old_data',
        'new_data',
        'ip_address',
        'user_device',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
