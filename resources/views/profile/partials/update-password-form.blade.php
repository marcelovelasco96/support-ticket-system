<section>
    <header>
        <h2 class="text-lg font-medium text-slate-900 dark:text-slate-100">
            Actualizar Contraseña
        </h2>

        <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">
            {{ __('Te recomendamos usar una contraseña que te sea familiar, difícil de olvidar.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div>
            <x-input-label for="update_password_current_password" :value="__('Contraseña Actual')" class="dark:!text-white" />
            <x-text-input id="update_password_current_password" name="current_password" type="password"
                class="mt-1 block w-full border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder:text-slate-400 focus:border-blue-500 focus:ring-blue-500"
                autocomplete="current-password" />
        </div>

        <div>
            <x-input-label for="update_password_password" :value="__('Contraseña Nueva')" class="dark:!text-white" />
            <x-text-input id="update_password_password" name="password" type="password"
                class="mt-1 block w-full border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder:text-slate-400 focus:border-blue-500 focus:ring-blue-500"
                autocomplete="new-password" />
        </div>

        <div>
            <x-input-label for="update_password_password_confirmation" :value="__('Confirmar Contraseña')" class="dark:!text-white" />
            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password"
                class="mt-1 block w-full border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:placeholder:text-slate-400 focus:border-blue-500 focus:ring-blue-500"
                autocomplete="new-password" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>Guardar</x-primary-button>
        </div>
    </form>
</section>
