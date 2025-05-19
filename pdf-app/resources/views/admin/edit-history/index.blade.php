<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            História použitia funkcií
        </h2>
    </x-slot>

    <form method="POST" action="{{ route('history.usage.action') }}">
        @csrf
        <div class="mt-6 flex items-center gap-4 px-12">
            <button type="submit" name="action" value="export" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 m-4 rounded">
                Exportovať CSV
            </button>
            <button type="submit" name="action" value="delete" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                Vymazať vybrané
            </button>
        </div>

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 overflow-x-auto">

                        @if (session('status'))
                            <div class="mb-4 text-green-600 font-semibold">
                                {{ session('status') }}
                            </div>
                        @endif

                            <div class="overflow-x-auto">
                              <table class="min-w-full divide-y divide-gray-200 text-sm text-gray-800">
                        <thead class="bg-gray-100 text-xs text-gray-600 uppercase tracking-wider text-center">
                            <tr>
                                <th class="px-4 py-2 text-center"></th>
                                <th class="px-4 py-2 text-center">Používateľ</th>
                                <th class="px-4 py-2 text-center">Použité</th>
                                <th class="px-4 py-2 text-center">Cez</th>
                                <th class="px-4 py-2 text-center">Dátum</th>
                                <th class="px-4 py-2 text-center">Čas</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($logs as $log)
                            <tr class="border-b dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700">
                                <td class="px-4 py-2">
                                    <input type="checkbox" name="selected_logs[]" value="{{ $log->id }}" class="row-checkbox w-4 h-4 bg-gray-100 border-gray-300 rounded text-primary-600 focus:ring-primary-500">
                                </td>
                                <td class="px-4 py-2 font-medium text-gray-900 dark:text-white">
                                    {{ $log->user->name }}
                                </td>
                                <td class="px-4 py-2">
                                    <span class="text-xs bg-gray-100 px-2 py-1 rounded">
                                        {{ $log->pdfEdit->name }}
                                    </span>
                                </td>
                                <td class="px-4 py-2">{{ $log->accessed_via }}</td>
                                <td class="px-4 py-2">{{ \Carbon\Carbon::parse($log->used_at)->format('d.m.Y') }}</td>
                                <td class="px-4 py-2">{{ \Carbon\Carbon::parse($log->used_at)->format('H:i') }}</td>
                            </tr>
                            @endforeach
                        </tbody>


                        </table>
                    </div>
                        <div class="mt-4">
                            {{ $logs->links() }}
                        </div>
                        <livewire:hello-world />
                        <livewire:users-table />
                    </div>
                </div>
            </div>
        </div>
    </form>

    <script>
        const selectAll = document.getElementById('checkbox-all');
        const checkboxes = document.querySelectorAll('.row-checkbox');

        selectAll.addEventListener('change', function () {
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
        });
    </script>

</x-app-layout>
