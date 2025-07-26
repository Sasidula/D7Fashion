
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
<div
    x-data="{
        formData: { employeeId: '', materialId: '', quantity: '' },
        errors: {},
        success: false,
        employees: [
            { id: 1, name: 'John Doe' },
            { id: 2, name: 'Jane Smith' },
            { id: 3, name: 'Robert Johnson' },
            { id: 4, name: 'Emily Davis' },
            { id: 5, name: 'Michael Wilson' }
        ],
        materials: [
            { id: 1, name: 'Cotton Fabric' },
            { id: 2, name: 'Silk Fabric' },
            { id: 3, name: 'Buttons' },
            { id: 4, name: 'Zippers' },
            { id: 5, name: 'Thread Spools' }
        ],
        validate() {
            const newErrors = {};
            if (!this.formData.employeeId) newErrors.employeeId = 'Employee is required';
            if (!this.formData.materialId) newErrors.materialId = 'Material is required';
            if (!this.formData.quantity.trim()) {
                newErrors.quantity = 'Quantity is required';
            } else if (isNaN(parseInt(this.formData.quantity)) || parseInt(this.formData.quantity) <= 0) {
                newErrors.quantity = 'Please enter a valid quantity';
            }
            this.errors = newErrors;
            return Object.keys(newErrors).length === 0;
        },
        handleSubmit(event) {
            event.preventDefault();
            if (this.validate()) {
                console.log('Assignment created:', this.formData);
                this.success = true;
                setTimeout(() => {
                    this.success = false;
                    this.formData = { employeeId: '', materialId: '', quantity: '' };
                    this.errors = {};
                }, 3000);
            }
        }
    }"
    class="bg-white rounded-lg shadow-md p-6"
>
    <h1 class="text-2xl font-bold mb-6 text-[#0f2360]">Create Assignment</h1>

    <!-- Success Message -->
    <div
        x-show="success"
        class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6 flex items-center"
        x-cloak
    >
        <x-lucide-check class="w-5 h-5 mr-2" />
        <span>Assignment created successfully!</span>
    </div>

    <!-- Form -->
    <!-- <form method="POST" action="{/{/ /r/o/ute('assig/nments.create/') }}" @submit="handleSubmit"> -->
    <!-- @csrf -->
    <div class="max-w-2xl mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Employee -->
            <div>
                <label for="employeeId" class="block text-sm font-medium text-gray-700 mb-1">
                    Select Employee
                </label>
                <select
                    id="employeeId"
                    name="employeeId"
                    x-model="formData.employeeId"
                    class="block w-full border"
                    :class="{ 'border-red-500': errors.employeeId, 'border-gray-300': !errors.employeeId }"
                    class="rounded-md shadow-sm p-2 focus:ring-[#0f2360] focus:border-[#0f2360]"
                >
                    <option value="">-- Select an employee --</option>
                    <template x-for="employee in employees" :key="employee.id">
                        <option :value="employee.id" x-text="employee.name"></option>
                    </template>
                </select>
                <p x-show="errors.employeeId" class="mt-1 text-sm text-red-500" x-text="errors.employeeId"></p>
            </div>

            <!-- Material -->
            <div>
                <label for="materialId" class="block text-sm font-medium text-gray-700 mb-1">
                    Select Material
                </label>
                <select
                    id="materialId"
                    name="materialId"
                    x-model="formData.materialId"
                    class="block w-full border"
                    :class="{ 'border-red-500': errors.materialId, 'border-gray-300': !errors.materialId }"
                    class="rounded-md shadow-sm p-2 focus:ring-[#0f2360] focus:border-[#0f2360]"
                >
                    <option value="">-- Select a material --</option>
                    <template x-for="material in materials" :key="material.id">
                        <option :value="material.id" x-text="material.name"></option>
                    </template>
                </select>
                <p x-show="errors.materialId" class="mt-1 text-sm text-red-500" x-text="errors.materialId"></p>
            </div>

            <!-- Quantity -->
            <div>
                <label for="quantity" class="block text-sm font-medium text-gray-700 mb-1">
                    Quantity
                </label>
                <input
                    type="number"
                    id="quantity"
                    name="quantity"
                    x-model="formData.quantity"
                    class="block w-full border"
                    :class="{ 'border-red-500': errors.quantity, 'border-gray-300': !errors.quantity }"
                    class="rounded-md shadow-sm p-2 focus:ring-[#0f2360] focus:border-[#0f2360]"
                />
                <p x-show="errors.quantity" class="mt-1 text-sm text-red-500" x-text="errors.quantity"></p>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="mt-8">
            <button
                type="submit"
                @click="handleSubmit"
                class="w-full bg-[#fd9c0a] text-white py-3 px-4 rounded-md hover:bg-[#e08c09] focus:outline-none flex items-center justify-center"
            >
                <x-lucide-plus class="w-5 h-5 mr-2" />
                Create Assignment
            </button>
        </div>
    </div>
    <!-- </form> -->
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
