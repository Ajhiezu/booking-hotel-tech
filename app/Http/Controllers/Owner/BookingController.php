<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Hotel;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    protected function getOwnerHotels(): \Illuminate\Support\Collection
    {
        return auth()->user()->hotels()->approved()->pluck('id');
    }

    public function index(Request $request)
    {
        $hotelIds = $this->getOwnerHotels();
        $query    = Booking::with(['user', 'hotel', 'room', 'transaction'])
            ->whereIn('hotel_id', $hotelIds)->latest();

        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('hotel_id')) $query->where('hotel_id', $request->hotel_id);

        $bookings = $query->paginate(20);
        $hotels   = auth()->user()->hotels()->approved()->get();

        return view('owner.bookings.index', compact('bookings', 'hotels'));
    }

    public function show(Booking $booking)
    {
        abort_if(!$this->getOwnerHotels()->contains($booking->hotel_id), 403);
        $booking->load(['user', 'hotel', 'room', 'transaction', 'review']);
        return view('owner.bookings.show', compact('booking'));
    }

    public function confirm(Booking $booking)
    {
        abort_if(!$this->getOwnerHotels()->contains($booking->hotel_id), 403);
        abort_if(!$booking->isPending(), 422, 'Only pending bookings can be confirmed.');
        $booking->update(['status' => 'confirmed']);
        return back()->with('success', 'Booking confirmed.');
    }

    public function checkIn(Booking $booking)
    {
        abort_if(!$this->getOwnerHotels()->contains($booking->hotel_id), 403);
        $booking->update(['status' => 'checked_in']);
        return back()->with('success', 'Guest checked in.');
    }

    public function complete(Booking $booking)
    {
        abort_if(!$this->getOwnerHotels()->contains($booking->hotel_id), 403);
        $booking->update(['status' => 'completed']);
        return back()->with('success', 'Booking marked as completed.');
    }

    public function cancel(Booking $booking, Request $request)
    {
        abort_if(!$this->getOwnerHotels()->contains($booking->hotel_id), 403);
        $request->validate(['reason' => 'required|string|max:500']);
        $booking->update([
            'status'              => 'cancelled',
            'cancelled_at'        => now(),
            'cancellation_reason' => $request->reason,
        ]);

        // Notify Customer
        if ($booking->user) {
            $booking->user->notify(new \App\Notifications\BookingStatusNotification($booking, 'owner'));
        }

        return back()->with('success', 'Booking cancelled.');
    }
}
