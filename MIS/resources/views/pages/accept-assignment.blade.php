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

            <!-- Page content wrapper -->
            <div class="flex-1 overflow-y-auto transition-all duration-300 ease-in-out">
                <main class="p-6">
<div
    x-data="{
        formData: { employeeId: '', productAssignments: {} },
        errors: {},
        success: false,
        employees: [
            { id: 1, name: 'John Doe', assignedMaterial: { materialId: 1, quantity: 10 } },
            { id: 2, name: 'Jane Smith', assignedMaterial: { materialId: 2, quantity: 15 } },
            { id: 3, name: 'Robert Johnson', assignedMaterial: { materialId: 3, quantity: 20 } },
            { id: 4, name: 'Emily Davis', assignedMaterial: { materialId: 4, quantity: 8 } },
            { id: 5, name: 'Michael Wilson', assignedMaterial: { materialId: 5, quantity: 12 } }
        ],
        products: [
            { id: 1, name: 'T-Shirt' },
            { id: 2, name: 'Jeans' },
            { id: 3, name: 'Jacket' },
            { id: 4, name: 'Dress' },
            { id: 5, name: 'Socks' }
        ],
        materials: [
            { id: 1, name: 'Cotton Fabric' },
            { id: 2, name: 'Silk Fabric' },
            { id: 3, name: 'Buttons' },
            { id: 4, name: 'Zippers' },
            { id: 5, name: 'Thread Spools' }
        ],
        getAssignedQuantity() {
            const employee = this.employees.find(e => e.id == this.formData.employeeId);
            return employee ? employee.assignedMaterial.quantity : 0;
        },
        getAssignedMaterialName() {
            const employee = this.employees.find(e => e.id == this.formData.employeeId);
            if (!employee) return '';
            const material = this.materials.find(m => m.id == employee.assignedMaterial.materialId);
            return material ? material.name : '';
        },
        validate() {
            const newErrors = {};
            if (!this.formData.employeeId) {
                newErrors.employeeId = 'Employee is required';
            } else {
                const totalAssigned = Object.values(this.formData.productAssignments)
                .reduce((sum, qty) => sum + (parseInt(qty) || 0), 0);
                if (totalAssigned !== this.getAssignedQuantity()) {
                    newErrors.productAssignments = `Total assigned quantity must equal ${this.getAssignedQuantity()}`;
                }
            }
            this.errors = newErrors;
            return Object.keys(newErrors).length === 0;
        },
        handleSubmit(event) {
            event.preventDefault();
            if (this.validate()) {
                console.log('Assignment completed:', this.formData);
                this.success = true;
                setTimeout(() => {
                    this.success = false;
                    this.formData = { employeeId: '', productAssignments: {} };
                    this.errors = {};
                }, 3000);
            }
        }
    }"
    class="bg-white rounded-lg shadow-md p-6"
>
<h1 class="text-2xl font-bold mb-6 text-[#0f2360]">Complete Assignment</h1>

<!-- Success Message -->
<div
    x-show="success"
    class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6 flex items-center"
    x-cloak
>
    <x-lucide-check class="w-5 h-5 mr-2" />
    <span>Assignment completed successfully!</span>
</div>

<!-- Form -->
<!-- <form method="POST" action="{/{ r/oute('assi/gnments.comple/te'/) }}" @submit="handleSubmit"> -->
<!-- @csrf -->
<div class="max-w-2xl mx-auto">
    <div class="grid grid-cols-1 gap-6">
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

        <!-- Assigned Material Info -->
        <div x-show="formData.employeeId" class="bg-gray-50 p-4 rounded-md">
            <p class="text-sm font-medium text-gray-700">
                Assigned Material: <span x-text="getAssignedMaterialName()"></span>
            </p>
            <p class="text-sm font-medium text-gray-700">
                Assigned Quantity: <span x-text="getAssignedQuantity()"></span>
            </p>
            <p class="text-sm text-gray-500 mt-2">Distribute the assigned quantity across products below:</p>
        </div>

        <!-- Product Assignments -->
        <div x-show="formData.employeeId">
            <template x-for="product in products" :key="product.id">
                <div class="mb-4">
                    <label :for="'product-' + product.id" class="block text-sm font-medium text-gray-700 mb-1">
                        <span x-text="product.name"></span> Quantity
                    </label>
                    <input
                        :id="'product-' + product.id"
                        type="number"
                        min="0"
                        x-model.number="formData.productAssignments[product.id]"
                        class="block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-[#0f2360] focus:border-[#0f2360]"
                    />
                </div>
            </template>
            <p x-show="errors.productAssignments" class="mt-1 text-sm text-red-500" x-text="errors.productAssignments"></p>
        </div>
    </div>

    <!-- Submit Button -->
    <div class="mt-8" x-show="formData.employeeId">
        <button
            type="submit"
            @click="handleSubmit"
            class="w-full bg-[#fd9c0a] text-white py-3 px-4 rounded-md hover:bg-[#e08c09] focus:outline-none flex items-center justify-center"
        >
            <x-lucide-check class="w-5 h-5 mr-2" />
            Complete Assignment
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
