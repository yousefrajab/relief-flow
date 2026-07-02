<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    /**
     * ربط الشحنة بطلب الاحتياج الأصلي
     */
    public function aidRequest()
    {
        return $this->belongsTo(AidRequest::class);
    }

    /**
     * ربط الشحنة بالمستودع الذي انطلقت منه
     */
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * تحويل حقول التواريخ المخصصة تلقائياً إلى كائنات Carbon زمنية (سلسلة ومعيارية)
     */
    protected $casts = [
        'delivered_at' => 'datetime',
    ];
}