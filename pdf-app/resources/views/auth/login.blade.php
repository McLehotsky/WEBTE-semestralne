<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="flex justify-between mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-amber-700 shadow-sm focus:ring-amber-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('login.remember') }}</span>
            </label>
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                    {{ __('login.forgot-password') }}
                </a>
            @endif
        </div>

        <div class="flex items-center justify-between mt-4">
            <a href="{{ route('register') }}" class="ms-2 bg-amber-600 hover:bg-amber-800 text-white font-semibold py-2 px-4 rounded shadow focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500">
                        {{ __('button.register') }}
            </a>
            <button
            type="submit"
            class="ms-3 bg-amber-600 hover:bg-amber-800 text-white font-semibold py-2 px-4 rounded shadow focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500">
                {{ __('button.login') }}
            </button>
        </div>

    </form>

    <div class="flex justify-center mt-4">
        <a href="{{ route('google.login') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-md shadow-sm hover:bg-gray-100">
            <svg class="w-5 h-5 mr-2" viewBox="0 0 533.5 544.3" xmlns="http://www.w3.org/2000/svg">
                <path d="M533.5 278.4c0-17.4-1.6-34.1-4.7-50.3H272v95.1h147.1c-6.3 34.1-25.1 62.9-53.5 82.2v68h86.4c50.6-46.6 81.5-115.3 81.5-195z" fill="#4285F4"/>
                <path d="M272 544.3c72.9 0 134.1-24.2 178.7-65.7l-86.4-68c-24.1 16.3-55 25.9-92.3 25.9-71 0-131.2-47.9-152.8-112.1h-89.6v70.6c44.3 87.3 134.3 149.3 242.4 149.3z" fill="#34A853"/>
                <path d="M119.2 324.4c-10.5-31-10.5-64.4 0-95.4V158.4H29.6c-42.3 83.8-42.3 182.7 0 266.5l89.6-70.5z" fill="#FBBC05"/>
                <path d="M272 107.7c39.7 0 75.4 13.7 103.6 40.4l77.8-77.8C405.8 24.2 344.6 0 272 0 163.9 0 73.9 62 29.6 149.3l89.6 70.6C140.8 155.6 201 107.7 272 107.7z" fill="#EA4335"/>
            </svg>
            {{__('button.login-with-google')}}
        </a>
    </div>

</x-guest-layout>
