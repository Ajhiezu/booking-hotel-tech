<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\ReportService;

class DashboardController extends Controller
{
    public function __construct(protected ReportService $reportService) {}

    public function index()
    {
        $hotel = auth()->user()->hotels()->approved()->first();

        if (!$hotel) {
            return view('owner.dashboard-pending');
        }

        $stats = [
            'total_bookings'     => Booking::where('hotel_id', $hotel->id)->count(),
            'pending_bookings'   => Booking::where('hotel_id', $hotel->id)->where('status', 'pending')->count(),
            'confirmed_bookings' => Booking::where('hotel_id', $hotel->id)->where('status', 'confirmed')->count(),
            'total_revenue'      => Booking::where('hotel_id', $hotel->id)
                ->whereIn('status', ['confirmed', 'checked_in', 'completed'])->sum('total_amount'),
            'total_rooms'        => $hotel->rooms()->count(),
            'total_reviews'      => $hotel->reviews()->count(),
            'rating_avg'         => $hotel->rating_avg,
        ];

        $financials    = $this->reportService->getHotelRevenue($hotel);
        $monthlyData   = $this->reportService->getMonthlyBreakdown($hotel, now()->year);
        $recentBookings = Booking::where('hotel_id', $hotel->id)->with(['user', 'room'])
            ->latest()->limit(8)->get();

        return view('owner.dashboard', compact('hotel', 'stats', 'financials', 'monthlyData', 'recentBookings'));
    }
}
