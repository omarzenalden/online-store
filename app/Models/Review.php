<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;
    protected $fillable = [
        'comment',
        'rate',
        'like',
        'user_id',
        'product_id',
        'likes_count',
        'dislikes_count',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function likes()
    {
        return $this->belongsToMany(User::class, 'likes')->withPivot('status_like' );
    }
}
