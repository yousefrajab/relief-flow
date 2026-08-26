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
        'notes',
        'status',
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
}
