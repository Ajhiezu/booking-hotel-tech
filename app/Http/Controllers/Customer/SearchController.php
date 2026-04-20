<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use App\Models\Hotel;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = Hotel::approved()
            ->with(['rooms', 'images', 'facilities'])
            ->withAvg('reviews', 'rating');

        // City / location search
        if ($request->filled('location')) {
            $loc = $request->location;
            $query->where(function ($q) use ($loc) {
                $q->where('city', 'like', "%{$loc}%")
                  ->orWhere('province', 'like', "%{$loc}%")
                  ->orWhere('address', 'like', "%{$loc}%")
                  ->orWhere('name', 'like', "%{$loc}%");
            });
        }

        // Price filter (based on min room price)
        if ($request->filled('min_price') || $request->filled('max_price')) {
            $query->whereHas('rooms', function ($q) use ($request) {
                if ($request->filled('min_price')) $q->where('price_per_night', '>=', $request->min_price);
                if ($request->filled('max_price')) $q->where('price_per_night', '<=', $request->max_price);
            });
        }

        // Star rating filter
        if ($request->filled('stars')) {
            $query->where('star_rating', $request->stars);
        }

        // Minimum review rating
        if ($request->filled('rating')) {
            $query->where('rating_avg', '>=', $request->rating);
        }

        // Facility filter
        if ($request->filled('facilities')) {
            foreach ($request->facilities as $fid) {
                $query->whereHas('facilities', fn($q) => $q->where('facility_id', $fid));
            }
        }

        // Sort
        match ($request->get('sort', 'recommended')) {
            'price_asc'  => $query->orderByRaw('(SELECT MIN(price_per_night) FROM rooms WHERE rooms.hotel_id = hotels.id) ASC'),
            'price_desc' => $query->orderByRaw('(SELECT MIN(price_per_night) FROM rooms WHERE rooms.hotel_id = hotels.id) DESC'),
            'rating'     => $query->orderByDesc('rating_avg'),
            'newest'     => $query->latest(),
            default      => $query->orderByDesc('is_featured')->orderByDesc('rating_avg'),
        };

        $hotels     = $query->paginate(12)->withQueryString();
        $facilities = Facility::orderBy('category')->orderBy('name')->get();

        return view('customer.search', compact('hotels', 'facilities'));
    }
}
