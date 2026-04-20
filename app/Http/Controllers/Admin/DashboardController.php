<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Hotel;
use App\Models\Review;
use App\Models\User;
use App\Services\ReportService;

class DashboardController extends Controller
{
    public function __construct(protected ReportService $reportService)
    {
    }

    public function index()
    {
        $stats = $this->reportService->getGlobalStats();

        $recentBookings = Booking::with(['user', 'hotel', 'room'])
            ->latest()->limit(10)->get();

        $recentHotels = Hotel::with('owner')
            ->latest()->limit(5)->get();

        $monthlyData = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthlyData[] = [
                'month'   => date('M', mktime(0, 0, 0, $m, 1)),
                'revenue' => Booking::whereIn('status', ['confirmed', 'checked_in', 'completed'])
                    ->whereMonth('created_at', $m)
                    ->whereYear('created_at', now()->year)
                    ->sum('total_amount'),
                'bookings' => Booking::whereMonth('created_at', $m)
                    ->whereYear('created_at', now()->year)->count(),
            ];
        }

        $pendingOwners = User::role('hotel_owner')
            ->where('verification_status', 'unverified')
            ->limit(5)->get();

        return view('admin.dashboard', compact(
            'stats', 'recentBookings', 'recentHotels', 'monthlyData', 'pendingOwners'
        ));
    }
}
