<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, hasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'social_id',
        'social_type',
        'referral_code',
        'referred_by_code'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // Through wallet, you can get user's transactions if needed.
    public function transactions()
    {
        return $this->hasManyThrough(Transaction::class, Wallet::class);
    }

    public function cart()
    {
        return $this->hasOne(Cart::class);
    }

    public function repairingCenter()
    {
        return $this->hasMany(RepairingCenter::class);
    }

    public function tradeStore()
    {
        return $this->hasMany(TradeCenter::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function wishlist()
    {
        return $this->belongsTo(Wishlist::class);
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }
    public function likes()
    {
        return $this->belongsToMany(Review::class, 'likes')->withPivot('status_like' );
    }
//    public function personalAccessTokens()
//    {
//        return $this->hasMany(PersonalAccessToken::class, 'tokenable_id');
//    }

}
