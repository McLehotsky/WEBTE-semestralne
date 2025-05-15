<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Vitaj, {{ Auth::user()->name }}!
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-100">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded-lg shadow-sm">
                <h3 class="text-lg font-semibold mb-4">Čo chceš dnes spraviť?</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <a href="#" class="block border border-gray-300 rounded-md p-4 hover:bg-gray-100">
                        📄 Nahrať PDF
                    </a>
                    <a href="#" class="block border border-gray-300 rounded-md p-4 hover:bg-gray-100">
                        ✂️ Rozdeliť PDF
                    </a>
                    <a href="#" class="block border border-gray-300 rounded-md p-4 hover:bg-gray-100">
                        🔗 Zlúčiť PDF
                    </a>
                    <a href="#" class="block border border-gray-300 rounded-md p-4 hover:bg-gray-100">
                        🕓 História operácií
                    </a>
                </div>

                <p class="mt-6 text-sm text-gray-500">Klikni na niektorú z akcií vyššie a začni pracovať s PDF súbormi.</p>
            </div>
        </div>
    </div>
</x-app-layout>
