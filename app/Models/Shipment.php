<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'aid_request_id',
        'warehouse_id',
        'driver_user_id',
        'driver_name',
        'driver_phone',
        'status',
        'qr_code_token',
        'picked_up_at',
        'pickup_photo_path',
        'pickup_ai_verification_status',
        'pickup_ai_verification_notes',
        'delivered_at',
        'delivery_photo_path',
        'ai_verification_status',
        'ai_verification_notes',
    ];

    protected $casts = [
        'picked_up_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function aidRequest()
    {
        return $this->belongsTo(AidRequest::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_user_id');
    }
}
