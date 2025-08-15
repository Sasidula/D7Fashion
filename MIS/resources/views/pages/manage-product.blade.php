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
            @include('components.sidebar', ['currentPage' => 'manage-product'])

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
                    @if (session('deleted'))
                        <div class="mb-4 text-green-600 bg-green-100 border border-green-300 rounded p-3" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 10000)">
                            {{ session('deleted') }}
                        </div>
                    @endif
                    @if (session('status'))
                        <div class="mb-4 text-green-600 bg-green-100 border border-green-300 rounded p-3" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 10000)">
                            {{ session('status') }}
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
                        class="bg-white rounded-lg shadow-md p-6"
                    >
                        <h1 class="text-2xl font-bold mb-6 text-[#0f2360]">View Products</h1>

                        <!-- Tabs -->
                        <div class="mb-4 border-b flex">
                            <button
                                @click="setActiveTab('Internal')"
                                class="py-2 px-4"
                                :class="{ 'border-b-2 border-[#fd9c0a] text-[#0f2360] font-medium': activeTab === 'Internal', 'text-gray-500': activeTab !== 'Internal' }"
                            >
                                Internal
                            </button>
                            <button
                                @click="setActiveTab('External')"
                                class="py-2 px-4"
                                :class="{ 'border-b-2 border-[#fd9c0a] text-[#0f2360] font-medium': activeTab === 'External', 'text-gray-500': activeTab !== 'External' }"
                            >
                                External
                            </button>
                        </div>

                        <!-- vlidation -->
                        <template x-if="activeTab === 'Internal' && internalProducts.length === 0">
                            <p class="text-gray-600">No internal products found.</p>
                        </template>
                        <template x-if="activeTab === 'External' && externalProducts.length === 0">
                            <p class="text-gray-600">No external products found.</p>
                        </template>

                        <!-- Table -->
                        <div class="overflow-x-auto border rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                    <th x-show="activeTab === 'External'" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bought Price</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sold/Unit Price</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                <template x-for="item in activeTab === 'Internal' ? internalProducts : externalProducts" :key="item.id">
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap" x-text="item.name"></td>
                                        <td class="px-6 py-4 whitespace-nowrap" x-text="item.available_quantity"></td>
                                        <td class="px-6 py-4 whitespace-nowrap" x-show="activeTab === 'External'" x-text="'Rs. ' + parseFloat(item.bought_price).toFixed(2)"></td>
                                        <td class="px-6 py-4 whitespace-nowrap" x-text="'Rs. ' + (activeTab === 'Internal' ? parseFloat(item.price).toFixed(2) : parseFloat(item.sold_price).toFixed(2))"></td>
                                        <td class="px-6 py-4 whitespace-nowrap pl-10">
                                            <button
                                                @click="selectedItem = item; $dispatch('open-modal', 'Edit-Product')"
                                                class="text-[#0f2360] hover:text-[#fd9c0a]"
                                            >
                                                <x-lucide-edit class="w-5 h-5" />
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </main>
            </div>

            <!-- Product Modal -->
            <x-modal name="Edit-Product" :scrollable="true" focusable>
                <div class="bg-white rounded-lg shadow-lg w-full max-w-3xl mx-4 overflow-hidden">
                    <div class="max-h-[90vh] overflow-y-auto rounded-lg scrollbar-thin">
                        <div class="flex items-center justify-between p-4 border-b sticky top-0 bg-white z-10">
                            <button x-on:click="$dispatch('close')" class="text-gray-600 hover:text-gray-900">
                                <x-lucide-arrow-left class="w-6 h-6" />
                            </button>
                            <h2 class="text-lg font-semibold text-gray-800" x-text="activeTab === 'Internal' ? 'Edit Internal Product' : 'Edit External Product'"></h2>
                            <div class="w-6"></div>
                        </div>
                        @if (session('status'))
                            <div class="mb-4 text-green-600 bg-green-100 border border-green-300 rounded p-3" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 10000)">
                                {{ session('status') }}
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
{{--                        update product information--}}
                        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg space-y-6">
                            <header>
                                <h2 class="text-lg font-medium text-gray-900">
                                    {{ __('Product Information') }}
                                </h2>

                                <p class="mt-1 text-sm text-gray-600">
                                    {{ __("Update your Product's information.") }}
                                </p>
                            </header>
                            <template x-if="activeTab === 'Internal'">
                                <form method="post" action="{{ route('internalProducts.update') }}" class="mt-6 space-y-6">
                                    @csrf
                                    @method('PATCH')

                                    <!-- Hidden material ID -->
                                    <input type="hidden" name="internal_product_id" :value="selectedItem?.id" />

                                    <div class="gap-6">
                                        <!-- Name -->
                                        <div class="gap-6">
                                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                                            <input
                                                type="text"
                                                id="name"
                                                name="name"
                                                x-model="selectedItem?.name"
                                                class="block w-full border rounded-md shadow-sm p-2 focus:ring-[#0f2360] focus:border-[#0f2360]"
                                            />
                                            <p x-show="errors.name" class="mt-1 text-sm text-red-500" x-text="errors.name"></p>
                                        </div>
                                    </div>


                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">

                                        <!-- Price -->
                                        <div>
                                            <label for="price" class="block text-sm font-medium text-gray-700 mb-1">Price (Rs)</label>
                                            <input
                                                type="text"
                                                id="price"
                                                name="price"
                                                x-model="selectedItem?.price"
                                                class="block w-full border rounded-md shadow-sm p-2 focus:ring-[#0f2360] focus:border-[#0f2360]"
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
                                                x-model="selectedItem?.sku_code"
                                                class="block w-full border rounded-md shadow-sm p-2 focus:ring-[#0f2360] focus:border-[#0f2360]"
                                            />
                                            <p x-show="errors.sku_code" class="mt-1 text-sm text-red-500" x-text="errors.sku_code"></p>
                                        </div>
                                    </div>

                                    <!-- Description -->
                                    <div class="gap-6 mt-6">
                                        <div class="md:col-span-2">
                                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                            <textarea
                                                id="description"
                                                name="description"
                                                x-model="selectedItem?.description"
                                                rows="4"
                                                class="block w-full border rounded-md shadow-sm p-2 focus:ring-[#0f2360] focus:border-[#0f2360]"
                                            ></textarea>
                                        </div>
                                    </div>

                                    <!-- Save -->
                                    <div class="flex items-center gap-4">
                                        <x-primary-button>{{ __('Save') }}</x-primary-button>

                                        @if (session('success') === 'Internal Product updated.')
                                            <p
                                                x-data="{ show: true }"
                                                x-show="show"
                                                x-transition
                                                x-init="setTimeout(() => show = false, 2000)"
                                                class="text-sm text-gray-600"
                                            >{{ __('Saved.') }}</p>
                                        @endif
                                    </div>
                                </form>
                            </template>



                            <template x-if="activeTab === 'External'">
                                <form method="post" action="{{ route('externalProducts.update') }}" class="mt-6 space-y-6">
                                    @csrf
                                    @method('PATCH')

                                    <!-- Hidden material ID -->
                                    <input type="hidden" name="external_product_id" :value="selectedItem?.id" />

                                    <div class="gap-6">
                                        <!-- Name -->
                                        <div>
                                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Product Name</label>
                                            <input
                                                type="text"
                                                id="name"
                                                name="name"
                                                x-model="selectedItem?.name"
                                                class="block w-full border rounded-md shadow-sm p-2 focus:ring-[#0f2360] focus:border-[#0f2360]"
                                            />
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
                                                x-model="selectedItem?.sku_code"
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
                                                x-model="selectedItem?.supplier"
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
                                                x-model="selectedItem?.description"
                                                class="w-full border border-gray-300 rounded-md p-2 focus:ring-[#0f2360] focus:border-[#0f2360]"
                                                rows="3"
                                            ></textarea>
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
                                                x-model="selectedItem?.bought_price"
                                                class="block w-full border rounded-md shadow-sm p-2 focus:ring-[#0f2360] focus:border-[#0f2360]"
                                            />
                                        </div>

                                        <!-- Sold Price -->
                                        <div>
                                            <label for="sold_price" class="block text-sm font-medium text-gray-700 mb-1">Sold Price (Rs)</label>
                                            <input
                                                type="text"
                                                id="sold_price"
                                                name="sold_price"
                                                x-model="selectedItem?.sold_price"
                                                class="block w-full border rounded-md shadow-sm p-2 focus:ring-[#0f2360] focus:border-[#0f2360]"
                                            />
                                        </div>
                                    </div>

                                    <!-- Save -->
                                    <div class="flex items-center gap-4">
                                        <x-primary-button>{{ __('Save') }}</x-primary-button>

                                        @if (session('success') === 'External product updated.')
                                            <p
                                                x-data="{ show: true }"
                                                x-show="show"
                                                x-transition
                                                x-init="setTimeout(() => show = false, 10000)"
                                                class="text-sm text-gray-600"
                                            >{{ __('Saved.') }}</p>
                                        @endif
                                    </div>
                                </form>
                            </template>
                        </div>

