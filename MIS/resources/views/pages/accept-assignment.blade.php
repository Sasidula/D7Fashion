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
            @include('components.sidebar', ['currentPage' => 'accept-assignment'])

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
            <div class="flex-1 overflow-y-auto transition-all duration-300 ease-in-out">
                <main class="p-6">
                    {{-- Flash Messages --}}
                    @if (session('status'))
                        <div class="mb-4 text-green-600 bg-green-100 border border-green-300 rounded p-3" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 10000)">
                            {{ session('status') }}
                        </div>
                    @endif
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

                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h1 class="text-2xl font-bold mb-6 text-[#0f2360]">Complete Assignment</h1>

                        {{-- Employee Selection --}}
                        <form method="POST" action="{{ route('assignments.get') }}" class="mb-6">
                            @csrf
                            <h2 class="text-xl font-bold mb-2">Select Employee to Manage Assignments</h2>
                            <div class="flex flex-col sm:flex-row gap-4">
                                <select name="user_id" required class="rounded-md border w-80 border-gray-300 p-2 focus:ring-[#0f2360] focus:border-[#0f2360]">
                                    <option value="">-- Select Employee --</option>
                                    @foreach ($employees as $employee)
                                        <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="w-16 sm:w-auto bg-[#fd9c0a] text-white py-2 px-4 rounded-md hover:bg-[#e08c09] focus:outline-none">
                                    View Assignments
                                </button>
                                <a href="{{ url('/dashboard/accept-assignment') }}"
                                   class="px-4 py-2 bg-[#fd9c0a] border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-[#e08c09] focus:outline-none">
                                    Reset
                                </a>
                            </div>
                        </form>

                        @if (isset($assignments))
                            @if (isset($user))
                                <h2 class="text-xl font-semibold mb-4 text-gray-800">Assignments for {{ $user->name }}</h2>
                                @if($assignments->isEmpty())
                                    <p class="text-gray-600">No assignments found for {{ $user->name }}.</p>
                                @endif
                            @else
                                <h2 class="text-xl font-semibold mb-4 text-gray-800">Assignments for All Employees</h2>
                                @if($assignments->isEmpty())
                                    <p class="text-gray-600">No assignments found for any employee.</p>
                                @endif
                            @endif

                            @foreach ($assignments as $assignment)
                                <div class="border rounded-lg p-4 mb-6 bg-gray-50 shadow-sm">

                                    @if(isset($user))
                                    @else
                                        <h3 class="text-xl font-semibold mb-3">{{ $assignment->user_name }}</h3>
                                    @endif

                                    <h3 class="text-xl font-semibold mb-3">
                                        <span class="text-sm text-gray-600">Material:</span> {{ $assignment->name }}
                                        <span class="text-sm text-gray-600">Qty of</span> {{ $assignment->assignment_count }}
                                        <span class="text-sm text-gray-600">Assigned</span>
                                    </h3>

                                    <div class="flex flex-col sm:flex-row gap-4 items-center">
                                        {{-- Complete Form --}}
                                        <form method="POST" action="{{ route('assignments.complete') }}" class="flex flex-col sm:flex-row gap-4 items-center">
                                            @csrf
                                            <input type="hidden" name="material_id" value="{{ $assignment->material_id }}">

                                            <input type="hidden" name="user_id" value="{{ $assignment->user_id }}">

                                            <select name="internal_product_id" required class="w-full sm:w-64 border rounded p-2">
                                                <option value="">-- Select Product --</option>
                                                @foreach ($availableProducts as $product)
                                                    @if ($product->available_count > 0)
                                                        <option value="{{ $product->id }}">
                                                            {{ $product->name }} ({{ $product->available_count }} available)
                                                        </option>
                                                    @endif
                                                @endforeach
                                            </select>

                                            <input type="number" name="assignment_quantity" max="{{ $assignment->assignment_count }}" min="1" required class="border rounded p-2 w-48" placeholder="Qty">

                                            <span class="text-gray-600 semibold"> To</span>

                                            <input type="number" name="product_quantity" min="1" required class="border rounded p-2 w-28" placeholder="Product Qty">

                                            <button type="submit" class="bg-[#fd9c0a] hover:bg-[#e08c09] text-white py-2 px-4 rounded-md transition duration-200 focus:outline-none">
                                                Complete
                                            </button>
                                        </form>

                                        {{-- Edit Button --}}
                                        <button @click="selectedItem = {{ $assignment->material_id }}; $dispatch('open-modal', 'edit-modal')"
                                                class="bg-[#fd9c0a] hover:bg-[#e08c09] text-white py-2 px-4 rounded-md transition duration-200 focus:outline-none">
                                            Edit
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-8">
                                <x-lucide-file-text class="mx-auto text-gray-300 mb-3 h-12 w-12" />
                                <p class="text-gray-500">Error</p>
                            </div>
                        @endif
                    </div>
                </main>
            </div>

            {{-- Edit Modal --}}
            <x-modal name="edit-modal"  :scrollable="true" focusable>
                <div class="bg-white rounded-lg shadow-lg w-full max-w-3xl mx-4 overflow-hidden">
                    <div class="max-h-[90vh] overflow-y-auto rounded-lg scrollbar-thin">
                        <form method="POST" action="{{ route('assignments.update') }}" class="mt-6 space-y-6">
                            @csrf
                            @method('PATCH')

                            <input type="hidden" name="material_id" value="selectedItem">
                            <input type="hidden" name="user_id" value="userId">

                            <h2 class="text-lg font-semibold mb-2 text-gray-800">Edit Number of Assignments for Assigned for <span x-text="name"></span></h2>

                            <div>
                                <label for="quantity" class="block text-sm font-medium text-gray-700 mb-1">Quantity</label>
                                <input type="number" name="quantity" min="1" required class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>

                            <div>
                                <label for="action" class="block text-sm font-medium text-gray-700 mb-1">Action</label>
                                <select name="action" required class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="delete">Delete</option>
                                    <option value="restore">Restore</option>
                                </select>
                            </div>

                            <div class="flex justify-end gap-3 mt-4">
                                <button type="submit" class="bg-[#fd9c0a] hover:bg-orange-600 text-white px-4 py-2 rounded-md">Submit</button>
                                <button type="button" @click="$dispatch('close')" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-md">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </x-modal>
        </div>

        <!-- Popup -->
        @include('components.popup')

    </div>
    <script>
        window.layoutHandler = () => ({
            selectedItem: null,
            userId: null,
            name: null,

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
