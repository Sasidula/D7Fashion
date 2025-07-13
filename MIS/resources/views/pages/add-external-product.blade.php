<div
    x-data="{
        formData: {
            name: '',
            bought_price: '',
            sold_price: ''
        },
        errors: {},
        success: false,
        validate() {
            const newErrors = {};
            if (!this.formData.name.trim()) {
                newErrors.name = 'Name is required';
            }
            if (!this.formData.bought_price.trim()) {
                newErrors.bought_price = 'bought_price is required';
            } else if (isNaN(parseFloat(this.formData.bought_price)) || parseFloat(this.formData.propertyIsEnumerable()) <= 0) {
                newErrors.bought_price = 'Please enter a valid bought_price';
            }
            if (!this.formData.sold_price.trim()) {
                newErrors.sold_price = 'sold_price is required';
            } else if (isNaN(parseFloat(this.formData.sold_price)) || parseFloat(this.formData.propertyIsEnumerable()) <= 0) {
                newErrors.sold_price = 'Please enter a sold_price';
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
                        bought_price: '',
                        sold_price: ''
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
    <!-- <form method="POST" action="{/{ ro/ute('E/xternalProduc/t.create/') }}" @submit="handleSubmit"> -->
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

            <!-- bought_price -->
            <div>
                <label for="bought price" class="block text-sm font-medium text-gray-700 mb-1">
                    Bought Price (Rs)
                </label>
                <input
                    type="text"
                    id="Price"
                    name="Price"
                    x-model="formData.bought_price"
                    class="block w-full border"
                    :class="{ 'border-red-500': errors.bought_price, 'border-gray-300': !errors.bought_price }"
                    class="rounded-md shadow-sm p-2 focus:ring-[#0f2360] focus:border-[#0f2360]"
                />
                <p x-show="errors.bought_price" class="mt-1 text-sm text-red-500" x-text="errors.bought_price"></p>
            </div>

            <!-- sold_price -->
            <div>
                <label for="sold price" class="block text-sm font-medium text-gray-700 mb-1">
                    Sold Price (Rs)
                </label>
                <input
                    type="text"
                    id="Price"
                    name="Price"
                    x-model="formData.sold_price"
                    class="block w-full border"
                    :class="{ 'border-red-500': errors.sold_price, 'border-gray-300': !errors.sold_price }"
                    class="rounded-md shadow-sm p-2 focus:ring-[#0f2360] focus:border-[#0f2360]"
                />
                <p x-show="errors.sold_price" class="mt-1 text-sm text-red-500" x-text="errors.sold_price"></p>
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
                Add  External Product
            </button>
        </div>
    </div>
    <!-- </form> -->
</div>
