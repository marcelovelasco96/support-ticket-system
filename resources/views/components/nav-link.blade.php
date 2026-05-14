@props(['active'])

@php
    $classes =
        $active ?? false
            ? 'inline-flex items-center h-full px-3 border-b-2
            border-[#00528e]
            text-sm font-medium leading-5
            text-gray-900
            focus:outline-none focus:border-[#003f6c]
            transition duration-150 ease-in-out
            dark:text-white dark:border-[#00528e]'
            : 'inline-flex items-center h-full px-3 border-b-2 border-transparent
            text-sm font-medium leading-5
            text-gray-500 hover:text-[#00528e] hover:border-[#00528e]
            focus:outline-none focus:text-gray-700 focus:border-gray-300
            transition duration-150 ease-in-out
            dark:text-slate-300 dark:hover:text-white dark:hover:border-[#00528e]';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
