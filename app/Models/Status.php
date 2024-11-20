<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    use HasFactory;
    protected $fillable = [
        'status',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function repairing_center()
    {
        return $this->hasMany(RepairingCenter::class);
    }
    public function trade_centers()
    {
        return $this->hasMany(TradeCenter::class);
    }
}
