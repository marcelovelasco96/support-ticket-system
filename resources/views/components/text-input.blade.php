@props(['disabled' => false])

<input @disabled($disabled)
    {{ $attributes->merge(['class' => 'border-gray-300 bg-white text-slate-900 placeholder-slate-400 focus:border-[#fbbf00] focus:ring-[#fbbf00] rounded-md shadow-sm']) }}>
