<div
    x-data="{
        formData: {
            name: '',
            price: ''
        },
        errors: {},
        success: false,
        validate() {
            const newErrors = {};
            if (!this.formData.name.trim()) {
                newErrors.name = 'Name is required';
            }
            if (!this.formData.price.trim()) {
                newErrors.price = 'Hourly pay is required';
            } else if (isNaN(parseFloat(this.formData.price)) || parseFloat(this.formData.propertyIsEnumerable()) <= 0) {
                newErrors.price = 'Please enter a valid price';
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
                        price: ''
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
        <span>Internal product created successfully!</span>
    </div>

    <!-- Form -->
    <!-- Form commented out for UI testing -->
    <!-- <form method="POST" action="{/{ ro/ute('/In/ternalProduct.cre/ate') }}" @submit="handleSubmit"> -->
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

            <!-- price -->
            <div>
                <label for="price" class="block text-sm font-medium text-gray-700 mb-1">
                    Price (Rs)
                </label>
                <input
                    type="text"
                    id="Price"
                    name="Price"
                    x-model="formData.price"
                    class="block w-full border"
                    :class="{ 'border-red-500': errors.price, 'border-gray-300': !errors.price }"
                    class="rounded-md shadow-sm p-2 focus:ring-[#0f2360] focus:border-[#0f2360]"
                />
                <p x-show="errors.price" class="mt-1 text-sm text-red-500" x-text="errors.price"></p>
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
                Add Internal Product
            </button>
        </div>
    </div>
    <!-- </form> -->
</div>

