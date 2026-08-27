<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'location',
        'latitude',
        'longitude',
        'capacity',
        'status',
    ];

    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }

    public function shipments()
    {
        return $this->hasMany(Shipment::class);
    }
}
