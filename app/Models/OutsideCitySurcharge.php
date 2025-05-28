<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OutsideCitySurcharge extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'region',
        'coordinates',
        'created_by_id',
        'updated_by_id',
    ];
}
