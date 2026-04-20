<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-all duration-300 hover:-translate-y-1 group">
    {{-- Image --}}
    <div class="relative aspect-[4/3] overflow-hidden">
        @if($hotel->cover_image)
            <img src="{{ $hotel->cover_url }}" alt="{{ $hotel->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
        @elseif($hotel->images->first())
            <img src="{{ $hotel->images->first()->url }}" alt="{{ $hotel->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
        @else
            <div class="w-full h-full bg-gradient-to-br from-blue-100 to-indigo-100 flex items-center justify-center">
                <svg class="w-12 h-12 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
        @endif

        @if($hotel->is_featured)
            <div class="absolute top-3 left-3 bg-yellow-400 text-yellow-900 text-xs font-bold px-2 py-1 rounded-full">⭐ Featured</div>
        @endif

        {{-- Wishlist button --}}
        @auth
            @if(auth()->user()->isCustomer())
                <form method="POST" action="{{ route('customer.wishlist.toggle', $hotel) }}" class="absolute top-3 right-3">
                    @csrf
                    <button type="submit" class="w-8 h-8 bg-white/90 hover:bg-white rounded-full flex items-center justify-center shadow-md transition-all">
                        <svg class="w-4 h-4 {{ $hotel->isWishlistedByUser(auth()->id()) ? 'text-red-500 fill-red-500' : 'text-gray-400' }}" fill="{{ $hotel->isWishlistedByUser(auth()->id()) ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                    </button>
                </form>
            @endif
        @endauth
    </div>

    {{-- Content --}}
    <div class="p-4">
        <div class="flex items-start justify-between gap-2 mb-2">
            <h3 class="font-semibold text-gray-900 text-sm leading-tight line-clamp-2">{{ $hotel->name }}</h3>
            <div class="flex items-center gap-1 text-yellow-500 flex-shrink-0">
                <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                <span class="text-sm font-medium text-gray-700">{{ number_format($hotel->rating_avg, 1) }}</span>
            </div>
        </div>

        <div class="flex items-center gap-1 text-gray-500 text-xs mb-3">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
            </svg>
            {{ $hotel->city }}, {{ $hotel->province }}
        </div>

        <div class="flex items-center justify-between">
            <div>
                <span class="text-xs text-gray-400">From</span>
                <p class="text-blue-600 font-bold text-sm">
                    Rp {{ number_format($hotel->rooms->min('price_per_night') ?? $hotel->base_price, 0, ',', '.') }}
                    <span class="text-gray-400 font-normal">/night</span>
                </p>
            </div>
            <a href="{{ route('hotels.show', $hotel) }}"
               class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold px-3 py-1.5 rounded-lg transition-colors">
                View
            </a>
        </div>
    </div>
</div>
