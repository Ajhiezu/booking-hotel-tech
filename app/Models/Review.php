<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'hotel_id', 'booking_id', 'rating',
        'cleanliness_rating', 'service_rating', 'location_rating', 'value_rating',
        'title', 'comment', 'owner_reply', 'replied_at',
        'is_approved', 'is_flagged', 'flag_reason',
    ];

    protected $casts = [
        'is_approved' => 'boolean',
        'is_flagged'  => 'boolean',
        'replied_at'  => 'datetime',
        'rating'      => 'integer',
    ];

    public function user()    { return $this->belongsTo(User::class); }
    public function hotel()   { return $this->belongsTo(Hotel::class); }
    public function booking() { return $this->belongsTo(Booking::class); }

    public function getAverageSubRatingAttribute(): float
    {
        $sub = array_filter([
            $this->cleanliness_rating,
            $this->service_rating,
            $this->location_rating,
            $this->value_rating,
        ]);
        return count($sub) ? array_sum($sub) / count($sub) : $this->rating;
    }
}
