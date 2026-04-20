@extends('layouts.owner')
@section('title', 'Manage Bookings')
@section('page-title', 'Bookings')

@section('content')
<div class="bg-white rounded-2xl border border-gray-100 p-5 mb-6">
    <form action="{{ route('owner.bookings.index') }}" method="GET" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="text-xs font-semibold text-gray-500 block mb-1">Status</label>
            <select name="status" class="border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All Status</option>
                <option value="pending" {{ request('status')=='pending'?'selected':'' }}>Pending</option>
                <option value="confirmed" {{ request('status')=='confirmed'?'selected':'' }}>Confirmed</option>
                <option value="completed" {{ request('status')=='completed'?'selected':'' }}>Completed</option>
                <option value="cancelled" {{ request('status')=='cancelled'?'selected':'' }}>Cancelled</option>
            </select>
        </div>
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-sm font-semibold">Filter</button>
        <a href="{{ route('owner.bookings.index') }}" class="text-sm text-gray-500 hover:text-gray-700 py-2">Clear</a>
    </form>
</div>

<div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Code</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Guest</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Room</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Check-in / Out</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Total</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Status</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($bookings as $booking)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-mono text-xs text-blue-600">{{ $booking->booking_code }}</td>
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $booking->guest_name }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ $booking->room?->name }}</td>
                        <td class="px-6 py-4 text-gray-500">
                            <div>{{ $booking->check_in->format('d M') }} - {{ $booking->check_out->format('d M') }}</div>
                            <div class="text-xs">{{ $booking->nights }} nights</div>
                        </td>
                        <td class="px-6 py-4 font-semibold text-gray-900">Rp {{ number_format($booking->total_amount,0,',','.') }}</td>
                        <td class="px-6 py-4">
                            @php $bc = match($booking->status){'pending'=>'bg-yellow-100 text-yellow-700','confirmed'=>'bg-blue-100 text-blue-700','checked_in'=>'bg-indigo-100 text-indigo-700','completed'=>'bg-green-100 text-green-700','cancelled'=>'bg-red-100 text-red-700',default=>'bg-gray-100 text-gray-700'}; @endphp
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $bc }}">{{ ucfirst($booking->status) }}</span>
                        </td>
                        <td class="px-6 py-4">
                            @if($booking->status === 'pending')
                                <form method="POST" action="{{ route('owner.bookings.confirm', $booking) }}" class="inline">
                                    @csrf<button type="submit" class="text-xs text-green-600 hover:underline font-semibold mr-2">Confirm</button>
                                </form>
                            @endif
                            @if($booking->status === 'confirmed')
                                <form method="POST" action="{{ route('owner.bookings.check-in', $booking) }}" class="inline">
                                    @csrf<button type="submit" class="text-xs text-indigo-600 hover:underline font-semibold mr-2">Check In</button>
                                </form>
                            @endif
                            @if($booking->status === 'checked_in')
                                <form method="POST" action="{{ route('owner.bookings.complete', $booking) }}" class="inline">
                                    @csrf<button type="submit" class="text-xs text-green-600 hover:underline font-semibold mr-2">Complete</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center py-10 text-gray-400">No bookings found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 border-t border-gray-100">{{ $bookings->links() }}</div>
</div>
@endsection
