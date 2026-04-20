<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'icon', 'category'];

    public function hotels()
    {
        return $this->belongsToMany(Hotel::class, 'hotel_facility');
    }
}
