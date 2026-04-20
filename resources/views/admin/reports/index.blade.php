@extends('layouts.admin')
@section('title', 'Global Reports')
@section('page-title', 'Global System Reports')

@section('content')
<div class="bg-white rounded-2xl border border-gray-100 p-6 mb-6">
    <form action="{{ route('admin.reports.index') }}" method="GET" class="flex flex-wrap gap-4 items-end">
        <div>
            <label class="text-xs font-semibold text-gray-500 block mb-1">Date Range</label>
            <div class="flex items-center gap-2">
                <input type="date" name="from" value="{{ request('from') }}" class="border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                <span class="text-gray-400">to</span>
                <input type="date" name="to" value="{{ request('to') }}" class="border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
            </div>
        </div>
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-sm font-semibold">Generate Report</button>
        <div class="ml-auto flex gap-2">
            <a href="{{ route('admin.reports.pdf', request()->all()) }}" class="bg-red-50 text-red-600 px-4 py-2 rounded-xl text-sm font-semibold">Export PDF</a>
            <a href="{{ route('admin.reports.excel', request()->all()) }}" class="bg-green-50 text-green-600 px-4 py-2 rounded-xl text-sm font-semibold">Export Excel</a>
        </div>
    </form>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
        <p class="text-sm text-gray-500 mb-1">Total Bookings</p>
        <p class="text-2xl font-bold text-gray-900">{{ number_format($metrics['total_bookings'] ?? 0) }}</p>
        <div class="mt-2 flex gap-3 text-xs">
            <span class="text-green-600 font-medium">{{ $metrics['completed'] ?? 0 }} Completed</span>
            <span class="text-red-600 font-medium">{{ $metrics['cancelled'] ?? 0 }} Cancelled</span>
        </div>
    </div>
    <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
        <p class="text-sm text-gray-500 mb-1">Total Revenue</p>
        <p class="text-2xl font-bold text-green-600">Rp {{ number_format($metrics['total_revenue'] ?? 0, 0, ',', '.') }}</p>
    </div>
    <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
        <p class="text-sm text-gray-500 mb-1">Admin Income (10%)</p>
        <p class="text-2xl font-bold text-blue-600">Rp {{ number_format(($metrics['total_revenue'] ?? 0) * 0.10, 0, ',', '.') }}</p>
    </div>
    <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
        <p class="text-sm text-gray-500 mb-1">System Load</p>
        <p class="text-2xl font-bold text-gray-900">{{ $metrics['total_hotels'] ?? 0 }} Hotels</p>
        <p class="text-xs text-gray-500 mt-1">{{ $metrics['total_users'] ?? 0 }} Users registered</p>
    </div>
</div>

<div class="bg-white rounded-2xl border border-gray-100 p-6 mb-8 shadow-sm">
    <div class="flex items-center justify-between mb-6">
        <h3 class="font-bold text-gray-900">Monthly Revenue Breakdown ({{ $year }})</h3>
        <select onchange="window.location.href = '{{ route('admin.reports.index') }}?year=' + this.value" class="border-gray-200 rounded-xl text-sm px-3 py-1.5 focus:ring-blue-500">
            @for($y = now()->year; $y >= 2022; $y--)
                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endfor
        </select>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead>
                <tr class="text-gray-400 border-b border-gray-50">
                    <th class="pb-3 font-semibold uppercase text-xs">Month</th>
                    <th class="pb-3 font-semibold uppercase text-xs">Bookings</th>
                    <th class="pb-3 font-semibold uppercase text-xs text-right">Revenue</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($monthlyData as $row)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="py-4 font-medium text-gray-900">{{ $row['month'] }}</td>
                    <td class="py-4 text-gray-600">{{ $row['bookings'] }}</td>
                    <td class="py-4 text-right font-bold text-green-600">Rp {{ number_format($row['revenue'], 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm">
    <div class="p-6 border-b border-gray-100">
        <h3 class="font-bold text-gray-900">System Activity Logs</h3>
        <p class="text-xs text-gray-500 mt-1">Audit trail for administrative actions</p>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                <tr>
                    <th class="px-6 py-3 font-semibold">User</th>
                    <th class="px-6 py-3 font-semibold">Action</th>
                    <th class="px-6 py-3 font-semibold">Details</th>
                    <th class="px-6 py-3 font-semibold">IP</th>
                    <th class="px-6 py-3 font-semibold text-right">Time</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($auditLogs as $log)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 font-medium">{{ $log->user?->name ?? 'System' }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700">
                            {{ strtoupper($log->action) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-gray-600">{{ $log->details }}</td>
                    <td class="px-6 py-4 font-mono text-xs text-gray-400">{{ $log->ip_address }}</td>
                    <td class="px-6 py-4 text-right text-gray-500">{{ $log->created_at->diffForHumans() }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-10 text-center text-gray-500 italic">No logs found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-gray-100">
        {{ $auditLogs->links() }}
    </div>
</div>
@endsection
