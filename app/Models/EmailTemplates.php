<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\User;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class EmailTemplates extends Model
{
    use SoftDeletes;

    protected $table = 'email_templates';

    protected $fillable = ['name','subject', 'header', 'footer', 'message', 'qr_code_image', 'qr_code_image_name', 'status', 'created_by_id', 'updated_by_id'];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by_id');
    }

    public function getQrCodeImageUrlAttribute()
    {
        if (!$this->qr_code_image) {
            return null;  // or return a default image URL if you want
        }

        $path = "email-templates/qr-codes/{$this->id}/{$this->qr_code_image}";

        // Use Storage facade to get public url assuming you saved in 'public' disk
        return Storage::disk('public')->url($path);
    }
}
