<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductPhoto extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_photo_path',
        'product_id',
    ];

    public static function create(array $array)
    {
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
