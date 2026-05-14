<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight
        text-gray-800 dark:text-slate-100">
            Administración - Usuarios
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('ok'))
                <div class="bg-green-50 border border-green-200 text-green-800 p-4 rounded">
                    {{ session('ok') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-800 p-4 rounded">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white dark:bg-slate-900 shadow-sm sm:rounded-lg p-6 dark:border dark:border-slate-800">
                <div class="flex items-center justify-between gap-4 mb-4">
                    <div>
                        <h3 class="font-semibold text-lg text-slate-900 dark:text-slate-100">Crear usuario</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            Crea usuarios y asigna rol. El password inicial puede cambiarse luego.
                        </p>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-5">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Nombre --}}
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Nombre</label>
                            <input name="name" value="{{ old('name') }}" required
                                class="mt-1 w-full rounded-md border border-slate-300 dark:border-slate-700
                           bg-white dark:bg-slate-800
                           text-slate-900 dark:text-slate-100
                           placeholder:text-slate-400
                           focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                           px-3 py-2" />
                        </div>

                        {{-- Correo --}}
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Correo</label>
                            <input name="email" type="text" value="{{ old('email') }}" required
                                class="mt-1 w-full rounded-md border border-slate-300 dark:border-slate-700
                           bg-white dark:bg-slate-800
                           text-slate-900 dark:text-slate-100
                           placeholder:text-slate-400
                           focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                           px-3 py-2" />
                        </div>

                        {{-- Número --}}
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Número</label>
                            <input name="phone" value="{{ old('phone') }}"
                                class="mt-1 w-full rounded-md border border-slate-300 dark:border-slate-700
       bg-white dark:bg-slate-800
       text-slate-900 dark:text-slate-100
       placeholder:text-slate-400
       focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
       px-3 py-2" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-200">Área</label>

                            <select name="area_id"
                                class="mt-1 w-full rounded-md
                                        border border-gray-300 dark:border-slate-700
                                        bg-white dark:bg-slate-900
                                        text-gray-900 dark:text-slate-100
                                        px-3 py-2"
                                required>
                                <option value="">-- Seleccionar --</option>

                                @foreach ($areas as $a)
                                    <option value="{{ $a->id }}" @selected((string) old('area_id') === (string) $a->id)>
                                        {{ $a->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Rol --}}
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Rol</label>
                            <select name="role" required
                                class="mt-1 w-full rounded-md border border-slate-300 dark:border-slate-700
                           bg-white dark:bg-slate-800
                           text-slate-900 dark:text-slate-100
                           focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                           px-3 py-2">
                                <option value="">-- Seleccionar --</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role }}" @selected(old('role') === $role)>{{ $role }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Password inicial --}}
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Password
                                inicial</label>
                            <input name="password" type="text" value="{{ old('password', 'Brena#2026!') }}" required
                                class="mt-1 w-full rounded-md border border-slate-300 dark:border-slate-700
                           bg-white dark:bg-slate-800
                           text-slate-900 dark:text-slate-100
                           placeholder:text-slate-400
                           focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                           px-3 py-2" />
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                Luego el usuario lo cambia en su perfil.
                            </p>
                        </div>
                    </div>

                    <div class="pt-2 flex items-center justify-end">
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 rounded-md text-xs font-semibold uppercase tracking-widest
                       bg-blue-600 text-white hover:bg-blue-700
                       focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2
                       dark:focus:ring-offset-slate-900 transition">
                            Crear usuario
                        </button>
                    </div>
                </form>
            </div>

            <div class="bg-white dark:bg-slate-900 shadow-sm sm:rounded-lg p-6 dark:border dark:border-slate-800">
                <div class="flex flex-col gap-4 mb-4 md:flex-row md:items-end md:justify-between">
                    <div>
                        <h3 class="font-semibold text-lg text-slate-900 dark:text-slate-100">Usuarios</h3>
                    </div>

                    <form method="GET" action="{{ route('admin.users.index') }}"
                        class="grid grid-cols-1 md:grid-cols-3 gap-3 w-full md:w-auto">
                        <div>
                            <label class="block text-xs font-medium text-slate-600 dark:text-slate-300">Buscar</label>
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Nombre o correo"
                                oninput="clearTimeout(this._timer); this._timer = setTimeout(() => this.form.submit(), 500)"
                                class="mt-1 w-full rounded-md border border-slate-300 dark:border-slate-700
    bg-white dark:bg-slate-800
    text-slate-900 dark:text-slate-100
    placeholder:text-slate-400
    focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
    px-3 py-2 text-sm" />
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-slate-600 dark:text-slate-300">Área</label>
                            <select name="area_id" onchange="this.form.submit()"
                                class="mt-1 w-full rounded-md border border-slate-300 dark:border-slate-700
                bg-white dark:bg-slate-800
                text-slate-900 dark:text-slate-100
                focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                px-3 py-2 text-sm">
                                <option value="">Todas</option>
                                @foreach ($areas as $a)
                                    <option value="{{ $a->id }}" @selected((string) request('area_id') === (string) $a->id)>
                                        {{ $a->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex items-end gap-2">

                            <a href="{{ route('admin.users.index') }}"
                                class="inline-flex items-center px-4 py-2 rounded-md text-xs font-semibold uppercase tracking-widest
                bg-slate-200 text-slate-700 hover:bg-slate-300
                dark:bg-slate-700 dark:text-slate-100 dark:hover:bg-slate-600
                transition">
                                Limpiar
                            </a>
                        </div>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr
                                class="border-b border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300">
                                <th class="py-3 px-3 text-center whitespace-nowrap">ID</th>
                                <th class="py-3 px-3 text-center whitespace-nowrap">Nombre</th>
                                <th class="py-3 px-3 text-center whitespace-nowrap">Correo</th>
                                <th class="py-3 px-3 text-center whitespace-nowrap">Número</th>
                                <th class="py-3 px-3 text-center whitespace-nowrap">Área</th>
                                <th class="py-3 px-3 text-center whitespace-nowrap">Roles</th>
                                <th class="py-3 px-3 text-center whitespace-nowrap">Creado</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @foreach ($users as $u)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/60 transition">
                                    <td class="py-3 px-3 whitespace-nowrap text-slate-700 dark:text-slate-200">
                                        {{ $u->id }}
                                    </td>

                                    <td class="py-3 px-3 text-slate-900 dark:text-slate-100">
                                        {{ $u->name }}
                                    </td>

                                    <td class="py-3 px-3 text-slate-700 dark:text-slate-200 text-center">
                                        {{ $u->email }}
                                    </td>

                                    <td class="py-3 px-3 text-center text-slate-700 dark:text-slate-200">
                                        {{ $u->phone ?? '—' }}
                                    </td>

                                    <td class="py-3 px-3 text-center">
                                        {{ $u->area?->short_name ?? ($u->area?->name ?? '—') }}
                                    </td>

                                    <td class="py-3 px-3 text-center">
                                        @php
                                            $rolesTxt = $u->getRoleNames()->join(', ');
                                        @endphp

                                        @if ($rolesTxt)
                                            <span
                                                class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold
                                    bg-slate-100 text-slate-700 border border-slate-200
                                    dark:bg-slate-800 dark:text-slate-100 dark:border-slate-700">
                                                {{ $rolesTxt }}
                                            </span>
                                        @else
                                            <span class="text-slate-400 dark:text-slate-500">—</span>
                                        @endif
                                    </td>

                                    <td
                                        class="py-3 px-3 whitespace-nowrap text-slate-700 dark:text-slate-200 text-center">
                                        {{ $u->created_at?->format('Y-m-d H:i') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $users->links() }}
                </div>

            </div>

        </div>
    </div>
</x-app-layout>
