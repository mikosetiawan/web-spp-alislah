@props(['type' => 'button', 'variant' => 'primary'])

@php
    $classes = [
        'primary' => 'bg-school-blue text-white hover:bg-blue-700 focus:ring-blue-500',
        'secondary' => 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50 focus:ring-gray-300',
        'danger' => 'bg-red-600 text-white hover:bg-red-700 focus:ring-red-500',
    ][$variant] ?? $classes['primary'];
@endphp

<button type="{{ $type }}" {{ $attributes->merge(['class' => "inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 $classes"]) }}>
    {{ $slot }}
</button>