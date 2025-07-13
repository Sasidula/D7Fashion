<div
    x-data="{
        selectedReportType: 'sales',
        dateRange: { start: '2023-01-01', end: '2023-01-31' },
        showFilters: false,
        reportTypes: [
            { id: 'sales', name: 'Sales Report' },
            { id: 'inventory', name: 'Inventory Report' },
            { id: 'employee', name: 'Employee Performance' },
            { id: 'attendance', name: 'Attendance Report' },
            { id: 'production', name: 'Production Report' }
        ],
        salesData: [
            { id: 1, date: '2023-01-05', product: 'T-Shirt', quantity: 25, amount: 625.00 },
            { id: 2, date: '2023-01-12', product: 'Jeans', quantity: 15, amount: 750.00 },
            { id: 3, date: '2023-01-18', product: 'Jacket', quantity: 10, amount: 1200.00 },
            { id: 4, date: '2023-01-23', product: 'Dress', quantity: 20, amount: 1600.00 },
            { id: 5, date: '2023-01-30', product: 'Shirt', quantity: 30, amount: 900.00 }
        ],
        inventoryData: [
            { id: 1, product: 'Cotton Fabric', stock: 500, unit: 'meters', reorderLevel: 100 },
            { id: 2, product: 'Buttons', stock: 2000, unit: 'pieces', reorderLevel: 500 },
            { id: 3, product: 'Zippers', stock: 300, unit: 'pieces', reorderLevel: 50 },
            { id: 4, product: 'Thread Spools', stock: 150, unit: 'spools', reorderLevel: 30 },
            { id: 5, product: 'Denim Fabric', stock: 200, unit: 'meters', reorderLevel: 50 }
        ],
        toggleFilters() {
            this.showFilters = !this.showFilters;
        },
        handlePrint() {
            window.print();
        },
        handleExport() {
            console.log('Exporting report...');
            alert('Report exported successfully!');
        },
        calculateSalesTotals() {
            return {
                quantity: this.salesData.reduce((sum, item) => sum + item.quantity, 0),
                amount: this.salesData.reduce((sum, item) => sum + item.amount, 0).toFixed(2)
            };
        }
    }"
    class="bg-white rounded-lg shadow-md p-6"
