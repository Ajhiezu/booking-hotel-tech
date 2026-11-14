<?php

namespace App\Demo;

use App\Models\Booking;
use App\Models\Expense;
use App\Models\Facility;
use App\Models\Hotel;
use App\Models\Review;
use App\Models\Room;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Database\Eloquent\Collection;
use Spatie\Permission\Models\Role;

/** In-memory models only: none of these records are saved or queried. */
class DemoData
{
    public Collection $hotels;

    public Collection $rooms;

    public Collection $users;

    public Collection $bookings;

    public Collection $reviews;

    public Collection $facilities;

    public Collection $expenses;

    public Collection $wishlists;

    public function __construct()
    {
        $this->facilities = new Collection([
            (new Facility)->forceFill(['id' => 1, 'name' => 'Free WiFi', 'category' => 'General']),
            (new Facility)->forceFill(['id' => 2, 'name' => 'Swimming Pool', 'category' => 'Leisure']),
            (new Facility)->forceFill(['id' => 3, 'name' => 'Restaurant', 'category' => 'Dining']),
            (new Facility)->forceFill(['id' => 4, 'name' => 'Free Parking', 'category' => 'General']),
        ]);
        $this->users = new Collection;
        foreach (['customer', 'hotel_owner', 'super_admin'] as $i => $role) {
            $user = new User;
            $user->forceFill([
                'id' => $i + 1, 'name' => ['Alex Traveler', 'Nadia Hotel Owner', 'Admin StayEase'][$i],
                'email' => $role.'@demo.example', 'phone' => '081234567890',
                'is_active' => true, 'verification_status' => 'approved',
                'email_verified_at' => now(), 'created_at' => now()->subMonths(3),
            ]);
            $user->setRelation('roles', new Collection([(new Role)->forceFill(['id' => $i + 1, 'name' => $role, 'guard_name' => 'web'])]));
            foreach (['notifications', 'unreadNotifications', 'hotels', 'bookings', 'reviews', 'wishlists'] as $relation) {
                $user->setRelation($relation, new Collection);
            }
            $this->users->push($user);
        }

        $this->hotels = new Collection;
        $this->rooms = new Collection;
        $destinations = [
            ['Bali Ocean Retreat', 'bali-ocean-retreat', 'Bali', 'Bali', 850000, 5, 4.8],
            ['Jakarta Grand Hotel', 'jakarta-grand-hotel', 'Jakarta', 'DKI Jakarta', 650000, 4, 4.6],
            ['Bandung Garden Stay', 'bandung-garden-stay', 'Bandung', 'Jawa Barat', 450000, 3, 4.5],
            ['Yogyakarta Heritage', 'yogyakarta-heritage', 'Yogyakarta', 'DI Yogyakarta', 380000, 4, 4.7],
            ['Surabaya City Suites', 'surabaya-city-suites', 'Surabaya', 'Jawa Timur', 520000, 4, 4.4],
            ['Lombok Sunset Resort', 'lombok-sunset-resort', 'Lombok', 'Nusa Tenggara Barat', 720000, 5, 4.9],
        ];
        foreach ($destinations as $i => [$name, $slug, $city, $province, $price, $stars, $rating]) {
            $hotel = new Hotel;
            $hotel->forceFill([
                'id' => $i + 1, 'user_id' => 2, 'name' => $name, 'slug' => $slug,
                'city' => $city, 'province' => $province, 'address' => 'Jl. Wisata No. '.($i + 10),
                'description' => 'Enjoy a relaxing stay with spacious rooms, friendly service, and easy access to local attractions. This sample hotel is available for the StayEase demo.',
                'base_price' => $price, 'star_rating' => $stars, 'rating_avg' => $rating,
                'total_reviews' => 1, 'status' => 'approved', 'is_featured' => $i < 4,
                'check_in_time' => '14:00', 'check_out_time' => '12:00', 'min_stay' => 1,
                'phone' => '0211234567', 'email' => 'hello@demo.example', 'created_at' => now()->subDays($i),
                'rooms_count' => 2, 'bookings_count' => 1,
            ]);
            $rooms = new Collection;
            foreach (['Deluxe Room', 'Family Suite'] as $j => $roomName) {
                $room = new Room;
                $room->forceFill([
                    'id' => $i * 2 + $j + 1, 'hotel_id' => $hotel->id, 'name' => $roomName,
                    'type' => $j ? 'family' : 'deluxe', 'capacity' => $j ? 4 : 2,
                    'price_per_night' => $price + $j * 300000, 'total_rooms' => 10, 'available_rooms' => 8,
                    'bed_count' => $j + 1, 'bed_type' => 'queen', 'has_wifi' => true,
                    'has_ac' => true, 'has_tv' => true, 'has_bathroom' => true, 'is_active' => true,
                ]);
                $room->setRelation('hotel', $hotel);
                $rooms->push($room);
                $this->rooms->push($room);
            }
            $hotel->setRelation('rooms', $rooms);
            $hotel->setRelation('images', new Collection);
            $hotel->setRelation('facilities', $this->facilities);
            $hotel->setRelation('owner', $this->users[1]);
            $hotel->setRelation('reviews', new Collection);
            $hotel->setRelation('bookings', new Collection);
            $hotel->setRelation('wishlists', new Collection);
            $this->hotels->push($hotel);
        }

        $this->bookings = new Collection;
        $this->reviews = new Collection;
        foreach ($this->hotels as $i => $hotel) {
            $review = new Review;
            $review->forceFill([
                'id' => $i + 1, 'user_id' => 1, 'hotel_id' => $hotel->id, 'rating' => 5,
                'title' => 'A wonderful stay', 'comment' => 'Comfortable rooms and a great location. We would love to come back!',
                'is_approved' => true, 'is_flagged' => false, 'created_at' => now()->subDays(2),
            ]);
            $review->setRelation('user', $this->users[0]);
            $review->setRelation('hotel', $hotel);
            $this->reviews->push($review);
            $hotel->setRelation('reviews', new Collection([$review]));

            $booking = new Booking;
            $booking->forceFill([
                'id' => $i + 1, 'booking_code' => 'BK-DEMO-00'.($i + 1), 'user_id' => 1,
                'hotel_id' => $hotel->id, 'room_id' => $hotel->rooms[0]->id,
                'guest_name' => 'Alex Traveler', 'guest_email' => 'customer@demo.example', 'guest_phone' => '081234567890',
                'check_in' => now()->addDays(1), 'check_out' => now()->addDays(3), 'nights' => 2, 'guests' => 2,
                'room_price' => $hotel->base_price, 'subtotal' => $hotel->base_price * 2,
                'discount_amount' => 0, 'tax_amount' => $hotel->base_price * 2 * .11,
                'total_amount' => $hotel->base_price * 2 * 1.11,
                'status' => ['pending', 'confirmed', 'checked_in', 'completed', 'cancelled', 'confirmed'][$i],
                'created_at' => now()->subDays($i),
            ]);
            $booking->setRelation('user', $this->users[0]);
            $booking->setRelation('hotel', $hotel);
            $booking->setRelation('room', $hotel->rooms[0]);
            $booking->setRelation('review', $i === 3 ? $review : null);
            $transaction = new Transaction;
            $transaction->forceFill([
                'id' => $i + 1, 'booking_id' => $booking->id, 'transaction_code' => 'TRX-DEMO-00'.($i + 1),
                'amount' => $booking->total_amount, 'payment_method' => 'bank_transfer',
                'status' => $i ? 'paid' : 'pending', 'created_at' => now(),
            ]);
            $booking->setRelation('transaction', $transaction);
            $hotel->setRelation('bookings', new Collection([$booking]));
            $this->bookings->push($booking);
        }
        $this->expenses = new Collection;
        $expense = new Expense;
        $expense->forceFill(['id' => 1, 'hotel_id' => 1, 'title' => 'Room maintenance', 'category' => 'maintenance', 'amount' => 250000, 'expense_date' => now(), 'created_at' => now()]);
        $expense->setRelation('hotel', $this->hotels[0]);
        $this->expenses->push($expense);

        $this->wishlists = new Collection;
        foreach ($this->hotels->take(2) as $hotel) {
            $wishlist = new Wishlist(['user_id' => 1, 'hotel_id' => $hotel->id]);
            $wishlist->setRelation('hotel', $hotel);
            $hotel->setRelation('wishlists', new Collection([$wishlist]));
            $this->wishlists->push($wishlist);
        }
        $this->users[0]->setRelation('bookings', $this->bookings);
        $this->users[1]->setRelation('hotels', $this->hotels);
    }

    public function user(string $role): User
    {
        return $this->users->first(fn (User $user) => $user->roles[0]->name === $role) ?? $this->users[0];
    }
}
