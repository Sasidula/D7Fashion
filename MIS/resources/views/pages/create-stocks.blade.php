
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
            @include('components.sidebar', ['currentPage' => 'create-stocks'])

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
                        x-data="createStock()"
                        class="bg-white rounded-lg shadow-md p-6"
                    >
                        <h1 class="text-2xl font-bold mb-6 text-[#0f2360]">Create Material</h1>

                        <!-- Success Message -->
                        @if(session('success'))
                            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6 flex items-center">
                                <x-lucide-check class="w-5 h-5 mr-2" />
                                <span>{{ session('success') }}</span>
                            </div>
                        @endif

                        <!-- Form -->
                        <form method="POST" action="{{ route('stockscreate.create') }}" @submit="handleSubmit">
                            @csrf
                            <div class="max-w-2xl mx-auto">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Name -->
                                    <div>
                                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                                        <input
                                            type="text"
                                            id="name"
                                            name="name"
                                            x-model="formData.name"
                                            class="block w-full border rounded-md shadow-sm p-2 focus:ring-[#0f2360] focus:border-[#0f2360]"
                                            :class="{ 'border-red-500': errors.name }"
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
                                            :class="{ 'border-red-500': errors.price }"
                                        />
                                        <p x-show="errors.price" class="mt-1 text-sm text-red-500" x-text="errors.price"></p>
                                    </div>

                                    <!-- Supplier -->
                                    <div>
                                        <label for="supplier" class="block text-sm font-medium text-gray-700 mb-1">Supplier</label>
                                        <input
                                            type="text"
                                            id="supplier"
                                            name="supplier"
                                            x-model="formData.supplier"
                                            class="block w-full border rounded-md shadow-sm p-2 focus:ring-[#0f2360] focus:border-[#0f2360]"
                                        />
                                    </div>
                                </div>

                                <!-- Description -->
                                <div>
                                    <div class="mt-6">
                                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                        <textarea
                                            id="description"
                                            name="description"
                                            x-model="formData.description"
                                            rows="3"
                                            class="block w-full border rounded-md shadow-sm p-2 focus:ring-[#0f2360] focus:border-[#0f2360]"
                                        ></textarea>
                                    </div>
                                </div>

                                <!-- Submit -->
                                <div class="mt-8">
                                    <button
                                        type="submit"
                                        class="w-full bg-[#fd9c0a] text-white py-3 px-4 rounded-md hover:bg-[#e08c09] focus:outline-none flex items-center justify-center"
                                    >
                                        <x-lucide-user-plus class="w-5 h-5 mr-2" />
                                        Create Material
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
        function createStock() {
            return {
                formData: {
                    name: '',
                    price: '',
                    supplier: '',
                    description: ''
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
                    this.errors = newErrors;
                    return Object.keys(newErrors).length === 0;
                },
                handleSubmit(event) {
                    event.preventDefault();
                    if (this.validate()) {
                        event.target.submit(); // Submit if no validation errors
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
