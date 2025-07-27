<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

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
        return !$this->roles->contains(fn($role) => strtolower($role->name) === 'user');
    }


    public function isActive()
    {
        return $this->status === 'Hoạt Động';
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
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
        $rolePermissions = $this->roles->flatMap->permissions->pluck('slug');
        $userPermissions = $this->permissions->pluck('slug');

        return $rolePermissions->merge($userPermissions)->unique()->contains($permission);
    }


    public function hasAnyPermission(array $permissions): bool
    {
        $rolePermissions = $this->roles->flatMap->permissions->pluck('slug');
        $userPermissions = $this->permissions->pluck('slug');

        return $rolePermissions->merge($userPermissions)->intersect($permissions)->isNotEmpty();
    }

    public function hasAllPermissions(array $permissions): bool
    {
        $rolePermissions = $this->roles->flatMap->permissions->pluck('slug');
        $userPermissions = $this->permissions->pluck('slug');

        $allPermissions = $rolePermissions->merge($userPermissions)->unique();

        return collect($permissions)->diff($allPermissions)->isEmpty();
    }


    public function wallet()
    {
        return $this->hasOne(\App\Models\Wallet::class);
    }
}
