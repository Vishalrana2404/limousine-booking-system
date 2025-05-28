<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserType extends Model
{
    use HasFactory,  SoftDeletes;
    protected $fillable = ['name', 'type', 'description'];
    const ADMIN = 'admin';
    const CLIENT = 'client';

    public function User()
    {
        return $this->hasOne(User::class);
    }
}
