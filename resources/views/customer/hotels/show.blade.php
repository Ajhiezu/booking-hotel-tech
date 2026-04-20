@extends('layouts.app')
@section('title', $hotel->name)
@section('meta_description', Str::limit($hotel->description, 160))

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm text-gray-500 mb-6">
        <a href="{{ route('home') }}" class="hover:text-blue-600">Home</a>
        <span>/</span>
        <a href="{{ route('hotels.search', ['location' => $hotel->city]) }}" class="hover:text-blue-600">{{ $hotel->city }}</a>
        <span>/</span>
        <span class="text-gray-900 font-medium">{{ $hotel->name }}</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- Left: Hotel Details --}}
        <div class="lg:col-span-2 space-y-8">

            {{-- Photo Gallery --}}
            <div class="grid grid-cols-4 grid-rows-2 gap-2 rounded-2xl overflow-hidden h-80">
                @php
                    $allImages = $hotel->images;
                    $hasCover = !empty($hotel->cover_image);
                    $totalPhotos = ($hasCover ? 1 : 0) + $allImages->count();
                @endphp

                @if($totalPhotos > 0)
                    {{-- Main Photo --}}
                    <div class="col-span-2 row-span-2">
                        @if($hasCover)
                            <img src="{{ $hotel->cover_url }}" alt="{{ $hotel->name }}" class="w-full h-full object-cover">
                        @else
                            <img src="{{ $allImages->first()->url }}" alt="{{ $hotel->name }}" class="w-full h-full object-cover">
                        @endif
                    </div>

                    {{-- Additional Photos --}}
                    @php 
                        $additionalImages = $hasCover ? $allImages->take(4) : $allImages->skip(1)->take(4);
                    @endphp
                    @foreach($additionalImages as $img)
                        <div><img src="{{ $img->url }}" alt="" class="w-full h-full object-cover"></div>
                    @endforeach

                    {{-- Fill empty slots if less than 5 photos total --}}
                    @for($i = $additionalImages->count(); $i < 4; $i++)
                        <div class="bg-gray-100 flex items-center justify-center">
                            <svg class="w-8 h-8 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    @endfor
                @else
                    {{-- No Photos Fallback --}}
                    <div class="col-span-4 row-span-2 bg-gradient-to-br from-blue-100 to-indigo-200 flex items-center justify-center">
                        <svg class="w-20 h-20 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                @endif
            </div>

            {{-- Hotel Header --}}
            <div>
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-yellow-500 text-sm">{{ $hotel->star_rating_stars }}</span>
                            <span class="text-xs text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full">{{ $hotel->star_rating }}-Star Hotel</span>
                        </div>
                        <h1 class="text-3xl font-extrabold text-gray-900 mb-2">{{ $hotel->name }}</h1>
                        <div class="flex items-center gap-1 text-gray-500 text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            </svg>
                            {{ $hotel->address }}, {{ $hotel->city }}, {{ $hotel->province }}
                        </div>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <div class="inline-flex items-center gap-1 bg-blue-600 text-white px-3 py-1 rounded-xl">
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            <span class="font-bold">{{ number_format($reviewStats['avg'], 1) }}</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">{{ $reviewStats['total'] }} reviews</p>
                    </div>
                </div>

                {{-- Wishlist --}}
                <div class="mt-3 flex gap-3">
                    @auth
                        @if(auth()->user()->isCustomer())
                            <form method="POST" action="{{ route('customer.wishlist.toggle', $hotel) }}">
                                @csrf
                                <button type="submit" class="flex items-center gap-2 text-sm {{ $isWishlisted ? 'text-red-500' : 'text-gray-500' }} hover:text-red-500 transition-colors border border-gray-200 rounded-xl px-3 py-1.5">
                                    <svg class="w-4 h-4 {{ $isWishlisted ? 'fill-current' : '' }}" fill="{{ $isWishlisted ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                    </svg>
                                    {{ $isWishlisted ? 'Saved' : 'Save to Wishlist' }}
                                </button>
                            </form>
                        @endif
                    @endauth
                </div>
            </div>

            {{-- Description --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-3">About This Hotel</h2>
                <p class="text-gray-600 leading-relaxed">{{ $hotel->description }}</p>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-6 pt-6 border-t border-gray-100">
                    <div class="text-center">
                        <svg class="w-6 h-6 text-blue-500 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-xs text-gray-500">Check-in</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $hotel->check_in_time }}</p>
                    </div>
                    <div class="text-center">
                        <svg class="w-6 h-6 text-blue-500 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-xs text-gray-500">Check-out</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $hotel->check_out_time }}</p>
                    </div>
                    <div class="text-center">
                        <svg class="w-6 h-6 text-blue-500 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <p class="text-xs text-gray-500">Min. Stay</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $hotel->min_stay }} night(s)</p>
                    </div>
                    <div class="text-center">
                        <svg class="w-6 h-6 text-blue-500 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-xs text-gray-500">Rooms</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $hotel->rooms->count() }} types</p>
                    </div>
                </div>
            </div>

            {{-- Facilities --}}
            @if($hotel->facilities->count())
            <div class="bg-white rounded-2xl border border-gray-100 p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Facilities & Amenities</h2>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    @foreach($hotel->facilities as $facility)
                        <div class="flex items-center gap-2 text-sm text-gray-700">
                            <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            {{ $facility->name }}
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Available Rooms --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Available Rooms</h2>
                <div class="space-y-4">
                    @forelse($hotel->rooms as $room)
                        <div class="border border-gray-100 rounded-xl p-4 hover:border-blue-200 hover:bg-blue-50/30 transition-all">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div class="flex gap-4">
                                    <div class="w-20 h-16 rounded-lg overflow-hidden flex-shrink-0">
                                        @if($room->cover_image)
                                            <img src="{{ $room->cover_url }}" alt="{{ $room->name }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full bg-blue-50 flex items-center justify-center">
                                                <svg class="w-8 h-8 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-gray-900">{{ $room->name }}</h3>
                                        <p class="text-xs text-gray-500 mt-0.5">{{ ucfirst($room->type) }} • {{ $room->capacity }} guests • {{ $room->bed_count }}x {{ $room->bed_type }} bed</p>
                                        <div class="flex items-center gap-3 mt-2 text-xs text-gray-500">
                                            @if($room->has_wifi)  <span class="flex items-center gap-1"><svg class="w-3 h-3 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg> WiFi</span> @endif
                                            @if($room->has_ac)    <span class="flex items-center gap-1"><svg class="w-3 h-3 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg> AC</span> @endif
                                            @if($room->has_tv)    <span class="flex items-center gap-1"><svg class="w-3 h-3 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg> TV</span> @endif
                                            @if($room->has_balcony)<span class="flex items-center gap-1"><svg class="w-3 h-3 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg> Balcony</span> @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right flex-shrink-0">
                                    <p class="text-blue-600 font-bold text-lg">Rp {{ number_format($room->price_per_night, 0, ',', '.') }}</p>
                                    <p class="text-xs text-gray-400">per night</p>
                                    @auth
                                        @if(auth()->user()->isCustomer())
                                            <a href="{{ route('customer.bookings.create', $room) }}"
                                               class="mt-2 inline-block bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
                                                Book Now
                                            </a>
                                        @endif
                                    @else
                                        <a href="{{ route('login') }}" class="mt-2 inline-block bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
                                            Login to Book
                                        </a>
                                    @endauth
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm text-center py-8">No rooms currently available.</p>
                    @endforelse
                </div>
            </div>

            {{-- Reviews --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Guest Reviews</h2>

                {{-- Summary --}}
                <div class="flex items-center gap-6 p-4 bg-blue-50 rounded-xl mb-6">
                    <div class="text-center">
                        <p class="text-4xl font-extrabold text-blue-600">{{ number_format($reviewStats['avg'], 1) }}</p>
                        <div class="flex items-center justify-center gap-0.5 text-yellow-400 mt-1">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="w-4 h-4 {{ $i <= round($reviewStats['avg']) ? 'fill-current' : 'text-gray-300' }}" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @endfor
                        </div>
                        <p class="text-xs text-blue-600 mt-1">{{ $reviewStats['total'] }} Reviews</p>
                    </div>
                    <div class="flex-1 space-y-1">
                        @foreach(range(5,1,-1) as $star)
                            <div class="flex items-center gap-2">
                                <span class="text-xs text-gray-500 w-3">{{ $star }}</span>
                                <div class="flex-1 bg-gray-200 rounded-full h-2">
                                    @php $pct = $reviewStats['total'] > 0 ? ($reviewStats['distribution'][$star] / $reviewStats['total']) * 100 : 0; @endphp
                                    <div class="bg-yellow-400 h-2 rounded-full" style="width:{{ $pct }}%"></div>
                                </div>
                                <span class="text-xs text-gray-400 w-4">{{ $reviewStats['distribution'][$star] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Review List --}}
                <div class="space-y-5">
                    @forelse($hotel->reviews->take(10) as $review)
                        <div class="border-b border-gray-100 pb-5 last:border-0">
                            <div class="flex items-center gap-3 mb-2">
                                <img src="{{ $review->user->avatar_url }}" alt="{{ $review->user->name }}" class="w-8 h-8 rounded-full object-cover">
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ $review->user->name }}</p>
                                    <p class="text-xs text-gray-400">{{ $review->created_at->format('d M Y') }}</p>
                                </div>
                                <div class="ml-auto flex items-center gap-0.5 text-yellow-400">
                                    @for($i=1; $i<=5; $i++)
                                        <svg class="w-3.5 h-3.5 {{ $i<=$review->rating ? 'fill-current' : 'text-gray-200' }}" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    @endfor
                                </div>
                            </div>
                            @if($review->title)<p class="text-sm font-semibold text-gray-900 mb-1">{{ $review->title }}</p>@endif
                            <p class="text-sm text-gray-600">{{ $review->comment }}</p>
                            @if($review->owner_reply)
                                <div class="mt-3 ml-4 bg-gray-50 rounded-xl p-3">
                                    <p class="text-xs font-semibold text-blue-600 mb-1">Owner's Reply</p>
                                    <p class="text-sm text-gray-600">{{ $review->owner_reply }}</p>
                                </div>
                            @endif
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm text-center py-6">No reviews yet. Be the first to review!</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Right: Booking Sidebar --}}
        <div class="lg:col-span-1">
            <div class="sticky top-24 bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <div class="flex items-baseline gap-1 mb-6">
                    <p class="text-3xl font-extrabold text-blue-600">
                        Rp {{ number_format($hotel->rooms->min('price_per_night') ?? $hotel->base_price, 0, ',', '.') }}
                    </p>
                    <span class="text-gray-400 text-sm">/night</span>
                </div>

                @auth
                    @if(auth()->user()->isCustomer() && $hotel->rooms->count())
                        <form action="#" method="GET" id="quick-book-form">
                            <div class="space-y-3 mb-4">
                                <div>
                                    <label class="text-xs font-semibold text-gray-500 block mb-1">Check-in</label>
                                    <input type="date" id="hero-checkin" name="check_in" min="{{ date('Y-m-d') }}" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="text-xs font-semibold text-gray-500 block mb-1">Check-out</label>
                                    <input type="date" id="hero-checkout" name="check_out" min="{{ date('Y-m-d', strtotime('+1 day')) }}" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="text-xs font-semibold text-gray-500 block mb-1">Select Room</label>
                                    <select name="room_id" id="room-select" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        @foreach($hotel->rooms as $room)
                                            <option value="{{ $room->id }}" data-price="{{ $room->price_per_night }}">{{ $room->name }} — Rp {{ number_format($room->price_per_night, 0, ',', '.') }}/night</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <a id="book-btn" href="#"
                               class="block text-center w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl transition-colors text-base">
                                Reserve Now
                            </a>
                        </form>
                        <p class="text-center text-xs text-gray-400 mt-2">Free cancellation within 24 hours</p>
                    @elseif($hotel->rooms->isEmpty())
                        <p class="text-center text-gray-500 text-sm">No rooms available at this time.</p>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="block text-center w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl transition-colors">
                        Login to Book
                    </a>
                    <p class="text-center text-xs text-gray-400 mt-2">Don't have an account? <a href="{{ route('register') }}" class="text-blue-600">Sign up</a></p>
                @endauth

                {{-- Contact --}}
                @if($hotel->phone || $hotel->email)
                <div class="mt-6 pt-6 border-t border-gray-100 space-y-2">
                    <p class="text-xs font-semibold text-gray-500">Contact Hotel</p>
                    @if($hotel->phone)
                        <a href="tel:{{ $hotel->phone }}" class="flex items-center gap-2 text-sm text-gray-600 hover:text-blue-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            {{ $hotel->phone }}
                        </a>
                    @endif
                    @if($hotel->email)
                        <a href="mailto:{{ $hotel->email }}" class="flex items-center gap-2 text-sm text-gray-600 hover:text-blue-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            {{ $hotel->email }}
                        </a>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Similar Hotels --}}
    @if($similarHotels->count())
    <div class="mt-12">
        <h2 class="text-xl font-bold text-gray-900 mb-6">Similar Hotels in {{ $hotel->city }}</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($similarHotels as $h)
                @include('customer.partials.hotel-card', ['hotel' => $h])
            @endforeach
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
    const rooms = @json($hotel->rooms->pluck('price_per_night', 'id'));
    const roomSelect = document.getElementById('room-select');
    const checkIn    = document.getElementById('hero-checkin');
    const bookBtn    = document.getElementById('book-btn');

    function updateBookBtn() {
        const roomId  = roomSelect?.value;
        const ci      = checkIn?.value;
        const co      = document.getElementById('hero-checkout')?.value;
        if (roomId) {
            let url = `/customer/book/${roomId}`;
            const params = new URLSearchParams();
            if (ci) params.set('check_in', ci);
            if (co) params.set('check_out', co);
            bookBtn.href = url + (params.toString() ? '?' + params : '');
        }
    }
    roomSelect?.addEventListener('change', updateBookBtn);
    checkIn?.addEventListener('change', updateBookBtn);
    document.getElementById('hero-checkout')?.addEventListener('change', updateBookBtn);
    updateBookBtn();
</script>
@endpush
@endsection
