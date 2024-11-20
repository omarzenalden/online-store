<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RepairingCenter extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_name',
        'memory',
        'brand_name',
        'date_of_manufacture',
        'malfunction_notes',
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
