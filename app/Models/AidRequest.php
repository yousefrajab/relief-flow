<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AidRequest extends Model
{
    /**
     * ربط الطلب بالمستخدم المنشئ للطلب
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * ربط الطلب بالمواد المطلوبة بداخله (علاقة واحد إلى متعدد)
     */
    public function requestItems()
    {
        return $this->hasMany(AidRequestItem::class);
    }

    /**
     * ربط الطلب بالشحنة الصادرة الخاصة به (إن وجدت)
     */
    public function shipment()
    {
        return $this->hasOne(Shipment::class);
    }
}