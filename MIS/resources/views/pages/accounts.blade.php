
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
    x-data="accountComponent()"
    x-init="initPopup()"
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
    <!-- <form method="POST" action="{/{ ro/ute('income-o/utcome.create/') }}" @submit="handleSubmit"> -->
    <!-- @csrf -->
    <div class="max-w-2xl mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Title -->
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                    Title
                </label>
                <select
                    id="title"
                    name="title"
                    x-model="formData.title"
                    class="block w-full border"
                    :class="{ 'border-red-500': errors.title, 'border-gray-300': !errors.title }"
                    class="rounded-md shadow-sm p-2 focus:ring-[#0f2360] focus:border-[#0f2360]"
                >
                    <option value="">-- Select a title --</option>
                    <template x-for="title in titles" :key="title">
                        <option :value="title" x-text="title"></option>
                    </template>
                </select>
                <p x-show="errors.title" class="mt-1 text-sm text-red-500" x-text="errors.title"></p>
            </div>

            <!-- Type -->
            <div>
                <label for="type" class="block text-sm font-medium text-gray-700 mb-1">
                    Type
                </label>
                <select
                    id="type"
                    name="type"
                    x-model="formData.type"
                    class="block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-[#0f2360] focus:border-[#0f2360]"
                >
                    <option value="income">Income</option>
                    <option value="outcome">Outcome</option>
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
                    class="block w-full border"
                    :class="{ 'border-red-500': errors.amount, 'border-gray-300': !errors.amount }"
                    class="rounded-md shadow-sm p-2 focus:ring-[#0f2360] focus:border-[#0f2360]"
                />
                <p x-show="errors.amount" class="mt-1 text-sm text-red-500" x-text="errors.amount"></p>
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                    Description (Optional)
                </label>
                <input
                    type="text"
                    id="description"
                    name="description"
                    x-model="formData.description"
                    class="block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-[#0f2360] focus:border-[#0f2360]"
                />
            </div>
        </div>

        <!-- Submit Button -->
        <div class="mt-8">
            <button
                type="submit"
                @click="handleSubmit"
                class="w-full bg-[#fd9c0a] text-white py-3 px-4 rounded-md hover:bg-[#e08c09] focus:outline-none flex items-center justify-center"
            >
                <x-lucide-wallet class="w-5 h-5 mr-2" />
                Add Entry
            </button>
        </div>
    </div>
    <!-- </form> -->
</div>

<div class="bg-white rounded-lg shadow-md p-6 mt-8 flex flex-col md:flex-row gap-6">
    <!-- Add New Title -->
    <div class="flex-1">
        <h2 class="text-lg font-medium mb-4 text-[#0f2360]">Add New Title</h2>
        <button
            @click="$dispatch('popup-open', {
                title: 'Add New Title',
                view: 'account-add-title',
                data: null
            })"
            class="w-full bg-[#fd9c0a] text-white py-2 px-4 rounded-md hover:bg-[#e08c09] focus:outline-none flex items-center justify-center"
        >
            <x-lucide-plus class="w-5 h-5 mr-2" />
            Add
        </button>
    </div>

    <!-- View Entries -->
    <div class="flex-1">
        <h2 class="text-lg font-medium mb-4 text-[#0f2360]">View Entries</h2>
        <button
            @click="$dispatch('popup-open', {
                title: 'View Entries',
                view: 'account-view-entries',
                data: null
            })"
            class="w-full bg-[#fd9c0a] text-white py-2 px-4 rounded-md hover:bg-[#e08c09] focus:outline-none flex items-center justify-center"
        >
            <x-lucide-eye class="w-5 h-5 mr-2" />
            View
        </button>
    </div>
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

<script>
    function accountComponent() {
        return {
            popup: {
                open: false,
                title: '',
                content: '',
                data: null,
                requestId: 0,
            },
            formData: { title: '', type: 'income', amount: '', description: '' },
            errors: {},
            success: false,
            titles: [
                'Sales Revenue', 'Supplier Payment', 'Utility Bill', 'Employee Bonus', 'Maintenance Cost'
            ],
            validate() {
                const newErrors = {};
                if (!this.formData.title.trim()) newErrors.title = 'Title is required';
                if (!this.formData.amount.trim()) {
                    newErrors.amount = 'Amount is required';
                } else if (isNaN(parseFloat(this.formData.amount)) || parseFloat(this.formData.amount) <= 0) {
                    newErrors.amount = 'Please enter a valid amount';
                }
                this.errors = newErrors;
                return Object.keys(newErrors).length === 0;
            },
            handleSubmit(event) {
                event.preventDefault();
                if (this.validate()) {
                    console.log('Entry added:', this.formData);
                    this.entries.push({
                        id: this.entries.length + 1,
                        title: this.formData.title,
                        type: this.formData.type,
                        amount: parseFloat(this.formData.amount),
                        description: this.formData.description
                    });
                    this.success = true;
                    setTimeout(() => {
                        this.success = false;
                        this.formData = { title: '', type: 'income', amount: '', description: '' };
                        this.errors = {};
                    }, 3000);
                }
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
</script>
