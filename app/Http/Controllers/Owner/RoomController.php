<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\RoomRequest;
use App\Models\Hotel;
use App\Models\Room;
use Illuminate\Support\Facades\Storage;

class RoomController extends Controller
{
    protected function getOwnerHotel(Hotel $hotel): Hotel
    {
        abort_if($hotel->user_id !== auth()->id(), 403, 'Unauthorized');
        return $hotel;
    }

    public function index(Hotel $hotel)
    {
        $hotel = $this->getOwnerHotel($hotel);
        $rooms = $hotel->rooms()->latest()->paginate(15);
        return view('owner.rooms.index', compact('hotel', 'rooms'));
    }

    public function create(Hotel $hotel)
    {
        $hotel = $this->getOwnerHotel($hotel);
        return view('owner.rooms.create', compact('hotel'));
    }

    public function store(RoomRequest $request, Hotel $hotel)
    {
        $hotel = $this->getOwnerHotel($hotel);
        $data  = $request->validated();
        $data['hotel_id'] = $hotel->id;

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('rooms/covers', 'public');
        }

        Room::create($data);
        return redirect()->route('owner.hotels.rooms.index', $hotel)
            ->with('success', 'Room added successfully.');
    }

    public function edit(Hotel $hotel, Room $room)
    {
        $hotel = $this->getOwnerHotel($hotel);
        abort_if($room->hotel_id !== $hotel->id, 403);
        return view('owner.rooms.create', compact('hotel', 'room'));
    }

    public function update(RoomRequest $request, Hotel $hotel, Room $room)
    {
        $hotel = $this->getOwnerHotel($hotel);
        abort_if($room->hotel_id !== $hotel->id, 403);

        $data = $request->validated();
        if ($request->hasFile('cover_image')) {
            if ($room->cover_image) Storage::disk('public')->delete($room->cover_image);
            $data['cover_image'] = $request->file('cover_image')->store('rooms/covers', 'public');
        }
        $room->update($data);
        return redirect()->route('owner.hotels.rooms.index', $hotel)->with('success', 'Room updated successfully.');
    }

    public function destroy(Hotel $hotel, Room $room)
    {
        $hotel = $this->getOwnerHotel($hotel);
        abort_if($room->hotel_id !== $hotel->id, 403);
        $room->delete();
        return back()->with('success', 'Room deleted.');
    }
}
