@props(['disabled' => false])

<input
    {{ $attributes->merge([
        'class' => 'border-gray-300 focus:border-amber-800 focus:ring-amber-500 rounded-md shadow-sm',
    ]) }}
/>
