<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    /**
     * ربط المخزون بالمستودع (علاقة متعدد إلى واحد)
     */
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * ربط المخزون بالمادة الإغاثية (علاقة متعدد إلى واحد)
     */
    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}