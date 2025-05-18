<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-semibold text-gray-700 text-center">📘 Používateľská príručka</h1>
    </x-slot>

    <div class="max-w-6xl mx-auto py-10 px-6 space-y-8">

    <!-- Added the Export button, works with browser printer -->
    <div class="flex justify-between mb-4 no-print">
        <button onclick="window.print()" class="bg-amber-600 hover:bg-amber-800 text-white font-semibold py-2 px-4 rounded shadow">
            Exportovať príručku do PDF
        </button>
    
        <button
            onclick="window.open('https://node23.webte.fei.stuba.sk/api/pdf/docs', '_blank')"
            class="bg-amber-600 hover:bg-amber-800 text-white font-semibold py-2 px-4 rounded shadow"
        >
            Zobraziť dokumentáciu
        </button>
    </div>

        @include('guide.sections.frontend')
        <!-- @include('guide.sections.backend') -->
    </div>
</x-app-layout>