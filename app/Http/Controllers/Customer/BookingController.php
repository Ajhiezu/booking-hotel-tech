<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\BookingRequest;
use App\Models\Booking;
use App\Models\Room;
use App\Services\BookingService;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function __construct(protected BookingService $bookingService) {}

    /** Show booking form */
    public function create(Room $room, Request $request)
    {
        abort_if($room->hotel->status !== 'approved', 404);

        $checkIn  = $request->get('check_in',  now()->addDay()->toDateString());
        $checkOut = $request->get('check_out', now()->addDays(2)->toDateString());

        try {
            $totals = $this->bookingService->calculateTotal($room, $checkIn, $checkOut);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        if (!$room->isAvailableForDates($checkIn, $checkOut)) {
            session()->now('error', 'Note: This room is already fully booked for the selected dates. Please try picking a different range.');
        }

        $hotel = $room->hotel->load(['facilities', 'images']);
        return view('customer.bookings.create', compact('room', 'hotel', 'checkIn', 'checkOut', 'totals'));
    }

    public function store(BookingRequest $request, Room $room)
    {
        if (!$room->isAvailableForDates($request->check_in, $request->check_out)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Sorry, this room is not available for the selected dates. Please try different dates.');
        }

        $booking = $this->bookingService->createBooking($request->validated(), $room);

        return redirect()->route('customer.bookings.show', $booking)
            ->with('success', 'Booking created! Please complete your payment.');
    }

    /** Show booking detail */
    public function show(Booking $booking)
    {
        abort_if($booking->user_id !== auth()->id(), 403);
        $booking->load(['hotel', 'room', 'transaction', 'review']);
        return view('customer.bookings.show', compact('booking'));
    }

    /** List user's bookings */
    public function index(Request $request)
    {
        $query = Booking::where('user_id', auth()->id())
            ->with(['hotel', 'room', 'transaction'])->latest();

        if ($request->filled('status')) $query->where('status', $request->status);

        $bookings = $query->paginate(10);
        return view('customer.bookings.index', compact('bookings'));
    }

    /** Upload payment proof */
    public function uploadProof(Booking $booking, Request $request)
    {
        abort_if($booking->user_id !== auth()->id(), 403);
        $request->validate(['proof' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120']);

        $path = $request->file('proof')->store('payments/proofs', 'public');
        $booking->transaction()->update(['payment_proof' => $path]);

        return back()->with('success', 'Payment proof uploaded. We will verify soon.');
    }

    /** Cancel booking */
    public function cancel(Booking $booking, Request $request)
    {
        abort_if($booking->user_id !== auth()->id(), 403);
        $request->validate(['reason' => 'nullable|string|max:500']);

        $cancelled = $this->bookingService->cancelBooking($booking, $request->reason ?? '');

        if (!$cancelled) {
            return back()->with('error', 'This booking cannot be cancelled at this stage.');
        }

        return back()->with('success', 'Booking cancelled.');
    }
}
