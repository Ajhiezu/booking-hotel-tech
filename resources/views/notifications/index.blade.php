@extends(auth()->user()->isSuperAdmin() ? 'layouts.admin' : (auth()->user()->isHotelOwner() ? 'layouts.owner' : 'layouts.app'))
@section('title', 'All Notifications')
@section('page-title', 'All Notifications')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Notifications</h1>
            <p class="text-sm text-gray-500 mt-1">View all your past and recent notifications.</p>
        </div>
        @if(auth()->user()->unreadNotifications->count() > 0)
            <form action="{{ route('notifications.mark-all-read') }}" method="POST">
                @csrf
                <button type="submit" class="bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 font-semibold px-4 py-2 rounded-xl text-sm transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Mark all as read
                </button>
            </form>
        @endif
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm">
        <div class="divide-y divide-gray-50">
            @forelse($notifications as $notification)
                <a href="{{ route('notifications.read', $notification->id) }}" 
                   class="group relative flex gap-5 p-5 transition-all hover:bg-gray-50 {{ $notification->read_at ? 'bg-white' : 'bg-blue-50/20' }}">
                   
                    @if(!$notification->read_at)
                        <div class="absolute left-0 top-0 w-1 h-full bg-blue-500"></div>
                    @endif

                    <div class="w-12 h-12 rounded-full flex-shrink-0 flex items-center justify-center {{ $notification->read_at ? 'bg-gray-100' : 'bg-blue-100 ring-4 ring-white' }}">
                        @php
                            $icon = $notification->data['icon'] ?? 'bell';
                            $iconColor = $notification->read_at ? 'text-gray-500' : 'text-blue-600';
                        @endphp
                        @if($icon === 'user-plus')
                            <svg class="w-6 h-6 {{ $iconColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                        @elseif($icon === 'office-building')
                            <svg class="w-6 h-6 {{ $iconColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        @elseif($icon === 'calendar-x')
                            <svg class="w-6 h-6 {{ $iconColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2zM9 13h6m-3-3v6"/></svg>
                        @elseif($icon === 'calendar-check')
                            <svg class="w-6 h-6 {{ $iconColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2zM16 11l-5 5-3-3"/></svg>
                        @else
                            <svg class="w-6 h-6 {{ $iconColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        @endif
                    </div>
                    
                    <div class="flex-1 min-w-0 pt-1">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-base font-semibold {{ $notification->read_at ? 'text-gray-700' : 'text-gray-900' }}">{{ $notification->data['title'] }}</p>
                                <p class="text-sm {{ $notification->read_at ? 'text-gray-500' : 'text-gray-600' }} mt-1 leading-relaxed">{{ $notification->data['message'] }}</p>
                            </div>
                            <span class="text-xs font-medium text-gray-400 whitespace-nowrap">{{ $notification->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </a>
            @empty
                <div class="px-6 py-16 text-center">
                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0a2 2 0 01-2 2H6a2 2 0 01-2-2m16 0l-2.586 2.586a1 1 0 01-1.414 0L15 13m-6 0l-2.586 2.586a1 1 0 01-1.414 0L5 13"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-1">No notifications yet</h3>
                    <p class="text-gray-500 text-sm">When you get notifications, they'll show up here.</p>
                </div>
            @endforelse
        </div>
        
        @if($notifications->hasPages())
            <div class="px-6 py-4 border-t border-gray-50 bg-gray-50/50">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
