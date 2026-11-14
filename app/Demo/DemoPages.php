<?php

namespace App\Demo;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Response;

class DemoPages
{
    public function render(Request $request, DemoData $data, string $role): Response
    {
        $name = $request->route()->getName() ?? '';
        $hotelKey = $request->route('hotel');
        $hotel = $hotelKey === null ? $data->hotels[0] : $data->hotels->first(fn ($hotel) => $hotel->slug === $hotelKey || (string) $hotel->id === $hotelKey);
        abort_if($hotel === null, 404);
        $roomKey = $request->route('room');
        $room = $roomKey === null ? $hotel->rooms[0] : $data->rooms->firstWhere('id', $roomKey);
        abort_if($room === null || ($hotelKey !== null && $room->hotel_id !== $hotel->id), 404);
        if ($roomKey !== null) {
            $hotel = $room->hotel;
        }
        $bookingKey = $request->route('booking');
        $booking = $bookingKey === null ? $data->bookings[0] : $data->bookings->firstWhere('id', $bookingKey);
        abort_if($booking === null, 404);
        $userKey = $request->route('user');
        $user = $userKey === null ? $data->user($role) : $data->users->firstWhere('id', $userKey);
        abort_if($user === null, 404);

        $bookings = $data->bookings;
        if ($request->filled('status')) {
            $bookings = $bookings->where('status', $request->string('status')->toString());
        }
        if ($request->filled('search')) {
            $search = mb_strtolower($request->string('search')->toString());
            $bookings = $bookings->filter(fn ($booking) => str_contains(mb_strtolower($booking->booking_code.' '.$booking->guest_name), $search));
        }
        $revenue = $data->bookings->whereIn('status', ['confirmed', 'checked_in', 'completed'])->sum('total_amount');
        $stats = [
            'total_users' => $data->users->count(), 'total_hotels' => $data->hotels->count(),
            'total_bookings' => $data->bookings->count(), 'total_revenue' => $revenue,
            'approved_hotels' => $data->hotels->count(), 'pending_hotels' => 0,
            'this_month_revenue' => $revenue, 'pending_bookings' => 1,
            'rating_avg' => $hotel->rating_avg, 'completed' => 1, 'cancelled' => 1,
        ];
        $monthlyData = collect(range(1, 12))->map(fn ($month) => [
            'month' => date('M', mktime(0, 0, 0, $month, 1)),
            'revenue' => 2500000 + $month * 350000, 'expenses' => 600000 + $month * 50000,
            'profit' => 1900000 + $month * 300000, 'bookings' => 5 + $month,
        ])->all();
        $values = [
            'hotel' => $hotel, 'room' => $room, 'booking' => $booking, 'user' => $user,
            'hotels' => $this->paginate($data->hotels, $request),
            'rooms' => $this->paginate($hotel->rooms, $request),
            'bookings' => $this->paginate($bookings, $request),
            'users' => $this->paginate($data->users, $request),
            'reviews' => $this->paginate($data->reviews, $request),
            'wishlists' => $this->paginate($data->wishlists, $request),
            'expenses' => $this->paginate($data->expenses, $request),
            'facilities' => $data->facilities, 'selectedFacilities' => $data->facilities->modelKeys(),
            'roles' => $data->users->map(fn ($user) => $user->roles[0]),
            'stats' => $stats, 'metrics' => $stats, 'monthlyData' => $monthlyData,
            'financials' => ['revenue' => $revenue, 'expenses' => 250000, 'profit' => $revenue - 250000],
            'recentBookings' => $data->bookings, 'recentHotels' => $data->hotels,
            'pendingOwners' => collect(), 'auditLogs' => $this->paginate(collect(), $request),
            'notifications' => $this->paginate(collect(), $request), 'year' => now()->year,
            'isWishlisted' => $hotel->wishlists->isNotEmpty(),
            'reviewStats' => ['avg' => $hotel->rating_avg, 'total' => 1, 'cleanliness' => 5, 'service' => 5, 'location' => 5, 'value' => 5, 'distribution' => [5 => 1, 4 => 0, 3 => 0, 2 => 0, 1 => 0]],
            'similarHotels' => $data->hotels->where('id', '!=', $hotel->id)->take(4),
            'featuredHotels' => $data->hotels->where('is_featured', true),
            'cheapestHotels' => $data->hotels->sortBy('base_price'),
            'topRatedHotels' => $data->hotels->sortByDesc('rating_avg')->take(3),
            'popularCities' => $data->hotels->map(fn ($hotel) => (object) ['city' => $hotel->city, 'hotel_count' => 1]),
        ];

        if ($name === 'hotels.search') {
            $values['hotels'] = $this->paginate($this->search($request, $data), $request);
        }
        if ($name === 'owner.hotels.create' || $name === 'owner.hotels.rooms.create') {
            unset($values[$name === 'owner.hotels.create' ? 'hotel' : 'room']);
        }
        if ($name === 'customer.bookings.create') {
            $request->validate(['check_in' => 'nullable|date', 'check_out' => 'nullable|date|after:check_in']);
            $checkIn = $request->input('check_in') ?: now()->addDay()->toDateString();
            $checkOut = $request->input('check_out') ?: Carbon::parse($checkIn)->addDay()->toDateString();
            $nights = max(1, (int) Carbon::parse($checkIn)->diffInDays(Carbon::parse($checkOut)));
            $subtotal = $room->price_per_night * $nights;
            $values += compact('checkIn', 'checkOut');
            $values['totals'] = ['nights' => $nights, 'room_price' => $room->price_per_night, 'subtotal' => $subtotal, 'discount_amount' => 0, 'tax_amount' => $subtotal * .11, 'total_amount' => $subtotal * 1.11];
        }

        $view = match ($name) {
            'home' => 'customer.home', 'hotels.search' => 'customer.search', 'hotels.show' => 'customer.hotels.show',
            'customer.wishlist.index' => 'customer.wishlists.index',
            'owner.hotels.rooms.index' => 'owner.rooms.index',
            'owner.hotels.rooms.create', 'owner.hotels.rooms.edit' => 'owner.rooms.create',
            'customer.profile.edit', 'owner.profile.edit', 'admin.profile.edit' => 'profile.edit',
            'login' => 'auth.login', 'register' => 'auth.register', 'password.request' => 'auth.forgot-password',
            'password.reset' => 'auth.reset-password', 'password.confirm' => 'auth.confirm-password',
            'verification.notice' => 'auth.verify-email',
            'admin.reports.pdf', 'admin.reports.excel', 'owner.reports.pdf' => 'demo.export',
            default => $name,
        };
        if ($name === 'password.reset') {
            $values['request'] = $request;
        }
        abort_unless(view()->exists($view), 404);

        return response()->view($view, $values);
    }

