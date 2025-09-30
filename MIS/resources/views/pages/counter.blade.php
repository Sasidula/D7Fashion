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
            @include('components.sidebar', ['currentPage' => 'counter'])

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
                    @if (session('success'))
                        <div class="mb-4 text-green-600 bg-green-100 border border-green-300 rounded p-3" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 10000)">
                            {{ session('success') }}
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
                                            @click="setActiveTab('internal')"
                                            class="py-2 px-4"
                                            :class="{ 'border-b-2 border-[#fd9c0a] text-[#0f2360] font-medium': activeTab === 'internal', 'text-gray-500': activeTab !== 'internal' }"
                                        >
                                            Internal Products
                                        </button>
                                        <button
                                            @click="setActiveTab('external')"
                                            class="py-2 px-4"
                                            :class="{ 'border-b-2 border-[#fd9c0a] text-[#0f2360] font-medium': activeTab === 'external', 'text-gray-500': activeTab !== 'external' }"
                                        >
                                            External Products
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
                                        <template x-for="item in activeTab === 'internal' ? internalproducts : externalproducts" :key="item.id">
                                            <tr
                                                @click="selectItem(item)"
                                                class="cursor-pointer"
                                                :class="{ 'bg-blue-50': selectedItem && selectedItem.id === item.id && selectedItem.name === item.name, 'hover:bg-gray-50': !(selectedItem && selectedItem.id === item.id && selectedItem.name === item.name) }"
                                            >
                                                <td class="px-6 py-4 whitespace-nowrap" x-text="item.name"></td>
                                                <td class="px-6 py-4 whitespace-nowrap" x-text="activeTab === 'internal' ? item.price : item.sold_price"></td>
                                                <td class="px-6 py-4 whitespace-nowrap" x-text="item.product_count"></td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <button
                                                        :disabled="item.product_count === 0"
                                                        @click.stop="selectItem(item)"
                                                        :class="{ 'text-[#0f2360] hover:text-[#fd9c0a]': item.product_count !== 0, 'text-gray-400 cursor-not-allowed': item.product_count === 0 }"
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
                                    <!-- @/csrf -->
                                    <input type="hidden" name="id" x-model="selectedItem.id" />
                                    <input type="hidden" name="name" x-model="selectedItem.name" />
                                    <input type="hidden" name="price" x-model="activeTab === 'internal' ? selectedItem.price : selectedItem.sold_price" />
                                    <input type="hidden" name="units" x-model="selectedItem.product_count" />
                                    <div class="flex items-center space-x-4">
                                        <div>
                                            <label for="quantity" class="block text-sm font-medium text-gray-700">
                                                Quantity
                                            </label>
                                            <input
                                                type="number"
                                                id="quantity"
                                                name="quantity"
                                                :min="1"
                                                :max= "selectedItem.product_count"
                                                x-model.number="quantity"
                                                class="mt-1 block w-24 border border-gray-300 rounded-md shadow-sm p-2"
                                            />
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">
                                                Price
                                            </label>
                                            <p class="mt-1" x-text="'$' + (activeTab === 'internal' ? selectedItem.price : selectedItem.sold_price + ' per unit')"></p>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">
                                                Total
                                            </label>
                                            <p class="mt-1" x-text="'$' + (activeTab === 'internal' ? selectedItem.price : selectedItem.sold_price) * quantity"></p><p class="mt-1" x-text="'$' + (activeTab === 'internal' ? selectedItem.price : selectedItem.sold_price * quantity).toFixed(2)"></p>
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
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Remove
                                            </th>
                                        </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                        <template x-for="(item, index) in cartItems" :key="index">
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap" x-text="item.name"></td>
                                                <td class="px-6 py-4 whitespace-nowrap" x-text="item.price"></td>
                                                <td class="px-6 py-4 whitespace-nowrap" x-text="item.quantity"></td>
                                                <td class="px-6 py-4 whitespace-nowrap" x-text="'$' + item.totalPrice.toFixed(2)"></td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <button @click="removeFromCart(index)" class="text-red-500 hover:text-red-700">
                                                        Remove
                                                    </button>
                                                </td>
                                            </tr>
                                        </template>
                                        </tbody>
                                    </table>
                                    <!-- ✅ Updated View -->
                                    <form id="checkout-form" method="POST" action="{{ route('counter.store') }}">
                                        @csrf
                                        <input type="hidden" name="price" :value="calculateTotal()">

                                        <input type="hidden" name="products" x-ref="productsInput" />

                                        <div class="bg-gray-50 px-6 py-4">
                                            <div class="flex justify-between items-center">
                                                <span class="font-medium">Total Amount:</span>
                                                <span class="font-bold text-lg" x-text="'$' + calculateTotal()"></span>
                                            </div>

                                            <!-- ✅ Replace @click with form submission -->
                                            <button
                                                type="button"
                                                @click="formSubmit"
                                                class="mt-4 w-full bg-[#fd9c0a] text-white py-2 px-4 rounded-md hover:bg-[#e08c09] focus:outline-none"
                                            >
                                                Complete Purchase
                                            </button>
                                        </div>
                                    </form>
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
            activeTab: 'internal',
            selectedItem: null,
            quantity: 1,
            internalproducts: [],
            externalproducts: [],
            cartItems: [],

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
                    const existing = this.cartItems.find(
                        i => i.id === this.selectedItem.id && i.type === this.activeTab
                    );

                    const price = this.activeTab === 'internal' ? this.selectedItem.price : this.selectedItem.sold_price;

                    if (existing) {
                        existing.quantity += this.quantity;
                        existing.totalPrice = existing.quantity * price;
                    } else {
                        this.cartItems.push({
                            id: this.selectedItem.id,
                            name: this.selectedItem.name,
                            type: this.activeTab,
                            price: price,
                            unit: this.selectedItem.unit ?? 'unit',
                            quantity: this.quantity,
                            totalPrice: price * this.quantity
                        });
                    }

                    this.selectedItem = null;
                    this.quantity = 1;
                }
            },
            submitCart() {
                const products = this.cartItems.map(item => ({
                    id: item.id,
                    type: item.type,
                    quantity: item.quantity
                }));
                this.$refs.productsInput.value = JSON.stringify(products);
            },
            formSubmit() {
                this.submitCart(); // first populate hidden field
                const form = document.getElementById('checkout-form');
                form.submit();     // then submit
            },
            removeFromCart(index) {
                this.cartItems.splice(index, 1);
            },
            calculateTotal() {
                return this.cartItems.reduce((total, item) => total + item.totalPrice, 0).toFixed(2);
            },

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
                this.internalproducts = @js($Internalproducts);
                this.externalproducts = @js($Externalproducts);


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
