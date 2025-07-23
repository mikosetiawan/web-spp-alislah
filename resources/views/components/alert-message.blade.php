@props(['type' => 'info'])

@php
    $baseClasses = 'p-4 mb-4 text-sm rounded-lg';

    switch ($type) {
        case 'success':
            $typeClasses = 'text-green-800 bg-green-100';
            break;
        case 'error':
            $typeClasses = 'text-red-800 bg-red-100';
            break;
        case 'warning':
            $typeClasses = 'text-yellow-800 bg-yellow-100';
            break;
        case 'info':
        default:
            $typeClasses = 'text-blue-800 bg-blue-100';
            break;
    }
@endphp

@if (session($type))
    <div {{ $attributes->merge(['class' => "$baseClasses $typeClasses"]) }}>
        {{ session($type) }}
    </div>
@endif
