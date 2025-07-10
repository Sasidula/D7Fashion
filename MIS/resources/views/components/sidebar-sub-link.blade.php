@props(['page', 'currentPage', 'label'])

<a href="{{ route('dashboard', $page) }}"
   class="block p-2 rounded hover:bg-[#0a1a4a] {{ $currentPage === $page ? 'bg-[#fd9c0a] text-white font-semibold' : '' }}">
    {{ $label }}
</a>
