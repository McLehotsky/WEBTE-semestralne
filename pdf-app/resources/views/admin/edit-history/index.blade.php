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
                    <div class="p-6">

                        @if (session('status'))
                            <div class="mb-4 text-green-600 font-semibold">
                                {{ session('status') }}
                            </div>
                        @endif

                        <table class="min-w-full divide-y divide-gray-200 text-sm text-gray-800">
                           <thead class="bg-gray-100 text-xs text-gray-600 uppercase tracking-wider">
                            <tr>
                                <th class="px-4 py-2 text-left"></th>
                                <th class="px-4 py-2 text-left">Používateľ</th>
                                <th class="px-4 py-2 text-left">Použité</th>
                                <th class="px-4 py-2 text-left">Cez</th>
                                <th class="px-4 py-2 text-left">Dátum</th>
                                <th class="px-4 py-2 text-left">Čas</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($logs as $log)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-2">
                                        <input type="checkbox" name="selected_logs[]" value="{{ $log->id }}" class="form-checkbox h-4 w-4 text-indigo-600">
                                    </td>
                                    <td class="px-4 py-2 font-medium">{{ $log->user->name }}</td>
                                    <td class="px-4 py-2">{{ $log->pdfEdit->name }}</td>
                                    <td class="px-4 py-2">{{ ucfirst($log->accessed_via) }}</td>
                                    <td class="px-4 py-2">{{ \Carbon\Carbon::parse($log->used_at)->format('d. m. Y') }}</td>
                                    <td class="px-4 py-2">{{ \Carbon\Carbon::parse($log->used_at)->format('H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>

                        </table>

                        <div class="mt-4">
                            {{ $logs->links() }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </form>
</x-app-layout>
