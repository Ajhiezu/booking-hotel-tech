@extends('layouts.app')
@section('title', 'My Wishlist')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <h1 class="text-2xl font-bold text-gray-900 mb-8">My Wishlist</h1>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @forelse($wishlists as $wishlist)
            @include('customer.partials.hotel-card', ['hotel' => $wishlist->hotel])
        @empty
            <div class="col-span-full py-10 text-center text-gray-500">Your wishlist is empty.</div>
        @endforelse
    </div>
    <div class="mt-6">{{ $wishlists->links() }}</div>
</div>
@endsection
