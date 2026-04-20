<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Hotel extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'name', 'slug', 'description',
        'address', 'city', 'province', 'country', 'postal_code',
        'latitude', 'longitude', 'phone', 'email', 'website',
        'star_rating', 'base_price', 'cover_image', 'status',
        'policies', 'check_in_time', 'check_out_time', 'min_stay',
        'is_featured', 'is_flagged', 'flag_reason',
        'rating_avg', 'total_reviews',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_flagged'  => 'boolean',
        'rating_avg'  => 'float',
        'base_price'  => 'float',
        'latitude'    => 'float',
        'longitude'   => 'float',
    ];

    // Auto-generate slug
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($hotel) {
            $hotel->slug = $hotel->slug ?? Str::slug($hotel->name) . '-' . Str::random(6);
        });
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    // ─── Relationships ───────────────────────────────────────────────
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    public function images()
    {
        return $this->hasMany(HotelImage::class)->orderBy('sort_order');
    }

    public function primaryImage()
    {
        return $this->hasOne(HotelImage::class)->where('is_primary', true);
    }

    public function facilities()
    {
        return $this->belongsToMany(Facility::class, 'hotel_facility');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class)->where('is_approved', true);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function coupons()
    {
        return $this->hasMany(Coupon::class);
    }

    // ─── Scopes ──────────────────────────────────────────────────────
    public function scopeApproved($q)       { return $q->where('status', 'approved'); }
    public function scopePending($q)        { return $q->where('status', 'pending'); }
    public function scopeFeatured($q)       { return $q->where('is_featured', true); }
    public function scopeByCity($q, $city)  { return $q->where('city', 'like', "%{$city}%"); }

    // ─── Helpers ─────────────────────────────────────────────────────
    public function getCoverUrlAttribute(): string
    {
        return $this->cover_image
            ? asset('storage/' . $this->cover_image)
            : asset('images/hotel-placeholder.jpg');
    }

    public function updateRatingAvg(): void
    {
        $this->rating_avg    = $this->reviews()->avg('rating') ?? 0;
        $this->total_reviews = $this->reviews()->count();
        $this->save();
    }

    public function isWishlistedByUser(?int $userId): bool
    {
        if (!$userId) return false;
        return $this->wishlists()->where('user_id', $userId)->exists();
    }

    public function getStarRatingStarsAttribute(): string
    {
        return str_repeat('★', $this->star_rating) . str_repeat('☆', 5 - $this->star_rating);
    }
}
