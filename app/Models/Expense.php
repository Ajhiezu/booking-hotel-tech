<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'hotel_id', 'title', 'description', 'amount',
        'category', 'expense_date', 'receipt',
    ];

    protected $casts = [
        'expense_date' => 'date',
        'amount'       => 'float',
    ];

    public function hotel() { return $this->belongsTo(Hotel::class); }
}
