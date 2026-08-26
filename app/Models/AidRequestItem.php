<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AidRequestItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'aid_request_id',
        'item_id',
        'quantity',
    ];

    public function aidRequest()
    {
        return $this->belongsTo(AidRequest::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
