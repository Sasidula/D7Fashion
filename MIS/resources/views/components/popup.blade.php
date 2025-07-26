<!-- resources/views/components/popup.blade.php -->
<x-app-layout>
<template x-if="popup.open">
    <div
        x-transition.opacity
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
    >
        <div class="bg-white rounded-lg shadow-lg w-full max-w-3xl mx-4 overflow-hidden">
            <div class="max-h-[90vh] overflow-y-auto rounded-lg scrollbar-thin">
                <!-- Popup Header -->
                <div class="flex items-center justify-between p-4 border-b border-gray-200 sticky top-0 bg-white z-10">
                    <button @click="popup.open = false; popup.requestId++"  class="text-gray-600 hover:text-gray-900">
                        <x-lucide-arrow-left class="w-6 h-6" />
                    </button>
                    <h2 class="text-lg font-semibold text-gray-800" x-text="popup.title">Popup</h2>
                    <div class="w-6"></div>
                </div>

                <!-- Dynamic Popup Content -->
                <div class="p-6" x-html="popup.content">
                    <!-- fallback if JS fails -->
                    Loading...
                </div>
            </div>
        </div>
    </div>
</template>
</x-app-layout>
