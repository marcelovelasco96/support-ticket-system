<x-guest-layout>

    @php
        $isSetPassword = request()->get('type') === 'set';
    @endphp

    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <input type="hidden" name="type" value="{{ request()->get('type') }}">

        <!-- Email Address -->
        <div>
            <x-input-label for="email" value="Correo electrónico" class="text-white" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $request->email)"
                required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="$isSetPassword ? 'Contraseña' : 'Nueva contraseña'" class="text-white" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required
                autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="$isSetPassword ? 'Confirmar contraseña' : 'Confirmar nueva contraseña'" class="text-white" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password"
                name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ $isSetPassword ? 'Establecer contraseña' : 'Restablecer contraseña' }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
