<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id', 'transaction_code', 'amount',
        'payment_method', 'status', 'payment_proof',
        'midtrans_token', 'midtrans_redirect_url', 'payment_response', 'paid_at',
    ];

    protected $casts = [
        'payment_response' => 'array',
        'paid_at'          => 'datetime',
        'amount'           => 'float',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($tx) {
            if (!$tx->transaction_code) {
                $tx->transaction_code = 'TXN-' . strtoupper(Str::random(12));
            }
        });
    }

    public function booking() { return $this->belongsTo(Booking::class); }

    public function isPaid()     : bool { return $this->status === 'paid'; }
    public function isPending()  : bool { return $this->status === 'pending'; }
    public function isFailed()   : bool { return $this->status === 'failed'; }
    public function isRefunded() : bool { return $this->status === 'refunded'; }
}
