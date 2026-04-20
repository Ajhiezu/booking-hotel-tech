<x-guest-layout>
    <div class="max-w-md mx-auto">

        <div class="text-center mb-8">
            <h2 class="text-2xl font-bold text-gray-900">Create an Account</h2>
            <p class="mt-1 text-sm text-gray-500">
                Join StayEase and start your journey
            </p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="space-y-5">
            @csrf

            <!-- Name -->
            <div>
                <label class="text-sm font-semibold text-gray-800 mb-1.5">Full Name</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </span>

                    <input type="text" name="name" value="{{ old('name') }}" required
                        class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-lg text-sm shadow-sm focus:ring-2 focus:ring-blue-100">
                </div>
                <x-input-error :messages="$errors->get('name')" class="mt-1 text-sm text-red-500" />
            </div>

            <!-- Phone -->
            <div>
                <label class="text-sm font-semibold text-gray-800 mb-1.5">Phone</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1" />
                        </svg>
                    </span>

                    <input type="text" name="phone" value="{{ old('phone') }}" required
                        class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-lg text-sm shadow-sm focus:ring-2 focus:ring-blue-100">
                </div>
                <x-input-error :messages="$errors->get('phone')" class="mt-1 text-sm text-red-500" />
            </div>

            <!-- Email -->
            <div>
                <label class="text-sm font-semibold text-gray-800 mb-1.5">Email</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                d="M16 12H8m8-4H8m8 8H8m-2 5h12a2 2 0 002-2V7a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </span>

                    <input type="email" name="email" value="{{ old('email') }}" required
                        class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-lg text-sm shadow-sm focus:ring-2 focus:ring-blue-100">
                </div>
                <x-input-error :messages="$errors->get('email')" class="mt-1 text-sm text-red-500" />
            </div>

            <!-- Password -->
            <div>
                <label class="text-sm font-semibold text-gray-800 mb-1.5">Password</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </span>

                    <input type="password" name="password" required
                        class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-lg text-sm shadow-sm focus:ring-2 focus:ring-blue-100">
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-1 text-sm text-red-500" />
            </div>

            <!-- Confirm -->
            <div>
                <label class="text-sm font-semibold text-gray-800 mb-1.5">Confirm Password</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                    </span>

                    <input type="password" name="password_confirmation" required
                        class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-lg text-sm shadow-sm focus:ring-2 focus:ring-blue-100">
                </div>
            </div>

            <!-- Role -->
            <div class="border-t pt-4">
                <label class="text-sm font-semibold text-gray-800 mb-2">Register as</label>
                <div class="grid grid-cols-2 gap-3">
                    <label
                        class="p-3 border rounded-lg cursor-pointer has-[:checked]:bg-blue-50 has-[:checked]:border-blue-600">
                        <input type="radio" name="role" value="customer" checked>
                        <span class="ml-2 text-sm">Guest</span>
                    </label>
                    <label
                        class="p-3 border rounded-lg cursor-pointer has-[:checked]:bg-blue-50 has-[:checked]:border-blue-600">
                        <input type="radio" name="role" value="hotel_owner">
                        <span class="ml-2 text-sm">Hotel Owner</span>
                    </label>
                </div>
            </div>

            <button type="submit"
                class="w-full py-3 text-sm font-semibold text-white bg-blue-600 rounded-lg shadow-sm hover:bg-blue-700">
                Create Account
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-gray-600">
            Already have an account?
            <a href="{{ route('login') }}" class="text-blue-600 font-semibold">Login</a>
        </p>

    </div>
</x-guest-layout>