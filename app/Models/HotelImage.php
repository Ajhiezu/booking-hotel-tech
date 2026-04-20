<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HotelImage extends Model
{
    use HasFactory;

    protected $fillable = ['hotel_id', 'image_path', 'caption', 'is_primary', 'sort_order'];

    protected $casts = ['is_primary' => 'boolean'];

    public function hotel() { return $this->belongsTo(Hotel::class); }

    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->image_path);
    }
}
