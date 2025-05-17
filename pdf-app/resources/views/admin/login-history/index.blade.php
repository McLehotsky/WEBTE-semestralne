<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            História prihlásení používateľov
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <table class="min-w-full divide-y divide-gray-200 text-sm text-gray-800">
                        <thead class="bg-gray-100 text-xs text-gray-600 uppercase tracking-wider">
                            <tr>
                                <th class="px-4 py-2 text-left">Používateľ</th>
                                <th class="px-4 py-2 text-left">Mesto</th>
                                <th class="px-4 py-2 text-left">Štát</th>
                                <th class="px-4 py-2 text-left">Dátum</th>
                                <th class="px-4 py-2 text-left">Čas</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($logs as $log)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-2 font-medium">{{ $log->user->name }}</td>
                                    <td class="px-4 py-2">{{ $log->city }}</td>
                                    <td class="px-4 py-2">{{ $log->country }}</td>
                                    <td class="px-4 py-2">
                                        {{ \Carbon\Carbon::parse($log->logged_in_at)->format('d. m. Y') }}
                                    </td>
                                    <td class="px-4 py-2">
                                        {{ \Carbon\Carbon::parse($log->logged_in_at)->format('H:i') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="mt-6">
                        {{ $logs->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
