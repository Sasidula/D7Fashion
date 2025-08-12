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
            @include('components.sidebar', ['currentPage' => 'salary'])

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
                            showFilters: false,
                            year: '{{ $year }}',
                            month: '{{ $month }}',
                            user: '{{$user}}',
                            monthName() {
                                if (this.month) {
                                    return new Date(2000, this.month - 1, 1).toLocaleString('default', { month: 'long' });
                                }
                                return '';
                            },
                            toggleFilters() {
                                this.showFilters = !this.showFilters;
                            }
                        }"
                        class="bg-white rounded-lg shadow-md p-6"
                    >
                        <!-- Header and Actions -->
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
                            <h1 class="text-2xl font-bold text-[#0f2360] mb-4 md:mb-0">Salary</h1>
                            <div class="flex flex-wrap gap-2">
                            </div>
                        </div>
                        <!-- Header and Actions -->
                        <div class="flex flex-row items-center justify-between mb-6 gap-4">
                            <!-- Filter Button -->
                            <button
                                @click="toggleFilters"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none"
                            >
                                <x-lucide-filter class="w-4 h-4 mr-2" />
                                <span x-text="showFilters ? 'Hide Filters' : 'Show Filters'"></span>
                            </button>

                            <!-- Form with Employee Select and Export Button -->
                            <form action="{{ route('page.salary') }}" method="GET" class="flex flex-row items-center gap-4">
                                <input type="hidden" name="year" x-model="year">
                                <input type="hidden" name="month" x-model="month">

                                <!-- Employee Select -->
                                <div class="flex items-center">
                                    <label for="user_id" class="text-sm font-medium text-gray-700 mr-2">
                                        Select Employee :
                                    </label>
                                    <select
                                        id="user_id"
                                        name="user_id"
                                        class="inline-flex border rounded-md shadow-sm p-2 focus:ring-[#0f2360] focus:border-[#0f2360]"
                                        required
                                    >
                                        <option value="">-- Select an employee --</option>
                                        @foreach($employees as $user)
                                            <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Export Button -->
                                <button
                                    type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-[#fd9c0a] border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-[#e08c09] focus:outline-none"
                                >
                                    <x-lucide-search class="w-4 h-4 mr-2" />
                                    Export
                                </button>
                            </form>
                        </div>

                        <!-- Filters -->
                        <div
                            x-show="showFilters"
                            class="bg-gray-50 p-4 rounded-lg mb-6"
                            x-cloak
                        >
                            <h2 class="text-lg font-medium mb-4">Report Filters</h2>
                            <form method="GET" action="{{ route('page.salary') }}" class="space-y-4">

                                <input type="hidden" name="user_id" x-model="user">

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

                                    <a href="{{ url('/dashboard/salary') }}"
                                       class="px-4 py-2 bg-[#fd9c0a] border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-[#e08c09] focus:outline-none">
                                        Reset
                                    </a>
                                </div>
                            </form>
                        </div>

                        <div x-show="user !== ''">

                            <!--Actions -->
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
                                <h3 class="text-lg font-medium mb-4 px-6 pt-4">
                                    Salary Report:
                                    <template x-if="year || month">
                                                <span>
                                                    <span x-text="year ? year : 'Current year'"></span>
                                                    <span x-show="month"> - <span x-text="monthName()"></span></span>
                                                </span>
                                    </template>
                                    <template x-if="!year && !month">
                                        <span>Current Records</span>
                                    </template>
                                </h3>
                                <div class="flex flex-wrap gap-2">
                                    <form action="{{route('salary.print')}}" method="POST">
                                        @method('POST')
                                        @csrf

                                        <input type="hidden" name="user_id" x-model="user">
                                        <input type="hidden" name="year" x-model="year">
                                        <input type="hidden" name="month" x-model="month">

                                        <button
                                            type="submit"
                                            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none"
                                        >
                                            <x-lucide-printer class="w-4 h-4 mr-2" />
                                            Print
                                        </button>
                                    </form>
                                    <form action="{{route('salary.export')}}" method="POST">
                                        @method('POST')
                                        @csrf

                                        <input type="hidden" name="user_id" x-model="user">
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

                            @forelse (($salaryReport ?? collect()) as $row)
                                <div class="bg-white shadow-lg rounded-lg mb-6 border border-gray-200 p-6 max-w-3xl mx-auto">
                                    <!-- Header -->
                                    <div class="flex justify-between items-center border-b pb-3 mb-4">
                                        <div>
                                            <h4 class="text-xl font-semibold text-gray-800">{{ $row['name'] }}</h4>
                                            <p class="text-sm text-gray-500">Salary Slip - {{ $row['month'] }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm text-gray-500">Generated On: {{ now()->format('d M Y') }}</p>
                                        </div>
                                    </div>

                                    <!-- Salary Details -->
                                    <div class="grid grid-cols-2 gap-4 text-sm">
                                        <div>
                                            <p><span class="font-medium text-gray-700">Worked Hours:</span> {{ $row['worked_hours'] }}</p>
                                            <p><span class="font-medium text-gray-700">Salary Type:</span> {{ $row['salary_type'] }}</p>
                                            <p><span class="font-medium text-gray-700">Rate:</span> Rs. {{ number_format($row['rate'], 2) }}</p>
                                        </div>
                                        <div>
                                            <p><span class="font-medium text-gray-700">Base Salary:</span> Rs. {{ number_format($row['base_salary'], 2) }}</p>
                                            <p class="text-green-600 font-semibold">+ Bonus Adds: Rs. {{ number_format($row['bonus_adds'], 2) }}</p>
                                            <p class="text-red-600 font-semibold">- Bonus Removes: Rs. {{ number_format($row['bonus_removes'], 2) }}</p>
                                        </div>
                                    </div>

                                    <!-- Final Salary -->
                                    <div class="mt-4 border-t pt-3">
                                        <p class="text-lg font-bold text-gray-800">Net Pay: Rs. {{ number_format($row['calculatedSalary'], 2) }}</p>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center text-gray-500 py-6">
                                    No records found
                                </div>
                            @endforelse
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
