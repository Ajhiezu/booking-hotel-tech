<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = Review::with(['user', 'hotel'])->latest();

        if ($request->filled('flagged')) $query->where('is_flagged', true);
        if ($request->filled('search')) {
            $query->whereHas('hotel', fn($q) => $q->where('name', 'like', "%{$request->search}%"));
        }

        $reviews = $query->paginate(20);
        return view('admin.reviews.index', compact('reviews'));
    }

    public function destroy(Review $review)
    {
        AuditLog::log('review_deleted', $review, ['comment' => $review->comment]);
        $review->delete();

        // Update hotel rating
        $review->hotel->updateRatingAvg();

        return back()->with('success', 'Review removed successfully.');
    }

    public function approve(Review $review)
    {
        $review->update(['is_approved' => true, 'is_flagged' => false]);
        return back()->with('success', 'Review approved.');
    }

    public function flag(Review $review, Request $request)
    {
        $request->validate(['reason' => 'required|string|max:500']);
        $review->update(['is_flagged' => true, 'flag_reason' => $request->reason, 'is_approved' => false]);
        return back()->with('success', 'Review flagged and hidden.');
    }
}
