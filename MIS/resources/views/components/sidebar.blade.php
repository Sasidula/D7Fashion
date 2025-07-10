<!-- resources/views/components/sidebar.blade.php -->
@props(['currentPage'])
@php
    $productPages = ['add-internal-product', 'add-external-product', 'manage-product'];
    $materialsPages = ['manage-stocks', 'view-stocks', 'add-stocks', 'edit-stocks'];

    $isProductOpen = in_array($currentPage, $productPages);
    $isMaterialsOpen = in_array($currentPage, $materialsPages);
@endphp
<aside
    x-show="sidebarOpen"
    x-transition:enter="transition ease-in-out duration-300 transform"
    x-transition:enter-start="-translate-x-full opacity-0"
    x-transition:enter-end="translate-x-0 opacity-100"
    x-transition:leave="transition ease-in-out duration-300 transform"
    x-transition:leave-start="translate-x-0 opacity-100"
    x-transition:leave-end="-translate-x-full opacity-0"
    class="bg-[#0f2360] text-white w-64 h-full fixed top-16 md:top-0 z-30"
    :class="{
        'md:relative md:left-0': sidebarOpen,
        'md:hidden': !sidebarOpen
    }"
    x-data="{ openProduct: {{ $isProductOpen ? 'true' : 'false' }}, openMaterials: {{ $isMaterialsOpen ? 'true' : 'false' }} }"
>
    <nav class="p-4 space-y-2 overflow-y-auto h-[calc(100vh-4rem)] pr-2 custom-scrollbar">
        <x-sidebar-link href="{{ route('dashboard', 'counter') }}" label="Counter" icon="layout-dashboard" :active="$currentPage === 'counter'" />
        <x-sidebar-link href="{{ route('dashboard', 'attendance') }}" label="Attendance" icon="calendar-clock" :active="$currentPage === 'attendance'" />

        <!-- Product -->
        <div>
            <button @click="openProduct = !openProduct"
                    class="flex items-center justify-between w-full p-3 rounded-lg hover:bg-[#0a1a4a] {{ $isProductOpen ? 'bg-[#fd9c0a] text-white font-semibold' : '' }}">
                <div class="flex items-center">
                    <x-lucide-box class="w-5 h-5 mr-3" />
                    Product
                </div>
                <x-lucide-chevron-down :class="{ 'rotate-180': openProduct }" class="w-4 h-4 transition-transform" />
            </button>
            <div x-show="openProduct" x-cloak class="pl-8 mt-1 space-y-1">
                <x-sidebar-sub-link page="add-internal-product" currentPage="{{ $currentPage }}" label="Add Internal Product" />
                <x-sidebar-sub-link page="add-external-product" currentPage="{{ $currentPage }}" label="Add External Product" />
                <x-sidebar-sub-link page="manage-product" currentPage="{{ $currentPage }}" label="Manage Product" />
            </div>
        </div>

        <!-- Materials -->
        <div>
            <button @click="openMaterials = !openMaterials"
                    class="flex items-center justify-between w-full p-3 rounded-lg hover:bg-[#0a1a4a] {{ $isMaterialsOpen ? 'bg-[#fd9c0a] text-white font-semibold' : '' }}">
                <div class="flex items-center">
                    <x-lucide-warehouse class="w-5 h-5 mr-3" />
                    Materials
                </div>
                <x-lucide-chevron-down :class="{ 'rotate-180': openMaterials }" class="w-4 h-4 transition-transform" />
            </button>
            <div x-show="openMaterials" x-cloak class="pl-8 mt-1 space-y-1">
                <x-sidebar-sub-link page="manage-stocks" currentPage="{{ $currentPage }}" label="Manage Stocks" />
                <x-sidebar-sub-link page="view-stocks" currentPage="{{ $currentPage }}" label="View Stocks" />
                <x-sidebar-sub-link page="add-stocks" currentPage="{{ $currentPage }}" label="Add Stocks" />
                <x-sidebar-sub-link page="edit-stocks" currentPage="{{ $currentPage }}" label="Edit Stocks" />
            </div>
        </div>

        <x-sidebar-link href="{{ route('dashboard', 'petty-cash') }}" label="Petty Cash" icon="wallet" :active="$currentPage === 'petty-cash'" />
        <x-sidebar-link href="{{ route('dashboard', 'accounts') }}" label="Accounts" icon="credit-card" :active="$currentPage === 'accounts'" />
        <x-sidebar-link href="{{ route('dashboard', 'reports') }}" label="Reports" icon="bar-chart-3" :active="$currentPage === 'reports'" />
    </nav>
</aside>
