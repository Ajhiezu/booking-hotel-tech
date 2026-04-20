@extends('layouts.admin')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard Overview')

@section('content')
{{-- Stats Grid --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    @php
        $statCards = [
            ['label' => 'Total Users',     'value' => number_format($stats['total_users']),     'color' => 'blue',   'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
            ['label' => 'Total Hotels',    'value' => number_format($stats['total_hotels']),    'color' => 'indigo', 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
            ['label' => 'Total Bookings',  'value' => number_format($stats['total_bookings']),  'color' => 'emerald','icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
            ['label' => 'Total Revenue',   'value' => 'Rp '.number_format($stats['total_revenue'],0,',','.'), 'color'=>'purple','icon'=>'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
        ];
    @endphp
    @foreach($statCards as $card)
        <div class="bg-white rounded-2xl border border-gray-100 p-6 flex items-center gap-4">
            <div class="w-12 h-12 bg-{{ $card['color'] }}-100 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-{{ $card['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}"/>
                </svg>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">{{ $card['label'] }}</p>
                <p class="text-2xl font-extrabold text-gray-900">{{ $card['value'] }}</p>
            </div>
        </div>
    @endforeach
</div>

{{-- Pending Approvals + Revenue Chart --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

    {{-- Revenue Chart --}}
    <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-base font-bold text-gray-900">Monthly Revenue ({{ now()->year }})</h2>
            <span class="text-sm text-gray-500">This Year</span>
        </div>
        <canvas id="revenueChart" class="w-full" height="200"></canvas>
    </div>

    {{-- Pending Owners --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-base font-bold text-gray-900">Pending Approvals</h2>
            <span class="inline-flex items-center justify-center w-6 h-6 bg-yellow-100 text-yellow-700 rounded-full text-xs font-bold">{{ $pendingOwners->count() }}</span>
        </div>
        @forelse($pendingOwners as $owner)
            <div class="flex items-center gap-3 py-3 border-b border-gray-50 last:border-0">
                <img src="{{ $owner->avatar_url }}" class="w-8 h-8 rounded-full object-cover flex-shrink-0" alt="">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 truncate">{{ $owner->name }}</p>
                    <p class="text-xs text-gray-400 truncate">{{ $owner->email }}</p>
                </div>
                <form method="POST" action="{{ route('admin.users.approve-owner', $owner) }}">
                    @csrf
                    <button type="submit" class="text-xs bg-green-100 text-green-700 hover:bg-green-200 px-2 py-1 rounded-lg transition-colors font-medium">Approve</button>
                </form>
            </div>
        @empty
            <p class="text-sm text-gray-400 text-center py-4">No pending approvals</p>
        @endforelse
        <a href="{{ route('admin.users.index',['role'=>'hotel_owner']) }}" class="block text-center text-sm text-blue-600 hover:text-blue-700 mt-4">View all owners →</a>
    </div>
</div>

{{-- Quick Stats Row --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
    <div class="bg-white rounded-2xl border border-gray-100 p-5">
        <p class="text-sm text-gray-500 mb-1">Approved Hotels</p>
        <p class="text-2xl font-bold text-green-600">{{ $stats['approved_hotels'] }}</p>
    </div>
    <div class="bg-white rounded-2xl border border-gray-100 p-5">
        <p class="text-sm text-gray-500 mb-1">Pending Hotels</p>
        <p class="text-2xl font-bold text-yellow-600">{{ $stats['pending_hotels'] }}</p>
    </div>
    <div class="bg-white rounded-2xl border border-gray-100 p-5">
        <p class="text-sm text-gray-500 mb-1">This Month Revenue</p>
        <p class="text-2xl font-bold text-blue-600">Rp {{ number_format($stats['this_month_revenue'],0,',','.') }}</p>
    </div>
</div>

{{-- Recent Bookings --}}
<div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
    <div class="flex items-center justify-between p-6 border-b border-gray-100">
        <h2 class="text-base font-bold text-gray-900">Recent Bookings</h2>
        <a href="{{ route('admin.bookings.index') }}" class="text-sm text-blue-600 hover:text-blue-700">View all →</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Code</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Guest</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Hotel</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Amount</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($recentBookings as $booking)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-3 font-mono text-xs text-blue-600">{{ $booking->booking_code }}</td>
                        <td class="px-6 py-3 text-gray-900">{{ $booking->user?->name }}</td>
                        <td class="px-6 py-3 text-gray-600">{{ $booking->hotel?->name }}</td>
                        <td class="px-6 py-3 font-semibold">Rp {{ number_format($booking->total_amount,0,',','.') }}</td>
                        <td class="px-6 py-3">
                            @php
                                $badgeClass = match($booking->status) {
                                    'pending'    => 'bg-yellow-100 text-yellow-700',
                                    'confirmed'  => 'bg-blue-100 text-blue-700',
                                    'completed'  => 'bg-green-100 text-green-700',
                                    'cancelled'  => 'bg-red-100 text-red-700',
                                    default      => 'bg-gray-100 text-gray-700',
                                };
                            @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $badgeClass }}">
                                {{ ucfirst($booking->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-3 text-gray-400 text-xs">{{ $booking->created_at->format('d M Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('revenueChart').getContext('2d');
    const monthlyData = @json($monthlyData);
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: monthlyData.map(d => d.month),
            datasets: [{
                label: 'Revenue (Rp)',
                data: monthlyData.map(d => d.revenue),
                backgroundColor: 'rgba(37, 99, 235, 0.15)',
                borderColor: 'rgba(37, 99, 235, 0.8)',
                borderWidth: 2,
                borderRadius: 6,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { callback: v => 'Rp ' + (v/1000000).toFixed(1) + 'M' } },
                x: { grid: { display: false } }
            }
        }
    });
</script>
@endpush
