<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-semibold text-gray-700 text-center flex items-center justify-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="#D97706" class="size-8">
                <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
            </svg> {{__('guide.title')}}
        </h1>
    </x-slot>

    <div class="max-w-6xl mx-auto py-10 px-6 space-y-8">

    <!-- Added the Export button, works with browser printer -->
     <div  class="max-w-3xl mx-auto">
         <div class="flex justify-between mb-4 no-print">
             <button onclick="window.print()" class="bg-amber-600 hover:bg-amber-800 text-white font-semibold py-2 px-4 rounded shadow">
                    {{__('button.exportPDF')}}
                </button>
                
                <button
                onclick="window.open('{{ config('pdf.base_url') }}/docs', '_blank')"
                class="bg-amber-600 hover:bg-amber-800 text-white font-semibold py-2 px-4 rounded shadow"
                >
                    {{__('button.docs')}}
            </button>
        </div>
    </div>

        @include('guide.sections.frontend')
        <!-- @include('guide.sections.backend') -->
    </div>
</x-app-layout>