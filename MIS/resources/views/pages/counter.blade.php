<div
    x-data="{
        activeTab: 'fabric',
        selectedItem: null,
        quantity: 1,
        fabricItems: [
            { id: 1, name: 'Cotton', price: 10.00, unit: 'meter' },
            { id: 2, name: 'Silk', price: 20.00, unit: 'meter' },
            { id: 3, name: 'Wool', price: 15.00, unit: 'meter' }
        ],
        accessoryItems: [
            { id: 1, name: 'Button', price: 0.50, unit: 'piece' },
            { id: 2, name: 'Zipper', price: 1.00, unit: 'piece' },
            { id: 3, name: 'Thread', price: 0.75, unit: 'spool' }
        ],
        cartItems: [
            { id: 1, name: 'Cotton', price: 10.00, unit: 'meter', quantity: 2, totalPrice: 20.00 },
            { id: 2, name: 'Button', price: 0.50, unit: 'piece', quantity: 10, totalPrice: 5.00 }
        ],
        setActiveTab(tab) {
            this.activeTab = tab;
            this.selectedItem = null;
            this.quantity = 1;
        },
        selectItem(item) {
            this.selectedItem = item;
            this.quantity = 1;
        },
        addToCart() {
            if (this.selectedItem) {
                this.cartItems.push({
                    id: this.selectedItem.id,
                    name: this.selectedItem.name,
                    price: this.selectedItem.price,
                    unit: this.selectedItem.unit,
                    quantity: this.quantity,
                    totalPrice: this.selectedItem.price * this.quantity
                });
                this.selectedItem = null;
                this.quantity = 1;
            }
        },
        completePurchase() {
            this.cartItems = [];
            alert('Purchase completed (simulated for testing)!');
        },
        calculateTotal() {
            return this.cartItems.reduce((total, item) => total + item.totalPrice, 0).toFixed(2);
        }
    }"
    class="bg-white rounded-lg shadow-md p-6"
>
    <h1 class="text-2xl font-bold mb-6 text-[#0f2360]">Counter</h1>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Items Section -->
        <div>
            <!-- Tabs -->
            <div class="mb-4">
                <div class="flex border-b">
                    <button
                        @click="setActiveTab('fabric')"
                        class="py-2 px-4"
                        :class="{ 'border-b-2 border-[#fd9c0a] text-[#0f2360] font-medium': activeTab === 'fabric', 'text-gray-500': activeTab !== 'fabric' }"
                    >
                        Fabrics
                    </button>
                    <button
                        @click="setActiveTab('accessory')"
                        class="py-2 px-4"
                        :class="{ 'border-b-2 border-[#fd9c0a] text-[#0f2360] font-medium': activeTab === 'accessory', 'text-gray-500': activeTab !== 'accessory' }"
                    >
                        Accessories
                    </button>
                </div>
            </div>

            <!-- Items Table -->
            <div class="overflow-x-auto border rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Name
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Price
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Unit
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Action
                        </th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    <template x-for="item in activeTab === 'fabric' ? fabricItems : accessoryItems" :key="item.id">
                        <tr
                            @click="selectItem(item)"
                            class="cursor-pointer"
                            :class="{ 'bg-blue-50': selectedItem && selectedItem.id === item.id && selectedItem.name === item.name, 'hover:bg-gray-50': !(selectedItem && selectedItem.id === item.id && selectedItem.name === item.name) }"
                        >
                            <td class="px-6 py-4 whitespace-nowrap" x-text="item.name"></td>
                            <td class="px-6 py-4 whitespace-nowrap" x-text="'$' + item.price.toFixed(2)"></td>
                            <td class="px-6 py-4 whitespace-nowrap" x-text="item.unit"></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <button
                                    @click.stop="selectItem(item)"
                                    class="text-[#0f2360] hover:text-[#fd9c0a]"
                                >
                                    Select
                                </button>
                            </td>
                        </tr>
                    </template>
                    </tbody>
                </table>
            </div>

            <!-- Selected Item -->
            <div x-show="selectedItem" class="mt-6 p-4 border rounded-lg bg-gray-50" x-cloak>
                <h3 class="text-lg font-medium mb-3">
                    Selected Item: <span x-text="selectedItem.name"></span>
                </h3>
                <!-- Form commented out for UI testing -->
                <!-- <form method="POST" action="{/{ route('co/unter.add'/) }}"> -->
                <!-- @csrf -->
                <input type="hidden" name="id" x-model="selectedItem.id" />
                <input type="hidden" name="name" x-model="selectedItem.name" />
                <input type="hidden" name="price" x-model="selectedItem.price" />
                <input type="hidden" name="unit" x-model="selectedItem.unit" />
                <div class="flex items-center space-x-4">
                    <div>
                        <label for="quantity" class="block text-sm font-medium text-gray-700">
                            Quantity
                        </label>
                        <input
                            type="number"
                            id="quantity"
                            name="quantity"
                            min="1"
                            x-model.number="quantity"
                            class="mt-1 block w-24 border border-gray-300 rounded-md shadow-sm p-2"
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Price
                        </label>
                        <p class="mt-1" x-text="'$' + selectedItem.price.toFixed(2) + ' per ' + selectedItem.unit"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Total
                        </label>
                        <p class="mt-1" x-text="'$' + (selectedItem.price * quantity).toFixed(2)"></p>
                    </div>
                    <button
                        @click="addToCart"
                        class="mt-4 bg-[#fd9c0a] text-white py-2 px-4 rounded-md hover:bg-[#e08c09] focus:outline-none flex items-center"
                    >
                        <x-lucide-plus class="w-4 h-4 mr-1" />
                        Add
                    </button>
                </div>
                <!-- </form> -->
            </div>
        </div>

        <!-- Cart Section -->
        <div>
            <h2 class="text-xl font-semibold mb-4 text-[#0f2360]">Cart</h2>
            <div x-show="cartItems.length === 0" class="text-center py-8 border rounded-lg">
                <x-lucide-shopping-cart class="mx-auto text-gray-300 mb-3 h-12 w-12" />
                <p class="text-gray-500">Your cart is empty</p>
            </div>
            <div x-show="cartItems.length > 0" class="border rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Item
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Price
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Qty
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Total
                        </th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    <template x-for="(item, index) in cartItems" :key="index">
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap" x-text="item.name"></td>
                            <td class="px-6 py-4 whitespace-nowrap" x-text="'$' + item.price.toFixed(2)"></td>
                            <td class="px-6 py-4 whitespace-nowrap" x-text="item.quantity"></td>
                            <td class="px-6 py-4 whitespace-nowrap" x-text="'$' + item.totalPrice.toFixed(2)"></td>
                        </tr>
                    </template>
                    </tbody>
                </table>
                <div class="bg-gray-50 px-6 py-4">
                    <div class="flex justify-between items-center">
                        <span class="font-medium">Total Amount:</span>
                        <span class="font-bold text-lg" x-text="'$' + calculateTotal()"></span>
                    </div>
                    <!-- Form commented out for UI testing -->
                    <!-- <form method="POST" action="{/{ ro/ute('coun/ter.purchase') }}"> -->
                    <!-- @csrf -->
                    <button
                        @click="completePurchase"
                        class="mt-4 w-full bg-[#fd9c0a] text-white py-2 px-4 rounded-md hover:bg-[#e08c09] focus:outline-none"
                    >
                        Complete Purchase
                    </button>
                    <!-- </form> -->
                </div>
            </div>
        </div>
    </div>
</div>
