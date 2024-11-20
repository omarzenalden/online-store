<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TradeCenter extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_name',
        'memory',
        'battery_percentage',
        'brand_name',
        'date_of_manufacture',
        'addition_notes',
        'user_id',
        'status_id',
    ];

    public function status()
    {
        return $this->belongsTo(Status::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
