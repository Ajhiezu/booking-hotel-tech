@extends('layouts.admin')
@section('title', 'User Details')
@section('page-title', 'User Details: ' . $user->name)

@section('content')
<div class="max-w-4xl">
    <div class="mb-4">
        <a href="{{ route('admin.users.index') }}" class="text-sm text-blue-600 hover:text-blue-700 flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Users
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 p-6 flex items-start gap-6 mb-6">
        <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-24 h-24 rounded-full object-cover shadow-sm">
        <div class="flex-1">
            <div class="flex items-start justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h2>
                    <p class="text-sm text-gray-500 mb-2">{{ $user->email }}</p>
                    <div class="flex gap-2">
                        @foreach($user->roles as $role)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ ucwords(str_replace('_', ' ', $role->name)) }}
                            </span>
                        @endforeach
                    </div>
                </div>
                <div class="flex gap-2">
                    <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}">
                        @csrf
                        <button type="submit" class="px-4 py-2 rounded-xl text-sm font-semibold transition-colors {{ $user->is_active ? 'bg-red-50 text-red-600 hover:bg-red-100' : 'bg-green-50 text-green-600 hover:bg-green-100' }}">
                            {{ $user->is_active ? 'Deactivate Account' : 'Activate Account' }}
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-6 mt-6 pt-6 border-t border-gray-100">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Phone Number</p>
                    <p class="font-medium text-gray-900">{{ $user->phone ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 mb-1">Joined Date</p>
                    <p class="font-medium text-gray-900">{{ $user->created_at->format('j M Y, H:i') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 mb-1">Status</p>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $user->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                <div>
                    <p class="text-sm text-gray-500 mb-1">Verification Setup</p>
                    @if($user->isHotelOwner())
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $user->verification_status === 'approved' ? 'bg-green-100 text-green-800' : ($user->verification_status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                            {{ ucfirst($user->verification_status) }}
                        </span>
                    @else
                        <span class="text-gray-400 text-sm">N/A</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if($user->isHotelOwner() && $user->verification_status === 'unverified')
    <div class="bg-yellow-50 border border-yellow-200 rounded-2xl p-6 mb-6">
        <h3 class="text-lg font-bold text-yellow-800 mb-2">Pending Owner Verification</h3>
        <p class="text-sm text-yellow-700 mb-4">This user registered as a Hotel Owner and is awaiting approval.</p>
        <div class="flex gap-3">
            <form method="POST" action="{{ route('admin.users.approve-owner', $user) }}">
                @csrf<button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-2 rounded-lg text-sm">Approve Owner</button>
            </form>
            <form method="POST" action="{{ route('admin.users.reject-owner', $user) }}">
                @csrf<button type="submit" class="bg-red-100 hover:bg-red-200 text-red-700 font-semibold px-4 py-2 rounded-lg text-sm border border-red-200">Reject</button>
            </form>
        </div>
    </div>
    @endif
</div>
@endsection
