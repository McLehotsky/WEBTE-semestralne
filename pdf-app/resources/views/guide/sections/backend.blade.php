<section class="max-w-3xl mx-auto p-6 bg-white shadow-sm rounded-md text-center mt-10">
            <h2 class="text-2xl font-bold text-gray-900 mb-6 border-b pb-4">⚙️ API Dokumentácia (Backend)</h2>
            <p class="text-gray-700 mb-6">
                Táto časť príručky popisuje API, ktoré aplikácia poskytuje pre backendovú manipuláciu so súbormi a históriou.
            </p>
            <div class="space-y-6 text-gray-800 text-left">
                <div>
                    <h3 class="text-lg font-semibold text-indigo-700 mb-1">📄 <code>GET /api/files</code></h3>
                    <p>Získanie zoznamu nahraných PDF súborov.</p>
                </div>

                <div>
                    <h3 class="text-lg font-semibold text-indigo-700 mb-1">📤 <code>POST /api/files</code></h3>
                    <p>Nahratie nového PDF súboru. Vyžaduje autentifikáciu pomocou tokenu.</p>
                </div>

                <div>
                    <h3 class="text-lg font-semibold text-indigo-700 mb-1">🕓 <code>GET /api/history</code> <span class="text-sm text-gray-500">(len admin)</span></h3>
                    <p>Načítanie histórie zmien a manipulácií so súbormi. Prístup len pre administrátorov.</p>
                </div>
            </div>
 </section>