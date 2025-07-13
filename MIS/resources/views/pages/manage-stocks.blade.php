<div
    x-data="{
        materials: [
            { id: 1, name: 'Cotton Fabric', price: 10.00, quantity: 500 },
            { id: 2, name: 'Silk Fabric', price: 20.00, quantity: 200 },
            { id: 3, name: 'Buttons', price: 0.50, quantity: 2000 },
            { id: 4, name: 'Zippers', price: 1.00, quantity: 300 },
            { id: 5, name: 'Thread Spools', price: 0.75, quantity: 150 }
        ],
        deleteMaterial(id) {
            if (confirm('Are you sure you want to delete this material?')) {
                this.materials = this.materials.filter(item => item.id !== id);
                alert('Material deleted successfully!');
            }
        }
    }"
    class="bg-white rounded-lg shadow-md p-6"
>
    <h1 class="text-2xl font-bold mb-6 text-[#0f2360]">View Materials</h1>

    <div x-show="materials.length === 0" class="text-center py-8">
        <x-lucide-package class="mx-auto text-gray-300 mb-3 h-12 w-12" />
        <p class="text-gray-500">No materials available</p>
    </div>

    <div x-show="materials.length > 0" class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
            <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Name
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Price ($)
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Quantity
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Actions
                </th>
            </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
            <template x-for="material in materials" :key="material.id">
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap" x-text="material.name"></td>
                    <td class="px-6 py-4 whitespace-nowrap" x-text="'$' + material.price.toFixed(2)"></td>
                    <td class="px-6 py-4 whitespace-nowrap" x-text="material.quantity"></td>
                    <td class="px-6 py-4 whitespace-nowrap flex space-x-2">
                        <a
                            href="{{ route('dashboard', 'materials-edit') }}?id=:material.id"
                            class="text-[#0f2360] hover:text-[#fd9c0a]"
                        >
                            <x-lucide-edit class="w-5 h-5" />
                        </a>
                        <button
                            @click="deleteMaterial(material.id)"
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