{{--                    update material quantity--}}
                        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg mt-4">
                            <header>
                                <h2 class="text-lg font-medium text-gray-900">
                                    {{ __('Product Quantity') }}
                                </h2>
                                <p class="mt-1 text-sm text-gray-600">
                                    {{ __("Update the quantity of this product stock.") }}
                                </p>
                            </header>

                            <form
                                method="post"
                                :action="activeTab === 'Internal' ? '{{ route('internalProducts.adjust') }}' : '{{ route('externalProducts.adjust') }}'"
                                class="mt-6 space-y-6"
                            >
                                @csrf
                                @method('PATCH')

                                <!-- product ID (hidden input passed from controller or Blade variable) -->
                                <template x-if="activeTab === 'Internal'">
                                    <input type="hidden" name="internal_product_id" :value="selectedItem?.id" />
                                </template>
                                <template x-if="activeTab === 'External'">
                                    <input type="hidden" name="external_product_id" :value="selectedItem?.id" />
                                </template>


                                <!-- Action -->
                                <div class="mb-4">
                                    <label for="action" class="block text-sm font-medium text-gray-700 mb-1">
                                        Action
                                    </label>
                                    <select
                                        id="action"
                                        name="action"
                                        class="block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-[#0f2360] focus:border-[#0f2360]"
                                        required
                                    >
                                        <option value="delete">Remove</option>
                                        <option value="restore">Restore</option>
                                    </select>
                                </div>

                                <!-- Quantity -->
                                <div class="mb-6">
                                    <label for="quantity" class="block text-sm font-medium text-gray-700 mb-1">
                                        Quantity
                                    </label>
                                    <input
                                        name="quantity"
                                        id="quantity"
                                        type="number"
                                        min="1"
                                        value="1"
                                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        required
                                    />
                                </div>

                                <!-- Submit -->
                                <button
                                    type="submit"
                                    class="flex items-center justify-center w-full bg-orange-500 hover:bg-orange-600 text-white font-medium py-3 rounded-md"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9l-6 6-6-6" />
                                    </svg>
                                    Save
                                </button>
                            </form>
                        </div>

