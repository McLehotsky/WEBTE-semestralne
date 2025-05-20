<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{__('history.page.usage.title')}}
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
                },
                responsive: true,
                scrollX: true
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
        /* Parent wrapper styling */
        div.dataTables_wrapper .dataTables_paginate {
            display: flex;
            justify-content: center;
            margin-top: .775rem;
        }

        /* Všetky tlačidlá */
        div.dataTables_wrapper .dataTables_paginate .paginate_button {
            background-color: transparent;
            color: #D97706 !important;
            border: 1px solid transparent;
            padding: 6px 12px;
            border-radius: 9999px; /* full rounded - kruh */
            transition: all 0.2s ease-in-out;
            font-weight: 500;
            min-width: 38px;
            text-align: center;
        }

        /* Aktívna strana */
        div.dataTables_wrapper .dataTables_paginate .paginate_button.current {
            border: 2px solid #D97706 !important;
            color: black !important;
            background-color: transparent !important;
            font-weight: 600;
        }

        /* Hover efekt pre neaktívne */
        div.dataTables_wrapper .dataTables_paginate .paginate_button:not(.current):hover {
            border: 1px solid #D97706;
            background-color: transparent;
            color: #D97706 !important;
        }

        /* Štýl pre "Previous"/"Next" tlačidlá */
        div.dataTables_wrapper .dataTables_paginate .paginate_button.previous,
        div.dataTables_wrapper .dataTables_paginate .paginate_button.next {
            font-weight: 500;
            padding: 6px 12px;
            border-radius: 9999px;
        }

        /* Hover efekt pre "Previous"/"Next" */
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
