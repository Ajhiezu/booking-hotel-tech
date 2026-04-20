<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Facility;
use App\Models\Hotel;
use App\Models\HotelImage;
use App\Models\Review;
use App\Models\Room;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ─── Create Roles ────────────────────────────────────────────
        $roles = ['super_admin', 'hotel_owner', 'customer'];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }

        // ─── Super Admin ─────────────────────────────────────────────
        $admin = User::firstOrCreate(['email' => 'admin@stayease.com'], [
            'name'              => 'Super Admin',
            'password'          => Hash::make('password'),
            'phone'             => '08119999000',
            'is_active'         => true,
            'verification_status' => 'approved',
            'email_verified_at' => now(),
        ]);
        $admin->assignRole('super_admin');

        // ─── Hotel Owners ─────────────────────────────────────────────
        $ownersData = [
            ['name'=>'Budi Santoso',    'email'=>'budi@owner.com'],
            ['name'=>'Siti Rahayu',     'email'=>'siti@owner.com'],
            ['name'=>'Ahmad Fauzi',     'email'=>'ahmad@owner.com'],
        ];
        $owners = [];
        foreach ($ownersData as $od) {
            $owner = User::firstOrCreate(['email' => $od['email']], [
                'name'              => $od['name'],
                'password'          => Hash::make('password'),
                'phone'             => '081' . rand(100000000, 999999999),
                'is_active'         => true,
                'verification_status' => 'approved',
                'email_verified_at' => now(),
            ]);
            $owner->assignRole('hotel_owner');
            $owners[] = $owner;
        }

        // ─── Customers ────────────────────────────────────────────────
        $customersData = [
            ['name'=>'Rini Wulandari', 'email'=>'rini@customer.com'],
            ['name'=>'Doni Setiawan',  'email'=>'doni@customer.com'],
            ['name'=>'Maya Putri',     'email'=>'maya@customer.com'],
            ['name'=>'Eko Prasetyo',   'email'=>'eko@customer.com'],
        ];
        $customers = [];
        foreach ($customersData as $cd) {
            $customer = User::firstOrCreate(['email' => $cd['email']], [
                'name'              => $cd['name'],
                'password'          => Hash::make('password'),
                'phone'             => '081' . rand(100000000, 999999999),
                'is_active'         => true,
                'email_verified_at' => now(),
            ]);
            $customer->assignRole('customer');
            $customers[] = $customer;
        }

        // ─── Facilities ───────────────────────────────────────────────
        $facilitiesData = [
            ['name'=>'Free WiFi',       'icon'=>'wifi',       'category'=>'amenity'],
            ['name'=>'Swimming Pool',   'icon'=>'pool',       'category'=>'amenity'],
            ['name'=>'Fitness Center',  'icon'=>'gym',        'category'=>'amenity'],
            ['name'=>'Restaurant',      'icon'=>'restaurant', 'category'=>'service'],
            ['name'=>'Free Parking',    'icon'=>'parking',    'category'=>'service'],
            ['name'=>'24-Hour Front Desk','icon'=>'desk',     'category'=>'service'],
            ['name'=>'Airport Shuttle', 'icon'=>'shuttle',    'category'=>'service'],
            ['name'=>'Spa & Wellness',  'icon'=>'spa',        'category'=>'amenity'],
            ['name'=>'Bar & Lounge',    'icon'=>'bar',        'category'=>'service'],
            ['name'=>'Business Center', 'icon'=>'business',   'category'=>'service'],
            ['name'=>'Pet Friendly',    'icon'=>'pet',        'category'=>'policy'],
            ['name'=>'Non-Smoking',     'icon'=>'nosmoking',  'category'=>'policy'],
        ];
        foreach ($facilitiesData as $fd) {
            Facility::firstOrCreate(['name' => $fd['name']], $fd);
        }
        $facilities = Facility::all();

        // ─── Hotels ───────────────────────────────────────────────────
        $hotelsData = [
            [
                'name' => 'The Grand Horizon Hotel',
                'city' => 'Jakarta', 'province' => 'DKI Jakarta',
                'address' => 'Jl. Sudirman No. 100, Senayan', 'star_rating' => 5,
                'base_price' => 1500000, 'description' => 'A luxurious 5-star hotel in the heart of Jakarta with world-class amenities and stunning city views.',
                'owner_idx' => 0,
            ],
            [
                'name' => 'Bali Serenity Resort',
                'city' => 'Bali', 'province' => 'Bali',
                'address' => 'Jl. Legian No. 45, Kuta', 'star_rating' => 5,
                'base_price' => 2000000, 'description' => 'Experience the magic of Bali at this stunning beachfront resort with infinity pools and traditional spa treatments.',
                'owner_idx' => 1,
            ],
            [
                'name' => 'Jogja Heritage Hotel',
                'city' => 'Yogyakarta', 'province' => 'DI Yogyakarta',
                'address' => 'Jl. Malioboro No. 22', 'star_rating' => 4,
                'base_price' => 600000, 'description' => 'A charming boutique hotel near Malioboro Street, blending Javanese culture with modern comfort.',
                'owner_idx' => 2,
            ],
            [
                'name' => 'Surabaya Business Inn',
                'city' => 'Surabaya', 'province' => 'Jawa Timur',
                'address' => 'Jl. Ahmad Yani No. 55', 'star_rating' => 3,
                'base_price' => 350000, 'description' => 'A comfortable business hotel in the center of Surabaya, ideal for corporate travelers.',
                'owner_idx' => 0,
            ],
            [
                'name' => 'Bandung Mountain View',
                'city' => 'Bandung', 'province' => 'Jawa Barat',
                'address' => 'Jl. Dago Pakar No. 10', 'star_rating' => 4,
                'base_price' => 800000, 'description' => 'Enjoy the cool mountain air and spectacular views at this elegant hotel in Bandung\'s highlands.',
                'owner_idx' => 1,
            ],
        ];

        $hotels = [];
        foreach ($hotelsData as $hd) {
            $owner = $owners[$hd['owner_idx']];
            $slug  = Str::slug($hd['name']) . '-' . Str::random(5);
            $hotel = Hotel::firstOrCreate(['slug' => $slug], [
                'user_id'      => $owner->id,
                'name'         => $hd['name'],
                'slug'         => $slug,
                'description'  => $hd['description'],
                'address'      => $hd['address'],
                'city'         => $hd['city'],
                'province'     => $hd['province'],
                'country'      => 'Indonesia',
                'phone'        => '021' . rand(1000000, 9999999),
                'email'        => Str::lower(Str::slug($hd['name'])) . '@hotel.com',
                'star_rating'  => $hd['star_rating'],
                'base_price'   => $hd['base_price'],
                'status'       => 'approved',
                'check_in_time'  => '14:00',
                'check_out_time' => '12:00',
                'min_stay'     => 1,
                'is_featured'  => rand(0, 1),
                'rating_avg'   => rand(38, 50) / 10,
                'total_reviews' => rand(10, 80),
            ]);

            // Attach random facilities
            $hotel->facilities()->sync($facilities->random(rand(4, 8))->pluck('id'));

            $hotels[] = $hotel;
        }

        // ─── Rooms ────────────────────────────────────────────────────
        $roomTypes = [
            ['name'=>'Deluxe Room',    'type'=>'deluxe',   'capacity'=>2, 'price'=>800000,  'beds'=>1, 'bed_type'=>'king'],
            ['name'=>'Standard Room',  'type'=>'standard', 'capacity'=>2, 'price'=>500000,  'beds'=>1, 'bed_type'=>'double'],
            ['name'=>'Suite',          'type'=>'suite',    'capacity'=>3, 'price'=>1500000, 'beds'=>1, 'bed_type'=>'king'],
            ['name'=>'Family Room',    'type'=>'family',   'capacity'=>4, 'price'=>1200000, 'beds'=>2, 'bed_type'=>'twin'],
        ];

        foreach ($hotels as $hotel) {
            $selectedRooms = array_slice($roomTypes, 0, rand(2, 4));
            foreach ($selectedRooms as $rt) {
                $price = round(($rt['price'] * (0.7 + 0.6 * $hotel->base_price / 1000000)) / 50000) * 50000;
                Room::firstOrCreate(
                    ['hotel_id' => $hotel->id, 'name' => $rt['name']],
                    [
                        'type'           => $rt['type'],
                        'capacity'       => $rt['capacity'],
                        'price_per_night' => $price,
                        'weekend_price'  => $price * 1.2,
                        'total_rooms'    => rand(5, 20),
                        'available_rooms' => rand(3, 10),
                        'bed_type'       => $rt['bed_type'],
                        'bed_count'      => $rt['beds'],
                        'size_sqm'       => rand(20, 60),
                        'has_wifi'       => true,
                        'has_ac'         => true,
                        'has_tv'         => true,
                        'has_bathroom'   => true,
                        'has_balcony'    => rand(0, 1),
                        'is_active'      => true,
                        'description'    => 'A comfortable ' . $rt['type'] . ' room with all modern amenities.',
                    ]
                );
            }
        }

        // ─── Bookings + Transactions ──────────────────────────────────
        $statuses = ['confirmed', 'completed', 'pending', 'cancelled'];
        foreach ($customers as $customer) {
            $numBookings = rand(1, 3);
            for ($b = 0; $b < $numBookings; $b++) {
                $hotel   = $hotels[array_rand($hotels)];
                $room    = $hotel->rooms()->first();
                if (!$room) continue;

                $checkIn  = now()->subDays(rand(10, 60));
                $nights   = rand(1, 5);
                $checkOut = $checkIn->copy()->addDays($nights);
                $status   = $statuses[array_rand($statuses)];
                $subtotal = $room->price_per_night * $nights;
                $tax      = $subtotal * 0.11;
                $total    = $subtotal + $tax;

                $booking = Booking::create([
                    'user_id'         => $customer->id,
                    'hotel_id'        => $hotel->id,
                    'room_id'         => $room->id,
                    'check_in'        => $checkIn->toDateString(),
                    'check_out'       => $checkOut->toDateString(),
                    'nights'          => $nights,
                    'guests'          => rand(1, $room->capacity),
                    'room_price'      => $room->price_per_night,
                    'subtotal'        => $subtotal,
                    'discount_amount' => 0,
                    'tax_amount'      => $tax,
                    'total_amount'    => $total,
                    'status'          => $status,
                    'guest_name'      => $customer->name,
                    'guest_email'     => $customer->email,
                    'guest_phone'     => $customer->phone ?? '08112345678',
                ]);

                Transaction::create([
                    'booking_id'     => $booking->id,
                    'amount'         => $total,
                    'payment_method' => 'bank_transfer',
                    'status'         => in_array($status, ['confirmed','completed']) ? 'paid' : 'pending',
                    'paid_at'        => in_array($status, ['confirmed','completed']) ? now()->subDays(rand(1, 5)) : null,
                ]);

                // Add review for completed bookings
                if ($status === 'completed') {
                    $rating = rand(3, 5);
                    Review::create([
                        'user_id'            => $customer->id,
                        'hotel_id'           => $hotel->id,
                        'booking_id'         => $booking->id,
                        'rating'             => $rating,
                        'cleanliness_rating' => $rating,
                        'service_rating'     => max(1, $rating - rand(0,1)),
                        'location_rating'    => $rating,
                        'value_rating'       => max(1, $rating - rand(0,1)),
                        'title'              => ['Great stay!', 'Wonderful experience', 'Highly recommended', 'Good value', 'Will come back!'][rand(0,4)],
                        'comment'            => 'The hotel exceeded my expectations. The staff was very friendly and the facilities were excellent. I would definitely recommend this place to anyone visiting ' . $hotel->city . '.',
                        'is_approved'        => true,
                    ]);
                    $hotel->updateRatingAvg();
                }
            }
        }

        $this->command->info('✅ Database seeded successfully!');
        $this->command->table(
            ['Role', 'Email', 'Password'],
            [
                ['Super Admin',  'admin@stayease.com',  'password'],
                ['Hotel Owner',  'budi@owner.com',      'password'],
                ['Hotel Owner',  'siti@owner.com',      'password'],
                ['Customer',     'rini@customer.com',   'password'],
                ['Customer',     'doni@customer.com',   'password'],
            ]
        );
    }
}
