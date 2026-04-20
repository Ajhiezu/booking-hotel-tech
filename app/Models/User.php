<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name', 'email', 'password',
        'phone', 'avatar', 'address',
        'date_of_birth', 'gender',
        'is_active', 'verification_status',
        'bio', 'id_card_number', 'id_card_image',
        'last_login_at',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at'     => 'datetime',
            'date_of_birth'     => 'date',
            'is_active'         => 'boolean',
            'password'          => 'hashed',
        ];
    }

    // ─── Relationships ───────────────────────────────────────────────
    public function hotels()
    {
        return $this->hasMany(Hotel::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }

    // ─── Helpers ─────────────────────────────────────────────────────
    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super_admin');
    }

    public function isHotelOwner(): bool
    {
        return $this->hasRole('hotel_owner');
    }

    public function isCustomer(): bool
    {
        return $this->hasRole('customer');
    }

    public function isApprovedOwner(): bool
    {
        return $this->isHotelOwner() && $this->verification_status === 'approved';
    }

    public function getAvatarUrlAttribute(): string
    {
        return $this->avatar
            ? asset('storage/' . $this->avatar)
            : 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=1E3A5F&color=fff';
    }
}
