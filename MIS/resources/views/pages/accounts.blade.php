<div
    x-data="{
        formData: { title: '', type: 'income', amount: '', description: '' },
        errors: {},
        success: false,
        titles: [
            'Sales Revenue', 'Supplier Payment', 'Utility Bill', 'Employee Bonus', 'Maintenance Cost'
        ],
        newTitle: '',
        entries: [
            { id: 1, title: 'Sales Revenue', type: 'income', amount: 5000.00, description: 'Monthly sales' },
            { id: 2, title: 'Supplier Payment', type: 'outcome', amount: 2000.00, description: 'Fabric purchase' }
        ],
        validate() {
            const newErrors = {};
            if (!this.formData.title.trim()) newErrors.title = 'Title is required';
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
                console.log('Entry added:', this.formData);
                this.entries.push({
                    id: this.entries.length + 1,
                    title: this.formData.title,
                    type: this.formData.type,
                    amount: parseFloat(this.formData.amount),
                    description: this.formData.description
                });
                this.success = true;
                setTimeout(() => {
                    this.success = false;
                    this.formData = { title: '', type: 'income', amount: '', description: '' };
                    this.errors = {};
                }, 3000);
            }
        },
        addTitle() {
            if (this.newTitle.trim() && !this.titles.includes(this.newTitle.trim())) {
                this.titles.push(this.newTitle.trim());
                this.newTitle = '';
                alert('Title added successfully!');
            }
        },
        removeTitle(title) {
            if (confirm('Are you sure you want to remove this title?')) {
                this.titles = this.titles.filter(t => t !== title);
                this.entries = this.entries.filter(e => e.title !== title);
                alert('Title removed successfully!');
            }
        },
        deleteEntry(id) {
            if (confirm('Are you sure you want to delete this entry?')) {
                this.entries = this.entries.filter(e => e.id !== id);
                alert('Entry deleted successfully!');
            }
        }
    }"
    class="bg-white rounded-lg shadow-md p-6"
>
    <h1 class="text-2xl font-bold mb-6 text-[#0f2360]">Income/Outcome</h1>

    <!-- Success Message -->
    <div
        x-show="success"
        class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6 flex items-center"
        x-cloak
    >
        <x-lucide-check class="w-5 h-5 mr-2" />
        <span>Entry added successfully!</span>
    </div>

    <!-- Form -->
    <!-- <form method="POST" action="{/{ ro/ute('income-o/utcome.create/') }}" @submit="handleSubmit"> -->
    <!-- @csrf -->
    <div class="max-w-2xl mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Title -->
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                    Title
                </label>
                <select
                    id="title"
                    name="title"
                    x-model="formData.title"
                    class="block w-full border"
                    :class="{ 'border-red-500': errors.title, 'border-gray-300': !errors.title }"
                    class="rounded-md shadow-sm p-2 focus:ring-[#0f2360] focus:border-[#0f2360]"
                >
                    <option value="">-- Select a title --</option>
                    <template x-for="title in titles" :key="title">
                        <option :value="title" x-text="title"></option>
                    </template>
                </select>
                <p x-show="errors.title" class="mt-1 text-sm text-red-500" x-text="errors.title"></p>
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
                    <option value="income">Income</option>
                    <option value="outcome">Outcome</option>
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
                <x-lucide-wallet class="w-5 h-5 mr-2" />
                Add Entry
            </button>
        </div>
    </div>
    <!-- </form> -->

    <!-- Add/Remove Titles -->
    <div class="mt-8 max-w-2xl mx-auto">
        <h2 class="text-lg font-medium mb-4 text-[#0f2360]">Manage Titles</h2>
        <div class="flex space-x-4">
            <input
                type="text"
                x-model="newTitle"
                placeholder="New title"
                class="block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-[#0f2360] focus:border-[#0f2360]"
            />
            <button
                @click="addTitle"
                class="bg-[#fd9c0a] text-white py-2 px-4 rounded-md hover:bg-[#e08c09] flex items-center"
            >
                <x-lucide-plus class="w-5 h-5 mr-2" />
                Add
            </button>
        </div>
        <div class="mt-4">
            <template x-for="title in titles" :key="title">
                <div class="flex justify-between items-center py-2">
                    <span x-text="title"></span>
                    <button
                        @click="removeTitle(title)"
                        class="text-red-600 hover:text-red-800"
                    >
                        <x-lucide-trash class="w-5 h-5" />
                    </button>
                </div>
            </template>
        </div>
    </div>

    <!-- Entries Table -->
    <div class="mt-8 max-w-2xl mx-auto">
        <h2 class="text-lg font-medium mb-4 text-[#0f2360]">Entries</h2>
        <div x-show="entries.length === 0" class="text-center py-8">
            <x-lucide-wallet class="mx-auto text-gray-300 mb-3 h-12 w-12" />
            <p class="text-gray-500">No entries available</p>
        </div>
        <div x-show="entries.length > 0" class="overflow-x-auto">
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
                        Action
                    </th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                <template x-for="entry in entries" :key="entry.id">
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap" x-text="entry.title"></td>
                        <td class="px-6 py-4 whitespace-nowrap" x-text="entry.type.charAt(0).toUpperCase() + entry.type.slice(1)"></td>
                        <td class="px-6 py-4 whitespace-nowrap" x-text="'$' + entry.amount.toFixed(2)"></td>
                        <td class="px-6 py-4 whitespace-nowrap" x-text="entry.description || '-'"></td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <button
                                @click="deleteEntry(entry.id)"
                                class="text-red-600 hover:text-red-800"
                            >
                                <x-lucide-trash class="w-5 h-5" />
                            </button>
                        </td>
                    </tr>
                </template>
                </tbody>
            </table>
        </div>
    </div>
</div>
