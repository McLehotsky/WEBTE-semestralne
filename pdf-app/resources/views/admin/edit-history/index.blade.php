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

                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($logs as $log)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-2 text-center align-middle">
                                        <input type="checkbox" name="selected_logs[]" value="{{ $log->id }}" class="form-checkbox h-4 w-4 text-indigo-600">
                                    </td>
                                    <td class="px-4 py-2 text-center align-middle">{{ $log->user->name }}</td>
                                    <td class="px-4 py-2 text-center align-middle">
                                        @php
                                            $id = $log->pdfEdit->id;
                                            $badgeClasses = match ($id) {
                                                1 => 'bg-purple-100 text-purple-800',
                                                2 => 'bg-red-100 text-red-800',
                                                3 => 'bg-yellow-100 text-yellow-800',
                                                4 => 'bg-orange-100 text-orange-800',
                                                5 => 'bg-blue-100 text-blue-800',
                                                6 => 'bg-green-100 text-green-800',
                                                7 => 'bg-pink-100 text-pink-800',
                                                8 => 'bg-indigo-100 text-indigo-800',
                                                9 => 'bg-emerald-100 text-emerald-800',
                                                10 => 'bg-teal-100 text-teal-800',
                                                default => 'bg-gray-100 text-gray-800'
                                            };
                                        @endphp

                                        <span class="inline-flex items-center justify-center rounded-full text-xs font-medium px-3 py-1 {{ $badgeClasses }}">
                                            {{ $log->pdfEdit->name }}
                                        </span>
                                    </td>



                                    <td class="px-4 py-2 text-center align-middle">
                                        @if ($log->accessed_via === 'frontend')
                                            <span class="inline-flex items-center justify-center rounded-full bg-blue-100 text-blue-800 text-xs font-medium px-3 py-1 w-20 text-center">
                                                Frontend
                                            </span>
                                        @elseif ($log->accessed_via === 'api')
                                            <span class="inline-flex items-center justify-center rounded-full bg-green-100 text-green-800 text-xs font-medium px-3 py-1 w-18 text-center">
                                                API
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2 text-center align-middle"">{{ \Carbon\Carbon::parse($log->used_at)->format('d. m. Y') }}</td>
                                    <td class="px-4 py-2 text-center align-middle">{{ \Carbon\Carbon::parse($log->used_at)->format('H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>

                        </table>
                    </div>
                        <div class="mt-4">
                            {{ $logs->links() }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </form>
</x-app-layout>