{{--                    delete material--}}
                        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg mt-4">
                            <header>
                                <h2 class="text-lg font-medium text-gray-900">
                                    {{ __('Delete Product') }}
                                </h2>

                                <p class="mt-1 text-sm text-gray-600">
                                    {{ __('Once your Product is deleted, all of its resources and data will be permanently deleted. Before deleting your Product, please download any data or information that you wish to retain.') }}
                                </p>
                            </header>

                            <x-danger-button
                                x-on:click.prevent="$dispatch('open-modal', 'confirm-Product-deletion')"
                            >{{ __('Delete Material') }}</x-danger-button>

                            <x-modal name="confirm-Product-deletion" focusable>
                                <form
                                    method="post"
                                    :action="activeTab === 'Internal' ? '{{ route('internalProducts.softDelete') }}' : '{{ route('externalProducts.softDelete') }}'"
                                    class="mt-6 space-y-6"
                                >
                                    @csrf
                                    @method('DELETE')

                                    <!-- product ID (hidden input passed from controller or Blade variable) -->
                                    <template x-if="activeTab === 'Internal'">
                                        <input type="hidden" name="internal_product_id" :value="selectedItem?.id" />
                                    </template>
                                    <template x-if="activeTab === 'External'">
                                        <input type="hidden" name="external_product_id" :value="selectedItem?.id" />
                                    </template>


                                    <h2 class="text-lg font-medium text-gray-900">
                                        {{ __('Are you sure you want to delete this Product?') }}
                                    </h2>

                                    <p class="mt-1 text-sm text-gray-600">
                                        {{ __('This action is irreversible. The product and all related data will be permanently deleted.') }}
                                    </p>

                                    <div class="mt-6 flex justify-end">
                                        <x-secondary-button x-on:click="$dispatch('close')">
                                            {{ __('Cancel') }}
                                        </x-secondary-button>

                                        <x-danger-button class="ms-3">
                                            {{ __('Delete Material') }}
                                        </x-danger-button>
                                    </div>
                                </form>
                            </x-modal>
                        </div>
                    </div>
                </div>
            </x-modal>

        </div>

        <!-- Popup -->
        @include('components.popup')

    </div>

    <script>
        window.layoutHandler = () => ({

            internalProducts: {{ Js::from($internalProducts) }},
            externalProducts: {{ Js::from($externalProducts) }},

            selectedItem: {!! session('updatedItem') ? json_encode(session('updatedItem')) : 'null' !!},

            activeTab: 'Internal',
            setActiveTab(tab) {
                this.activeTab = tab;
                this.selectedItem = null;
            },

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
