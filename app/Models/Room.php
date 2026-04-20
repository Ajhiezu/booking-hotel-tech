<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Room extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'hotel_id', 'name', 'type', 'description',
        'capacity', 'price_per_night', 'weekend_price', 'holiday_price',
        'total_rooms', 'available_rooms',
        'bed_type', 'bed_count', 'size_sqm',
        'has_wifi', 'has_ac', 'has_tv', 'has_bathroom', 'has_balcony',
        'is_active', 'cover_image',
    ];

    protected $casts = [
        'has_wifi'       => 'boolean',
        'has_ac'         => 'boolean',
        'has_tv'         => 'boolean',
        'has_bathroom'   => 'boolean',
        'has_balcony'    => 'boolean',
        'is_active'      => 'boolean',
        'price_per_night' => 'float',
        'weekend_price'  => 'float',
        'holiday_price'  => 'float',
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function availability()
    {
        return $this->hasMany(RoomAvailability::class);
    }

    public function getCoverUrlAttribute(): string
    {
        return $this->cover_image
            ? asset('storage/' . $this->cover_image)
            : asset('images/room-placeholder.jpg');
    }

    /**
     * Get price for a specific date (with dynamic pricing).
     */
    public function getPriceForDate(\Carbon\Carbon $date): float
    {
        $avail = $this->availability()->whereDate('date', $date)->first();
        if ($avail && $avail->price_override) {
            return $avail->price_override;
        }
        if ($date->isWeekend() && $this->weekend_price) {
            return $this->weekend_price;
        }
        return $this->price_per_night;
    }

    public function isAvailableForDates($checkIn, $checkOut): bool
    {
        // Check if any availability record blocks these dates
        $blocked = $this->availability()
            ->whereBetween('date', [$checkIn, $checkOut])
            ->where(function($q) {
                $q->where('is_blocked', true)->orWhere('available_count', 0);
            })->exists();

        if ($blocked) return false;

        // Check overlapping bookings
        $overlapping = $this->bookings()
            ->whereNotIn('status', ['cancelled'])
            ->where('check_in', '<', $checkOut)
            ->where('check_out', '>', $checkIn)
            ->count();

        return $overlapping < $this->total_rooms;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
