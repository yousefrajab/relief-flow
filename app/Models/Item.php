<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'unit',
        'description',
    ];

    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }
}
