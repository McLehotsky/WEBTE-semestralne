<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-700 leading-tight flex items-center justify-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="#D97706" class="size-8">
              <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0Zm0 0c0 1.657 1.007 3 2.25 3S21 13.657 21 12a9 9 0 1 0-2.636 6.364M16.5 12V8.25" />
            </svg>
            {{__('history.page.login.title')}}
        </h2>
    </x-slot>

    {{-- ✅ CDN jQuery + DataTables --}}
    {{-- <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css"> --}}
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

                    // Dolný riadok: info + paginate
                    $('div.dataTables_wrapper .dataTables_info, div.dataTables_wrapper .dataTables_paginate')
                        .wrapAll('<div class="flex flex-col sm:flex-row items-center justify-between gap-4 sm:gap-6 mt-4"></div>');
                },
                responsive: true,
                scrollX: true
            });
        });
    </script>

    {{-- Štýl pre pagination --}}
    <style>
        /* ✅ Spodný panel s info a pagination bližšie k tabuľke */
        div.dataTables_wrapper .dataTables_info,
        div.dataTables_wrapper .dataTables_paginate {
            padding-top: 0.5rem;
            padding-bottom: 0.5rem;
            margin-top: 0 !important;
        }

        /* ✅ Flexbox pre rozloženie info doľava a stránkovania doprava */
        div.dataTables_wrapper .dataTables_info {
            flex: 1 1 auto;
            text-align: left;
        }

        div.dataTables_wrapper .dataTables_paginate {
            flex: 1 1 auto;
            justify-content: flex-end;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-top: 0 !important;
        }

        /* ✅ Flex aj pre horný panel (length/filter) */
        div.dataTables_wrapper .dataTables_length,
        div.dataTables_wrapper .dataTables_filter,
        div.dataTables_wrapper .dataTables_info,
        div.dataTables_wrapper .dataTables_paginate {
            display: flex;
            align-items: center;
        }

        /* ✅ Štýl stránkovacích tlačidiel */
        div.dataTables_wrapper .dataTables_paginate .paginate_button {
            background-color: transparent !important;
            color: #D97706 !important;
            border: 2px solid transparent;
            padding: 0.5rem 0.75rem;
            border-radius: 9999px;
            transition: all 0.2s ease-in-out;
            font-weight: 500;
            min-width: 40px;
            text-align: center;
            cursor: pointer;
        }

        /* Hover efekt */
        div.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background-color: #fef3c7;
            border-color: #D97706;
        }

        /* Aktívna stránka */
        div.dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background-color: #D97706 !important;
            color: white !important;
            border-color: #D97706 !important;
            font-weight: 600;
            cursor: default;
        }

        /* 🚫 Neaktívne tlačidlo (napr. „Predošlá“ na prvej stránke) */
        div.dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
            opacity: 0.4;
            pointer-events: none;
            cursor: default;
        }

        /* Zaoblený select */
        div.dataTables_wrapper .dataTables_length select {
            border: 1px solid #D97706;
            border-radius: 0.5rem;
            padding: 0.375rem 0.75rem;
            background-color: white;
            color: #374151;
            font-size: 0.875rem;
            line-height: 1.25rem;
            outline: none;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 20 20' fill='gray' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath fill-rule='evenodd' d='M5.23 7.21a.75.75 0 011.06.02L10 10.939l3.71-3.71a.75.75 0 111.06 1.061l-4.24 4.25a.75.75 0 01-1.06 0l-4.24-4.25a.75.75 0 01.02-1.06z' clip-rule='evenodd'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.5rem center;
            background-size: 1rem;
        }

        /* Zaoblený vyhľadávací input */
        div.dataTables_wrapper .dataTables_filter input {
            border: 1px solid #D97706;
            border-radius: 0.5rem;
            padding: 0.375rem 0.75rem;
            background-color: white;
            color: #374151;
            font-size: 0.875rem;
            line-height: 1.25rem;
            outline: none;
            transition: border-color 0.2s ease-in-out;
        }

        div.dataTables_wrapper .dataTables_filter input:focus,
        div.dataTables_wrapper .dataTables_length select:focus {
            border-color: #fb923c; /* svetlejšia oranžová pri focus */
            box-shadow: 0 0 0 1px #fb923c;
        }

        /* Voliteľné: select padding */
        select[name="datatable_length"] {
            padding-right: 2rem !important;
        }
    </style>
</x-app-layout>
