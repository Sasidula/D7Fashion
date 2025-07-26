<div
    x-data="accountComponent()"
    x-init="initPopup()"
    class="bg-white rounded-lg shadow-md p-6"
>
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

<script>
    function accountComponent() {
        return {
            entries: [
                { id: 1, title: 'Sales Revenue', type: 'income', amount: 5000.00, description: 'Monthly sales' },
                { id: 2, title: 'Supplier Payment', type: 'outcome', amount: 2000.00, description: 'Fabric purchase' }
            ],
            deleteEntry(id) {
                if (confirm('Are you sure you want to delete this entry?')) {
                    this.entries = this.entries.filter(e => e.id !== id);
                    alert('Entry deleted successfully!');
                }
            },
        };
    }
</script>
