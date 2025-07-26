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
            <div class="flex-1 overflow-y-auto transition-all duration-300 ease-in-out">
                <main class="p-6">
<div
    x-data="{
        selectedEmployee: '',
        attendanceType: 'in',
        currentTime: '',
        currentDate: '',
        saved: false,
        employees: [
            { id: 1, name: 'John Doe' },
            { id: 2, name: 'Jane Smith' },
            { id: 3, name: 'Robert Johnson' },
            { id: 4, name: 'Emily Davis' },
            { id: 5, name: 'Michael Wilson' }
        ],
        init() {
            // Update time every second
            this.updateTime();
            this.$nextTick(() => {
                setInterval(() => this.updateTime(), 1000);
            });
        },
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
        setAttendanceType(type) {
            this.attendanceType = type;
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
        }
    }"
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

        <!-- Success Message -->
        <div
            x-show="saved"
            class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6 flex items-center justify-center"
            x-cloak
        >
            <x-lucide-check class="w-5 h-5 mr-2" />
            <span>Attendance recorded successfully!</span>
        </div>

        <!-- Form -->
        <div class="space-y-6">
            <!-- Employee Select -->
            <div>
                <label for="employee" class="block text-sm font-medium text-gray-700 mb-1">
                    Select Employee
                </label>
                <select
                    id="employee"
                    x-model="selectedEmployee"
                    class="block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-[#0f2360] focus:border-[#0f2360]"
                >
                    <option value="">-- Select an employee --</option>
                    <template x-for="employee in employees" :key="employee.id">
                        <option :value="employee.id" x-text="employee.name"></option>
                    </template>
                </select>
            </div>

            <!-- Attendance Type -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Attendance Type
                </label>
                <div class="flex space-x-4">
                    <button
                        type="button"
                        @click="setAttendanceType('in')"
                        class="flex-1 py-2 px-4 rounded-md"
                        :class="{ 'bg-[#0f2360] text-white': attendanceType === 'in', 'bg-gray-100 text-gray-700 hover:bg-gray-200': attendanceType !== 'in' }"
                    >
                        Clock In
                    </button>
                    <button
                        type="button"
                        @click="setAttendanceType('out')"
                        class="flex-1 py-2 px-4 rounded-md"
                        :class="{ 'bg-[#0f2360] text-white': attendanceType === 'out', 'bg-gray-100 text-gray-700 hover:bg-gray-200': attendanceType !== 'out' }"
                    >
                        Clock Out
                    </button>
                </div>
            </div>

            <!-- Save Button -->
            <div class="pt-4">
                <!-- Form commented out for UI testing -->
                <!-- <form method="POST" action="{/{ rout/e('at/tendance.sa/ve') }}"> -->
                <!-- @csrf -->
                <input type="hidden" name="employee_id" x-model="selectedEmployee" />
                <input type="hidden" name="type" x-model="attendanceType" />
                <input type="hidden" name="time" x-model="currentTime" />
                <input type="hidden" name="date" x-model="currentDate" />
                <button
                    type="button"
                    @click="saveAttendance"
                    :disabled="selectedEmployee === ''"
                    class="w-full py-3 px-4 rounded-md flex items-center justify-center"
                    :class="{ 'bg-gray-300 cursor-not-allowed': selectedEmployee === '', 'bg-[#fd9c0a] hover:bg-[#e08c09] text-white font-medium': selectedEmployee !== '' }"
                >
                    <x-lucide-clock class="w-5 h-5 mr-2" />
                    <span x-text="attendanceType === 'in' ? 'Record Clock In' : 'Record Clock Out'"></span>
                </button>
                <!-- </form> -->
            </div>
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
