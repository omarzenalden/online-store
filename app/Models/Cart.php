<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;
    protected $fillable = [
        'total_price',
        'user_id',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'cart_items', 'cart_id', 'product_id')
            ->withPivot('price' , 'quantity')
            ->withTimeStamps();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
