<?php

namespace App\Services;

use App\Models\Hotel;
use App\Models\Booking;
use App\Models\Expense;
use Carbon\Carbon;

class ReportService
{
    /**
     * Get revenue report for a hotel within a date range.
     */
    public function getHotelRevenue(Hotel $hotel, ?string $from = null, ?string $to = null): array
    {
        $from = $from ? Carbon::parse($from)->startOfDay() : Carbon::now()->startOfMonth();
        $to   = $to   ? Carbon::parse($to)->endOfDay()    : Carbon::now()->endOfMonth();

        $revenue = Booking::where('hotel_id', $hotel->id)
            ->whereIn('status', ['confirmed', 'checked_in', 'completed'])
            ->whereBetween('created_at', [$from, $to])
            ->sum('total_amount');

        $expenses = Expense::where('hotel_id', $hotel->id)
            ->whereBetween('expense_date', [$from->toDateString(), $to->toDateString()])
            ->sum('amount');

        return [
            'revenue'  => $revenue,
            'expenses' => $expenses,
            'profit'   => $revenue - $expenses,
            'from'     => $from,
            'to'       => $to,
        ];
    }

    /**
     * Monthly breakdown for a hotel.
     */
    public function getMonthlyBreakdown(Hotel $hotel, int $year): array
    {
        $months = [];
        for ($m = 1; $m <= 12; $m++) {
            $start = Carbon::create($year, $m, 1)->startOfMonth();
            $end   = $start->copy()->endOfMonth();

            $rev = Booking::where('hotel_id', $hotel->id)
                ->whereIn('status', ['confirmed', 'checked_in', 'completed'])
                ->whereBetween('created_at', [$start, $end])
                ->sum('total_amount');

            $exp = Expense::where('hotel_id', $hotel->id)
                ->whereBetween('expense_date', [$start->toDateString(), $end->toDateString()])
                ->sum('amount');

            $months[] = [
                'month'    => $start->format('M'),
                'revenue'  => $rev,
                'expenses' => $exp,
                'profit'   => $rev - $exp,
            ];
        }
        return $months;
    }

    /**
     * Global admin stats with optional filtering.
     */
    public function getFilteredStats(?string $from = null, ?string $to = null): array
    {
        $query = Booking::query();

        if ($from) $query->whereDate('created_at', '>=', $from);
        if ($to)   $query->whereDate('created_at', '<=', $to);

        $bookings = $query->get();

        $revenue = $bookings->whereIn('status', ['confirmed', 'checked_in', 'completed'])->sum('total_amount');

        return [
            'total_bookings'     => $bookings->count(),
            'total_revenue'      => $revenue,
            'completed'          => $bookings->where('status', 'completed')->count(),
            'cancelled'          => $bookings->where('status', 'cancelled')->count(),
            'total_users'        => \App\Models\User::count(),
            'total_hotels'       => Hotel::count(),
            'approved_hotels'    => Hotel::where('status', 'approved')->count(),
            'pending_hotels'     => Hotel::where('status', 'pending')->count(),
            'this_month_revenue' => Booking::whereIn('status', ['confirmed', 'checked_in', 'completed'])
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('total_amount'),
        ];
    }

    /**
     * Global admin stats.
     */
    public function getGlobalStats(): array
    {
        return $this->getFilteredStats();
    }
}
