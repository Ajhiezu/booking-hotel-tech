@extends('layouts.owner')
@section('title', 'Dashboard')
@section('page-title', 'Getting Started')

@section('content')
    <div class="flex items-center justify-center min-h-[calc(100vh-80px)] px-4">
        <div class="w-full max-w-xl text-center bg-white border border-gray-200 rounded-3xl px-10 py-12 shadow-sm">

            <div class="flex flex-col items-center gap-8">

                {{-- Icon --}}
                <div
                    class="w-20 h-20 bg-gradient-to-br from-blue-600 to-indigo-700 text-white rounded-2xl flex items-center justify-center shadow-lg shadow-blue-600/30">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                        </path>
                    </svg>
                </div>

                <h2 class="text-3xl font-extrabold text-gray-900">
                    Welcome to StayEase
                </h2>

                <p class="text-gray-500 leading-relaxed max-w-md">
                    You're just one step away from managing your property! Register your hotel to unlock advanced booking
                    management, powerful dashboards, and millions of possible customers.
                </p>

                <a href="{{ route('owner.hotels.create') }}"
                    class="mt-4 inline-flex items-center justify-center px-8 py-3.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold shadow-md">
                    Register My Hotel Now
                </a>

            </div>

        </div>
    </div>
@endsection