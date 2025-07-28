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
                    <div
                        x-data="{
                            internalProducts: {{ Js::from($internalProducts) }},
                            externalProducts: {{ Js::from($externalProducts) }},
                            selectedItem: {!! session('updatedItem') ? json_encode(session('updatedItem')) : 'null' !!},
                            activeTab: 'Internal',
                            setActiveTab(tab) {
                                this.activeTab = tab;
                                this.selectedItem = null;
                            }
                        }"
                        x-init="initPopup()"
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
                                                @click="selectedItem = item; $dispatch('open-modal', activeTab === 'Internal' ? 'Edit-Internal-Product' : 'Edit-External-Product')"
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

                <!-- Internal Product Modal -->
                <x-modal name="Edit-Internal-Product" focusable>
                    <div class="bg-white rounded-lg shadow-lg w-full max-w-3xl mx-4 overflow-hidden">
                        <div class="max-h-[90vh] overflow-y-auto rounded-lg scrollbar-thin">
                            <div class="flex items-center justify-between p-4 border-b sticky top-0 bg-white z-10">
                                <button x-on:click="$dispatch('close')" class="text-gray-600 hover:text-gray-900">
                                    <x-lucide-arrow-left class="w-6 h-6" />
                                </button>
                                <h2 class="text-lg font-semibold text-gray-800">Edit Internal Product</h2>
                                <div class="w-6"></div>
                            </div>
                            <div class="p-6">
                                <!-- Add your internal product edit form here -->
                            </div>
                        </div>
                    </div>
                </x-modal>

                <!-- External Product Modal -->
                <x-modal name="Edit-External-Product" focusable>
                    <div class="bg-white rounded-lg shadow-lg w-full max-w-3xl mx-4 overflow-hidden">
                        <div class="max-h-[90vh] overflow-y-auto rounded-lg scrollbar-thin">
                            <div class="flex items-center justify-between p-4 border-b sticky top-0 bg-white z-10">
                                <button x-on:click="$dispatch('close')" class="text-gray-600 hover:text-gray-900">
                                    <x-lucide-arrow-left class="w-6 h-6" />
                                </button>
                                <h2 class="text-lg font-semibold text-gray-800">Edit External Product</h2>
                                <div class="w-6"></div>
                            </div>
                            <div class="p-6">
                                <!-- Add your external product edit form here -->
                            </div>
                        </div>
                    </div>
                </x-modal>
            </div>


            <!-- Popup -->
        @include('components.popup')

    </div>

    <script>
        function manageProductComponent() {
            return undefined;
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
