@if(config('demo.enabled'))
    <div class="bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 text-sm text-amber-900 m-4" role="status">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div><strong>Demo Mode</strong> &middot; Sample data. Actions are simulated; no reservations or payments are made.</div>
            <nav class="flex flex-wrap gap-4 font-semibold" aria-label="Demo role">
                <a class="underline" href="{{ route('demo.role', 'customer') }}">Customer</a>
                <a class="underline" href="{{ route('demo.role', 'hotel_owner') }}">Hotel Owner</a>
                <a class="underline" href="{{ route('demo.role', 'super_admin') }}">Admin</a>
            </nav>
        </div>
        @if(session('success'))
            <p class="mt-2">{{ session('success') }}</p>
        @endif
    </div>
@endif
