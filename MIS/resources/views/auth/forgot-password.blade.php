<div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-auto p-8 space-y-6">
    <h2 class="text-xl font-semibold text-gray-800">
        {{ __('Forgot your password?') }}
    </h2>

    <p class="text-sm text-gray-600">
        {{ __('No problem. Just enter your email below and we will send you a password reset link.') }}
    </p>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email Address')" />
            <x-text-input
                id="email"
                class="block mt-1 w-full"
                type="email"
                name="email"
                :value="old('email')"
                required
                autofocus
            />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
            <x-secondary-button x-on:click="$dispatch('close')">
                {{ __('Cancel') }}
            </x-secondary-button>
            <x-primary-button>
                {{ __('Send Reset Link') }}
            </x-primary-button>
        </div>
    </form>
</div>
