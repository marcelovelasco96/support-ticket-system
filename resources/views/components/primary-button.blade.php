<button
    {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-[#fbbf00] border border-transparent rounded-md font-semibold text-xs text-black uppercase tracking-widest shadow-sm hover:bg-[#f59e0b] hover:shadow-md hover:-translate-y-[1px] focus:bg-[#f59e0b] active:bg-[#d97706] transition-all duration-200 ease-in-out']) }}>
    {{ $slot }}
</button>
