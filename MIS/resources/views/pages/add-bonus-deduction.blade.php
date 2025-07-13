<div
    x-data="{
        formData: { employeeId: '', type: 'bonus', amount: '', description: '' },
        errors: {},
        success: false,
        employees: [
            { id: 1, name: 'John Doe' },
            { id: 2, name: 'Jane Smith' },
            { id: 3, name: 'Robert Johnson' },
            { id: 4, name: 'Emily Davis' },
            { id: 5, name: 'Michael Wilson' }
        ],
        validate() {
            const newErrors = {};
            if (!this.formData.employeeId) newErrors.employeeId = 'Employee is required';
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
                console.log('Bonus/Deduction added:', this.formData);
                this.success = true;
                setTimeout(() => {
                    this.success = false;
                    this.formData = { employeeId: '', type: 'bonus', amount: '', description: '' };
                    this.errors = {};
                }, 3000);
            }
        }
    }"
    class="bg-white rounded-lg shadow-md p-6"
>
    <h1 class="text-2xl font-bold mb-6 text-[#0f2360]">Add Bonus/Deduction</h1>

    <!-- Success Message -->
    <div
        x-show="success"
        class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6 flex items-center"
        x-cloak
    >
        <x-lucide-check class="w-5 h-5 mr-2" />
        <span>Bonus/Deduction added successfully!</span>
    </div>

    <!-- Form -->
    <!-- <form method="POST" action="{/{ rou/te('bo/nus-deduct/ion.create') }}" @submit="handleSubmit"> -->
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
                    <option value="bonus">Bonus</option>
                    <option value="deduction">Deduction</option>
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
                <x-lucide-dollar-sign class="w-5 h-5 mr-2" />
                Add Bonus/Deduction
            </button>
        </div>
    </div>
    <!-- </form> -->
</div>
