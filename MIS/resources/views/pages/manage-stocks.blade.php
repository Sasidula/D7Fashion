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
            <div
                class="flex-1 overflow-y-auto transition-all duration-300 ease-in-out"
            >
                <main class="p-6">
                    @if ($errors->any())
                        <div class="mb-4 text-red-600 bg-red-100 border border-red-300 rounded p-3" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 10000)">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                        @if (session('success'))
                            <div class="mb-4 text-green-600 bg-green-100 border border-green-300 rounded p-3" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 10000)">
                                {{ session('success') }}
                            </div>
                        @endif
                        @if (session('status'))
                            <div class="mb-4 text-green-600 bg-green-100 border border-green-300 rounded p-3" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 10000)">
                                {{ session('status') }}
                            </div>
                        @endif

                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h1 class="text-2xl font-bold mb-6 text-[#0f2360]">View Materials</h1>

                        @if ($materials->isEmpty())
                            <div class="text-center py-8">
                                <x-lucide-package class="mx-auto text-gray-300 mb-3 h-12 w-12" />
                                <p class="text-gray-500">No materials available</p>
                            </div>
                        @else
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price ($)</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($materials as $material)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $material->name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">${{ number_format($material->price, 2) }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $material->available_quantity }}</td> <!-- ✅ updated line -->
                                            <td class="px-6 py-4 whitespace-nowrap pl-10">
                                                <button
                                                    @click="selectedMaterial = {{ json_encode($material) }}"
                                                    @click.prevent="$dispatch('open-modal', 'edit-stock');"
                                                    class="text-[#0f2360] hover:text-[#fd9c0a]">
                                                    <x-lucide-edit class="w-5 h-5" />
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach

                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </main>
            </div>


            <!-- Modal -->
            <x-modal name="edit-stock" :show="$errors->any() || session('status')" :scrollable="true">

{{--                update material info--}}
                <div class="w-full  mx-auto p-8 space-y-6 ">
                    <div class="flex items-center justify-between p-4 border-b border-gray-200 sticky top-0 bg-white z-10">
                        <button x-on:click="$dispatch('close')" class="text-gray-600 hover:text-gray-900">
                            <x-lucide-arrow-left class="w-6 h-6" />
                        </button>
                        <div class="w-6"></div>
                    </div>
                    <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                        <header>
                            <h2 class="text-lg font-medium text-gray-900">
                                {{ __('Material Information') }}
                            </h2>

                            <p class="mt-1 text-sm text-gray-600">
                                {{ __("Update your Material's information.") }}
                            </p>
                        </header>
                        <form method="post" action="{{ route('stocks.update') }}" class="mt-6 space-y-6">
                            @csrf
                            @method('PATCH')

                            <!-- Hidden material ID -->
                            <input type="hidden" name="material_id" :value="selectedMaterial?.id" />

                            <!-- Name -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                                <input
                                    type="text"
                                    id="name"
                                    name="name"
                                    x-model="selectedMaterial?.name"
                                    class="block w-full border rounded-md shadow-sm p-2 focus:ring-[#0f2360] focus:border-[#0f2360]"
                                />
                            </div>

                            <!-- Price -->
                            <div>
                                <label for="price" class="block text-sm font-medium text-gray-700 mb-1">Price (Rs)</label>
                                <input
                                    type="text"
                                    id="price"
                                    name="price"
                                    x-model="selectedMaterial?.price"
                                    class="block w-full border rounded-md shadow-sm p-2 focus:ring-[#0f2360] focus:border-[#0f2360]"
                                />
                            </div>

                            <!-- Supplier -->
                            <div>
                                <label for="supplier" class="block text-sm font-medium text-gray-700 mb-1">Supplier</label>
                                <input
                                    type="text"
                                    id="supplier"
                                    name="supplier"
                                    x-model="selectedMaterial?.supplier"
                                    class="block w-full border rounded-md shadow-sm p-2 focus:ring-[#0f2360] focus:border-[#0f2360]"
                                />
                            </div>

                            <!-- Description -->
                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                <textarea
                                    id="description"
                                    name="description"
                                    rows="3"
                                    x-model="selectedMaterial?.description"
                                    class="block w-full border rounded-md shadow-sm p-2 focus:ring-[#0f2360] focus:border-[#0f2360]"
                                ></textarea>
                            </div>

                            <!-- Save -->
                            <div class="flex items-center gap-4">
                                <x-primary-button>{{ __('Save') }}</x-primary-button>

                                @if (session('status') === 'Material updated.')
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
                    </div>


                    {{--                    update material quantity--}}
                    <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                        <header>
                            <h2 class="text-lg font-medium text-gray-900">
                                {{ __('Material Quantity') }}
                            </h2>
                            <p class="mt-1 text-sm text-gray-600">
                                {{ __("Update the quantity of this material stock.") }}
                            </p>
                        </header>

                        <form method="post" action="{{ route('stocks.adjust') }}" class="mt-6 space-y-6">
                            @csrf
                            @method('PATCH')

                            <!-- Material ID (hidden input passed from controller or Blade variable) -->
                            <input type="hidden" name="material_id" :value="selectedMaterial?.id" />

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
                    <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                        <header>
                            <h2 class="text-lg font-medium text-gray-900">
                                {{ __('Delete Material') }}
                            </h2>

                            <p class="mt-1 text-sm text-gray-600">
                                {{ __('Once your Material is deleted, all of its resources and data will be permanently deleted. Before deleting your Material, please download any data or information that you wish to retain.') }}
                            </p>
                        </header>

                        <x-danger-button
                            x-data=""
                            x-on:click.prevent="$dispatch('open-modal', 'confirm-material-deletion')"
                        >{{ __('Delete Material') }}</x-danger-button>

                        <x-modal name="confirm-material-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
                            <form method="POST" action="{{ route('materials.softDelete') }}" class="p-6">
                                @csrf
                                @method('DELETE')

                                <!-- Material ID (hidden input passed from controller or Blade variable) -->
                                <input type="hidden" name="material_id" :value="selectedMaterial?.id" />

                                <h2 class="text-lg font-medium text-gray-900">
                                    {{ __('Are you sure you want to delete this Material?') }}
                                </h2>

                                <p class="mt-1 text-sm text-gray-600">
                                    {{ __('This action is irreversible. The Material and all related data will be permanently deleted.') }}
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
            </x-modal>


        </div>

        <!-- Popup -->
        @include('components.popup')

    </div>

    <script>
        window.layoutHandler = () => ({
            selectedMaterial: {!! session('updatedMaterial') ? json_encode(session('updatedMaterial')) : 'null' !!},

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
