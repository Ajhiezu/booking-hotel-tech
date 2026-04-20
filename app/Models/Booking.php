<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_code', 'user_id', 'hotel_id', 'room_id',
        'check_in', 'check_out', 'nights', 'guests',
        'room_price', 'subtotal', 'discount_amount', 'tax_amount', 'total_amount',
        'coupon_code', 'status',
        'special_requests', 'guest_name', 'guest_email', 'guest_phone',
        'cancelled_at', 'cancellation_reason',
    ];

    protected $casts = [
        'check_in'      => 'date',
        'check_out'     => 'date',
        'cancelled_at'  => 'datetime',
        'subtotal'      => 'float',
        'discount_amount' => 'float',
        'tax_amount'    => 'float',
        'total_amount'  => 'float',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($booking) {
            if (!$booking->booking_code) {
                $booking->booking_code = 'BK-' . date('Ymd') . '-' . strtoupper(Str::random(6));
            }
        });
    }

    // ─── Relationships ───────────────────────────────────────────────
    public function user()       { return $this->belongsTo(User::class); }
    public function hotel()      { return $this->belongsTo(Hotel::class); }
    public function room()       { return $this->belongsTo(Room::class); }
    public function transaction(){ return $this->hasOne(Transaction::class); }
    public function review()     { return $this->hasOne(Review::class); }
    public function coupons()    { return $this->belongsToMany(Coupon::class, 'coupon_booking')->withPivot('discount_applied'); }

    // ─── Helpers ─────────────────────────────────────────────────────
    public function isPending()    : bool { return $this->status === 'pending'; }
    public function isConfirmed()  : bool { return $this->status === 'confirmed'; }
    public function isCompleted()  : bool { return $this->status === 'completed'; }
    public function isCancelled()  : bool { return $this->status === 'cancelled'; }
    public function isCheckedIn()  : bool { return $this->status === 'checked_in'; }
    public function canBeReviewed(): bool { return $this->isCompleted() && !$this->review; }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'pending'    => '<span class="badge-warning">Pending</span>',
            'confirmed'  => '<span class="badge-info">Confirmed</span>',
            'checked_in' => '<span class="badge-primary">Checked In</span>',
            'completed'  => '<span class="badge-success">Completed</span>',
            'cancelled'  => '<span class="badge-danger">Cancelled</span>',
            default      => '<span class="badge-secondary">' . $this->status . '</span>',
        };
    }
}