    private function paginate(Collection $items, Request $request): LengthAwarePaginator
    {
        $page = max(1, (int) $request->input('page', 1));

        return new LengthAwarePaginator($items->forPage($page, 12)->values(), $items->count(), 12, $page, [
            'path' => $request->url(), 'query' => $request->query(),
        ]);
    }

    private function search(Request $request, DemoData $data): Collection
    {
        $hotels = $data->hotels->filter(function ($hotel) use ($request) {
            $location = mb_strtolower($request->string('location')->toString());

            return str_contains(mb_strtolower($hotel->name.' '.$hotel->city.' '.$hotel->province.' '.$hotel->address), $location)
                && (! $request->filled('min_price') || $hotel->base_price >= (float) $request->input('min_price'))
                && (! $request->filled('max_price') || $hotel->base_price <= (float) $request->input('max_price'))
                && (! $request->filled('stars') || $hotel->star_rating == $request->input('stars'))
                && (! $request->filled('rating') || $hotel->rating_avg >= (float) $request->input('rating'))
                && collect($request->input('facilities', []))->every(fn ($id) => $hotel->facilities->contains('id', $id));
        });

        return match ($request->input('sort')) {
            'price_asc' => $hotels->sortBy('base_price'), 'price_desc' => $hotels->sortByDesc('base_price'),
            'rating' => $hotels->sortByDesc('rating_avg'), 'newest' => $hotels->sortByDesc('created_at'),
            default => $hotels->sortByDesc('rating_avg'),
        };
    }
}
