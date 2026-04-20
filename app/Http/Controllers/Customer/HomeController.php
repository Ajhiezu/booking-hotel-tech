<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Hotel;

class HomeController extends Controller
{
    public function index()
    {
        $featuredHotels = Hotel::approved()->featured()
            ->with(['facilities', 'images', 'rooms'])
            ->withAvg('reviews', 'rating')
            ->limit(8)->get();

        $popularCities = Hotel::approved()
            ->selectRaw('city, COUNT(*) as hotel_count, AVG(rating_avg) as avg_rating')
            ->groupBy('city')
            ->orderByDesc('hotel_count')
            ->limit(6)->get();

        $cheapestHotels = Hotel::approved()
            ->with(['rooms', 'images'])
            ->orderByRaw('(SELECT MIN(price_per_night) FROM rooms WHERE rooms.hotel_id = hotels.id)')
            ->limit(6)->get();

        $topRatedHotels = Hotel::approved()
            ->with(['images'])
            ->where('rating_avg', '>', 4.0)
            ->orderByDesc('rating_avg')
            ->limit(6)->get();

        return view('customer.home', compact(
            'featuredHotels', 'popularCities', 'cheapestHotels', 'topRatedHotels'
        ));
    }
}
