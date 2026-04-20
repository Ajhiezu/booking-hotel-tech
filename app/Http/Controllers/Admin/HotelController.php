<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Hotel;
use Illuminate\Http\Request;

class HotelController extends Controller
{
    public function index(Request $request)
    {
        $query = Hotel::with('owner')->latest();

        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('city', 'like', "%{$request->search}%");
            });
        }

        $hotels = $query->paginate(15);
        return view('admin.hotels.index', compact('hotels'));
    }

    public function show(Hotel $hotel)
    {
        $hotel->load(['owner', 'rooms', 'reviews.user', 'facilities', 'images']);
        return view('admin.hotels.show', compact('hotel'));
    }

    public function approve(Hotel $hotel)
    {
        $hotel->update(['status' => 'approved']);
        AuditLog::log('hotel_approved', $hotel);
        return back()->with('success', "Hotel '{$hotel->name}' approved successfully.");
    }

    public function reject(Hotel $hotel, Request $request)
    {
        $request->validate(['reason' => 'required|string|max:1000']);
        $hotel->update(['status' => 'rejected']);
        AuditLog::log('hotel_rejected', $hotel, [], ['reason' => $request->reason]);
        return back()->with('success', "Hotel '{$hotel->name}' rejected.");
    }

    public function suspend(Hotel $hotel, Request $request)
    {
        $request->validate(['reason' => 'required|string|max:1000']);
        $hotel->update(['status' => 'suspended', 'is_flagged' => true, 'flag_reason' => $request->reason]);
        AuditLog::log('hotel_suspended', $hotel, [], ['reason' => $request->reason]);
        return back()->with('success', "Hotel '{$hotel->name}' suspended.");
    }

    public function feature(Hotel $hotel)
    {
        $hotel->update(['is_featured' => !$hotel->is_featured]);
        AuditLog::log('hotel_featured_toggled', $hotel);
        return back()->with('success', 'Hotel featured status updated.');
    }

    public function destroy(Hotel $hotel)
    {
        $hotel->delete();
        AuditLog::log('hotel_deleted', null, ['hotel_name' => $hotel->name]);
        return redirect()->route('admin.hotels.index')->with('success', 'Hotel deleted.');
    }
}
