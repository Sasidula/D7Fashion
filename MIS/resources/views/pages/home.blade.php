@php
    use Illuminate\Support\Facades\Auth;
    $isAdminOrManager = Auth::check() && in_array(Auth::user()->role, ['admin', 'manager']);
@endphp
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
            @include('components.sidebar', ['currentPage' => 'home'])

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
                    <div>
                        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                            @if (session('success'))
                                <div class="mb-4 text-green-600 bg-green-100 border border-green-300 rounded p-3" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 10000)">
                                    {{ session('success') }}
                                </div>
                            @endif
                            @if (session('status'))
                                <div class="mb-4 text-green-600 bg-green-100 border border-green-300 rounded p-3" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 10000)">
                                    {{ session('status') }}
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

                                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                                    <div class="p-6 bg-white border-b border-gray-200 text-center">

                                        You're logged in!
                                        @if($isAdminOrManager)
                                            <br>
                                            Hello, {{ Auth::user()->name }} you are an Admin/Manager.
                                        @else
                                            <br>
                                            Hello, {{ Auth::user()->name }} you are an Employee.
                                        @endif

{{--                                        <div class="relative w-full h-20 flex items-center justify-center">--}}

{{--                                            <!-- Moving GIF -->--}}
{{--                                            <img src="{{ asset('images/19-d97ba53a-unscreen.gif') }}"--}}
{{--                                                 alt="Moving GIF"--}}
{{--                                                 class="absolute h-12 animate-moveGif z-10"--}}
{{--                                                 style="top: 50%; transform: translateY(-50%) rotate(180deg);">--}}
{{--                                        </div>--}}

                                        {{-- GIF Carousel --}}
                                        <div class="mt-8 relative w-64 h-40 mx-auto overflow-hidden rounded-lg shadow-lg">
                                            <div id="gif-carousel" class="flex transition-transform duration-700 ease-in-out">
                                                <img src="{{ asset('images/sell.gif') }}" class="w-64 h-40 object-cover flex-shrink-0" alt="GIF 1">
                                                <img src="{{ asset('images/sale.gif') }}" class="w-64 h-40 object-cover flex-shrink-0" alt="GIF 2">
                                                <img src="{{ asset('images/tshirt.gif') }}" class="w-64 h-40 object-cover flex-shrink-0" alt="GIF 3">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <script>
                                    document.addEventListener("DOMContentLoaded", function () {
                                        const carousel = document.getElementById('gif-carousel');
                                        const slides = carousel.children.length;
                                        let index = 0;

                                        setInterval(() => {
                                            index = (index + 1) % slides;
                                            carousel.style.transform = `translateX(-${index * 100}%)`;
                                        }, 5000); // 5 seconds
                                    });
                                </script>

                                <style>
                                    @keyframes moveGif {
                                        0% { left: 0; transform: rotate(180deg); }
                                        100% { left: calc(100% - 48px); transform: rotate(180deg); } /* 48px = GIF width approx */
                                    }

                                    .animate-moveGif {
                                        animation: moveGif 5s linear infinite;
                                    }
                                </style>

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
