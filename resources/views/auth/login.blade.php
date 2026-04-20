<x-guest-layout>
    <div class="text-center mb-8">
        <h2 class="text-2xl font-bold text-gray-900">Welcome Back</h2>
        <p class="mt-1 text-sm text-gray-500">Sign in to your account to continue</p>
    </div>

    <x-auth-session-status class="mb-4 bg-green-50 text-green-600 px-4 py-3 rounded-lg text-sm text-center"
        :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <!-- Email -->
        <div>
            <label for="email" class="block text-sm font-semibold text-gray-800 mb-1.5">
                Email address
            </label>

            <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9" />
                    </svg>
                </span>

                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                    autocomplete="username" class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-lg text-sm
                           focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-gray-900 shadow-sm"
                    placeholder="john@example.com">
            </div>

            <x-input-error :messages="$errors->get('email')" class="mt-1 text-sm text-red-500" />
        </div>

        <!-- Password -->
        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label for="password" class="text-sm font-semibold text-gray-800">Password</label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-sm text-blue-600 hover:text-blue-500">
                        Forgot password?
                    </a>
                @endif
            </div>

            <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </span>

                <input id="password" type="password" name="password" required autocomplete="current-password" class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-lg text-sm
                           focus:border-blue-500 focus:ring-2 focus:ring-blue-100 text-gray-900 shadow-sm"
                    placeholder="••••••••">
            </div>

            <x-input-error :messages="$errors->get('password')" class="mt-1 text-sm text-red-500" />
        </div>

        <!-- Remember -->
        <div class="flex items-center">
            <input id="remember_me" type="checkbox" name="remember"
                class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
            <label for="remember_me" class="ml-2 text-sm text-gray-700">
                Remember me
            </label>
        </div>

        <button type="submit" class="w-full py-3 text-sm font-semibold text-white bg-blue-600 rounded-lg shadow-sm
                   hover:bg-blue-700 transition active:scale-[0.98]">
            Sign in securely
        </button>
    </form>

    <div class="mt-6 text-center text-sm text-gray-600">
        Don't have an account?
        <a href="{{ route('register') }}" class="font-semibold text-blue-600 hover:text-blue-500">
            Create one now
        </a>
    </div>
</x-guest-layout>