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
            @include('components.sidebar', ['currentPage' => 'create-external-product'])

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
                    @if (session('success'))
                        <div class="mb-4 text-green-600 bg-green-100 border border-green-300 rounded p-3" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 10000)">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if ($errors->any())
                        <div class="mb-4 text-red-600 bg-red-100 border border-red-300 rounded p-3" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 10000)">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div
                        x-data="ExternalProductFormhandler()"
                        class="bg-white rounded-lg shadow-md p-6"
                    >
                        <h1 class="text-2xl font-bold mb-6 text-[#0f2360]">External Product</h1>
                        <!-- Form -->
                        <form method="POST" action="{{ route('ExternalProducts.store') }}" >
                            @method('POST')
                            @csrf
                            <div class="max-w-2xl mx-auto">
                                <div class="gap-6">
                                    <!-- Name -->
                                    <div>
                                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Product Name</label>
                                        <input
                                            type="text"
                                            id="name"
                                            name="name"
                                            required
                                            x-model="formData.name"
                                            :class="{ 'border-red-500': errors.name }"
                                            class="block w-full border rounded-md shadow-sm p-2 focus:ring-[#0f2360] focus:border-[#0f2360]"
                                        />
                                        <p x-show="errors.name" class="mt-1 text-sm text-red-500" x-text="errors.name"></p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                                    <!-- Bought Price -->
                                    <div>
                                        <label for="bought_price" class="block text-sm font-medium text-gray-700 mb-1">Bought Price (Rs)</label>
                                        <input
                                            type="text"
                                            id="bought_price"
                                            name="bought_price"
                                            required
                                            x-model="formData.bought_price"
                                            :class="{ 'border-red-500': errors.bought_price }"
                                            class="block w-full border rounded-md shadow-sm p-2 focus:ring-[#0f2360] focus:border-[#0f2360]"
                                        />
                                        <p x-show="errors.bought_price" class="mt-1 text-sm text-red-500" x-text="errors.bought_price"></p>
                                    </div>

                                    <!-- Sold Price -->
                                    <div>
                                        <label for="sold_price" class="block text-sm font-medium text-gray-700 mb-1">Sold Price (Rs)</label>
                                        <input
                                            type="text"
                                            id="sold_price"
                                            name="sold_price"
                                            required
                                            x-model="formData.sold_price"
                                            :class="{ 'border-red-500': errors.sold_price }"
                                            class="block w-full border rounded-md shadow-sm p-2 focus:ring-[#0f2360] focus:border-[#0f2360]"
                                        />
                                        <p x-show="errors.sold_price" class="mt-1 text-sm text-red-500" x-text="errors.sold_price"></p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">

                                    <!-- SKU Code -->
                                    <div>
                                        <label for="sku_code" class="block text-sm font-medium text-gray-700 mb-1">SKU Code</label>
                                        <input
                                            type="text"
                                            id="sku_code"
                                            name="sku_code"
                                            x-model="formData.sku_code"
                                            class="block w-full border border-gray-300 rounded-md p-2 focus:ring-[#0f2360] focus:border-[#0f2360]"
                                        />
                                    </div>

                                    <!-- Supplier -->
                                    <div>
                                        <label for="supplier" class="block text-sm font-medium text-gray-700 mb-1">Supplier</label>
                                        <input
                                            type="text"
                                            id="supplier"
                                            name="supplier"
                                            x-model="formData.supplier"
                                            class="block w-full border border-gray-300 rounded-md p-2 focus:ring-[#0f2360] focus:border-[#0f2360]"
                                        />
                                    </div>
                                </div>

                                <div class="gap-6 mt-6">
                                    <!-- Description -->
                                    <div class="md:col-span-2">
                                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                        <textarea
                                            id="description"
                                            name="description"
                                            x-model="formData.description"
                                            class="w-full border border-gray-300 rounded-md p-2 focus:ring-[#0f2360] focus:border-[#0f2360]"
                                            rows="3"
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
                                        Add External Product
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
        function ExternalProductFormhandler() {
            return {
                formData: {
                    name: '',
                    sku_code: '',
                    description: '',
                    supplier: '',
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
                    if (!this.formData.bought_price.trim() || isNaN(this.formData.bought_price) || parseFloat(this.formData.bought_price) <= 0) {
                        newErrors.bought_price = 'Please enter a valid bought price';
                    }
                    if (!this.formData.sold_price.trim() || isNaN(this.formData.sold_price) || parseFloat(this.formData.sold_price) <= 0) {
                        newErrors.sold_price = 'Please enter a valid sold price';
                    }
                    this.errors = newErrors;
                    return Object.keys(newErrors).length === 0;
                },
                handleSubmit(event) {
                    if (!this.validate()) {
                        event.preventDefault();
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
