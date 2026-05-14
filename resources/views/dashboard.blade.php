<x-app-layout>
    <div class="p-4 bg-yellow-200 font-bold">
        DASHBOARD.BLADE CARGADO
    </div>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    @php
        // Ajusta estos checks según cómo tengas guardado el rol:
        // Opción típica: users.role = 'admin' | 'tecnico' | 'usuario'
        $role = auth()->user()->role ?? null;
    @endphp

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if ($role === 'admin')
                @include('dashboards.admin')
            @elseif($role === 'tecnico')
                @include('dashboards.tecnico')
            @else
                @include('dashboards.usuario')
            @endif
        </div>
    </div>
</x-app-layout>
