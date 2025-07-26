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
            <div class="flex-1 overflow-y-auto transition-all duration-300 ease-in-out">
                <main class="p-6">
<div
    x-data="{
        assignments: [
            { id: 1, employeeId: 1, employeeName: 'John Doe', date: '2023-01-05', quantity: 10, approvedQuantity: 8 },
            { id: 2, employeeId: 2, employeeName: 'Jane Smith', date: '2023-01-10', quantity: 15, approvedQuantity: 12 },
            { id: 3, employeeId: 3, employeeName: 'Robert Johnson', date: '2023-01-15', quantity: 20, approvedQuantity: 18 },
            { id: 4, employeeId: 4, employeeName: 'Emily Davis', date: '2023-01-20', quantity: 8, approvedQuantity: 8 },
            { id: 5, employeeId: 5, employeeName: 'Michael Wilson', date: '2023-01-25', quantity: 12, approvedQuantity: 10 }
        ],
        approvedQuantities: {},
        errors: {},
        success: false,
        validate(id) {
            const newErrors = {};
            const assignment = this.assignments.find(a => a.id === id);
            const approvedQty = parseInt(this.approvedQuantities[id] || assignment.approvedQuantity);
            if (isNaN(approvedQty) || approvedQty < 0) {
                newErrors[id] = 'Please enter a valid approved quantity';
            }
            this.errors = { ...this.errors, [id]: newErrors[id] };
            return !newErrors[id];
        },
        handleSubmit(id) {
            if (this.validate(id)) {
                const assignment = this.assignments.find(a => a.id === id);
                assignment.approvedQuantity = parseInt(this.approvedQuantities[id] || assignment.approvedQuantity);
                console.log('Approved quantity updated for assignment:', assignment);
                this.success = true;
                setTimeout(() => {
                    this.success = false;
                }, 3000);
            }
        }
    }"
    class="bg-white rounded-lg shadow-md p-6"
>
    <h1 class="text-2xl font-bold mb-6 text-[#0f2360]">Manage Assignments</h1>

    <!-- Success Message -->
    <div
        x-show="success"
        class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6 flex items-center"
        x-cloak
    >
        <x-lucide-check class="w-5 h-5 mr-2" />
        <span>Approved quantities updated successfully!</span>
    </div>

    <div x-show="assignments.length === 0" class="text-center py-8">
        <x-lucide-list class="mx-auto text-gray-300 mb-3 h-12 w-12" />
        <p class="text-gray-500">No assignments available</p>
    </div>

    <div x-show="assignments.length > 0" class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
            <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Employee Name
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Date
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Quantity
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Approved Quantity
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Action
                </th>
            </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
            <template x-for="assignment in assignments" :key="assignment.id">
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap" x-text="assignment.employeeName"></td>
                    <td class="px-6 py-4 whitespace-nowrap" x-text="assignment.date"></td>
                    <td class="px-6 py-4 whitespace-nowrap" x-text="assignment.quantity"></td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <input
                            type="number"
                            min="0"
                            x-model.number="approvedQuantities[assignment.id]"
                            :placeholder="assignment.approvedQuantity"
                            class="block w-24 border"
                            :class="{ 'border-red-500': errors[assignment.id], 'border-gray-300': !errors[assignment.id] }"
                            class="rounded-md shadow-sm p-2 focus:ring-[#0f2360] focus:border-[#0f2360]"
                        />
                        <p x-show="errors[assignment.id]" class="mt-1 text-sm text-red-500" x-text="errors[assignment.id]"></p>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <button
                            @click="handleSubmit(assignment.id)"
                            class="bg-[#fd9c0a] text-white py-2 px-4 rounded-md hover:bg-[#e08c09]"
                        >
                            <x-lucide-check class="w-5 h-5" />
                        </button>
                    </td>
                </tr>
            </template>
            </tbody>
        </table>
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
