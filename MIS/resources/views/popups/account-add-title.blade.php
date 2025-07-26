<div
    x-data="accountComponent()"
    x-init="initPopup()"
    class="bg-white rounded-lg shadow-md p-6"
>

    <!-- Success Message -->
    <div
        x-show="success"
        class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6 flex items-center"
        x-cloak
    >
        <x-lucide-check class="w-5 h-5 mr-2" />
        <span>Entry added successfully!</span>
    </div>

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

</div>

<script>
    function accountComponent() {
        return {
            success: false,
            titles: [
                'Sales Revenue', 'Supplier Payment', 'Utility Bill', 'Employee Bonus', 'Maintenance Cost'
            ],
            newTitle: '',
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
            }
        };
    }
</script>

