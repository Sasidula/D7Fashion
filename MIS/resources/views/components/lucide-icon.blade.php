@php
    $iconComponent = 'lucide-' . str_replace('_', '-', $name);
@endphp

<x-{{ $iconComponent }} {{ $attributes->merge(['class' => $class]) }} />
