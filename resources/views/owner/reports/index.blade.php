@extends('layouts.owner')
@section('title', 'Reports & Analytics')
@section('page-title', 'Financial Reports')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="bg-white rounded-2xl border border-gray-100 p-5">
        <p class="text-sm text-gray-500 mb-1">Total Revenue</p>
        <p class="text-2xl font-bold text-green-600">Rp {{ number_format($financials['revenue'] ?? 0, 0, ',', '.') }}</p>
    </div>
    <div class="bg-white rounded-2xl border border-gray-100 p-5">
        <p class="text-sm text-gray-500 mb-1">Total Expenses</p>
        <p class="text-2xl font-bold text-red-600">Rp {{ number_format($financials['expenses'] ?? 0, 0, ',', '.') }}</p>
    </div>
    <div class="bg-white rounded-2xl border border-gray-100 p-5">
        <p class="text-sm text-gray-500 mb-1">Net Profit</p>
        <p class="text-2xl font-bold text-blue-600">Rp {{ number_format(($financials['revenue'] ?? 0) - ($financials['expenses'] ?? 0), 0, ',', '.') }}</p>
    </div>
</div>

<div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
    <h3 class="font-bold text-lg mb-4 text-gray-900">Add New Expense</h3>
    <form action="{{ route('owner.reports.expense') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-4">
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1">Hotel</label>
                <select name="hotel_id" required class="w-full border-gray-200 rounded-xl focus:ring-blue-500 text-sm py-2">
                    @foreach(auth()->user()->hotels()->approved()->get() as $h)
                        <option value="{{ $h->id }}" {{ old('hotel_id') == $h->id ? 'selected' : '' }}>{{ $h->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1">Expense Title</label>
                <input type="text" name="title" value="{{ old('title') }}" placeholder="e.g. AC Repair" required 
                    class="w-full border-gray-200 rounded-xl focus:ring-blue-500 text-sm py-2 @error('title') border-red-500 @enderror">
                @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1">Category</label>
                <select name="category" required class="w-full border-gray-200 rounded-xl focus:ring-blue-500 text-sm py-2 @error('category') border-red-500 @enderror">
                    <option value="maintenance">Maintenance</option>
                    <option value="renovation">Renovation</option>
                    <option value="utilities">Utilities</option>
                    <option value="salary">Salary</option>
                    <option value="supplies">Supplies</option>
                    <option value="marketing">Marketing</option>
                    <option value="other">Other</option>
                </select>
                @error('category') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1">Amount (Rp)</label>
                <input type="number" name="amount" value="{{ old('amount') }}" required 
                    class="w-full border-gray-200 rounded-xl focus:ring-blue-500 text-sm py-2 @error('amount') border-red-500 @enderror">
                @error('amount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1">Expense Date</label>
                <input type="date" name="expense_date" value="{{ old('expense_date', date('Y-m-d')) }}" required 
                    class="w-full border-gray-200 rounded-xl focus:ring-blue-500 text-sm py-2 @error('expense_date') border-red-500 @enderror">
                @error('expense_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>
        <div class="flex justify-end mt-4">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded-xl text-sm transition-colors">
                Record Expense
            </button>
        </div>
    </form>
</div>

{{-- Recent Expenses --}}
<div class="mt-8 bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm">
    <div class="p-6 border-b border-gray-100 flex justify-between items-center">
        <h3 class="font-bold text-gray-900">Recent Expenses</h3>
        <a href="{{ route('owner.reports.pdf') }}" class="text-sm text-blue-600 hover:underline">Export PDF</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                <tr>
                    <th class="px-6 py-3 font-semibold">Date</th>
                    <th class="px-6 py-3 font-semibold">Title</th>
                    <th class="px-6 py-3 font-semibold">Category</th>
                    <th class="px-6 py-3 font-semibold text-right">Amount</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($expenses as $expense)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">{{ $expense->expense_date->format('d M Y') }}</td>
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $expense->title }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 rounded-lg text-xs font-medium bg-gray-100 text-gray-600 capitalize">
                                {{ $expense->category }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right font-bold text-red-600">Rp {{ number_format($expense->amount, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-gray-500">No expenses recorded yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($expenses->hasPages())
        <div class="p-4 border-t border-gray-100">
            {{ $expenses->links() }}
        </div>
    @endif
</div>
@endsection
