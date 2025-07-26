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
            @include('components.sidebar', ['currentPage' => 'add-bonus-deduction'])

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
                        x-data= "employee_manager()"
                        x-init="formData = {
                            user_id: '{{ old('user_id') }}',
                            action: '{{ old('action', 'add') }}',
                            amount: '{{ old('amount') }}',
                            title: '{{ old('title') }}'
                        }"
                        class="bg-white rounded-lg shadow-md p-6"
                    >
                        <h1 class="text-2xl font-bold mb-6 text-[#0f2360]">Add Bonus/Deduction</h1>

                        <!-- Success Message -->
                        <div
                            x-show="success"
                            x-transition
                            x-init="setTimeout(() => success = false, 10000)"
                            x-cloak
                            class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6 flex items-center"
                        >
                            <x-lucide-check class="w-5 h-5 mr-2" />
                            <span>Bonus/Deduction added successfully!</span>
                        </div>

                        <!-- Form -->
                        <form method="POST" action="{{ route('employee_bonus_adjustments.store') }}" @submit="handleSubmit">
                            @csrf
                            <div class="max-w-2xl mx-auto">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Employee -->
                                    <div>
                                        <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1">
                                            Select Employee
                                        </label>
                                        <select
                                            id="user_id"
                                            name="user_id"
                                            x-model="formData.user_id"
                                            class="block w-full rounded-md shadow-sm p-2 focus:ring-[#0f2360] focus:border-[#0f2360]"
                                            :class="{ 'border-red-500': errors.user_id, 'border-gray-300': !errors.user_id }"
                                        >
                                            <option value="">-- Select an employee --</option>
                                            <template x-for="employee in employees" :key="employee.id">
                                                <option :value="employee.id" x-text="employee.name"></option>
                                            </template>
                                        </select>
                                        <p x-show="errors.user_id" class="mt-1 text-sm text-red-500" x-text="errors.user_id"></p>
                                    </div>

                                    <!-- Action -->
                                    <div>
                                        <label for="action" class="block text-sm font-medium text-gray-700 mb-1">
                                            Type
                                        </label>
                                        <select
                                            id="action"
                                            name="action"
                                            x-model="formData.action"
                                            class="block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-[#0f2360] focus:border-[#0f2360]"
                                        >
                                            <option value="add">Bonus</option>
                                            <option value="remove">Deduction</option>
                                        </select>
                                    </div>

                                    <!-- Amount -->
                                    <div>
                                        <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">
                                            Amount ($)
                                        </label>
                                        <input
                                            type="text"
                                            id="amount"
                                            name="amount"
                                            x-model="formData.amount"
                                            class="block w-full rounded-md shadow-sm p-2 focus:ring-[#0f2360] focus:border-[#0f2360]"
                                            :class="{ 'border-red-500': errors.amount, 'border-gray-300': !errors.amount }"
                                        />
                                        <p x-show="errors.amount" class="mt-1 text-sm text-red-500" x-text="errors.amount"></p>
                                    </div>

                                    <!-- Title -->
                                    <div>
                                        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                                            Description / Title
                                        </label>
                                        <input
                                            type="text"
                                            id="title"
                                            name="title"
                                            x-model="formData.title"
                                            class="block w-full rounded-md shadow-sm p-2 focus:ring-[#0f2360] focus:border-[#0f2360]"
                                            :class="{ 'border-red-500': errors.title, 'border-gray-300': !errors.title }"
                                        />
                                        <p x-show="errors.title" class="mt-1 text-sm text-red-500" x-text="errors.title"></p>
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div class="mt-8">
                                    <button
                                        type="submit"
                                        class="w-full bg-[#fd9c0a] text-white py-3 px-4 rounded-md hover:bg-[#e08c09] focus:outline-none flex items-center justify-center"
                                    >
                                        <x-lucide-dollar-sign class="w-5 h-5 mr-2" />
                                        Add Bonus/Deduction
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </main>
            </div>
        </div>

        <!-- Popup -->
        @include('components.popup')

    </div>
    <script>
        function employee_manager() {
            return {
                formData: { user_id: '', action: 'add', amount: '', title: '' },
                errors: {},
                success: {{ session('success') ? 'true' : 'false' }},
                employees: @json($employees), // Pass from controller
                validate() {
                    const newErrors = {};
                    if (!this.formData.user_id) newErrors.user_id = 'Employee is required';
                    if (!this.formData.amount.trim()) {
                        newErrors.amount = 'Amount is required';
                    } else if (isNaN(parseFloat(this.formData.amount)) || parseFloat(this.formData.amount) <= 0) {
                        newErrors.amount = 'Please enter a valid amount';
                    }
                    if (!this.formData.title.trim()) {
                        newErrors.title = 'Title is required';
                    }
                    this.errors = newErrors;
                    return Object.keys(newErrors).length === 0;
                },
                handleSubmit(event) {
                    if (!this.validate()) {
                        event.preventDefault();
                    }
                }
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
