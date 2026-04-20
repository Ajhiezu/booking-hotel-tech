<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\ReviewRequest;
use App\Models\Booking;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(ReviewRequest $request, Booking $booking)
    {
        abort_if($booking->user_id !== auth()->id(), 403);
        abort_if(!$booking->canBeReviewed(), 422, 'Review not allowed for this booking.');

        $review = Review::create([
            'user_id'            => auth()->id(),
            'hotel_id'           => $booking->hotel_id,
            'booking_id'         => $booking->id,
            'rating'             => $request->rating,
            'cleanliness_rating' => $request->cleanliness_rating,
            'service_rating'     => $request->service_rating,
            'location_rating'    => $request->location_rating,
            'value_rating'       => $request->value_rating,
            'title'              => $request->title,
            'comment'            => $request->comment,
            'is_approved'        => true, // Auto-approve for immediate feedback
        ]);

        $booking->hotel->updateRatingAvg();

        return back()->with('success', 'Review submitted. Thank you!');
    }

    public function replyOwner(Review $review, Request $request)
    {
        // Only hotel owner can reply
        abort_if($review->hotel->user_id !== auth()->id(), 403);
        $request->validate(['reply' => 'required|string|max:1000']);
        $review->update(['owner_reply' => $request->reply, 'replied_at' => now()]);
        return back()->with('success', 'Reply submitted.');
    }
}
