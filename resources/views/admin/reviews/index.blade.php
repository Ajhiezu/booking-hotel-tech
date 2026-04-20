@extends('layouts.admin')
@section('title', 'Manage Reviews')
@section('page-title', 'Global Reviews Management')

@section('content')
<div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Review</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Hotel</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Guest</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Date</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Status</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($reviews as $review)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-1 text-yellow-500 mb-1">
                                {{ $review->rating }} ★
                            </div>
                            <p class="font-medium text-gray-900 mb-1">{{ $review->title }}</p>
                            <p class="text-xs text-gray-500 line-clamp-2 max-w-sm">{{ $review->comment }}</p>
                        </td>
                        <td class="px-6 py-4"><a href="{{ route('admin.hotels.show', $review->hotel) }}" class="text-blue-600 hover:underline">{{ $review->hotel->name }}</a></td>
                        <td class="px-6 py-4">{{ $review->user->name }}</td>
                        <td class="px-6 py-4 text-gray-400 text-xs">{{ $review->created_at->format('d M Y') }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $review->is_approved ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                {{ $review->is_approved ? 'Approved' : 'Pending/Flagged' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                @if(!$review->is_approved)
                                    <form method="POST" action="{{ route('admin.reviews.approve', $review) }}">@csrf<button type="submit" class="text-xs text-green-600 hover:underline">Approve</button></form>
                                @endif
                                <form method="POST" action="{{ route('admin.reviews.destroy', $review) }}" 
                                      data-confirm="true" 
                                      data-confirm-title="Delete Review?" 
                                      data-confirm-text="This action cannot be undone. The review will be permanently removed." 
                                      data-confirm-type="danger" 
                                      data-confirm-button="Yes, delete review">
                                    @csrf @method('DELETE')<button type="submit" class="text-xs text-red-600 hover:underline">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center py-10 text-gray-400">No reviews found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 border-t border-gray-100">{{ $reviews->links() }}</div>
</div>
@endsection