>
    <!-- Header and Actions -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <h1 class="text-2xl font-bold text-[#0f2360] mb-4 md:mb-0">Reports</h1>
        <div class="flex flex-wrap gap-2">
            <button
                @click="toggleFilters"
                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none"
            >
                <x-lucide-filter class="w-4 h-4 mr-2" />
                <span x-text="showFilters ? 'Hide Filters' : 'Show Filters'"></span>
            </button>
            <button
                @click="handlePrint"
                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none"
            >
                <x-lucide-printer class="w-4 h-4 mr-2" />
                Print
            </button>
            <button
                @click="handleExport"
                class="inline-flex items-center px-4 py-2 bg-[#fd9c0a] border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-[#e08c09] focus:outline-none"
            >
                <x-lucide-download class="w-4 h-4 mr-2" />
                Export
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div
        x-show="showFilters"
        class="bg-gray-50 p-4 rounded-lg mb-6"
        x-cloak
    >
        <h2 class="text-lg font-medium mb-4">Report Filters</h2>
        <!-- Form commented out for UI testing -->
        <!-- <form method="POST" action="{/{ rou/te('repo/rts.filter'/) }}"> -->
        <!-- @csrf -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Report Type -->
            <div>
                <label for="reportType" class="block text-sm font-medium text-gray-700 mb-1">
                    Report Type
                </label>
                <select
                    id="reportType"
                    x-model="selectedReportType"
                    class="block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-[#0f2360] focus:border-[#0f2360]"
                >
                    <template x-for="type in reportTypes" :key="type.id">
                        <option :value="type.id" x-text="type.name"></option>
                    </template>
                </select>
            </div>
            <!-- Start Date -->
            <div>
                <label for="startDate" class="block text-sm font-medium text-gray-700 mb-1">
                    Start Date
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <x-lucide-calendar class="w-4 h-4 text-gray-400" />
                    </div>
                    <input
                        type="date"
                        id="startDate"
                        name="start"
                        x-model="dateRange.start"
                        class="block w-full pl-10 border border-gray-300 rounded-md shadow-sm p-2 focus:ring-[#0f2360] focus:border-[#0f2360]"
                    />
                </div>
            </div>
            <!-- End Date -->
            <div>
                <label for="endDate" class="block text-sm font-medium text-gray-700 mb-1">
                    End Date
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <x-lucide-calendar class="w-4 h-4 text-gray-400" />
                    </div>
                    <input
                        type="date"
                        id="endDate"
                        name="end"
                        x-model="dateRange.end"
                        class="block w-full pl-10 border border-gray-300 rounded-md shadow-sm p-2 focus:ring-[#0f2360] focus:border-[#0f2360]"
                    />
                </div>
            </div>
        </div>
        <!-- </form> -->
    </div>

    <!-- Report Data -->
    <div class="bg-white border rounded-lg overflow-hidden">
        <div x-show="selectedReportType === 'sales'">
            <h3 class="text-lg font-medium mb-4 px-6 pt-4">
                Sales Report: <span x-text="dateRange.start"></span> to <span x-text="dateRange.end"></span>
            </h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Date
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Product
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Quantity
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Amount
                        </th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    <template x-for="item in salesData" :key="item.id">
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap" x-text="item.date"></td>
                            <td class="px-6 py-4 whitespace-nowrap" x-text="item.product"></td>
                            <td class="px-6 py-4 whitespace-nowrap" x-text="item.quantity"></td>
                            <td class="px-6 py-4 whitespace-nowrap" x-text="'$' + item.amount.toFixed(2)"></td>
                        </tr>
                    </template>
                    <tr class="bg-gray-50 font-medium">
                        <td colSpan="2" class="px-6 py-4 text-right">
                            Total:
                        </td>
                        <td class="px-6 py-4" x-text="calculateSalesTotals().quantity"></td>
                        <td class="px-6 py-4" x-text="'$' + calculateSalesTotals().amount"></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div x-show="selectedReportType === 'inventory'">
            <h3 class="text-lg font-medium mb-4 px-6 pt-4">
                Inventory Report: As of <span x-text="new Date().toLocaleDateString()"></span>
            </h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Product
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Current Stock
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Unit
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Reorder Level
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    <template x-for="item in inventoryData" :key="item.id">
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap" x-text="item.product"></td>
                            <td class="px-6 py-4 whitespace-nowrap" x-text="item.stock"></td>
                            <td class="px-6 py-4 whitespace-nowrap" x-text="item.unit"></td>
                            <td class="px-6 py-4 whitespace-nowrap" x-text="item.reorderLevel"></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                        :class="{
                                            'bg-red-100 text-red-800': item.stock <= item.reorderLevel,
                                            'bg-yellow-100 text-yellow-800': item.stock > item.reorderLevel && item.stock <= item.reorderLevel * 1.5,
                                            'bg-green-100 text-green-800': item.stock > item.reorderLevel * 1.5
                                        }"
                                        x-text="item.stock <= item.reorderLevel ? 'Reorder Now' : item.stock <= item.reorderLevel * 1.5 ? 'Low Stock' : 'In Stock'"
                                    ></span>
                            </td>
                        </tr>
                    </template>
                    </tbody>
                </table>
            </div>
        </div>
        <div x-show="selectedReportType !== 'sales' && selectedReportType !== 'inventory'" class="text-center py-8">
            <x-lucide-file-text class="mx-auto text-gray-300 mb-3 h-12 w-12" />
            <p class="text-gray-500">Select a report type to view data</p>
        </div>
    </div>
</div>
