<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Coupon;
use App\Models\Room;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BookingService
{
    /**
     * Calculate booking totals.
     */
    public function calculateTotal(Room $room, string $checkIn, string $checkOut, ?string $couponCode = null): array
    {
        $checkInDate  = Carbon::parse($checkIn);
        $checkOutDate = Carbon::parse($checkOut);
        $nights       = $checkInDate->diffInDays($checkOutDate);

        if ($nights < 1) {
            throw new \InvalidArgumentException('Check-out must be after check-in.');
        }

        // Calculate nightly total with dynamic pricing
        $subtotal = 0;
        for ($i = 0; $i < $nights; $i++) {
            $date      = $checkInDate->copy()->addDays($i);
            $subtotal += $room->getPriceForDate($date);
        }

        $discount = 0;
        $coupon   = null;
        if ($couponCode) {
            $coupon = Coupon::where('code', $couponCode)->first();
            if ($coupon && $coupon->isValid()) {
                $discount = $coupon->calculateDiscount($subtotal);
            }
        }

        $tax   = ($subtotal - $discount) * 0.11; // 11% VAT
        $total = $subtotal - $discount + $tax;

        return [
            'nights'          => $nights,
            'room_price'      => $room->price_per_night,
            'subtotal'        => round($subtotal, 2),
            'discount_amount' => round($discount, 2),
            'tax_amount'      => round($tax, 2),
            'total_amount'    => round($total, 2),
            'coupon'          => $coupon,
        ];
    }

    /**
     * Create a booking and its transaction.
     */
    public function createBooking(array $data, Room $room): Booking
    {
        return DB::transaction(function () use ($data, $room) {
            $totals = $this->calculateTotal($room, $data['check_in'], $data['check_out'], $data['coupon_code'] ?? null);

            $booking = Booking::create([
                'user_id'         => auth()->id(),
                'hotel_id'        => $room->hotel_id,
                'room_id'         => $room->id,
                'check_in'        => $data['check_in'],
                'check_out'       => $data['check_out'],
                'nights'          => $totals['nights'],
                'guests'          => $data['guests'] ?? 1,
                'room_price'      => $totals['room_price'],
                'subtotal'        => $totals['subtotal'],
                'discount_amount' => $totals['discount_amount'],
                'tax_amount'      => $totals['tax_amount'],
                'total_amount'    => $totals['total_amount'],
                'coupon_code'     => $data['coupon_code'] ?? null,
                'status'          => 'pending',
                'special_requests' => $data['special_requests'] ?? null,
                'guest_name'      => $data['guest_name'] ?? auth()->user()->name,
                'guest_email'     => $data['guest_email'] ?? auth()->user()->email,
                'guest_phone'     => $data['guest_phone'] ?? auth()->user()->phone,
            ]);

            // Create initial transaction
            Transaction::create([
                'booking_id'     => $booking->id,
                'amount'         => $totals['total_amount'],
                'payment_method' => $data['payment_method'] ?? 'bank_transfer',
                'status'         => 'pending',
            ]);

            // Update coupon usage
            if ($totals['coupon']) {
                $totals['coupon']->increment('used_count');
                $booking->coupons()->attach($totals['coupon']->id, ['discount_applied' => $totals['discount_amount']]);
            }

            // Notify Hotel Owner
            if ($booking->hotel?->owner) {
                $booking->hotel->owner->notify(new \App\Notifications\NewBookingNotification($booking));
            }

            return $booking;
        });
    }

    /**
     * Cancel a booking.
     */
    public function cancelBooking(Booking $booking, string $reason = ''): bool
    {
        if (!in_array($booking->status, ['pending', 'confirmed'])) {
            return false;
        }

        return DB::transaction(function () use ($booking, $reason) {
            $booking->update([
                'status'              => 'cancelled',
                'cancelled_at'        => now(),
                'cancellation_reason' => $reason,
            ]);

            // Mark transaction as refunded if already paid
            if ($booking->transaction?->isPaid()) {
                $booking->transaction->update(['status' => 'refunded']);
            }

            // Notify Hotel Owner
            if ($booking->hotel?->owner) {
                $booking->hotel->owner->notify(new \App\Notifications\BookingStatusNotification($booking, 'customer'));
            }

            return true;
        });
    }
}
