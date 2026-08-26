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
        'driver_name',
        'driver_phone',
        'status',
        'qr_code_token',
        'delivered_at',
    ];

    protected $casts = [
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
}
