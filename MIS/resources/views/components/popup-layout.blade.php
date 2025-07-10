<!-- resources/views/components/popup-layout.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'Popup' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="overflow-hidden">
<div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-3xl mx-4 overflow-hidden">
        <div class="max-h-[90vh] overflow-y-auto rounded-lg scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-transparent">
            <!-- Header -->
            <div class="flex items-center justify-between p-4 border-b border-gray-200 sticky top-0 bg-white z-10">
                <button onclick="window.history.back()" class="text-gray-600 hover:text-gray-900">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </button>
                <h2 class="text-lg font-semibold text-gray-800">{{ $title ?? 'Popup' }}</h2>
                <div class="w-6"></div>
            </div>

            <!-- Slot content -->
            <div class="p-6">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
</body>
</html>
