<x-guest-layout>
    <h1 class="mb-6 text-center font-display text-2xl font-semibold text-navy-950">Administrator Login</h1>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" autocomplete="off">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="mt-1 block w-full border-navy-200 focus:border-gold-500 focus:ring-gold-500" type="email" name="email" required autofocus autocomplete="off" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="mt-1 block w-full border-navy-200 focus:border-gold-500 focus:ring-gold-500"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-4 block">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-navy-300 text-navy-800 shadow-sm focus:ring-gold-500" name="remember">
                <span class="ms-2 text-sm text-navy-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="mt-6 flex items-center justify-between">
            @if (Route::has('password.request'))
                <a class="rounded-md text-sm text-navy-500 underline hover:text-navy-800" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <button type="submit" class="rounded-lg bg-gold-400 px-6 py-2.5 font-semibold text-navy-950 transition hover:bg-gold-300">
                {{ __('Log in') }}
            </button>
        </div>
    </form>
</x-guest-layout>
