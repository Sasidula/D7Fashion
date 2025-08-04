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
            @include('components.sidebar', ['currentPage' => 'manage-assignment'])

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
                        <h1 class="text-2xl font-bold mb-6 text-[#0f2360]">Manage Assignments</h1>

                        {{-- Products under review --}}
                        <h2 class="text-xl font-semibold mb-4">Internal Product Review Summary</h2>

                        @if ($products->isEmpty())
                            <div class="text-center py-8 text-gray-500">
                                <x-lucide-list class="mx-auto text-gray-300 mb-3 h-12 w-12" />
                                <p>No internal product assignments to review.</p>
                            </div>
                        @else
                            <table class="min-w-full divide-y divide-gray-200 mb-8">
                                <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reviewing Quantity</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Approved Quantity</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Options</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                                </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($products as $product)
                                    <tr>
                                        <td class="px-6 py-4">{{ $product->name }}</td>
                                        <td class="px-6 py-4">{{ $product->reviewing_count }}</td>
                                        <form action="{{ route('assignments.review', $product->id) }}" method="POST" class="flex items-center">
                                            @method('patch')
                                            @csrf
                                            <td class="px-6 py-4">
                                                <input type="number" name="approved_quantity" min="0" max="{{ $product->reviewing_count }}" class="p-2 border rounded w-20 mr-2" required>
                                            </td>
                                            <input type="hidden" name="internal_product_id" value="{{ $product->id }}">
                                            <td class="px-6 py-4">
                                                <select name="use" required class="p-1 w-32 rounded border mr-2">
                                                    <option value="approved">Approve</option>
                                                    <option value="rejected">Reject</option>
                                                </select>
                                            </td>
                                            <td class="px-6 py-4">
                                                <button type="submit" class="bg-[#fd9c0a] text-white px-4 py-2 rounded hover:bg-[#e08c09]">
                                                    <x-lucide-check class="w-4 h-4" />
                                                </button>
                                            </td>
                                        </form>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        @endif

                        {{-- Individual Reviewing Assignments --}}
                        <h2 class="text-xl font-semibold mb-4">Individual Reviewing Assignments</h2>

                        @if ($completedassignments->isEmpty())
                            <p class="text-gray-500">No individual assignments in reviewing state.</p>
                        @else
                            <table class="min-w-full divide-y divide-gray-200 mb-8">
                                <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Assigned User</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Note</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Options</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($completedassignments as $item)
                                    <tr>
                                        <td class="px-6 py-4">{{ $item->internalProduct->name }}</td>
                                        <td class="px-6 py-4">{{ $item->assignment?->user?->name ?? '-' }}</td>
                                        <td class="px-6 py-4">{{ $item->assignment?->notes ?? '-' }}</td>
                                        <td class="px-6 py-4">{{ $item->created_at->format('Y-m-d') }}</td>
                                        <form action="{{ route('assignments.revieweach') }}" method="POST" class="flex items-center">
                                            @method('patch')
                                            @csrf
                                            <input type="hidden" name="internal_product_item_id" value="{{ $item->id }}">
                                            <td class="px-6 py-4">
                                                <select name="use" required class="p-1 rounded border mr-2 w-32">
                                                    <option value="approved">Approve</option>
                                                    <option value="rejected">Reject</option>
                                                </select>
                                            </td>
                                            <td class="px-6 py-4">
                                                <button type="submit" class="bg-[#0f2360] text-white px-3 py-1 rounded hover:bg-[#091936]">
                                                    <x-lucide-check class="w-4 h-4" />
                                                </button>
                                            </td>
                                        </form>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        @endif

                        <div class="bg-white rounded-lg shadow-md p-6 flex sm:flex-row flex-col items-center">
                            <div class="flex-1">
                                <h2 class="text-lg font-medium mb-4 text-[#0f2360]">Rejected Assignments</h2>
                            </div>
                            <button
                                type="button"
                                @click="$dispatch('open-modal', 'assignments-rejected')"
                                class="bg-[#fd9c0a] text-white px-4 py-2 rounded hover:bg-[#e08c09]">
                                View Rejected Assignments
                            </button>
                        </div>

                        <x-modal name="assignments-rejected" focusable>
                            <div class="bg-white rounded-lg shadow-lg w-full max-w-3xl mx-4 overflow-hidden">
                                <div class="max-h-[90vh] overflow-y-auto rounded-lg scrollbar-thin">
                                    <div class="flex items-center justify-between p-4 border-b sticky top-0 bg-white z-10">
                                        <button x-on:click="$dispatch('close')" class="text-gray-600 hover:text-gray-900">
                                            <x-lucide-arrow-left class="w-6 h-6" />
                                        </button>
                                        <h2 class="text-lg font-semibold text-gray-800">Edit Internal Product</h2>
                                        <div class="w-6"></div>
                                    </div>

                                    <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg space-y-6">
                                        {{-- Rejected Assignments --}}
                                        <h2 class="text-xl font-semibold mb-4">Rejected Assignments</h2>

                                        @if ($rejectedproducts->isEmpty())
                                            <p class="text-gray-500">No rejected assignments.</p>
                                        @else
                                            <table class="min-w-full divide-y divide-gray-200">
                                                <thead class="bg-gray-50">
                                                <tr>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product Name</th>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reviewing Quantity</th>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Approved Quantity</th>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach ($rejectedproducts as $item)
                                                    <tr>
                                                        <td class="px-6 py-4">{{ $item->name }}</td>
                                                        <td class="px-6 py-4">{{ $item->rejected_count }}</td>
                                                        <td class="px-6 py-4">
                                                            <input type="number" name="quantity" min="0" max="{{ $item->rejected_count }}" class="p-2 border rounded w-20 mr-2" required>
                                                        </td>
                                                        <td class="px-6 py-4">
                                                            <button
                                                                @click="selected = {{ $item->id }}; qty = $el.closest('tr').querySelector('input[name=approved_quantity]').value; $dispatch('open-modal', 'edit-rejected')"
                                                                class="bg-[#0f2360] text-white px-3 py-1 rounded hover:bg-[#091936]"
                                                            >
                                                                <x-lucide-check class="w-4 h-4" />
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        @endif

                                        {{-- Individual Reviewing Assignments --}}
                                        <h2 class="text-xl font-semibold mb-4">Individual Reviewing Assignments</h2>

                                        @if ($rejectedassignments->isEmpty())
                                            <p class="text-gray-500">No individual assignments in reviewing state.</p>
                                        @else
                                            <table class="min-w-full divide-y divide-gray-200 mb-8">
                                                <thead class="bg-gray-50">
                                                <tr>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Assigned User</th>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Note</th>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach ($rejectedassignments as $item)
                                                    <tr>
                                                        <td class="px-6 py-4">{{ $item->internalProduct->name }}</td>
                                                        <td class="px-6 py-4">{{ $item->assignment?->user?->name ?? '-' }}</td>
                                                        <td class="px-6 py-4">{{ $item->assignment?->notes ?? '-' }}</td>
                                                        <td class="px-6 py-4">{{ $item->created_at->format('Y-m-d') }}</td>
                                                        <td class="px-6 py-4">
                                                            <button
                                                                @click="selected = {{ $item->id }}; $dispatch('open-modal', 'edit-each-rejected')"
                                                                class="bg-[#0f2360] text-white px-3 py-1 rounded hover:bg-[#091936]"
                                                            >
                                                                <x-lucide-check class="w-4 h-4" />
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </x-modal>
                        <x-modal name="edit-each-rejected" focusable>
                            <div class="bg-white rounded-lg shadow-lg w-full max-w-3xl mx-4 overflow-hidden mr-4">
                                <div class="flex items-center justify-between p-4 border-b sticky top-0 bg-white z-10">
                                    <header class="flex items-center space-x-4">
                                        <h2 class="text-lg font-semibold text-gray-800">Edit Assignment</h2>
                                    </header>
                                </div>
                                <div class="p-4">
                                    <p class="text-gray-500 mb-4">Are you sure you want to approve this assignment?</p>
                                    <div class="flex justify-end space-x-4">
                                        <form action="{{ route('assignments.revieweach') }}" method="POST" class="flex items-center">
                                            @method('patch')
                                            @csrf
                                            <input type="hidden" name="internal_product_item_id" :value="selected">
                                            <input type="hidden" name="use" value="approved">
                                            <button type="submit" class="bg-[#fd9c0a] text-white px-4 py-2 rounded hover:bg-[#e08c09]">
                                                Confirm
                                            </button>
                                        </form>
                                        <button
                                            type="button"
                                            @click="$dispatch('close')"
                                            class="bg-[#fd9c0a] text-white px-4 py-2 rounded hover:bg-[#e08c09]"
                                        >
                                            Close
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </x-modal>

                        <x-modal name="edit-rejected" focusable>
                            <div class="bg-white rounded-lg shadow-lg w-full max-w-3xl mx-4 overflow-hidden mr-4">
                                <div class="flex items-center justify-between p-4 border-b sticky top-0 bg-white z-10">
                                    <header>
                                        <h2 class="text-lg font-semibold text-gray-800">Edit Assignments</h2>
                                    </header>
                                </div>
                                <div class="p-4">
                                    <p class="text-gray-500 mb-4">Are you sure you want to approve those assignments?</p>
                                    <div class="flex justify-end space-x-4">
                                        <form action="{{ route('assignments.review') }}" method="POST" class="flex items-center">
                                            @method('patch')
                                            @csrf
                                            <input type="hidden" name="use" value="approved">
                                            <input type="hidden" name="product_id" :value="selected">
                                            <input type="hidden" name="approved_quantity" :value="qty">
                                            <button type="submit" class="bg-[#fd9c0a] text-white px-4 py-2 rounded hover:bg-[#e08c09]">
                                                Confirm
                                            </button>
                                        </form>
                                        <button
                                            type="button"
                                            @click="$dispatch('close')"
                                            class="bg-[#fd9c0a] text-white px-4 py-2 rounded hover:bg-[#e08c09]"
                                        >
                                            Close
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </x-modal>

                    </div>
                </main>
            </div>

        </div>

        <!-- Popup -->
        @include('components.popup')

    </div>
    <script>
        window.layoutHandler = () => ({
            qty: null,
            selected: null,

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
