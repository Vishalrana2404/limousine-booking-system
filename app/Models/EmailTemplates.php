<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\User;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmailTemplates extends Model
{
    use SoftDeletes;

    protected $table = 'email_templates';

    protected $fillable = ['name','subject', 'header', 'footer', 'message', 'qr_code_image', 'status', 'created_by_id', 'updated_by_id'];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by_id');
    }
}
