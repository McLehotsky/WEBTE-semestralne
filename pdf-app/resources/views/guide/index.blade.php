<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-semibold text-gray-700 text-center">📘 Používateľská príručka</h1>
    </x-slot>

    <div class="max-w-6xl mx-auto py-10 px-6 space-y-8">

    <!-- Added the Export button, works with browser printer -->
    <div class="flex justify-end mb-4 no-print">
        <button onclick="window.print()" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded shadow">
            Exportovať príručku do PDF
        </button>
    </div>
        @include('guide.sections.frontend')
        @include('guide.sections.backend')
    </div>
</x-app-layout>