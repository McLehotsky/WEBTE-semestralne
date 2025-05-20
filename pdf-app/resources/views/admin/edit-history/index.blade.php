<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-700 leading-tight flex items-center justify-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="#D97706" class="size-8">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
            </svg>
            {{__('history.page.usage.title')}}
        </h2>
    </x-slot>

    {{-- ✅ CDN jQuery + DataTables --}}
    {{-- <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css"> --}}
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.tailwindcss.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    

    <form method="POST" action="{{ route('history.usage.action') }}">
        @csrf

        <div class="max-w-7xl mt-6 mx-auto sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row justify-center sm:justify-start items-center gap-4">
                <button type="submit" name="action" value="export"
                    class="bg-amber-600 hover:bg-amber-800 text-white font-bold py-2 px-6 rounded text-center">
                    {{__('history.page.usage.export.button')}}
                </button>
                <button type="submit" name="action" value="delete"
                    class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-6 rounded text-center">
                    {{__('history.page.usage.delete.button')}}
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
                                        class="w-5 h-5 rounded-md border-gray-300 text-amber-500 focus:ring-amber-400 focus:ring-2 appearance-none checked:bg-amber-500 checked:border-transparent">
                                    </th>
                                    <th class="px-4 py-2 text-center">{{__('history.page.usage.table.user')}}</th>
                                    <th class="px-4 py-2 text-center">{{__('history.page.usage.table.used')}}</th>
                                    <th class="px-4 py-2 text-center">{{__('history.page.usage.table.type')}}</th>
                                    <th class="px-4 py-2 text-center">{{__('history.page.usage.table.date')}}</th>
                                    <th class="px-4 py-2 text-center">{{__('history.page.usage.table.time')}}</th>
                                </tr>
                            </thead>

                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($logs as $log)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-2 text-center align-middle">
                                            <input type="checkbox" name="selected[]" value="{{ $log->id }}" 
                                            class="row-checkbox w-5 h-5 rounded-md border-gray-300 text-amber-500 focus:ring-amber-400 focus:ring-2 appearance-none checked:bg-amber-500 checked:border-transparent">
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
        const dtLang = @json(__('datatable'));
        $(document).ready(function () {
            const table = $('#datatable').DataTable({
                order: [[4, 'desc'], [5, 'desc']],
                columnDefs: [
                    { orderable: false, targets: 0 } // zakáže sortovanie pre prvý stĺpec
                ],
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

                    // Flex container pre oba
                    $('div.dataTables_wrapper .dataTables_length, div.dataTables_wrapper .dataTables_filter')
                        .wrapAll('<div class="flex flex-col sm:flex-row items-center justify-between gap-4 sm:gap-6 mb-4"></div>');

                    // Dolný riadok: info + paginate
                    $('div.dataTables_wrapper .dataTables_info, div.dataTables_wrapper .dataTables_paginate')
                        .wrapAll('<div class="flex flex-col sm:flex-row items-center justify-between gap-4 sm:gap-6 mt-4"></div>');
                },
                responsive: true,
                scrollX: true,
                paging: true
            });

            // checkbox "označ všetkých" iba pre viditeľné riadky
            let globalSelected = new Set();

            $('#select-all').on('click', function () {
                const rows = table.rows({ search: 'applied' }).nodes();
                const isChecked = this.checked;

                $('input[type="checkbox"]', rows).each(function () {
                    $(this).prop('checked', isChecked);

                    const val = $(this).val();
                    if (isChecked) {
                        globalSelected.add(val);
                    } else {
                        globalSelected.delete(val);
                    }
                });
            });


            table.on('search.dt', function () {
                // odznačí všetky checkboxy vo všetkých riadkoch
                $('#datatable input[type="checkbox"]').prop('checked', false);

                // odznačí aj hlavný select-all checkbox
                $('#select-all').prop('checked', false);
            });

            $('form').on('submit', function () {

                // Odstráni staré hidden inputy (ak sú)
                $('input[name="selected[]"][type="hidden"]').remove();

                // Pridá nové hidden inputy pre každý selected[] záznam
                for (const val of globalSelected) {
                    $('<input>').attr({
                        type: 'hidden',
                        name: 'selected[]',
                        value: val
                    }).appendTo('form');
                }
            });
        });
    </script>

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
