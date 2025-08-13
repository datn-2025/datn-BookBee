<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /**
     * Get the role that owns the user.
     */
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }
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
        'reset_token',
        'avatar',
        'activation_token',
        'activation_expires',
        'google_id',
        'last_seen'
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
            'last_seen' => 'datetime',
            'activation_expires' => 'datetime',
        ];
    }

    public function isAdmin()
    {
        return $this->role && strtolower($this->role->name) !== 'user';
    }


    public function isActive()
    {
        return $this->status === 'Hoạt Động';
    }



    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'user_permissions');
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

    public function hasPermission(string $permission): bool
    {
        $rolePermissions = $this->role ? $this->role->permissions->pluck('slug') : collect();
        $userPermissions = $this->permissions->pluck('slug');
        return $rolePermissions->merge($userPermissions)->unique()->contains($permission);
    }


    public function hasAnyPermission(array $permissions): bool
    {
        $rolePermissions = $this->role ? $this->role->permissions->pluck('slug') : collect();
        $userPermissions = $this->permissions->pluck('slug');
        return $rolePermissions->merge($userPermissions)->intersect($permissions)->isNotEmpty();
    }

    public function hasAllPermissions(array $permissions): bool
    {
        $rolePermissions = $this->role ? $this->role->permissions->pluck('slug') : collect();
        $userPermissions = $this->permissions->pluck('slug');
        $allPermissions = $rolePermissions->merge($userPermissions)->unique();
        return collect($permissions)->diff($allPermissions)->isEmpty();
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

    /**
     * Check if user is currently online
     * User is considered online if they were active within the last 5 minutes
     */
    public function isOnline()
    {
        if ($this->last_seen) {
            return $this->last_seen->diffInMinutes(now()) <= 5;
        }
        
        return false;
    }

    /**
     * Check if user was active within specified minutes
     */
    public function isActiveWithin($minutes = 60)
    {
        if ($this->last_seen) {
            return $this->last_seen->diffInMinutes(now()) <= $minutes;
        }
        
        return false;
    }

    /**
     * Update user's last seen timestamp
     */
    public function updateLastSeen()
    {
        $this->update([
            'last_seen' => now()
        ]);
    }

    // protected $dispatchesEvents = [
    //     'created' => UserCreated::class,
    // ];
}
