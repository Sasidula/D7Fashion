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
            @include('components.sidebar', ['currentPage' => 'reports'])

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
                    <div
                        x-data="{
                            selectedReportType: 'sales',
                            showFilters: false,
                            reportTypes: [
                                { id: 'sales', name: 'Sales Report' },
                                { id: 'expenses', name: 'Expenses Report' },
                                { id: 'petty', name: 'Petty Cash Report' },
                                { id: 'employee', name: 'Employee Performance' },
                                { id: 'salary', name: 'Salary Report' }
                            ],
                            year: '{{ $year }}',
                            month: '{{ $month }}',
                            monthName() {
                                if (this.month) {
                                    return new Date(2000, this.month - 1, 1).toLocaleString('default', { month: 'long' });
                                }
                                return '';
                            },
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
                                    @click="handlePrint"
                                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none"
                                >
                                    <x-lucide-printer class="w-4 h-4 mr-2" />
                                    Print
                                </button>
                                <form action="{{route('report.export')}}" method="POST">
                                    @method('POST')
                                    @csrf

                                    <input type="hidden" name="reportType" x-model="selectedReportType">
                                    <input type="hidden" name="year" x-model="year">
                                    <input type="hidden" name="month" x-model="month">

                                    <button
                                        type="submit"
                                        class="inline-flex items-center px-4 py-2 bg-[#fd9c0a] border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-[#e08c09] focus:outline-none"
                                    >
                                        <x-lucide-download class="w-4 h-4 mr-2" />
                                        Export
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
                            <div>
                                <button
                                    @click="toggleFilters"
                                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none"
                                >
                                    <x-lucide-filter class="w-4 h-4 mr-2" />
                                    <span x-text="showFilters ? 'Hide Filters' : 'Show Filters'"></span>
                                </button>
                            </div>
                            <!-- Report Type -->
                            <div class="flex flex-wrap gap-2">
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
                        </div>

                        <!-- Filters -->
                        <div
                            x-show="showFilters"
                            class="bg-gray-50 p-4 rounded-lg mb-6"
                            x-cloak
                        >
                            <h2 class="text-lg font-medium mb-4">Report Filters</h2>
                            <form method="GET" action="{{ url('/monthly-expenses/report') }}" class="space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <!-- Year -->
                                    <div>
                                        <label for="year" class="block text-sm font-medium text-gray-700 mb-1">
                                            Year
                                        </label>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <x-lucide-calendar class="w-4 h-4 text-gray-400" />
                                            </div>
                                            <select name="yearx" id="yearx" class="block w-full pl-10 border rounded-md">
                                                <option value="">All</option>
                                                @for($y = now()->year; $y >= 2000; $y--)
                                                    <option value="{{ $y }}" >
                                                        {{ $y }}
                                                    </option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Month -->
                                    <div>
                                        <label for="month" class="block text-sm font-medium text-gray-700 mb-1">
                                            Month
                                        </label>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <x-lucide-calendar class="w-4 h-4 text-gray-400" />
                                            </div>
                                            <select name="monthx" id="monthx" class="block w-full pl-10 border rounded-md">
                                                <option value="">All</option>
                                                @foreach(range(1, 12) as $m)
                                                    <option value="{{ $m }}" >
                                                        {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Buttons -->
                                <div class="flex space-x-3">
                                    <button type="submit"
                                            class="px-4 py-2 bg-[#fd9c0a] border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-[#e08c09] focus:outline-none">
                                        Filter
                                    </button>

                                    <a href="{{ url('/dashboard/reports') }}"
                                       class="px-4 py-2 bg-[#fd9c0a] border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-[#e08c09] focus:outline-none">
                                        Reset
                                    </a>
                                </div>
                            </form>
                        </div>

                        <!-- Report Data -->
                        <div class="bg-white border rounded-lg overflow-hidden">
                            <div x-show="selectedReportType === 'sales'">
                                <h3 class="text-lg font-medium mb-4 px-6 pt-4">
                                    Sales Report:
                                    <template x-if="year || month">
                                        <span>
                                            <span x-text="year ? year : 'All Years'"></span>
                                            <span x-show="month"> - <span x-text="monthName()"></span></span>
                                        </span>
                                    </template>
                                    <template x-if="!year && !month">
                                        <span>All Records</span>
                                    </template>
                                </h3>

                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Name
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Type
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Price
                                            </th>
                                        </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($sales as $saleId => $items)
                                            <tr class="bg-gray-200 font-bold">
                                                <td colspan="3">
                                                    Sale ID: {{ $saleId }} |
                                                    Total Price: Rs. {{ $items->first()->sale->price }}
                                                    Created At: {{$items->first()->sale->created_at}}
                                                </td>
                                            </tr>

                                            @foreach($items as $item)
                                                <tr>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        {{ $item->product_type === 'internal'
                                                            ? $item->internalProductItem->internalProduct->name
                                                            : $item->externalProductItem->external_product->name }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap $item->product_type === 'internal' ? 'text-green-600' : 'text-red-600 ">
                                                        {{ $item->product_type}} product
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        Rs. {{ $item->product_type === 'internal'
                                                            ? $item->internalProductItem->internalProduct->price
                                                            : $item->externalProductItem->external_product->sold_price }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div x-show="selectedReportType === 'employee'">
                                <h3 class="text-lg font-medium mb-4 px-6 pt-4">
                                    Employee Attendance Report:
                                    <template x-if="year || month">
                                        <span>
                                            <span x-text="year ? year : 'All Years'"></span>
                                            <span x-show="month"> - <span x-text="monthName()"></span></span>
                                        </span>
                                    </template>
                                    <template x-if="!year && !month">
                                        <span>All Records</span>
                                    </template>
                                </h3>

                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check In</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check Out</th>
                                        </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($attendance as $userId => $attendances)
                                            {{-- Print user name from first attendance (user relation loaded) --}}
                                            <tr class="bg-gray-200 font-bold">
                                                <td colspan="4">
                                                    User: {{ $attendances->first()->user->name ?? 'Unknown' }} — Role: {{ $attendances->first()->user->role ?? '-' }}
                                                    (ID: {{ $userId }})
                                                </td>
                                            </tr>
                                            @foreach($attendances as $attendance)
                                                <tr>
                                                    <td class="px-6 py-4 whitespace-nowrap">{{ $attendance->date->format('Y-m-d') }}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap">{{ $attendance->check_in ?? '-' }}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap">{{ $attendance->check_out ?? '-' }}</td>
                                                </tr>
                                            @endforeach
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div x-show="selectedReportType === 'expenses'">
                                <h3 class="text-lg font-medium mb-4 px-6 pt-4">
                                    Expense Report:
                                    <template x-if="year || month">
                                        <span>
                                            <span x-text="year ? year : 'All Years'"></span>
                                            <span x-show="month"> - <span x-text="monthName()"></span></span>
                                        </span>
                                    </template>
                                    <template x-if="!year && !month">
                                        <span>All Records</span>
                                    </template>
                                </h3>

                                <div class="overflow-x-auto">
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
                                                Created At
                                            </th>
                                        </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($expenses as $expense)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap">{{$expense->expense->title}}</td>
                                                <td class="px-6 py-4 whitespace-nowrap {{$expense->type === 'income' ? 'text-green-600' : 'text-red-600'}}">{{$expense->type}}</td>
                                                <td class="px-6 py-4 whitespace-nowrap">Rs.{{$expense->amount}}</td>
                                                <td class="px-6 py-4 whitespace-nowrap">{{$expense->description}}</td>
                                                <td class="px-6 py-4 whitespace-nowrap">{{$expense->created_at->format('Y-m-d H:i')}}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div x-show="selectedReportType === 'petty'">
                                <h3 class="text-lg font-medium mb-4 px-6 pt-4">
                                    Petty Cash Report:
                                    <template x-if="year || month">
                                        <span>
                                            <span x-text="year ? year : 'All Years'"></span>
                                            <span x-show="month"> - <span x-text="monthName()"></span></span>
                                        </span>
                                    </template>
                                    <template x-if="!year && !month">
                                        <span>All Records</span>
                                    </template>
                                </h3>

                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created At</th>
                                        </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                        @forelse ($pettyCash as $row)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $row->title }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                    <span class="{{ $row->type === 'income' ? 'text-green-600' : 'text-red-600' }}">
                                                        {{ ucfirst($row->type) }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Rs. {{ number_format($row->amount, 2) }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $row->created_at->format('Y-m-d H:i') }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">No records found.</td>
                                                <x-lucide-file-text class="mx-auto text-gray-300 mb-3 h-12 w-12" />
                                            </tr>
                                        @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div x-show="selectedReportType === 'salary'">
                                <h3 class="text-lg font-medium mb-4 px-6 pt-4">
                                    Salary Report:
                                    <template x-if="year || month">
                                        <span>
                                            <span x-text="year ? year : 'All Years'"></span>
                                            <span x-show="month"> - <span x-text="monthName()"></span></span>
                                        </span>
                                    </template>
                                    <template x-if="!year && !month">
                                        <span>All Records</span>
                                    </template>
                                </h3>
                                <table class="min-w-full divide-y divide-gray-200 table-auto">
                                    <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase">Month</th>
                                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase">Name</th>
                                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase">Worked Hours</th>
                                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase">Type</th>
                                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase">Rate</th>
                                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase">Base Salary</th>
                                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase">Bonus Adds</th>
                                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase">Bonus Removes</th>
                                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase">Calculated Salary</th>
                                    </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse ($salaryReport as $row)
                                        <tr>
                                            <td class="px-6 py-4 text-sm text-gray-900">{{ $row['month'] }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-900">{{ $row['name'] }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-900">{{ $row['worked_hours'] }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-900">{{ $row['salary_type'] }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-900">Rs. {{ number_format($row['rate'], 2) }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-900">Rs. {{ number_format($row['base_salary'], 2) }}</td>
                                            <td class="px-6 py-4 text-sm text-green-600 font-semibold">+ Rs. {{ number_format($row['bonus_adds'], 2) }}</td>
                                            <td class="px-6 py-4 text-sm text-red-600 font-semibold">- Rs. {{ number_format($row['bonus_removes'], 2) }}</td>
                                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">Rs. {{ number_format($row['calculatedSalary'], 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="px-6 py-4 text-center text-sm text-gray-500">No records found</td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
{{--                            <div x-show="selectedReportType === 'employee'" class="text-center py-8">--}}
{{--                                <x-lucide-file-text class="mx-auto text-gray-300 mb-3 h-12 w-12" />--}}
{{--                                <p class="text-gray-500">Select a report type to view data</p>--}}
{{--                            </div>--}}
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
