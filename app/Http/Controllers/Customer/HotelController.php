<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use App\Models\Review;
use Illuminate\Http\Request;

class HotelController extends Controller
{
    public function show(Hotel $hotel)
    {
        abort_if($hotel->status !== 'approved', 404);

        $hotel->load([
            'rooms'     => fn($q) => $q->active()->orderBy('price_per_night'),
            'images',
            'facilities',
            'reviews'   => fn($q) => $q->with('user')->latest()->limit(10),
        ]);

        $isWishlisted = auth()->check()
            ? $hotel->wishlists()->where('user_id', auth()->id())->exists()
            : false;

        $reviewStats = [
            'avg'          => $hotel->rating_avg,
            'total'        => $hotel->total_reviews,
            'cleanliness'  => $hotel->reviews()->avg('cleanliness_rating'),
            'service'      => $hotel->reviews()->avg('service_rating'),
            'location'     => $hotel->reviews()->avg('location_rating'),
            'value'        => $hotel->reviews()->avg('value_rating'),
            'distribution' => [
                5 => $hotel->reviews()->where('rating', 5)->count(),
                4 => $hotel->reviews()->where('rating', 4)->count(),
                3 => $hotel->reviews()->where('rating', 3)->count(),
                2 => $hotel->reviews()->where('rating', 2)->count(),
                1 => $hotel->reviews()->where('rating', 1)->count(),
            ],
        ];

        $similarHotels = Hotel::approved()
            ->where('city', $hotel->city)
            ->where('id', '!=', $hotel->id)
            ->with('images')
            ->limit(4)->get();

        return view('customer.hotels.show', compact('hotel', 'isWishlisted', 'reviewStats', 'similarHotels'));
    }
}
