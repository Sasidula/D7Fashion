
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
            @include('components.sidebar', ['currentPage' => 'add-assignment'])

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
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h1 class="text-2xl font-bold mb-6 text-[#0f2360]">Create Assignment</h1>

                        <!-- Display server-side success -->
                        @if(session('success'))
                            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6">
                                <strong>{{ session('success') }}</strong>
                            </div>
                        @endif

                        <!-- Display server-side validation errors -->
                        @if($errors->any())
                            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6">
                                <ul class="list-disc pl-5">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('assignments.store') }}">
                            @csrf
                            <div class="max-w-2xl mx-auto">
                                    <!-- Employee -->
                                    <div class="mb-4">
                                        <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1">
                                            Select Employee
                                        </label>
                                        <select
                                            id="user_id"
                                            name="user_id"
                                            class="block w-full border rounded-md shadow-sm p-2 focus:ring-[#0f2360] focus:border-[#0f2360]"
                                            required
                                        >
                                            <option value="">-- Select an employee --</option>
                                            @foreach($users as $user)
                                                <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                                    {{ $user->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Notes -->
                                    <div>
                                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">
                                            Notes (optional)
                                        </label>
                                        <textarea
                                            id="notes"
                                            name="notes"
                                            rows="3"
                                            class="block w-full border rounded-md shadow-sm p-2 focus:ring-[#0f2360] focus:border-[#0f2360]"
                                        >{{ old('notes') }}</textarea>
                                    </div>

                                <!-- Materials Section -->
                                <div class="mt-6">
                                    <h3 class="text-lg font-semibold mb-2">Assign Materials</h3>

                                    <div id="materials-container">
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 material-row">
                                            <!-- Material -->
                                            <div>
                                                <select
                                                    name="materials[0][material_stock_id]"
                                                    class="block w-full border rounded-md shadow-sm p-2 focus:ring-[#0f2360] focus:border-[#0f2360]"
                                                    required
                                                >
                                                    <option value="">-- Select a material --</option>
                                                    @foreach($materials as $material)
                                                        <option value="{{ $material->id }}">
                                                            {{ $material->name }} ({{ $material->available_quantity }} available)
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <!-- Quantity -->
                                            <div>
                                                <input
                                                    type="number"
                                                    name="materials[0][quantity]"
                                                    min="1"
                                                    class="block w-full border rounded-md shadow-sm p-2 focus:ring-[#0f2360] focus:border-[#0f2360]"
                                                    placeholder="Quantity"
                                                    required
                                                />
                                            </div>

                                            <!-- Remove Button -->
                                            <!-- Remove Button -->
                                            <div class="flex items-center">
                                                <button type="button" class="remove-material">
                                                    Remove
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Add Material Button -->
                                    <div class="mt-4">
                                        <button type="button" id="add-material"
                                                class="bg-[#0f2360] text-white py-2 px-4 rounded-md hover:bg-[#1c347a]">
                                            + Add Another Material
                                        </button>
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div class="mt-8">
                                    <button
                                        type="submit"
                                        class="w-full bg-[#fd9c0a] text-white py-3 px-4 rounded-md hover:bg-[#e08c09] focus:outline-none flex items-center justify-center"
                                    >
                                        <x-lucide-plus class="w-5 h-5 mr-2" />
                                        Create Assignment
                                    </button>
                                </div>
                            </div>
                        </form>

                        <script>
                            let materialIndex = 1;

                            document.getElementById('add-material').addEventListener('click', function() {
                                const container = document.getElementById('materials-container');
                                const row = document.createElement('div');
                                row.classList.add('grid', 'grid-cols-1', 'md:grid-cols-3', 'gap-4', 'material-row', 'mt-2');

                                row.innerHTML = `
                                    <div>
                                        <select name="materials[${materialIndex}][material_stock_id]"
                                            class="block w-full border rounded-md shadow-sm p-2 focus:ring-[#0f2360] focus:border-[#0f2360]"
                                            required>
                                            <option value="">-- Select a material --</option>
                                            @foreach($materials as $material)
                                                                <option value="{{ $material->id }}">
                                                    {{ $material->name }} ({{ $material->available_quantity }} available)
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <input type="number" name="materials[${materialIndex}][quantity]" min="1"
                                            class="block w-full border rounded-md shadow-sm p-2 focus:ring-[#0f2360] focus:border-[#0f2360]"
                                            placeholder="Quantity" required />
                                    </div>

                                    <div class="flex items-center">
                                        <button type="button" class="remove-material">
                                            Remove
                                        </button>
                                    </div>
                                `;

                                container.appendChild(row);
                                materialIndex++;
                            });

                            document.addEventListener('click', function(e) {
                                if (e.target.classList.contains('remove-material')) {
                                    e.target.closest('.material-row').remove();
                                }
                            });
                        </script>

                    </div>
                </main>
            </div>

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
