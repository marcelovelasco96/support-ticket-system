<x-guest-layout>

    <div class="text-center mb-6">
        <h1 class="text-lg font-semibold text-white">
            Sistema de Soporte OTDTI
        </h1>
        <p class="text-sm text-blue-100">
            Municipalidad Distrital de Breña
        </p>
    </div>

    @if (session('status'))
        <div class="mb-6 rounded-md border border-green-200 bg-green-50 p-4 text-sm text-green-800">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" value="Correo electrónico" class="!text-blue-100" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')"
                required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" value="Contraseña" class="!text-blue-100" />

            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required
                autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox"
                    class="rounded border-gray-300 text-[#fbbf00] shadow-sm focus:ring-[#fbbf00]" name="remember">
                <span class="ms-2 text-sm text-blue-100">Recordarme</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-blue-100 hover:text-white rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    href="{{ route('password.request') }}">
                    ¿Olvidaste tu contraseña?
                </a>
            @endif

            <x-primary-button class="ms-3">
                Ingresar
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
