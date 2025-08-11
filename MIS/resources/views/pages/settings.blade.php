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
            @include('components.sidebar', ['currentPage' => 'settings'])

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
                    <div class="py-6">
                        <div class="max-w-3xl mx-auto sm:px-6 lg:max-w-7xl lg:px-8">
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
                            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                                <div class="p-6 sm:px-20 bg-white border-b border-gray-200">

                                    {{-- Page Header --}}
                                    <header>
                                        <h2 class="text-xl font-semibold text-gray-800 mb-4">Settings</h2>
                                    </header>

                                    {{-- Manage Material --}}
                                    <div class="mt-10">
                                        <header>
                                            <h3 class="text-lg font-semibold text-gray-800 mt-4 mb-4 ml-2">Manage Material</h3>
                                        </header>

                                        {{-- Unavailable Material --}}
                                        <div class="mt-6 bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                                            <h4 class="text-md font-medium text-gray-700">Delete Unavailable Material</h4>
                                            <p class="text-sm text-gray-600">Are you sure you want to delete this material?</p>
                                            <div class="flex justify-end mt-4">
                                                <button
                                                    @click="$dispatch('open-modal', 'delete-material-unavailable')"
                                                    class="bg-[#fd9c0a] hover:bg-orange-600 text-white px-4 py-2 rounded-md"
                                                >
                                                    <x-lucide-trash class="w-4 h-4 inline-block mr-2" />
                                                    {{ __('Delete') }}
                                                </button>
                                            </div>
                                        </div>

                                        {{-- Deleted Material --}}
                                        <div class="mt-6 bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                                            <h4 class="text-md font-medium text-gray-700">Delete Deleted Material</h4>
                                            <p class="text-sm text-gray-600">Are you sure you want to delete these deleted this materials?</p>
                                            <div class="flex justify-end mt-4">
                                                <button @click="$dispatch('open-modal', 'delete-material-deleted')" class="bg-[#fd9c0a] hover:bg-orange-600 text-white px-4 py-2 rounded-md">
                                                    <x-lucide-trash class="w-4 h-4 inline-block mr-2" />
                                                    {{ __('Delete') }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Manage Internal Products --}}
                                    <div class="mt-10">
                                        <header>
                                            <h3 class="text-lg font-semibold text-gray-800 mt-4 mb-4 ml-2">Manage Internal Products</h3>
                                        </header>

                                        {{-- Sold Internal Products --}}
                                        <div class="mt-6 bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                                            <h4 class="text-md font-medium text-gray-700">Delete Sold Internal Products</h4>
                                            <p class="text-sm text-gray-600">Are you sure you want to delete these deleted Internal Products?</p>
                                            <div class="flex justify-end mt-4">
                                                <button @click="$dispatch('open-modal', 'delete-Internal-Product-sold')" class="bg-[#fd9c0a] hover:bg-orange-600 text-white px-4 py-2 rounded-md">
                                                    <x-lucide-trash class="w-4 h-4 inline-block mr-2" />
                                                    {{ __('Delete') }}
                                                </button>
                                            </div>
                                        </div>

                                        {{-- Deleted Internal Products --}}
                                        <div class="mt-6 bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                                            <h4 class="text-md font-medium text-gray-700">Delete Deleted Internal Products</h4>
                                            <p class="text-sm text-gray-600">Are you sure you want to delete these deleted Internal Products?</p>
                                            <div class="flex justify-end mt-4">
                                                <button
                                                    @click="$dispatch('open-modal', 'delete-Internal-Product-deleted')"
                                                    class="bg-[#fd9c0a] hover:bg-orange-600 text-white px-4 py-2 rounded-md"
                                                >
                                                    <x-lucide-trash class="w-4 h-4 inline-block mr-2" />
                                                    {{ __('Delete') }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Manage External Products --}}
                                    <div class="mt-10">
                                        <header>
                                            <h3 class="text-lg font-semibold text-gray-800 mt-4 mb-4 ml-2">Manage External Products</h3>
                                        </header>

                                        {{-- Sold External Products --}}
                                        <div class="mt-6 bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                                            <h4 class="text-md font-medium text-gray-700">Delete Sold External Products</h4>
                                            <p class="text-sm text-gray-600">Are you sure you want to delete these deleted External products?</p>
                                            <div class="flex justify-end mt-4">
                                                <button
                                                    @click="$dispatch('open-modal', 'delete-External-Product-sold')"
                                                    class="bg-[#fd9c0a] hover:bg-orange-600 text-white px-4 py-2 rounded-md"
                                                >
                                                    <x-lucide-trash class="w-4 h-4 inline-block mr-2" />
                                                    {{ __('Delete') }}
                                                </button>
                                            </div>
                                        </div>

                                        {{-- Deleted External Products --}}
                                        <div class="mt-6 bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                                            <h4 class="text-md font-medium text-gray-700">Delete Deleted External Products</h4>
                                            <p class="text-sm text-gray-600">Are you sure you want to delete these deleted External products?</p>
                                            <div class="flex justify-end mt-4">
                                                <button
                                                    @click="$dispatch('open-modal', 'delete-External-Product-deleted')"
                                                    class="bg-[#fd9c0a] hover:bg-orange-600 text-white px-4 py-2 rounded-md"
                                                >
                                                    <x-lucide-trash class="w-4 h-4 inline-block mr-2" />
                                                    {{ __('Delete') }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
            </div>

            <x-modal name="delete-material-unavailable" focusable>
                <div class="p-6">
                    <p class="mb-4 text-gray-700">Are you sure you want to delete these unavailable materials?</p>
                    <form action="{{ route('page.settings.material.unavailable') }}" method="POST">
                        @csrf
                        <div class="flex justify-end">
                            <button type="submit" class="bg-[#fd9c0a] hover:bg-orange-600 text-white px-4 py-2 rounded-md justify-end">
                                <x-lucide-trash class="w-4 h-4 inline-block mr-2" />
                                {{ __('Delete') }}
                            </button>
                        </div>
                    </form>
                </div>
            </x-modal>

            <x-modal name="delete-material-deleted" focusable>
                <div class="p-6">
                    <p class="mb-4 text-gray-700">Are you sure you want to delete these deleted this materials?</p>
                    <form action="{{ route('page.settings.material.deleted') }}" method="POST">
                        @csrf
                        <div class="flex justify-end">
                            <button type="submit" class="bg-[#fd9c0a] hover:bg-orange-600 text-white px-4 py-2 rounded-md justify-end">
                                <x-lucide-trash class="w-4 h-4 inline-block mr-2" />
                                {{ __('Delete') }}
                            </button>
                        </div>
                    </form>
                </div>
            </x-modal>

            <x-modal name="delete-Internal-Product-sold" focusable>
                <div class="p-6">
                    <p class="mb-4 text-gray-700">Are you sure you want to delete these Sold Internal Products?</p>
                    <form action="{{ route('page.settings.internalProduct.sold') }}" method="POST">
                        @csrf
                        <div class="flex justify-end">
                            <button type="submit" class="bg-[#fd9c0a] hover:bg-orange-600 text-white px-4 py-2 rounded-md justify-end">
                                <x-lucide-trash class="w-4 h-4 inline-block mr-2" />
                                {{ __('Delete') }}
                            </button>
                        </div>
                    </form>
                </div>
            </x-modal>

            <x-modal name="delete-Internal-Product-deleted" focusable>
                <div class="p-6">
                    <p class="mb-4 text-gray-700">Are you sure you want to delete these deleted Internal Products?</p>
                    <form action="{{ route('page.settings.internalProduct.deleted') }}" method="POST">
                        @csrf
                        <div class="flex justify-end">
                            <button type="submit" class="bg-[#fd9c0a] hover:bg-orange-600 text-white px-4 py-2 rounded-md justify-end">
                                <x-lucide-trash class="w-4 h-4 inline-block mr-2" />
                                {{ __('Delete') }}
                            </button>
                        </div>
                    </form>
                </div>
            </x-modal>

            <x-modal name="delete-External-Product-deleted" focusable>
                <div class="p-6">
                    <p class="mb-4 text-gray-700">Are you sure you want to delete these deleted External Products?</p>
                    <form action="{{ route('page.settings.externalProduct.deleted') }}" method="POST">
                        @csrf
                        <div class="flex justify-end">
                            <button type="submit" class="bg-[#fd9c0a] hover:bg-orange-600 text-white px-4 py-2 rounded-md justify-end">
                                <x-lucide-trash class="w-4 h-4 inline-block mr-2" />
                                {{ __('Delete') }}
                            </button>
                        </div>
                    </form>
                </div>
            </x-modal>

            <x-modal name="delete-External-Product-sold" focusable>
                <div class="p-6">
                    <p class="mb-4 text-gray-700">Are you sure you want to delete these Sold External Products?</p>
                    <form action="{{ route('page.settings.externalProduct.sold') }}" method="POST">
                        @csrf
                        <div class="flex justify-end">
                            <button type="submit" class="bg-[#fd9c0a] hover:bg-orange-600 text-white px-4 py-2 rounded-md justify-end">
                                <x-lucide-trash class="w-4 h-4 inline-block mr-2" />
                                {{ __('Delete') }}
                            </button>
                        </div>
                    </form>
                </div>
            </x-modal>
        </div>

        <!-- Popup -->
        @include('components.popup')

    </div>
    <script>
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
