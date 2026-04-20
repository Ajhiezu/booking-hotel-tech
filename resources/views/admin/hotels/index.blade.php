@extends('layouts.admin')
@section('title', 'Manage Hotels')
@section('page-title', 'Hotel Management')

@section('content')
<div class="bg-white rounded-2xl border border-gray-100 p-5 mb-6">
    <form action="{{ route('admin.hotels.index') }}" method="GET" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="text-xs font-semibold text-gray-500 block mb-1">Search</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Hotel name or city..."
                class="border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 w-64">
        </div>
        <div>
            <label class="text-xs font-semibold text-gray-500 block mb-1">Status</label>
            <select name="status" class="border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All</option>
                <option value="pending"  {{ request('status')=='pending'  ? 'selected':'' }}>Pending</option>
                <option value="approved" {{ request('status')=='approved' ? 'selected':'' }}>Approved</option>
                <option value="rejected" {{ request('status')=='rejected' ? 'selected':'' }}>Rejected</option>
                <option value="suspended"{{ request('status')=='suspended'? 'selected':'' }}>Suspended</option>
            </select>
        </div>
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-sm font-semibold">Filter</button>
        <a href="{{ route('admin.hotels.index') }}" class="text-sm text-gray-500 py-2">Clear</a>
    </form>
</div>

<div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Hotel</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Owner</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">City</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Rating</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Status</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($hotels as $hotel)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-blue-100 rounded-xl overflow-hidden flex-shrink-0">
                                    @if($hotel->cover_image)
                                        <img src="{{ $hotel->cover_url }}" alt="" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center">
                                            <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $hotel->name }}</p>
                                    <p class="text-xs text-gray-400">{{ $hotel->star_rating }}★</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-600">{{ $hotel->owner?->name }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ $hotel->city }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-1">
                                <svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                <span class="text-sm font-medium">{{ number_format($hotel->rating_avg, 1) }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $sc = match($hotel->status) {
                                    'pending'   => 'bg-yellow-100 text-yellow-700',
                                    'approved'  => 'bg-green-100 text-green-700',
                                    'rejected'  => 'bg-red-100 text-red-700',
                                    'suspended' => 'bg-orange-100 text-orange-700',
                                    default     => 'bg-gray-100 text-gray-700',
                                };
                            @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $sc }}">
                                {{ ucfirst($hotel->status) }}
                            </span>
                            @if($hotel->is_flagged)
                                <span class="ml-1 inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-600">🚩</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2 flex-wrap">
                                <a href="{{ route('admin.hotels.show', $hotel) }}" class="text-xs text-blue-600 hover:underline">View</a>
                                @if($hotel->status === 'pending')
                                    <form method="POST" action="{{ route('admin.hotels.approve', $hotel) }}"><@csrf<button type="submit" class="text-xs text-green-600 hover:underline font-semibold">Approve</button></form>
                                @endif
                                @if(in_array($hotel->status, ['pending','approved']))
                                    <form method="POST" action="{{ route('admin.hotels.feature', $hotel) }}"><@csrf<button type="submit" class="text-xs text-indigo-600 hover:underline">{{ $hotel->is_featured ? 'Unfeature' : 'Feature' }}</button></form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center py-10 text-gray-400">No hotels found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 border-t border-gray-100">{{ $hotels->links() }}</div>
</div>
@endsection
