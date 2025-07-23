@props(['name', 'type' => 'text', 'label' => null, 'required' => false, 'value' => null])

@if($label)
    <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 mb-1">
        {{ $label }} @if($required) <span class="text-red-500">*</span> @endif
    </label>
@endif

<input 
    type="{{ $type }}" 
    id="{{ $name }}" 
    name="{{ $name }}" 
    value="{{ old($name, $value) }}"
    {{ $required ? 'required' : '' }}
    {{ $attributes->merge(['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm']) }}
>

@error($name)
    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
@enderror