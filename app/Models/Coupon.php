<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;
    protected $fillable = [
        'code',
        'discount_amount',
        'discount_percentage',
        'usage_limit',
        'usage_count',
        'expires_in',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
