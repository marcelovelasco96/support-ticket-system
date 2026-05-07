<x-guest-layout>

    <div class="mb-4 text-sm text-blue-100">
        ¿Olvidaste tu contraseña? No hay problema. Ingresa tu correo electrónico y te enviaremos un enlace para
        restablecerla.
    </div>

    @if (session('status'))
        <div class="mb-6 rounded-md border border-green-200 bg-green-50 p-4 text-sm text-green-800">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email -->
        <div>
            <x-input-label for="email" value="Correo electrónico" class="!text-blue-100" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required
                autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                Enviar
            </x-primary-button>
        </div>
    </form>

</x-guest-layout>
