<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AidRequestItem extends Model
{
    /**
     * ربط تفاصيل المادة بالطلب الرئيسي
     */
    public function aidRequest()
    {
        return $this->belongsTo(AidRequest::class);
    }

    /**
     * ربط المادة الإغاثية المطلوبة
     */
    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}