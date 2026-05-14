@props([
    'label' => null,
    'clearUrl' => null,
])

@if (!empty($label))
    <div class="mb-3">
        <span
            class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs
                   bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200
                   border border-gray-200 dark:border-gray-700">
            <span class="text-gray-500 dark:text-gray-400">Filtrado:</span>
            <strong class="font-semibold">{{ $label }}</strong>

            @if (!empty($clearUrl))
                <a href="{{ $clearUrl }}"
                    class="ml-1 inline-flex items-center justify-center w-5 h-5 rounded-full
                          hover:bg-gray-200 dark:hover:bg-gray-700 transition"
                    title="Quitar filtro">×</a>
            @endif
        </span>
    </div>
@endif
