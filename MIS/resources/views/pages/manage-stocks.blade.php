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
            @include('components.sidebar', ['currentPage' => 'manage-stocks'])

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
    x-data="stockComponent()"
    x-init="initPopup()"
    class="bg-white rounded-lg shadow-md p-6"
>
    <h1 class="text-2xl font-bold mb-6 text-[#0f2360]">View Materials</h1>

    <div x-show="materials.length === 0" class="text-center py-8">
        <x-lucide-package class="mx-auto text-gray-300 mb-3 h-12 w-12" />
        <p class="text-gray-500">No materials available</p>
    </div>

    <div x-show="materials.length > 0" class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
            <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Name
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Price ($)
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Quantity
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Actions
                </th>
            </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
            <template x-for="material in materials" :key="material.id">
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap" x-text="material.name"></td>
                    <td class="px-6 py-4 whitespace-nowrap" x-text="'$' + material.price.toFixed(2)"></td>
                    <td class="px-6 py-4 whitespace-nowrap" x-text="material.quantity"></td>
                    <td class="px-6 py-4 whitespace-nowrap pl-10">
                        <button
                            @click="$dispatch('popup-open', {
                                    title: 'Edit Material',
                                    view: 'stock-edit',
                                    data: material
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

                </main>
            </div>
        </div>

        <!-- Popup -->
        @include('components.popup')

    </div>

    <script>
        function stockComponent() {
            return {
                popup: {
                    open: false,
                    title: '',
                    content: '',
                    data: null,
                    requestId: 0,
                },
                materials: [
                    { id: 1, name: 'Cotton Fabric', price: 10.00, quantity: 500 },
                    { id: 2, name: 'Silk Fabric', price: 20.00, quantity: 200 },
                    { id: 3, name: 'Buttons', price: 0.50, quantity: 2000 },
                    { id: 4, name: 'Zippers', price: 1.00, quantity: 300 },
                    { id: 5, name: 'Thread Spools', price: 0.75, quantity: 150 }
                ],
                //deleteMaterial(id) {
                //    if (confirm('Are you sure you want to delete this material?')) {
                //        this.materials = this.materials.filter(item => item.id !== id);
                //        alert('Material deleted successfully!');
                //    }
                //},
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
