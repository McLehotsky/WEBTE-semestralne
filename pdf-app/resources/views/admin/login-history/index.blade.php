<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            História prihlásení používateľov
        </h2>
    </x-slot>

    {{-- ✅ CDN jQuery + DataTables --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 overflow-x-auto">
                    <table id="datatable" class="min-w-full divide-y divide-gray-200 text-sm text-gray-800">
                        <thead class="bg-gray-100 text-xs text-gray-600 uppercase tracking-wider">
                            <tr>
                                <th class="px-4 py-2 text-left">{{__('history.page.usage.table.user')}}</th>
                                <th class="px-4 py-2 text-left">{{__('history.page.login.table.city')}}</th>
                                <th class="px-4 py-2 text-left">{{__('history.page.login.table.country')}}</th>
                                <th class="px-4 py-2 text-left">{{__('history.page.usage.table.date')}}</th>
                                <th class="px-4 py-2 text-left">{{__('history.page.usage.table.time')}}</th>
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
                </div>
            </div>
        </div>
    </div>

    {{-- DataTables init --}}
    <script>
        const dtLang = @json(__('datatable'));
        $(document).ready(function () {
            $('#datatable').DataTable({
                order: [[3, 'desc'], [4, 'desc']], // zoradenie podľa dátumu a času
                language: {
                    search: dtLang.search,
                    lengthMenu: dtLang.lengthMenu,
                    zeroRecords: dtLang.zeroRecords,
                    info: dtLang.info,
                    infoEmpty: dtLang.infoEmpty,
                    infoFiltered: dtLang.infoFiltered,
                    paginate: dtLang.paginate
                },
                initComplete: function () {
                    // Flex container pre length a search
                    $('div.dataTables_wrapper .dataTables_length, div.dataTables_wrapper .dataTables_filter')
                        .wrapAll('<div class="flex flex-col sm:flex-row items-center justify-between gap-4 sm:gap-6 mb-4"></div>');
                },
                responsive: true,
                scrollX: true
            });
        });
    </script>

    {{-- Štýl pre pagination --}}
    <style>
        div.dataTables_wrapper .dataTables_paginate {
            display: flex;
            justify-content: center;
            margin-top: 0.75rem;
        }

        div.dataTables_wrapper .dataTables_paginate .paginate_button {
            background-color: transparent;
            color: #D97706 !important;
            border: 1px solid transparent;
            padding: 6px 12px;
            border-radius: 9999px;
            transition: all 0.2s ease-in-out;
            font-weight: 500;
            min-width: 38px;
            text-align: center;
        }

        div.dataTables_wrapper .dataTables_paginate .paginate_button.current {
            border: 2px solid #D97706 !important;
            color: black !important;
            background-color: transparent !important;
            font-weight: 600;
        }

        div.dataTables_wrapper .dataTables_paginate .paginate_button:not(.current):hover {
            border: 1px solid #D97706;
            background-color: transparent;
            color: #D97706 !important;
        }

        div.dataTables_wrapper .dataTables_paginate .paginate_button.previous,
        div.dataTables_wrapper .dataTables_paginate .paginate_button.next {
            font-weight: 500;
            padding: 6px 12px;
            border-radius: 9999px;
        }

        div.dataTables_wrapper .dataTables_paginate .paginate_button.previous:hover,
        div.dataTables_wrapper .dataTables_paginate .paginate_button.next:hover {
            background-color: transparent;
            border: 1px solid #D97706;
            color: #D97706 !important;
        }

        select[name="datatable_length"] {
            padding-right: 2rem !important;
        }
    </style>
</x-app-layout>
