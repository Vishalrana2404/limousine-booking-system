<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DriverOffDay extends Model
{
    use SoftDeletes;

    protected $table = 'driver_off_days';

    protected $fillable = [
        'driver_id',
        'off_date',
        'created_by_id',
        'updated_by_id',
    ];

    protected $dates = ['deleted_at'];

    public function driver(){
        return $this->belongsTo(Driver::class);
    }
}
