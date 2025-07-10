<header class="bg-[#0f2360] text-white h-16 flex items-center justify-between px-4 shadow-md relative z-40">
    <div class="flex items-center">
        <!-- Sidebar Toggle -->
        <button
            class="p-2 mr-2 rounded-md hover:bg-[#0a1a4a] focus:outline-none"
            @click="toggleSidebar()"
        >
            <svg x-show="!sidebarOpen" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
            <svg x-show="sidebarOpen" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>

        <!-- Logo & Title -->
        <div class="flex items-center space-x-3">
            <!-- Logo Image -->
            <img src="{{ asset('images/logo.png') }}" alt="Logo"
                 class="w-10 h-10 rounded-full shadow-md" />

            <!-- Title -->
            <div class="font-bold text-xl leading-tight">
                <span class="bg-clip-text text-transparent bg-gradient-to-r from-[#fd9c0a] to-[#ff6b00]">D7</span>
                <span class="text-white">Fashion</span>
            </div>
        </div>


    </div>

    <!-- User Dropdown -->
    <div x-data="{ open: false }" class="relative">
        <button @click="open = !open" class="p-2 rounded-full hover:bg-[#0a1a4a] focus:outline-none">
            <x-lucide-user class="w-6 h-6" />
        </button>

        <div x-show="open"
             x-transition
             @click.outside="open = false"
             class="absolute right-0 mt-2 w-56 bg-white text-gray-700 rounded-md shadow-lg z-50"
        >
            <div class="p-3 border-b border-gray-200">
                <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</p>
                <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
            </div>
            <div class="py-1">
                <a href="{{ route('profile.edit') }}"
                   class="flex items-center w-full px-4 py-2 text-sm hover:bg-gray-100">
                    <x-lucide-edit class="w-4 h-4 mr-2" />
                    Edit Profile
                </a>
                <a href="#"
                   class="flex items-center w-full px-4 py-2 text-sm hover:bg-gray-100"
                   @click.prevent="$dispatch('open-user-create')"
                >
                    <x-lucide-user-plus class="w-4 h-4 mr-2" />
                    Create User
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="flex items-center w-full px-4 py-2 text-sm hover:bg-gray-100">
                        <x-lucide-log-out class="w-4 h-4 mr-2" />
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
