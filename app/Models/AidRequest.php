<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AidRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'location',
        'latitude',
        'longitude',
        'notes',
        'status',
        'priority',
        'rejection_reason',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function requestItems()
    {
        return $this->hasMany(AidRequestItem::class);
    }

    public function shipment()
    {
        return $this->hasOne(Shipment::class);
    }

    public function activities()
    {
        return $this->hasMany(AidRequestActivity::class)->orderBy('created_at');
    }
}
