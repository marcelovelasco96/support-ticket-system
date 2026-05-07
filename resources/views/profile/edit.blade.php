<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight text-slate-900 dark:text-slate-100">
            Mi Perfil
        </h2>
    </x-slot>

    <div
        class="{{ session('status') === 'password-updated' || $errors->updatePassword->any() || session('status') === 'profile-updated' ? 'pt-6 pb-12' : 'py-12' }}">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status') === 'profile-updated')
                <div class="rounded-md border border-green-200 bg-green-50 p-3 text-sm text-green-800">
                    Información de perfil actualizada correctamente.
                </div>
            @elseif (session('status') === 'password-updated')
                <div class="rounded-md border border-green-200 bg-green-50 p-3 text-sm text-green-800">
                    Contraseña actualizada correctamente.
                </div>
            @elseif ($errors->updatePassword->any())
                <div class="rounded-md border border-red-200 bg-red-50 p-3 text-sm text-red-800">
                    {{ $errors->updatePassword->first() }}
                </div>
            @endif

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg dark:bg-slate-900 dark:border dark:border-slate-800">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg dark:bg-slate-900 dark:border dark:border-slate-800">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg dark:bg-slate-900 dark:border dark:border-slate-800">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
