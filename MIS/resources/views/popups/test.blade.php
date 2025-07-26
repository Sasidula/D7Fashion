<!-- resources/views/components/popup.blade.php -->
<x-app-layout>
    <div
        x-transition.opacity
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
    >
        <div class="bg-white rounded-lg shadow-lg w-full max-w-3xl mx-4 overflow-hidden">
            <div class="max-h-[90vh] overflow-y-auto rounded-lg scrollbar-thin">
                <!-- Popup Header -->
                <div class="flex items-center justify-between p-4 border-b border-gray-200 sticky top-0 bg-white z-10">
                    <button @click="popup.open = false; popup.requestId++"  class="text-gray-600 hover:text-gray-900">
                        <x-lucide-arrow-left class="w-6 h-6" />
                    </button>
                    <h2 class="text-lg font-semibold text-gray-800" x-text="register">Popup</h2>
                    <div class="w-6"></div>
                </div>

                <!-- Dynamic Popup Content -->
                <div class="p-6">
                    @if (session('success'))
                        <div class="mb-4 text-green-600 bg-green-100 border border-green-300 rounded p-3">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mb-4 text-red-600 bg-red-100 border border-red-300 rounded p-3">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form method="POST" action="{{ route('profile.store') }}">
                        @csrf
                        <!-- Name -->
                        <div class="mb-4">
                            <x-input-label for="name" :value="__('Name')" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" required autofocus :value="old('name')" />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Email -->
                        <div class="mb-4">
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" required :value="old('email')" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <!-- Phone Number -->
                        <div class="mb-4">
                            <x-input-label for="phone_number" :value="__('Phone Number')" />
                            <x-text-input id="phone_number" name="phone_number" type="text" class="mt-1 block w-full" :value="old('phone_number')" />
                            <x-input-error :messages="$errors->get('phone_number')" class="mt-2" />
                        </div>

                        <!-- Role -->
                        <div class="mb-4">
                            <x-input-label for="role" :value="__('Role')" />
                            <select id="role" name="role" class="mt-1 block w-full rounded border-gray-300">
                                <!--<option value="admin" {/{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>-->
                                <option value="manager" {{ old('role') == 'manager' ? 'selected' : '' }}>Manager</option>
                                <!--<option value="employee" {/{ old('role') == 'employee' ? 'selected' : '' }}>Employee</option>-->
                            </select>
                            <x-input-error :messages="$errors->get('role')" class="mt-2" />
                        </div>

                        <!-- Salary Type -->
                        <div class="mb-4">
                            <x-input-label for="salary_type" :value="__('Salary Type')" />
                            <select id="salary_type" name="salary_type" class="mt-1 block w-full rounded border-gray-300">
                                <option value="none" {{ old('salary_type') == 'none' ? 'selected' : '' }}>None</option>
                                <option value="monthly" {{ old('salary_type') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                <option value="hourly" {{ old('salary_type') == 'hourly' ? 'selected' : '' }}>Hourly</option>
                            </select>
                            <x-input-error :messages="$errors->get('salary_type')" class="mt-2" />
                        </div>

                        <!-- Salary Amount -->
                        <div class="mb-4">
                            <x-input-label for="salary_amount" :value="__('Salary Amount')" />
                            <x-text-input id="salary_amount" name="salary_amount" type="number" step="0.01" class="mt-1 block w-full" :value="old('salary_amount')" />
                            <x-input-error :messages="$errors->get('salary_amount')" class="mt-2" />
                        </div>

                        <!-- Password -->
                        <div class="mb-4">
                            <x-input-label for="password" :value="__('Password')" />
                            <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" required />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <!-- Confirm Password -->
                        <div class="mb-4">
                            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                            <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" required />
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                        </div>

                        <!-- Submit -->
                        <div class="flex justify-end mt-6">
                            <x-primary-button>
                                {{ __('Create User') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
