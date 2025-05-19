<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            História použitia funkcií
        </h2>
    </x-slot>

    {{-- ✅ CDN jQuery + DataTables --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
    <!-- <link href="https://cdn.datatables.net/1.13.6/css/dataTables.tailwindcss.min.css" rel="stylesheet"> -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    

    <form method="POST" action="{{ route('history.usage.action') }}">
        @csrf

        <div class="max-w-7xl mt-6 mx-auto sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row justify-center sm:justify-start items-center gap-4">
                <button type="submit" name="action" value="export"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded text-center">
                    Exportovať CSV
                </button>
                <button type="submit" name="action" value="delete"
                    class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-6 rounded text-center">
                    Vymazať vybrané
                </button>
            </div>
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

                        <table id="datatable" class="min-w-full divide-y divide-gray-200 text-sm text-gray-800">
                            <thead class="bg-gray-100 text-xs text-gray-600 uppercase tracking-wider text-center">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-center">
                                        <input type="checkbox" id="select-all"
                                        class="w-5 h-5 rounded-md border-gray-300 text-indigo-600 focus:ring-indigo-500 focus:ring-2 appearance-none checked:bg-indigo-600 checked:border-transparent">
                                    </th>
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
                                            <input type="checkbox" name="selected[]" value="{{ $log->id }}" 
                                            class="row-checkbox w-5 h-5 rounded-md border-gray-300 text-indigo-600 focus:ring-indigo-500 focus:ring-2 appearance-none checked:bg-indigo-600 checked:border-transparent">
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
                                        <td class="px-4 py-2 text-center align-middle">{{ \Carbon\Carbon::parse($log->used_at)->format('d. m. Y') }}</td>
                                        <td class="px-4 py-2 text-center align-middle">{{ \Carbon\Carbon::parse($log->used_at)->format('H:i') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </form>

    <script>
        $(document).ready(function () {
            const table = $('#datatable').DataTable({
                initComplete: function () {

                    // Flex container pre oba
                    $('div.dataTables_wrapper .dataTables_length, div.dataTables_wrapper .dataTables_filter')
                        .wrapAll('<div class="flex flex-col sm:flex-row items-center justify-between gap-4 sm:gap-6 mb-4"></div>');
                },
                responsive: true,
                scrollX: true
            });

            // checkbox "označ všetkých" iba pre viditeľné riadky
            $('#select-all').on('click', function () {
                const rows = table.rows({ search: 'applied' }).nodes();
                $('input[type="checkbox"]', rows).prop('checked', this.checked);
            });
        });
    </script>

    <style>
        /* Parent wrapper styling */
        div.dataTables_wrapper .dataTables_paginate {
            display: flex;
            justify-content: center;
            margin-top: 1rem;
        }

        /* Base button styling */
        div.dataTables_wrapper .dataTables_paginate .paginate_button {
            background-color: transparent;
            color: #D97706 !important;
            border: 1px solid #4B5563; /* Tailwind gray-700 */
            padding: 6px 12px;
            margin: 0 2px;
            border-radius: 0;
            transition: all 0.2s ease-in-out;
            font-weight: 500;
            min-width: 38px;
            text-align: center;
        }

        /* First and last buttons (← →) */
        div.dataTables_wrapper .dataTables_paginate .paginate_button:first-child {
            border-top-left-radius: 12px;
            border-bottom-left-radius: 12px;
        }

        div.dataTables_wrapper .dataTables_paginate .paginate_button:last-child {
            border-top-right-radius: 12px;
            border-bottom-right-radius: 12px;
        }

        /* Active page */
        div.dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background-color: #D97706;
            color: black !important;
            border-color: #D97706;
        }

        /* Hover effect */
        div.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background-color: #D97706;
            color: white !important;
            border-color: #D97706;
        }

        select[name="datatable_length"] {
            padding-right: 2rem !important;
        }
    </style>


</x-app-layout>
