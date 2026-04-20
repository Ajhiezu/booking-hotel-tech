<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomAvailability extends Model
{
    use HasFactory;

    protected $table = 'room_availability';

    protected $fillable = [
        'room_id', 'date', 'available_count',
        'price_override', 'is_blocked', 'block_reason',
    ];

    protected $casts = [
        'date'           => 'date',
        'is_blocked'     => 'boolean',
        'price_override' => 'float',
    ];

    public function room() { return $this->belongsTo(Room::class); }
}
