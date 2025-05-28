<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PeakPeriod extends Model
{
    use HasFactory,SoftDeletes;
    
    protected $fillable = [
        'event',
        'start_date',
        'end_date',
        'status'
    ];
}
