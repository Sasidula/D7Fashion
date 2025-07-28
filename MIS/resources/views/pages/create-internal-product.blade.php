<x-app-layout>
    <div
        x-data="layoutHandler()"
        x-init="init()"
        class="h-screen flex flex-col overflow-hidden bg-white"
    >
        <!-- Header -->
        @include('components.header')

        <div class="flex-1 flex overflow-hidden">
            <!-- Sidebar -->
            @include('components.sidebar', ['currentPage' => 'create-internal-product'])

            <!-- Overlay (Mobile Only) -->
            <div
                x-show="sidebarOpen"
                x-transition:enter="transition ease-in-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in-out duration-300"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 top-16 bg-black bg-opacity-40 z-20 md:hidden"
                @click="sidebarOpen = false"
            ></div>

            <!-- Page content wrapper -->
            <div class="flex-1 overflow-y-auto transition-all duration-300 ease-in-out">
                <main class="p-6">
                    <div
                        x-data="createInternalProductHandler()"
                        class="bg-white rounded-lg shadow-md p-6"
                    >
                        <h1 class="text-2xl font-bold mb-6 text-[#0f2360]">Create Internal Product</h1>

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
                        <form method="POST" action="{{ route('InternalProducts.store') }}" @submit="handleSubmit">
                            @csrf
                            <div class="max-w-2xl mx-auto">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Name -->
                                    <div>
                                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                                        <input
                                            type="text"
                                            id="name"
                                            name="name"
                                            x-model="formData.name"
                                            class="block w-full border rounded-md shadow-sm p-2 focus:ring-[#0f2360] focus:border-[#0f2360]"
                                            :class="{ 'border-red-500': errors.name, 'border-gray-300': !errors.name }"
                                        />
                                        <p x-show="errors.name" class="mt-1 text-sm text-red-500" x-text="errors.name"></p>
                                    </div>

                                    <!-- Price -->
                                    <div>
                                        <label for="price" class="block text-sm font-medium text-gray-700 mb-1">Price (Rs)</label>
                                        <input
                                            type="text"
                                            id="price"
                                            name="price"
                                            x-model="formData.price"
                                            class="block w-full border rounded-md shadow-sm p-2 focus:ring-[#0f2360] focus:border-[#0f2360]"
                                            :class="{ 'border-red-500': errors.price, 'border-gray-300': !errors.price }"
                                        />
                                        <p x-show="errors.price" class="mt-1 text-sm text-red-500" x-text="errors.price"></p>
                                    </div>

                                    <!-- SKU Code -->
                                    <div class="md:col-span-2">
                                        <label for="sku_code" class="block text-sm font-medium text-gray-700 mb-1">SKU Code (optional)</label>
                                        <input
                                            type="text"
                                            id="sku_code"
                                            name="sku_code"
                                            x-model="formData.sku_code"
                                            class="block w-full border rounded-md shadow-sm p-2 focus:ring-[#0f2360] focus:border-[#0f2360]"
                                            :class="{ 'border-red-500': errors.sku_code, 'border-gray-300': !errors.sku_code }"
                                        />
                                        <p x-show="errors.sku_code" class="mt-1 text-sm text-red-500" x-text="errors.sku_code"></p>
                                    </div>

                                    <!-- Description -->
                                    <div class="md:col-span-2">
                                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                        <textarea
                                            id="description"
                                            name="description"
                                            x-model="formData.description"
                                            rows="4"
                                            class="block w-full border rounded-md shadow-sm p-2 focus:ring-[#0f2360] focus:border-[#0f2360]"
                                        ></textarea>
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div class="mt-8">
                                    <button
                                        type="submit"
                                        class="w-full bg-[#fd9c0a] text-white py-3 px-4 rounded-md hover:bg-[#e08c09] focus:outline-none flex items-center justify-center"
                                    >
                                        <x-lucide-user-plus class="w-5 h-5 mr-2" />
                                        Add Internal Product
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </main>
            </div>



        </div>

        <!-- Popup -->
        @include('components.popup')

    </div>
    <script>
        function createInternalProductHandler() {
            return {
                formData: {
                    name: '',
                    price: '',
                    sku_code: '',
                    description: '',
                },
                errors: {},
                success: false,
                validate() {
                    const newErrors = {};
                    if (!this.formData.name.trim()) {
                        newErrors.name = 'Name is required';
                    }
                    if (!this.formData.price.trim()) {
                        newErrors.price = 'Price is required';
                    } else if (isNaN(parseFloat(this.formData.price)) || parseFloat(this.formData.price) <= 0) {
                        newErrors.price = 'Please enter a valid price';
                    }
                    if (this.formData.sku_code && this.formData.sku_code.length > 100) {
                        newErrors.sku_code = 'SKU code must be less than 100 characters';
                    }
                    this.errors = newErrors;
                    return Object.keys(newErrors).length === 0;
                },
                handleSubmit(event) {
                    event.preventDefault();
                    if (this.validate()) {
                        $el.submit(); // submit real form if validation passes
                    }
                }
            };
        }
        window.layoutHandler = () => ({
            sidebarOpen: JSON.parse(localStorage.getItem('sidebarOpen')) || false,

            popup: {
                open: false,
                title: '',
                content: '',
                requestId: 0,
            },

            toggleSidebar() {
                this.sidebarOpen = !this.sidebarOpen;
                localStorage.setItem('sidebarOpen', JSON.stringify(this.sidebarOpen));
            },

            init() {
                this.$watch('sidebarOpen', value => {
                    localStorage.setItem('sidebarOpen', JSON.stringify(value));
                });

                window.addEventListener('popup-open', (e) => {
                    const { title, view } = e.detail;
                    this.open(title, view);
                });
            },

            async open(title, bladeRoute) {
                this.popup.requestId++; // 🆕 Increment ID for new request
                const currentId = this.popup.requestId;

                this.popup.title = title;
                this.popup.content = 'Loading...';

                try {
                    const response = await fetch(`/popup/${bladeRoute}`);
                    const html = await response.text();

                    // 🛑 If another request was made after this, cancel update
                    if (currentId !== this.popup.requestId) return;

                    this.popup.content = html;
                    this.popup.open = true;
                } catch (e) {
                    if (currentId !== this.popup.requestId) return;

                    this.popup.content = '<div class="text-red-500">Failed to load content.</div>';
                    this.popup.open = true;
                }
            },
        });
    </script>
</x-app-layout>
