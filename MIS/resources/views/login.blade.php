<x-app-layout>
    <div x-data="{ scrollY: 0, showForm: false }"
         x-init="() => {
         window.addEventListener('scroll', () => {
             scrollY = window.scrollY;
             showForm = scrollY > 50;
         });
     }"
         class="min-h-screen bg-cover bg-center relative overflow-y-auto"
         style="background-image: url('https://images.unsplash.com/photo-1718184021018-d2158af6b321?q=80&w=870&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D')"
    >
        <!-- Backdrop + content wrapper -->
        <div class="min-h-[120vh] backdrop-blur-md backdrop-brightness-50 flex flex-col items-center justify-start pt-[20vh] relative">

            <!-- Logo & Title -->
            <div class="flex flex-col items-center justify-center text-center transition-all duration-300 ease-in-out"
                 :class="{ 'opacity-0 scale-95': showForm, 'opacity-100 scale-100': !showForm }">

                <img src="{{ asset('images/logo.png') }}" alt="Logo"
                     class="w-32 h-32 rounded-full shadow-lg mb-4" />

                <h1 class="text-5xl font-bold text-white mt-6">
                    <span class="bg-clip-text text-transparent bg-gradient-to-r from-[#fd9c0a] to-[#ff6b00]">D7</span>Fashion
                </h1>

                <p class="text-white text-lg mt-2">
                    Garment Factory Management System
                </p>
            </div>


            <!-- Animated arrow -->
            <div @click="window.scrollTo({ top: 300, behavior: 'smooth' })"
                 class="absolute bottom-10 cursor-pointer transition-all duration-500 ease-in-out transform"
                 :class="{ 'opacity-50 scale-75': showForm, 'opacity-100 scale-100': !showForm }">
                <svg xmlns="http://www.w3.org/2000/svg" class="animate-pulse h-10 w-10 text-white" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </div>

            <!-- Login Form -->
            <div class="max-w-md w-full px-4 transition-all duration-500 ease-in-out mb-4"
                 :class="{ 'opacity-100 scale-100': showForm, 'opacity-0 scale-95': !showForm }"
            >
                <div class="bg-white rounded-lg shadow-lg p-6 md:p-8 mt-12">
                    <h2 class="text-2xl font-semibold text-[#0f2360] mb-6">Login to your account</h2>

                    <!-- Session Status -->
                    <x-auth-session-status class="mb-4" :status="session('status')" />

                    <!-- Validation Errors -->
                    @if ($errors->any())
                        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-4">
                            {{ implode(', ', $errors->all()) }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <!-- Email -->
                        <div class="mb-4">
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                            <input id="email" name="email" type="email" required autofocus
                                   class="w-full p-3 border border-gray-300 rounded-md focus:ring-[#0f2360] focus:border-[#0f2360]"
                                   placeholder="Enter your email" value="{{ old('email') }}">
                        </div>

                        <!-- Password -->
                        <div class="mb-6">
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                            <input id="password" name="password" type="password" required
                                   class="w-full p-3 border border-gray-300 rounded-md focus:ring-[#0f2360] focus:border-[#0f2360]"
                                   placeholder="Enter your password">
                        </div>

                        <!-- Remember Me -->
                        <div class="block mb-4">
                            <label for="remember_me" class="inline-flex items-center">
                                <input id="remember_me" type="checkbox"
                                       class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                       name="remember">
                                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                            </label>
                        </div>

                        <button type="submit"
                                class="w-full bg-[#fd9c0a] hover:bg-[#e08c09] text-white font-medium py-3 px-4 rounded-md flex items-center justify-center">
                            <svg class="mr-2 w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M5 12h14M12 5l7 7-7 7" />
                            </svg>
                            Login
                        </button>
                    </form>

                    @if (Route::has('password.request'))
                        <div class="text-right mt-4">
                            <a
                                x-data=""
                                x-on:click.prevent="$dispatch('open-modal', 'reset-password')"
                            ><u>Reset Password</u></a>

                            <x-modal name="reset-password" focusable>
                                @include('auth.forgot-password')
                            </x-modal>
                        </div>
                    @endif

                    <div class="mt-6 text-center">
                        <p class="text-sm text-gray-600">D7Fashion © {{ date('Y') }} All Rights Reserved</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

