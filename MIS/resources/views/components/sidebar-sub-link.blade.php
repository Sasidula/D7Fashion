@props(['href', 'label', 'currentPage'])

@php
    $isActive = url()->current() === url($href);
@endphp

<a href="{{ $href }}"
   class="block p-2 rounded hover:bg-[#0a1a4a] {{ $isActive ? 'bg-[#fd9c0a] text-white font-semibold' : '' }}">
    {{ $label }}
</a>
