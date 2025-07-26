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
            @include('components.sidebar', ['currentPage' => 'manage-employee'])

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
                    @if (session('deleted'))
                        <div class="mb-4 text-green-600 bg-green-100 border border-green-300 rounded p-3" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 10000)">
                            {{ session('deleted') }}
                        </div>
                    @endif

                    @if (session('status'))
                        <div class="mb-4 text-green-600 bg-green-100 border border-green-300 rounded p-3" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 10000)">
                            {{ session('status') }}
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
                        x-data="employeeComponent({{$employees->toJson() }})"
                        x-init="initPopup(); init()"
                        class="bg-white rounded-lg shadow-md p-6"
                    >
                        <h1 class="text-2xl font-bold mb-6 text-[#0f2360]">View Employees</h1>

                        <div x-show="employees.length === 0" class="text-center py-8">
                            <x-lucide-package class="mx-auto text-gray-300 mb-3 h-12 w-12" />
                            <p class="text-gray-500">No employees available</p>
                        </div>

                        <div x-show="employees.length > 0" class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Salary</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                                </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                <template x-for="employee in employees" :key="employee.id">
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap" x-text="employee.name"></td>
                                        <td class="px-6 py-4 whitespace-nowrap" x-text="employee.role"></td>
                                        <td class="px-6 py-4 whitespace-nowrap" x-text="'Rs. ' + parseFloat(employee.salary_amount).toFixed(2)"></td>
                                        <td class="px-6 py-4 whitespace-nowrap pl-10">
                                            <!--Edit Button -->
                                            <button
                                                @click="$store.modal.setSelectedEmployee(employee);"
                                                @click.prevent="
                                                    $dispatch('open-modal', 'edit-employee');"
                                                class="text-[#0f2360] hover:text-[#fd9c0a]"
                                            >
                                                <x-lucide-edit class="w-5 h-5" />
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

        <!-- Modal -->
        <x-modal name="edit-employee" :show="$errors->any() || session('status')"  @close="$store.modal.clearSelectedEmployee()" :scrollable="true">
            <div class="w-full  mx-auto p-8 space-y-6 ">
                <div class="flex items-center justify-between p-4 border-b border-gray-200 sticky top-0 bg-white z-10">
                    <button x-on:click="$dispatch('close')" class="text-gray-600 hover:text-gray-900">
                        <x-lucide-arrow-left class="w-6 h-6" />
                    </button>
                    <div class="w-6"></div>
                </div>
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <header>
                        <h2 class="text-lg font-medium text-gray-900">
                            {{ __('Employee Information') }}
                        </h2>

                        <p class="mt-1 text-sm text-gray-600">
                            {{ __("Update your Employee's profile information and email address.") }}
                        </p>
                    </header>

                    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
                        @csrf
                    </form>

                    <form method="post" action="{{ route('employees.update') }}" class="mt-6 space-y-6">
                        @csrf
                        @method('patch')

                        <input type="hidden" name="employee_id" :value="$store.modal.selectedEmployee?.id"  />

                        <div>
                            <x-input-label for="name" :value="__('Name')" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" x-model="$store.modal.selectedEmployee?.name" required autofocus autocomplete="name" />
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>

                        <div>
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" x-model="$store.modal.selectedEmployee?.email" required autocomplete="username" />
                            <x-input-error class="mt-2" :messages="$errors->get('email')" />
                        </div>

                        <div>
                            <x-input-label for="phone_number" :value="__('Phone Number')" />
                            <x-text-input id="phone_number" name="phone_number" type="text" class="mt-1 block w-full" x-model="$store.modal.selectedEmployee?.phone_number" autocomplete="tel" />
                            <x-input-error class="mt-2" :messages="$errors->get('phone_number')" />
                        </div>

                        <div>
                            <x-input-label for="salary_amount" :value="__('Salary Amount')" />
                            <x-text-input id="salary_amount" name="salary_amount" type="number" step="0.01" class="mt-1 block w-full" x-model="$store.modal.selectedEmployee?.salary_amount" />
                            <x-input-error class="mt-2" :messages="$errors->get('salary_amount')" />
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Save') }}</x-primary-button>

                            @if (session('status') === 'profile-updated')
                                <p
                                    x-data="{ show: true }"
                                    x-show="show"
                                    x-transition
                                    x-init="setTimeout(() => show = false, 2000)"
                                    class="text-sm text-gray-600"
                                >{{ __('Saved.') }}</p>
                            @endif
                        </div>
                    </form>
                </div>

                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <header>
                        <h2 class="text-lg font-medium text-gray-900">
                            {{ __('Update Password') }}
                        </h2>

                        <p class="mt-1 text-sm text-gray-600">
                            {{ __('Ensure your account is using a long, random password to stay secure.') }}
                        </p>
                    </header>

                    <form method="post" action="{{ route('employees.updatePassword') }}" class="mt-6 space-y-6">
                        @csrf
                        @method('put')

                        <input type="hidden" name="employee_id" :value="$store.modal.selectedEmployee?.id" />

                        <div>
                            <x-input-label for="update_password_password" :value="__('New Password')" />
                            <x-text-input id="update_password_password" name="password" type="password" class="mt-1 block w-full" autocomplete="new-password" />
                            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="update_password_password_confirmation" :value="__('Confirm Password')" />
                            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" autocomplete="new-password" />
                            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Save') }}</x-primary-button>

                            @if (session('status') === 'password-updated')
                                <p
                                    x-data="{ show: true }"
                                    x-show="show"
                                    x-transition
                                    x-init="setTimeout(() => show = false, 2000)"
                                    class="text-sm text-gray-600"
                                >{{ __('Saved.') }}</p>
                            @endif
                        </div>
                    </form>
                </div>

                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <header>
                        <h2 class="text-lg font-medium text-gray-900">
                            {{ __('Delete Account') }}
                        </h2>

                        <p class="mt-1 text-sm text-gray-600">
                            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
                        </p>
                    </header>

                    <x-danger-button
                        x-data=""
                        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
                    >{{ __('Delete Account') }}</x-danger-button>

                    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
                        <form method="POST" action="{{ route('employees.destroy') }}" class="p-6">
                            @csrf
                            @method('DELETE')

                            <input type="hidden" name="employee_id" :value="$store.modal.selectedEmployee?.id" />

                            <h2 class="text-lg font-medium text-gray-900">
                                {{ __('Are you sure you want to delete this employee?') }}
                            </h2>

                            <p class="mt-1 text-sm text-gray-600">
                                {{ __('This action is irreversible. The employee and all related data will be permanently deleted.') }}
                            </p>

                            <div class="mt-6 flex justify-end">
                                <x-secondary-button x-on:click="$dispatch('close')">
                                    {{ __('Cancel') }}
                                </x-secondary-button>

                                <x-danger-button class="ms-3">
                                    {{ __('Delete Employee') }}
                                </x-danger-button>
                            </div>
                        </form>
                    </x-modal>
                </div>
            </div>
        </x-modal>

        <!-- Popup -->
        @include('components.popup')

    </div>
    <script>
        function employeeComponent(serverData) {
            return {
                employees: serverData,
                init() {
                    // Try to restore selectedEmployee from sessionStorage on page load
                    const stored = JSON.parse(sessionStorage.getItem('selectedEmployee'));
                    if (stored?.id) {
                        const fullEmployee = this.employees.find(emp => emp.id === stored.id);
                        if (fullEmployee) {
                            Alpine.store('modal')?.setSelectedEmployee(fullEmployee);
                        }
                    }
                }
            };
        }
        document.addEventListener('alpine:init', () => {
            Alpine.store('modal', {
                selectedEmployee: JSON.parse(sessionStorage.getItem('selectedEmployee')) || null,

                setSelectedEmployee(employee) {
                    this.selectedEmployee = employee;
                    sessionStorage.setItem('selectedEmployee', JSON.stringify(employee));
                },

                clearSelectedEmployee() {
                    this.selectedEmployee = null;
                    sessionStorage.removeItem('selectedEmployee');
                }
            });
        });
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
