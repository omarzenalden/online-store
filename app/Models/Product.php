<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
        'price',
        'quantity',
        'details',
        'category_id',
        'brand_id',
    ];

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function carts()
    {
        return $this->belongsToMany(Cart::class, 'cart_items', 'product_id', 'cart_id')
            ->withPivot('price' , 'quantity')
            ->withTimeStamps();
    }

    public function product_photos()
    {
        return $this->hasMany(ProductPhoto::class);
    }

    public function offers()
    {
        return $this->belongsToMany(Offer::class , 'offer_products') ->withPivot('quantity')
            ->withTimestamps();
    }

    public function wishlists()
    {
        return $this->morphMany(Wishlist::class, 'favoriteable');
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class ,'order_items')
            ->withPivot('quantity','total_price')->withTimestamps();
    }
}
