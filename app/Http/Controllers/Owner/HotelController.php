<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\HotelRequest;
use App\Models\Facility;
use App\Models\Hotel;
use App\Models\HotelImage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HotelController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $hotels = auth()->user()->hotels()->with(['rooms', 'bookings'])->latest()->paginate(10);
        return view('owner.hotels.index', compact('hotels'));
    }

    public function create()
    {
        $facilities = Facility::orderBy('category')->orderBy('name')->get();
        return view('owner.hotels.create', compact('facilities'));
    }

    public function store(HotelRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('hotels/covers', 'public');
        }

        $data['user_id'] = auth()->id();
        $hotel = Hotel::create($data);

        // Attach facilities
        if ($request->filled('facilities')) {
            $hotel->facilities()->sync($request->facilities);
        }

        // Upload multiple images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $i => $img) {
                $path = $img->store('hotels/images', 'public');
                HotelImage::create([
                    'hotel_id'   => $hotel->id,
                    'image_path' => $path,
                    'is_primary' => $i === 0,
                    'sort_order' => $i,
                ]);
            }
        }

        // Notify Admins
        $admins = \App\Models\User::role('super_admin')->get();
        \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\NewHotelNotification($hotel));

        return redirect()->route('owner.hotels.index')
            ->with('success', 'Hotel created successfully! Awaiting admin approval.');
    }

    public function edit(Hotel $hotel)
    {
        $this->authorize('update', $hotel);
        $facilities    = Facility::orderBy('category')->orderBy('name')->get();
        $selectedFacilities = $hotel->facilities()->pluck('facilities.id')->toArray();
        return view('owner.hotels.edit', compact('hotel', 'facilities', 'selectedFacilities'));
    }

    public function update(HotelRequest $request, Hotel $hotel)
    {
        $this->authorize('update', $hotel);
        $data = $request->validated();

        if ($request->hasFile('cover_image')) {
            if ($hotel->cover_image) Storage::disk('public')->delete($hotel->cover_image);
            $data['cover_image'] = $request->file('cover_image')->store('hotels/covers', 'public');
        }

        $hotel->update($data);

        if ($request->filled('facilities')) {
            $hotel->facilities()->sync($request->facilities);
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $i => $img) {
                $path = $img->store('hotels/images', 'public');
                HotelImage::create([
                    'hotel_id'   => $hotel->id,
                    'image_path' => $path,
                    'is_primary' => false,
                    'sort_order' => $hotel->images()->count() + $i,
                ]);
            }
        }

        return redirect()->route('owner.hotels.index')
            ->with('success', 'Hotel updated successfully.');
    }

    public function deleteImage(HotelImage $image)
    {
        $this->authorize('update', $image->hotel);
        Storage::disk('public')->delete($image->image_path);
        $image->delete();
        return back()->with('success', 'Image deleted.');
    }

    public function destroy(Hotel $hotel)
    {
        $this->authorize('delete', $hotel);
        $hotel->delete();
        return redirect()->route('owner.hotels.index')->with('success', 'Hotel deleted.');
    }
}
