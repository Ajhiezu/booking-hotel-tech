<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'hotel_id', 'code', 'name', 'description',
        'discount_type', 'discount_value', 'max_discount',
        'min_booking_amount', 'usage_limit', 'used_count',
        'valid_from', 'valid_until', 'is_active',
    ];

    protected $casts = [
        'valid_from'    => 'date',
        'valid_until'   => 'date',
        'is_active'     => 'boolean',
        'discount_value' => 'float',
        'max_discount'  => 'float',
    ];

    public function hotel()    { return $this->belongsTo(Hotel::class); }
    public function bookings() { return $this->belongsToMany(Booking::class, 'coupon_booking')->withPivot('discount_applied'); }

    public function isValid(): bool
    {
        return $this->is_active
            && $this->valid_from->isPast()
            && $this->valid_until->isFuture()
            && (!$this->usage_limit || $this->used_count < $this->usage_limit);
    }

    public function calculateDiscount(float $amount): float
    {
        if ($amount < $this->min_booking_amount) return 0;

        $discount = $this->discount_type === 'percentage'
            ? $amount * ($this->discount_value / 100)
            : $this->discount_value;

        if ($this->max_discount) {
            $discount = min($discount, $this->max_discount);
        }

        return min($discount, $amount);
    }
}
