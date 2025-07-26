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
    x-data="productComponent()"
    x-init="initPopup()"
    class="bg-white rounded-lg shadow-md p-6"
>
    <h1 class="text-2xl font-bold mb-6 text-[#0f2360]">View Products</h1>
    <div>
        <!-- Items Section -->
        <div>
            <!-- Tabs -->
            <div class="mb-4">
                <div class="flex border-b">
                    <button
                        @click="setActiveTab('Internal')"
                        class="py-2 px-4"
                        :class="{ 'border-b-2 border-[#fd9c0a] text-[#0f2360] font-medium': activeTab === 'Internal', 'text-gray-500': activeTab !== 'Internal' }" :class="{ 'border-b-2 border-[#fd9c0a] text-[#0f2360] font-medium': activeTab === 'Internal', 'text-gray-500': activeTab !== 'Internal' }"
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
            </div>

            <!-- Items Table -->
            <div class="overflow-x-auto border rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Name
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Quantity
                        </th>
                        <th scope="col" x-show="activeTab === 'External'" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Value
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Price
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Action
                        </th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    <template x-for="item in activeTab === 'Internal' ? InternalItems : ExternalItems" :key="item.id">
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap" x-text="item.name"></td>
                            <td class="px-6 py-4 whitespace-nowrap" x-text="item.quantity"></td>
                            <td class="px-6 py-4 whitespace-nowrap" x-show="activeTab === 'External'" x-text="'$' + item.value.toFixed(2)"></td>
                            <td class="px-6 py-4 whitespace-nowrap" x-text="'$' + item.price.toFixed(2)"></td>
                            <td class="px-6 py-4 whitespace-nowrap pl-10">
                                <button
                                    @click="$dispatch('popup-open', {
                                title: activeTab === 'Internal' ? 'Edit Internal Product' : 'Edit External Product',
                                view: activeTab === 'Internal' ? 'internal-product-edit' : 'external-product-edit',
                                data: item
                            })"
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
    </div>
</div>

                </main>
            </div>
        </div>

        <!-- Popup -->
        @include('components.popup')

    </div>

    <script>
        function productComponent() {
            return {
                popup: {
                    open: false,
                    title: '',
                    content: '',
                    data: null,
                    requestId: 0,
                },
                activeTab: 'Internal',
                InternalItems: [
                    { id: 1, name: 'Gucci', quantity: 10, price: 10.00 },
                    { id: 2, name: 'Louis Vuitton', quantity: 5, price: 20.00 },
                    { id: 3, name: 'Chanel', quantity: 8, price: 15.00 },
                    { id: 4, name: 'Dior', quantity: 3, price: 25.00 },
                    { id: 5, name: 'Prada', quantity: 6, price: 30.00 }
                ],
                ExternalItems: [
                    { id: 1, name: 'Armani', quantity: 10, value: 10.00, price: 10.00 },
                    { id: 2, name: 'Gabbana', quantity: 5, value: 20.00, price: 20.00 },
                    { id: 3, name: 'Dolce', quantity: 8, value: 15.00, price: 15.00 },
                    { id: 4, name: 'Gucci', quantity: 3, value: 25.00, price: 25.00 },
                    { id: 5, name: 'Louis Vuitton', quantity: 6, value: 30.00, price: 30.00 }

                ],
                setActiveTab(tab) {
                    this.activeTab = tab;
                    this.selectedItem = null;
                    this.quantity = 1;
                },
                initPopup() {
                    window.addEventListener('popup-open', (e) => {
                        const { title, view, data } = e.detail;
                        this.popup.data = data;
                        this.loadPopup(title, view);
                    });
                },
                async loadPopup(title, bladeRoute) {
                    this.popup.requestId++;
                    const currentId = this.popup.requestId;

                    this.popup.title = title;
                    this.popup.content = 'Loading...';

                    try {
                        const response = await fetch(`/popup/${bladeRoute}`);
                        const html = await response.text();

                        if (currentId !== this.popup.requestId) return;

                        this.popup.content =
                            `<script>window.popupData = ${JSON.stringify(this.popup.data)}<\/script>` + html;

                        this.popup.open = true;
                    } catch (e) {
                        if (currentId !== this.popup.requestId) return;

                        this.popup.content = '<div class="text-red-500">Failed to load content.</div>';
                        this.popup.open = true;
                    }
                },
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
