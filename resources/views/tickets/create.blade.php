<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Crear Ticket de Soporte
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('ok'))
                <div
                    class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-700 text-green-800 dark:text-green-200 p-4 rounded-lg">
                    {{ session('ok') }}
                </div>
            @endif

            @if ($errors->any())
                <div
                    class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700 text-red-800 dark:text-red-200 p-4 rounded-lg">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div
                class="bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-700 text-blue-800 dark:text-blue-200 p-4 rounded-lg text-sm">
                <span class="font-semibold">Antes de crear un ticket:</span>
                si ya registraste uno por el mismo caso, revisa <span class="font-semibold">Mis Tickets</span> y agrega
                la información en el detalle para evitar duplicidades.
            </div>

            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 text-gray-900 dark:text-gray-100">
                <form method="POST" action="{{ route('tickets.store') }}" class="space-y-4"
                    enctype="multipart/form-data">
                    @csrf

                    <div>
                        <div class="flex items-center justify-between">
                            <label class="block text-sm font-medium">Asunto</label>
                            <span class="text-xs text-gray-500 dark:text-gray-400" id="subjectCount">0/100</span>
                        </div>

                        <input id="subjectInput" name="subject"
                            class="mt-1 w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            maxlength="100" value="{{ old('subject') }}" required
                            placeholder="Ej: No puedo imprimir / Sin acceso a internet / No prende el monitor">

                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            Resume el problema en una frase. Evita poner datos sensibles.
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium">Categoría</label>

                        <select name="category"
                            class="mt-1 w-full rounded-md p-2 pr-10
                            border border-slate-300 dark:border-slate-700
                            bg-white dark:bg-slate-900
                            text-slate-900 dark:text-slate-100"
                            required>
                            <option value="">-- Seleccionar --</option>

                            <option value="Hardware" @selected(old('category') === 'Hardware')>
                                Equipo o accesorios (computadora, impresora, teclado, mouse, pantalla, escáner, etc.)
                            </option>

                            <option value="Software" @selected(old('category') === 'Software')>
                                Programas o aplicaciones (Word, Excel, Gestión Documental, SATMUN, SIAF u otros
                                sistemas)
                            </option>

                            <option value="Red" @selected(old('category') === 'Red')>
                                Internet o red interna (sin internet, sin acceso a carpetas compartidas, fallas en
                                telefonía IP/anexos)
                            </option>

                            <option value="Accesos" @selected(old('category') === 'Accesos')>
                                Accesos y cuentas (problemas con usuarios o contraseñas, cuenta bloqueada, correo
                                institucional)
                            </option>
                        </select>


                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            Elige la categoría que mejor describa tu solicitud.
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium">Descripción</label>

                        <textarea name="description" rows="6"
                            class="mt-1 w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            required placeholder="Describe el problema de forma clara y concreta.">{{ old('description') }}</textarea>

                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            Incluye información relevante para facilitar la atención.
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium">Adjuntar evidencias (opcional)</label>

                        <input type="file" name="attachments[]" multiple
                            class="mt-1 block w-full text-sm text-gray-700 dark:text-gray-200
               file:mr-4 file:py-2 file:px-4
               file:rounded-md file:border-0
               file:text-xs file:font-semibold file:uppercase file:tracking-widest
               file:bg-gray-100 dark:file:bg-gray-700
               file:text-gray-700 dark:file:text-gray-200
               hover:file:bg-gray-200 dark:hover:file:bg-gray-600" />

                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            Puedes adjuntar hasta 5 archivos (imágenes o PDF). Máx. 10 MB por archivo.
                        </p>
                    </div>

                    <div class="pt-2">
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-[#00528e] text-white rounded-md text-xs font-semibold uppercase tracking-widest hover:bg-[#003f6c] transition">
                            Registrar Ticket
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <script>
        (function() {
            const input = document.getElementById('subjectInput');
            const count = document.getElementById('subjectCount');
            if (!input || !count) return;

            const update = () => {
                count.textContent = `${input.value.length}/100`;
            };

            input.addEventListener('input', update);
            update();
        })();
    </script>

</x-app-layout>
