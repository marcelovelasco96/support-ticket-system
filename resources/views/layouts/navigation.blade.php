<nav x-data="{ open: false }"
    class="bg-white border-b border-gray-100
            dark:bg-slate-900 dark:border-slate-800">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-[70px]">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center gap-3">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto" />
                    </a>

                    <div class="hidden md:block leading-tight">
                        <div class="text-sm font-semibold text-slate-800 dark:text-slate-100">
                            Sistema de Soporte OTDTI
                        </div>
                        <div class="text-[11px] text-slate-500 dark:text-slate-400">
                            Municipalidad Distrital de Breña
                        </div>
                    </div>
                </div>

                <!-- Navigation Links -->
                <!-- Navigation Links -->
                <div class="hidden h-full items-center space-x-6 sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    @if (auth()->user()->hasRole('usuario'))
                        <x-nav-link :href="route('tickets.index')" :active="request()->routeIs('tickets.index')">
                            Mis Tickets
                        </x-nav-link>

                        <x-nav-link :href="route('tickets.create')" :active="request()->routeIs('tickets.create')">
                            Crear Ticket
                        </x-nav-link>
                    @endif

                    @if (auth()->user()->hasRole('admin'))
                        <x-nav-link :href="route('tickets.create')" :active="request()->routeIs('tickets.create')">
                            Crear Ticket
                        </x-nav-link>
                    @endif

                    @if (auth()->user()->hasRole('tecnico'))
                        <x-nav-link :href="route('tickets.inbox')" :active="request()->routeIs('tickets.inbox')">
                            Bandeja
                        </x-nav-link>

                        <x-nav-link :href="route('tickets.mywork')" :active="request()->routeIs('tickets.mywork')">
                            Mis Asignados
                        </x-nav-link>

                        <x-nav-link :href="route('tickets.history')" :active="request()->routeIs('tickets.history')">
                            Historial
                        </x-nav-link>
                    @endif

                    @if (auth()->user()->hasRole('admin'))
                        <x-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.index')">
                            Usuarios
                        </x-nav-link>

                        <x-nav-link :href="route('admin.tickets')" :active="request()->routeIs('admin.tickets')">
                            Tickets
                        </x-nav-link>
                    @endif
                </div>

            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 shrink-0">
                <button type="button" @click="toggle()"
                    class="mr-3 relative inline-flex h-8 w-14 items-center rounded-full
    bg-slate-200 dark:bg-slate-700 transition-colors duration-300
    focus:outline-none"
                    title="Cambiar modo claro / oscuro">

                    <!-- círculo -->
                    <span
                        class="inline-flex h-6 w-6 transform items-center justify-center rounded-full
        bg-white dark:bg-slate-900 shadow-md transition-transform duration-300"
                        :class="dark ? 'translate-x-7' : 'translate-x-1'">

                        <!-- icono -->
                        <svg x-show="!dark" xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-slate-600"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12.79A9 9 0 1111.21 3
                   7 7 0 0021 12.79z" />
                        </svg>

                        <svg x-show="dark" xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-yellow-400"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v2m0 14v2m9-9h-2M5 12H3
                   m15.364 6.364l-1.414-1.414
                   M7.05 7.05 5.636 5.636
                   m12.728 0L16.95 7.05
                   M7.05 16.95l-1.414 1.414
                   M12 8a4 4 0 100 8 4 4 0 000-8z" />
                        </svg>

                    </span>

                </button>

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button
                            class="inline-flex items-center px-3 py-2 rounded-md text-sm leading-4 font-medium
                                    border border-transparent
                                    text-gray-500 bg-white hover:text-gray-700
                                    focus:outline-none transition ease-in-out duration-150

                                    dark:text-slate-200 dark:bg-slate-800 dark:hover:text-white">
                            <div class="max-w-[160px] truncate" title="{{ Auth::user()->name }}">
                                {{ Auth::user()->name }}
                            </div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            Mi perfil
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                Cerrar sesión
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            @if (auth()->user()->hasRole('usuario'))
                <x-responsive-nav-link :href="route('tickets.index')" :active="request()->routeIs('tickets.index')">
                    Mis Tickets
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('tickets.create')" :active="request()->routeIs('tickets.create')">
                    Crear Ticket
                </x-responsive-nav-link>
            @endif

            @if (auth()->user()->hasRole('tecnico'))
                <x-responsive-nav-link :href="route('tickets.inbox')" :active="request()->routeIs('tickets.inbox')">
                    Bandeja
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('tickets.mywork')" :active="request()->routeIs('tickets.mywork')">
                    Mis Asignados
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('tickets.history')" :active="request()->routeIs('tickets.history')">
                    Historial
                </x-responsive-nav-link>
            @endif

            @if (auth()->user()->hasRole('admin'))
                <x-responsive-nav-link :href="route('tickets.create')" :active="request()->routeIs('tickets.create')">
                    Crear Ticket
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.index')">
                    Usuarios
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('admin.tickets')" :active="request()->routeIs('admin.tickets')">
                    Tickets
                </x-responsive-nav-link>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    Mi perfil
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        Cerrar sesión
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
