<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index()
    {
        $wishlists = auth()->user()->wishlists()
            ->with(['hotel.images', 'hotel.rooms'])
            ->latest()->paginate(12);

        return view('customer.wishlists.index', compact('wishlists'));
    }

    public function toggle(Hotel $hotel)
    {
        $existing = Wishlist::where('user_id', auth()->id())
            ->where('hotel_id', $hotel->id)->first();

        if ($existing) {
            $existing->delete();
            $message = 'Removed from wishlist.';
            $wishlisted = false;
        } else {
            Wishlist::create(['user_id' => auth()->id(), 'hotel_id' => $hotel->id]);
            $message = 'Added to wishlist!';
            $wishlisted = true;
        }

        if (request()->expectsJson()) {
            return response()->json(['wishlisted' => $wishlisted, 'message' => $message]);
        }

        return back()->with('success', $message);
    }
}
