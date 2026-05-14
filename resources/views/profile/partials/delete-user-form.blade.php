<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-slate-900 dark:text-slate-100">
            Borrar Cuenta
        </h2>

        <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">
            {{ __('Si eliminas tu cuenta ahora, perderás el seguimiento de tus consultas actuales. Si tienes un problema técnico sin resolver, te recomendamos esperar a que el ticket se cierre.') }}
        </p>
    </header>

    <x-danger-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')">Borrar
        Cuenta</x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-medium text-slate-900 dark:text-slate-100">
                {{ __('¿Estás seguro(a) que quieres eliminar tu cuenta?') }}
            </h2>

            <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">
                {{ __('Una vez eliminada tu cuenta, se perderá el acceso permanentemente. Introduce tu contraseña para confirmar que deseas eliminar tu cuenta.') }}
            </p>

            <div class="mt-6">
                <x-input-label for="password" value="{{ __('Password') }}" class="sr-only" />

                <x-text-input id="password" name="password" type="password"
                    class="mt-1 block w-3/4 border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder:text-slate-400 focus:border-blue-500 focus:ring-blue-500"
                    placeholder="Contraseña" />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancelar') }}
                </x-secondary-button>

                <x-danger-button class="ms-3">
                    {{ __('Eliminar cuenta') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
