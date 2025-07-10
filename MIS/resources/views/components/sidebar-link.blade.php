@props(['href', 'label', 'icon', 'active' => false])

<a
    href="{{ $href }}"
    class="flex items-center p-3 rounded-lg hover:bg-[#0a1a4a] {{ $active ? 'bg-[#fd9c0a] text-white font-semibold' : '' }}"
>
    @switch($icon)
        @case('layout-dashboard')
            <x-lucide-layout-dashboard class="w-5 h-5 mr-3" />
            @break
        @case('calendar-clock')
            <x-lucide-calendar-clock class="w-5 h-5 mr-3" />
            @break
        @case('wallet')
            <x-lucide-wallet class="w-5 h-5 mr-3" />
            @break
        @case('credit-card')
            <x-lucide-credit-card class="w-5 h-5 mr-3" />
            @break
        @case('bar-chart-3')
            <x-lucide-bar-chart-3 class="w-5 h-5 mr-3" />
            @break
    @endswitch
    {{ $label }}
</a>
