<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Events\UserCreated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'status',
        'role_id',
        'reset_token',
        'avatar',
        'activation_token',
        'activation_expires',
        'google_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isAdmin()
    {
        return isset($this->role) && strtolower($this->role->name) === 'admin';
    }

    public function isActive()
    {
        return $this->status === 'Hoạt Động';
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public $incrementing = false;
    protected $keyType = 'string';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function appliedVouchers()
    {
        return $this->hasMany(AppliedVoucher::class);
    }

    public function cart()
    {
        return $this->hasMany(Cart::class);
    }

    public function wallet()
    {
        return $this->hasOne(\App\Models\Wallet::class);
    }

    public function conversationsAsCustomer()
    {
        return $this->hasMany(\App\Models\Conversation::class, 'customer_id');
    }

    public function conversationsAsAdmin()
    {
        return $this->hasMany(\App\Models\Conversation::class, 'admin_id');
    }

    protected $dispatchesEvents = [
        'created' => UserCreated::class,
    ];
}
