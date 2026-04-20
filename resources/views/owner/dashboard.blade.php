@extends('layouts.owner')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
    @if(!isset($hotel))
        <div class="max-w-lg mx-auto text-center py-20">
            <div class="w-20 h-20 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.834-1.964-.834-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 mb-3">Account Pending Approval</h2>
            <p class="text-gray-500 mb-6">Your hotel owner account is awaiting admin verification. Once approved, you can add
                your hotel and start managing bookings.</p>
            <a href="{{ route('owner.hotels.create') }}"
                class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-xl transition-colors">Add Your
                Hotel</a>
        </div>
    @else

        {{-- Stats --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
            @php
                $ownerStats = [
                    ['label' => 'Total Bookings', 'value' => $stats['total_bookings'], 'color' => 'blue', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                    ['label' => 'Pending', 'value' => $stats['pending_bookings'], 'color' => 'yellow', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                    ['label' => 'Total Revenue', 'value' => 'Rp ' . number_format($stats['total_revenue'], 0, ',', '.'), 'color' => 'green', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                    ['label' => 'Rating', 'value' => number_format($stats['rating_avg'], 1) . '★', 'color' => 'purple', 'icon' => 'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z'],
                ];
            @endphp
            @foreach($ownerStats as $s)
                <div class="bg-white rounded-2xl border border-gray-100 p-5 flex items-center gap-4">
                    <div class="w-11 h-11 bg-{{ $s['color'] }}-100 rounded-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-{{ $s['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $s['icon'] }}" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-medium">{{ $s['label'] }}</p>
                        <p class="text-xl font-extrabold text-gray-900">{{ $s['value'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Charts + Financial --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 p-6">
                <h2 class="text-base font-bold text-gray-900 mb-4">Monthly Revenue vs Expenses</h2>
                <canvas id="ownerChart" height="220"></canvas>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 p-6">
                <h2 class="text-base font-bold text-gray-900 mb-4">This Month Summary</h2>
                <div class="space-y-4">
                    <div class="flex justify-between items-center py-3 border-b border-gray-50">
                        <span class="text-sm text-gray-500">Revenue</span>
                        <span class="font-bold text-green-600">Rp {{ number_format($financials['revenue'], 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center py-3 border-b border-gray-50">
                        <span class="text-sm text-gray-500">Expenses</span>
                        <span class="font-bold text-red-500">Rp {{ number_format($financials['expenses'], 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center py-3">
                        <span class="text-sm font-semibold text-gray-900">Net Profit</span>
                        <span class="font-extrabold text-blue-600">Rp
                            {{ number_format($financials['profit'], 0, ',', '.') }}</span>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-gray-100 space-y-3">
                    <a href="{{ route('owner.hotels.rooms.create', $hotel) }}"
                        class="flex items-center gap-2 text-sm text-blue-600 hover:text-blue-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add New Room
                    </a>
                    <a href="{{ route('owner.reports.index') }}"
                        class="flex items-center gap-2 text-sm text-blue-600 hover:text-blue-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        View Full Report
                    </a>
                </div>
            </div>
        </div>

        {{-- Recent Bookings --}}
        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
            <div class="flex items-center justify-between p-6 border-b border-gray-100">
                <h2 class="text-base font-bold text-gray-900">Recent Bookings</h2>
                <a href="{{ route('owner.bookings.index') }}" class="text-sm text-blue-600 hover:text-blue-700">View all →</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Code</th>
                            <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Guest</th>
                            <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Room</th>
                            <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Check-in</th>
                            <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Amount</th>
                            <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Status</th>
                            <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($recentBookings as $booking)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-3 font-mono text-xs text-blue-600">{{ $booking->booking_code }}</td>
                                <td class="px-6 py-3 font-medium text-gray-900">{{ $booking->guest_name }}</td>
                                <td class="px-6 py-3 text-gray-600">{{ $booking->room?->name }}</td>
                                <td class="px-6 py-3 text-gray-500">{{ $booking->check_in->format('d M Y') }}</td>
                                <td class="px-6 py-3 font-semibold">Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</td>
                                <td class="px-6 py-3">
                                    @php $bc = match ($booking->status) { 'pending' => 'bg-yellow-100 text-yellow-700', 'confirmed' => 'bg-blue-100 text-blue-700', 'completed' => 'bg-green-100 text-green-700', 'cancelled' => 'bg-red-100 text-red-700', default => 'bg-gray-100 text-gray-700'}; @endphp
                                    <span
                                        class="px-2 py-0.5 rounded-full text-xs font-medium {{ $bc }}">{{ ucfirst($booking->status) }}</span>
                                </td>
                                <td class="px-6 py-3">
                                    @if($booking->isPending())
                                        <form method="POST" action="{{ route('owner.bookings.confirm', $booking) }}" class="inline">
                                            @csrf
                                            <button type="submit"
                                                class="text-xs bg-green-100 text-green-700 hover:bg-green-200 px-2 py-1 rounded-lg font-medium">Confirm</button>
                                        </form>
                                    @else
                                        <a href="{{ route('owner.bookings.show', $booking) }}"
                                            class="text-xs text-blue-600 hover:underline">View</a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-8 text-gray-400">No bookings yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('ownerChart')?.getContext('2d');
        if (ctx) {
            const data = @json($monthlyData ?? []);
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.map(d => d.month),
                    datasets: [
                        { label: 'Revenue', data: data.map(d => d.revenue), borderColor: '#2563eb', backgroundColor: 'rgba(37,99,235,0.1)', fill: true, tension: 0.4, borderWidth: 2 },
                        { label: 'Expenses', data: data.map(d => d.expenses), borderColor: '#ef4444', backgroundColor: 'rgba(239,68,68,0.05)', fill: true, tension: 0.4, borderWidth: 2 },
                    ]
                },
                options: {
                    responsive: true,
                    interaction: { mode: 'index', intersect: false },
                    plugins: { legend: { position: 'top' } },
                    scales: {
                        y: { beginAtZero: true, ticks: { callback: v => 'Rp ' + (v / 1000000).toFixed(1) + 'M' } },
                        x: { grid: { display: false } }
                    }
                }
            });
        }
    </script>
@endpush