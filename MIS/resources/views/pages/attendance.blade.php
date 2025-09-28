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
            @include('components.sidebar', ['currentPage' => 'attendance'])

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
                    @if(session('status') == 'found')
                        <span></span>
                    @elseif(session('status') == 'not found')
                        <div class="mt-4 p-2 bg-yellow-100 text-yellow-800 rounded mb-4">
                            No attendance found for selected user and date.
                        </div>
                    @elseif(session('status'))
                        <div class="mt-4 p-2 bg-red-100 text-red-800 rounded mb-4">
                            {{ session('status') }}
                        </div>
                    @endif
                    <div
                        class="bg-white rounded-lg shadow-md p-6"
                    >
                        <h1 class="text-2xl font-bold mb-6 text-[#0f2360]">Attendance</h1>
                        <div class="max-w-md mx-auto">
                            <!-- Current Time and Date -->
                            <div class="flex justify-center mb-8">
                                <div class="text-center">
                                    <div class="text-5xl font-bold text-[#0f2360] mb-2" x-text="currentTime"></div>
                                    <div class="text-gray-500" x-text="currentDate"></div>
                                </div>
                            </div>

                            <!-- Form -->
                            <div class="space-y-6">
                                @if(Auth::user()->role === 'admin' || Auth::user()->role === 'manager')
                                    <div
                                        class="max-w-md mx-auto mt-6 bg-white rounded-lg shadow-md p-6"
                                    >
                                        <!-- Form -->
                                        <form method="POST" action="{{ route('attendance.check') }}">
                                            @csrf
                                            <div class="space-y-6">
                                                <!-- Employee Select -->
                                                <div>
                                                    <label for="employee"
                                                           class="block text-sm font-medium text-gray-700 mb-1">
                                                        Select Employee
                                                    </label>
                                                    <select
                                                        id="employee"
                                                        name="user_id"
                                                        x-model="selectedEmployee"
                                                        class="block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-[#0f2360] focus:border-[#0f2360]"
                                                        required
                                                    >
                                                        <option value="">-- Select an employee --</option>
                                                        @foreach($employees as $user)
                                                            <option
                                                                value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                                                {{ $user->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <!-- Submit Button -->
                                                <div class="flex justify-end gap-3 mt-4">
                                                    <button
                                                        type="submit"
                                                        class="bg-[#0f2360] text-white px-4 py-2 rounded-md hover:bg-[#0d1d4f] transition w-full"
                                                    >
                                                        Select Employee
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                @else
                                    <div
                                        class="max-w-md mx-auto mt-6 bg-white rounded-lg shadow-md p-6"
                                    >
                                        <div class="text-center">
                                            <h2 class="text-xl font-bold mb-4 text-[#0f2360]">Employee:</h2>
                                            <h2 class="text-2xl font-bold mb-4 text-[#0f2360]">{{ Auth::user()->name }}</h2>
                                        </div>

                                        <form method="POST" action="{{ route('attendance.check') }}">
                                            @method('POST')
                                            @csrf

                                            <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">

                                            <!-- Submit Button -->
                                            <div class="flex justify-end gap-3 mt-4">
                                                <button
                                                    type="submit"
                                                    class="bg-[#0f2360] text-white px-4 py-2 rounded-md hover:bg-[#0d1d4f] transition w-full"
                                                >
                                                    Comfirm Employee
                                                </button>
                                            </div>

                                        </form>
                                    </div>
                                @endif

                                @if(session("status") == "found")
                                    <div class="max-w-md mx-auto mt-6 bg-white rounded-lg shadow-md p-6">
                                        <div class="text-center">
                                            <h2 class="text-xl font-bold mb-4 text-[#0f2360]">Employee:</h2>
                                            <h2 class="text-2xl font-bold mb-4 text-[#0f2360]">
                                                {{ session('userx')->name }}
                                            </h2>
                                        </div>
                                    </div>
                                    <!-- Attendance Type -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            Attendance Type
                                        </label>
                                        <div class="flex space-x-4">
                                            <button
                                                type="button"
                                                class="flex-1 py-2 px-4 rounded-md"
                                                :class="{ 'bg-[#0f2360] text-white': attendanceType === 'in', 'bg-gray-100 text-gray-700 hover:bg-gray-200': attendanceType !== 'in' }"
                                            >
                                                Clock In
                                            </button>
                                            <button
                                                type="button"
                                                class="flex-1 py-2 px-4 rounded-md"
                                                :class="{ 'bg-[#0f2360] text-white': attendanceType === 'out', 'bg-gray-100 text-gray-700 hover:bg-gray-200': attendanceType !== 'out' }"
                                            >
                                                Clock Out
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Save Button -->
                                    <div class="pt-4">
                                        <form method="POST" action="{{ route('attendance.mark') }}">
                                            @method('PATCH')
                                            @csrf
                                            <input type="hidden" name="user_id" x-model="Attendance.user_id"/>
                                            <button
                                                type="submit"
                                                class="w-full py-3 px-4 rounded-md flex items-center justify-center bg-[#fd9c0a] hover:bg-[#e08c09] text-white font-medium"
                                            >
                                                <x-lucide-clock class="w-5 h-5 mr-2"/>
                                                <span
                                                    x-text="attendanceType === 'in' ? 'Record Clock In' : 'Record Clock Out'"></span>
                                            </button>
                                        </form>
                                    </div>

                                    @if(Auth::user()->role === 'admin' || Auth::user()->role === 'manager')
                                        @if(session('data') && session('data')['check_in'] !== null && session('data')['check_out'] !== null)
                                            <div x-data="{ showDiv: false }">
                                                <input type="checkbox" name="vis" x-model="showDiv">
                                                <label for="vis"><b>Custom Attendance</b></label>
                                                <div class="pt-4" x-show="showDiv">
                                                    <form method="POST" action="{{ route('attendance.mark') }}">
                                                        @method('PATCH')
                                                        @csrf
                                                        <input type="hidden" name="user_id"
                                                               x-model="Attendance.user_id"/>
                                                        <input type="date" name="date" value="{{ date('Y-m-d') }}"
                                                               class="block w-full border rounded-md shadow-sm p-2 focus:ring-[#0f2360] focus:border-[#0f2360] mb-2"/>
                                                        <input type="time" name="check_in"
                                                               value="{{ now()->format('H:i') }}"
                                                               class="block w-full border rounded-md shadow-sm p-2 focus:ring-[#0f2360] focus:border-[#0f2360] mb-2"/>
                                                        <input type="time" name="check_out"
                                                               value="{{ now()->format('H:i') }}"
                                                               class="block w-full border rounded-md shadow-sm p-2 focus:ring-[#0f2360] focus:border-[#0f2360] mb-2"/>
                                                        <button
                                                            type="submit"
                                                            class="w-full py-3 px-4 rounded-md flex items-center justify-center bg-[#fd9c0a] hover:bg-[#e08c09] text-white font-medium"
                                                        >
                                                            <x-lucide-clock class="w-5 h-5 mr-2"/>
                                                            Mark Attendance
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        @else
                                            <div x-data="{ showDiv: false }">
                                                <input type="checkbox" name="vis" x-model="showDiv">
                                                <label for="vis"><b>Custom Attendance</b></label>
                                                <div class="pt-4" x-show="showDiv">
                                                    <div x-data="{ showDiv: false }">
                                                        <div
                                                            class="mt-4 p-2 bg-yellow-100 text-yellow-800 rounded mb-4">
                                                            <span> Please clock out to mark a custom attendance.</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endif
                                @endif
                            </div>
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

            Attendance: @json(session('data')),

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

            setAttendanceType(type) {
                this.attendanceType = type;
            },

            selectedEmployee: '',
            attendanceType: 'in',
            saved: false,
            updateTime() {
                const now = new Date();
                this.currentTime = now.toLocaleTimeString();
                this.currentDate = now.toLocaleDateString('en-US', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
            },
            saveAttendance() {
                if (this.selectedEmployee !== '') {
                    // Simulate saving (log to console for testing)
                    console.log({
                        employeeId: this.selectedEmployee,
                        type: this.attendanceType,
                        time: this.currentTime,
                        date: this.currentDate
                    });
                    this.saved = true;
                    setTimeout(() => {
                        this.saved = false;
                        this.selectedEmployee = '';
                        this.attendanceType = 'in';
                    }, 3000);
                }
            },

            init() {

                if (this.Attendance) {
                    if (this.Attendance.check_in !== null && this.Attendance.check_out !== null) {
                        this.setAttendanceType('in');
                    }
                    if (this.Attendance.check_in !== null && this.Attendance.check_out === null) {
                        this.setAttendanceType('out');
                    }
                }

                this.updateTime();
                this.$nextTick(() => {
                    setInterval(() => this.updateTime(), 1000);
                });

                this.$watch('sidebarOpen', value => {
                    localStorage.setItem('sidebarOpen', JSON.stringify(value));
                });

                window.addEventListener('popup-open', (e) => {
                    const {title, view} = e.detail;
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
