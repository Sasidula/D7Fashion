<div
    x-data="{
        formData: { id: 1, name: 'Cotton Fabric', price: '10.00', quantity: '500' },
        errors: {},
        success: false,
        validate() {
            const newErrors = {};
            if (!this.formData.name.trim()) newErrors.name = 'Name is required';
            if (!this.formData.price.trim()) {
                newErrors.price = 'Price is required';
            } else if (isNaN(parseFloat(this.formData.price)) || parseFloat(this.formData.price) <= 0) {
                newErrors.price = 'Please enter a valid price';
            }
            if (!this.formData.quantity.trim()) {
                newErrors.quantity = 'Quantity is required';
            } else if (isNaN(parseInt(this.formData.quantity)) || parseInt(this.formData.quantity) < 0) {
                newErrors.quantity = 'Please enter a valid quantity';
            }
            this.errors = newErrors;
            return Object.keys(newErrors).length === 0;
        },
        handleSubmit(event) {
            event.preventDefault();
            if (this.validate()) {
                console.log('Material updated:', this.formData);
                this.success = true;
                setTimeout(() => {
                    this.success = false;
                }, 3000);
            }
        }
    }"
    class="bg-white rounded-lg shadow-md p-6"
>
    <h1 class="text-2xl font-bold mb-6 text-[#0f2360]">Edit Material</h1>

    <!-- Success Message -->
    <div
        x-show="success"
        class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6 flex items-center"
        x-cloak
    >
        <x-lucide-check class="w-5 h-5 mr-2" />
        <span>Material updated successfully!</span>
    </div>

    <!-- Form -->
    <!-- <form method="POST" action="{/{ ro/ute('ma/terials.upd/ate') }}" @submit="handleSubmit"> -->
    <!-- @csrf -->
    <div class="max-w-2xl mx-auto">
        <input type="hidden" name="id" x-model="formData.id" />
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                    Material Name
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

            <!-- Price -->
            <div>
                <label for="price" class="block text-sm font-medium text-gray-700 mb-1">
                    Price ($)
                </label>
                <input
                    type="text"
                    id="price"
                    name="price"
                    x-model="formData.price"
                    class="block w-full border"
                    :class="{ 'border-red-500': errors.price, 'border-gray-300': !errors.price }"
                    class="rounded-md shadow-sm p-2 focus:ring-[#0f2360] focus:border-[#0f2360]"
                />
                <p x-show="errors.price" class="mt-1 text-sm text-red-500" x-text="errors.price"></p>
            </div>

            <!-- Quantity -->
            <div>
                <label for="quantity" class="block text-sm font-medium text-gray-700 mb-1">
                    Quantity
                </label>
                <input
                    type="number"
                    id="quantity"
                    name="quantity"
                    x-model="formData.quantity"
                    class="block w-full border"
                    :class="{ 'border-red-500': errors.quantity, 'border-gray-300': !errors.quantity }"
                    class="rounded-md shadow-sm p-2 focus:ring-[#0f2360] focus:border-[#0f2360]"
                />
                <p x-show="errors.quantity" class="mt-1 text-sm text-red-500" x-text="errors.quantity"></p>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="mt-8">
            <button
                type="submit"
                @click="handleSubmit"
                class="w-full bg-[#fd9c0a] text-white py-3 px-4 rounded-md hover:bg-[#e08c09] focus:outline-none flex items-center justify-center"
            >
                <x-lucide-check class="w-5 h-5 mr-2" />
                Update Material
            </button>
        </div>
    </div>
    <!-- </form> -->
</div>
