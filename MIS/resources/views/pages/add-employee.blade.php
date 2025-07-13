<div
    x-data="{
        formData: {
            name: '',
            password: '',
            number: '',
            email: '',
            hourlyPay: ''
        },
        errors: {},
        success: false,
        validate() {
            const newErrors = {};
            if (!this.formData.name.trim()) {
                newErrors.name = 'Name is required';
            }
            if (!this.formData.password) {
                newErrors.password = 'Password is required';
            } else if (this.formData.password.length < 6) {
                newErrors.password = 'Password must be at least 6 characters';
            }
            if (!this.formData.number.trim()) {
                newErrors.number = 'Phone number is required';
            } else if (!/^\d{10}$/.test(this.formData.number.replace(/\D/g, ''))) {
                newErrors.number = 'Please enter a valid 10-digit phone number';
            }
            if (!this.formData.email.trim()) {
                newErrors.email = 'Email is required';
            } else if (!/\S+@\S+\.\S+/.test(this.formData.email)) {
                newErrors.email = 'Please enter a valid email address';
            }
            if (!this.formData.hourlyPay.trim()) {
                newErrors.hourlyPay = 'Hourly pay is required';
            } else if (isNaN(parseFloat(this.formData.hourlyPay)) || parseFloat(this.formData.hourlyPay) <= 0) {
                newErrors.hourlyPay = 'Please enter a valid hourly pay rate';
            }
            this.errors = newErrors;
            return Object.keys(newErrors).length === 0;
        },
        handleSubmit(event) {
            event.preventDefault();
            if (this.validate()) {
                console.log('Form data submitted:', this.formData);
                this.success = true;
                setTimeout(() => {
                    this.success = false;
                    this.formData = {
                        name: '',
                        password: '',
                        number: '',
                        email: '',
                        hourlyPay: ''
                    };
                    this.errors = {};
                }, 3000);
            }
        }
    }"
    class="bg-white rounded-lg shadow-md p-6"
>
    <h1 class="text-2xl font-bold mb-6 text-[#0f2360]">Create Employee</h1>

    <!-- Success Message -->
    <div
        x-show="success"
        class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6 flex items-center"
        x-cloak
    >
        <x-lucide-check class="w-5 h-5 mr-2" />
        <span>Employee created successfully!</span>
    </div>

    <!-- Form -->
    <!-- Form commented out for UI testing -->
    <!-- <form method="POST" action="{/{ r/oute('emp/loyee.c/reate') }}" @submit="handleSubmit"> -->
    <!-- @csrf -->
    <div class="max-w-2xl mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Full Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                    Full Name
                </label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    x-model="formData.name"
                    class="block w-full border"
                    :class="{ 'border-red-500': errors.name, 'border-gray-300': !errors.name }"
                    class="rounded-md shadow-sm p-2 focus:ring-[#0f2360] focus:border-[#0f2360]"
                />
                <p x-show="errors.name" class="mt-1 text-sm text-red-500" x-text="errors.name"></p>
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                    Password
                </label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    x-model="formData.password"
                    class="block w-full border"
                    :class="{ 'border-red-500': errors.password, 'border-gray-300': !errors.password }"
                    class="rounded-md shadow-sm p-2 focus:ring-[#0f2360] focus:border-[#0f2360]"
                />
                <p x-show="errors.password" class="mt-1 text-sm text-red-500" x-text="errors.password"></p>
            </div>

            <!-- Phone Number -->
            <div>
                <label for="number" class="block text-sm font-medium text-gray-700 mb-1">
                    Phone Number
                </label>
                <input
                    type="tel"
                    id="number"
                    name="number"
                    x-model="formData.number"
                    class="block w-full border"
                    :class="{ 'border-red-500': errors.number, 'border-gray-300': !errors.number }"
                    class="rounded-md shadow-sm p-2 focus:ring-[#0f2360] focus:border-[#0f2360]"
                />
                <p x-show="errors.number" class="mt-1 text-sm text-red-500" x-text="errors.number"></p>
            </div>

            <!-- Email Address -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                    Email Address
                </label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    x-model="formData.email"
                    class="block w-full border"
                    :class="{ 'border-red-500': errors.email, 'border-gray-300': !errors.email }"
                    class="rounded-md shadow-sm p-2 focus:ring-[#0f2360] focus:border-[#0f2360]"
                />
                <p x-show="errors.email" class="mt-1 text-sm text-red-500" x-text="errors.email"></p>
            </div>

            <!-- Hourly Pay Rate -->
            <div>
                <label for="hourlyPay" class="block text-sm font-medium text-gray-700 mb-1">
                    Hourly Pay Rate ($)
                </label>
                <input
                    type="text"
                    id="hourlyPay"
                    name="hourlyPay"
                    x-model="formData.hourlyPay"
                    class="block w-full border"
                    :class="{ 'border-red-500': errors.hourlyPay, 'border-gray-300': !errors.hourlyPay }"
                    class="rounded-md shadow-sm p-2 focus:ring-[#0f2360] focus:border-[#0f2360]"
                />
                <p x-show="errors.hourlyPay" class="mt-1 text-sm text-red-500" x-text="errors.hourlyPay"></p>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="mt-8">
            <button
                type="submit"
                @click="handleSubmit"
                class="w-full bg-[#fd9c0a] text-white py-3 px-4 rounded-md hover:bg-[#e08c09] focus:outline-none flex items-center justify-center"
            >
                <x-lucide-user-plus class="w-5 h-5 mr-2" />
                Create Employee
            </button>
        </div>
    </div>
    <!-- </form> -->
</div>
