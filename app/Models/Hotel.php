<?php

namespace App\Models;

use App\Models\User;
use App\Models\Events;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Hotel extends Model
{
    use SoftDeletes;

    protected $fillable = ['name','term_conditions', 'is_head_office', 'linked_head_office', 'status', 'created_by_id', 'updated_by_id'];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by_id');
    }

    public function linkedHeadOffice()
    {
        return $this->belongsTo(Hotel::class, 'linked_head_office');
    }

    public function officesOfHeadOffice()
    {
        return $this->hasMany(Hotel::class, 'linked_head_office', 'id');
    }

    public function pointOfContact()
    {
        return $this->hasMany(HotelPOC::class, 'hotel_id');
    }

    public function hotelLinkageLogs()
    {
        return $this->hasMany(HotelLinkageLogs::class, 'hotel_id');
    }

    public function billingAgreement(){
        return $this->hasOne(BillingAgreement::class);
    }
    public function client()
    {
        return $this->hasOne(Client::class)
                    ->whereHas('user', function ($query) {
                        $query->where('user_type_id', 3);
                    })
                    ->with(['user'])
                    ->oldest('created_at');
    } 

    public function events(): HasMany
    {
        return $this->hasMany(Events::class);
    }   

    public function fairBilling(): HasMany
    {
        return $this->hasMany(CorporateFairBilling::class);
    }   

    public function multiClients(): HasMany
    {
        return $this->hasMany(ClientMultiCorporates::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoices::class);
    }
}
