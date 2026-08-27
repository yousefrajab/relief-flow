<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AidRequestActivity extends Model
{
    protected $fillable = [
        'aid_request_id',
        'user_id',
        'action',
        'notes',
    ];

    public function aidRequest()
    {
        return $this->belongsTo(AidRequest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
