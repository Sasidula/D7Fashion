
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
            @include('components.sidebar', ['currentPage' => 'accounts'])

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
                        class="bg-white rounded-lg shadow-md p-6"
                    >
                        <h1 class="text-2xl font-bold mb-6 text-[#0f2360]">Income/Outcome</h1>

                        <!-- Success Message -->
                        <div
                            x-show="success"
                            class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6 flex items-center"
                            x-cloak
                        >
                            <x-lucide-check class="w-5 h-5 mr-2" />
                            <span>Entry added successfully!</span>
                        </div>

                        <!-- Form -->
                        <form method="POST" action="{{ route('expense.store') }}">
                        @csrf
                            <div class="max-w-2xl mx-auto">
                                <div class="grid grid-cols-1 gap-6">

                                    <div class="flex items-end gap-4">
                                        <!-- Title -->
                                        <div class="flex-1">
                                            <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                                                Title
                                            </label>
                                            <select
                                                id="title"
                                                name="title"
                                                class="w-full block border rounded-md shadow-sm p-2 focus:ring-[#0f2360] focus:border-[#0f2360]"
                                                required
                                            >
                                                <option value="">-- Select a title --</option>
                                                    @foreach ($options as $title)
                                                        <option :value="{{ $title->id}}">{{ $title->title }}</option>
                                                    @endforeach
                                            </select>
                                        </div>
                                        <!-- edit -->
                                        <div>
                                            <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                                                Edit
                                            </label>
                                            <button
                                                type="button"
                                                @click="$dispatch('open-modal', 'edit-modal')"
                                                class="bg-[#fd9c0a] text-white py-2 px-4 rounded-md hover:bg-[#e08c09] flex items-center"
                                            >
                                                <x-lucide-pencil class="w-5 h-5 mr-2" />
                                                Edit
                                            </button>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <!-- Type -->
                                        <div class="col-span-1">
                                            <label for="type" class="block text-sm font-medium text-gray-700 mb-1">
                                                Type
                                            </label>
                                            <select
                                                id="type"
                                                name="type"
                                                class="block w-full border rounded-md shadow-sm p-2 focus:ring-[#0f2360] focus:border-[#0f2360]"
                                                required
                                            >
                                                <option value="income">Income</option>
                                                <option value="outcome">Outcome</option>
                                            </select>
                                        </div>

                                        <!-- Amount -->
                                        <div class="col-span-1">
                                            <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">
                                                Amount ($)
                                            </label>
                                            <input
                                                type="text"
                                                id="amount"
                                                name="amount"
                                                class="block w-full border rounded-md shadow-sm p-2 focus:ring-[#0f2360] focus:border-[#0f2360]"
                                                required
                                            />
                                        </div>
                                    </div>

                                    <!-- Description -->
                                    <div>
                                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                                            Description (Optional)
                                        </label>
                                        <textarea
                                            id="description"
                                            name="description"
                                            class="block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-[#0f2360] focus:border-[#0f2360]"
                                            rows="3"
                                        ></textarea>
                                    </div>

                                    <!-- Submit Button -->
                                    <div class="mt-8">
                                        <button
                                            type="submit"
                                            class="w-full bg-[#fd9c0a] text-white py-3 px-4 rounded-md hover:bg-[#e08c09] focus:outline-none flex items-center justify-center"
                                        >
                                            <x-lucide-wallet class="w-5 h-5 mr-2" />
                                            Add Entry
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="bg-white rounded-lg shadow-md p-6 mt-6">
                        <h2 class="text-2xl font-bold mb-6 text-[#2a3f7d]">Entries</h2>
                        @if($records->count() === 0)
                            <x-lucide-wallet class="mx-auto text-gray-300 mb-3 h-12 w-12" />
                            <p class="text-gray-500">No entries available</p>
                        @else
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Title
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Type
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Amount
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Description
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Action
                                    </th>
                                </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($records as $row)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{$row->expense->title}}</td>
                                        <td class="px-6 py-4 whitespace-nowrap {{$row->type === 'income' ? 'text-green-600' : 'text-red-600'}}">{{$row->type}}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">Rs.{{$row->amount}}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{$row->description}}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <button
                                                @click="selectedRecord = {{ $row->id }}; $dispatch('open-modal', 'delete-modal')"
                                                class="text-red-600 hover:text-red-800"
                                            >
                                                <x-lucide-trash class="w-5 h-5" />
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </main>
            </div>

{{--            edit titles--}}
            <x-modal name="edit-modal" :scrollable="true">
                <!-- Add/Remove Titles -->
                <div class="mt-8 max-w-2xl mx-auto">
                    <div class="flex items-center justify-between p-4 border-b border-gray-200 sticky top-0 bg-white z-10">
                        <button x-on:click="$dispatch('close')" class="text-gray-600 hover:text-gray-900">
                            <x-lucide-arrow-left class="w-6 h-6" />
                        </button>
                        <div class="w-6"></div>
                    </div>
                    <h2 class="text-lg font-medium mb-4 text-[#0f2360]">Manage Titles</h2>
{{--                    add new form--}}
                    <div class="flex space-x-4">
                        <input
                            type="text"
                            x-model="newTitle"
                            placeholder="New title"
                            class="block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-[#0f2360] focus:border-[#0f2360]"
                        />
                        <button
                            class="bg-[#fd9c0a] text-white py-2 px-4 rounded-md hover:bg-[#e08c09] flex items-center"
                        >
                            <x-lucide-plus class="w-5 h-5 mr-2" />
                            Add
                        </button>
                    </div>
{{--                    delete title--}}
                    <div class="mt-4">
                        @foreach($options as $title)
                            <div class="flex justify-between items-center py-2">
                                <span>{{ $title->title }}</span>
                                <button
                                    class="text-red-600 hover:text-red-800"
                                    @click="selectedItem = {{ $title->id }}; $dispatch('open-modal', 'delete-title')"
                                >
                                    <x-lucide-trash class="w-5 h-5" />
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
            </x-modal>

{{--            delete record--}}
            <x-modal name="delete-modal" focusable>
                <div class="p-6">
                    <p class="mb-4 text-gray-700">Are you sure you want to delete this record?</p>
                    <form method="POST" action="{{ route('pettyCash.destroy') }}">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="id" :value="selected">
                        <div class="flex justify-end space-x-3">
                            <button type="button" @click="$dispatch('close')" class="px-4 py-2 text-gray-600 bg-gray-200 rounded-md hover:bg-gray-300">
                                Cancel
                            </button>
                            <button type="submit" class="px-4 py-2 text-white bg-red-600 rounded-md hover:bg-red-700">
                                Delete
                            </button>
                        </div>
                    </form>
                </div>
            </x-modal>

{{--            delete title--}}
            <x-modal name="delete-title" focusable>
                <div class="p-6">
                    <p class="mb-4 text-gray-700">Are you sure you want to delete this record?</p>
                    <form method="POST" action="{{ route('title.destroy') }}">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="id" :value="selected">
                        <div class="flex justify-end space-x-3">
                            <button type="button" @click="$dispatch('close')" class="px-4 py-2 text-gray-600 bg-gray-200 rounded-md hover:bg-gray-300">
                                Cancel
                            </button>
                            <button type="submit" class="px-4 py-2 text-white bg-red-600 rounded-md hover:bg-red-700">
                                Delete
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

            selectedItem: null,

            selectedRecord: null,

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
