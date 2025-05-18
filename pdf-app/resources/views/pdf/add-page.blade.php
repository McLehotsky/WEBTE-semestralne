<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Pridať stránku z jedného PDF do druhého
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-100 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-8 rounded-lg shadow-md border-2 border-dashed border-gray-300 text-center">
                <form id="add-page-form" enctype="multipart/form-data">
                    @csrf
                    <input type="file" name="insert_file" id="insert_file" class="hidden" accept="application/pdf" required>

                    <div id="drop-insert"
                         class="cursor-pointer p-8 border-2 border-dashed border-gray-400 rounded-lg bg-gray-50 hover:bg-gray-100 transition">
                        <p class="text-xl font-semibold text-gray-600 mb-4">Vyber PDF z ktorého sa bude brať</p>
                        <p class="text-gray-400">... alebo sem presuň súbor</p>
                    </div>

                    <div id="insert-file-name" class="mt-4 text-sm text-gray-600"></div>

                    <div id="insert-preview-wrapper" class="mt-8 hidden">
                        <h3 class="text-lg font-semibold mb-2">Náhľad PDF, z ktorého berieme stranu:</h3>
                        <div id="insert-preview-container" class="flex justify-center"></div>
                    </div>

                    <input type="file" name="base_file" id="base_file" class="hidden" accept="application/pdf" required>

                    <div id="dropBase"
                        class="cursor-pointer p-8 border-2 border-dashed border-gray-400 rounded-lg bg-gray-50 hover:bg-gray-100 transition mt-8">
                        <p class="text-xl font-semibold text-gray-600 mb-4">Vyber cieľový PDF súbor, do ktorého sa bude pridávať</p>
                        <p class="text-gray-400">... alebo sem presuň súbor</p>
                    </div>

                    <div id="base-file-name" class="mt-4 text-sm text-gray-600"></div>

                    <div id="base-preview-wrapper" class="mt-8 hidden">
                        <h3 class="text-lg font-semibold mb-2">Náhľad cieľového PDF:</h3>
                        <div id="base-preview-container" class="flex justify-center"></div>
                    </div>

                    <div class="mt-6 text-left">
                        <label for="position" class="block font-medium text-gray-700 mb-2">Pozícia vloženia (od 0)</label>
                        <input type="number" name="position" id="position" min="0" required
                               class="w-full border-gray-300 rounded-md shadow-sm">
                    </div>

                    <button type="submit"
                            class="mt-6 bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-6 rounded transition">
                        Pridať stránku
                    </button>
                </form>

                <div id="result" class="mt-6 hidden">
                    <div class="bg-green-100 text-green-800 p-4 rounded shadow">
                        Hotovo! <a id="download-link" href="#" class="underline" target="_blank">Stiahnuť výsledné PDF</a>
                    </div>
                </div>

                <div id="error" class="mt-6 hidden">
                    <div class="bg-red-100 text-red-800 p-4 rounded shadow" id="error-msg">
                        Niečo sa pokazilo.
                    </div>
                </div>
            </div>
        </div>
    </div>

 <script>
        const dropInsert = document.getElementById('drop-insert');
        const insertInput = document.getElementById('insert_file');
        const insertFileName = document.getElementById('insert-file-name');

        dropInsert.addEventListener('click', () => insertInput.click());
        dropInsert.addEventListener('dragover', (e) => { e.preventDefault(); dropInsert.classList.add('bg-gray-200'); });
        dropInsert.addEventListener('dragleave', () => dropInsert.classList.remove('bg-gray-200'));
        dropInsert.addEventListener('drop', (e) => {
            e.preventDefault(); dropInsert.classList.remove('bg-gray-200');
            if (e.dataTransfer.files.length === 1) {
                insertInput.files = e.dataTransfer.files;
                insertFileName.innerText = e.dataTransfer.files[0].name;
                previewInsertPDF(e.dataTransfer.files[0]);
            }
        });

        insertInput.addEventListener('change', () => {
            const file = insertInput.files[0];
            insertFileName.innerText = file?.name || "";
            if (file) previewInsertPDF(file);
        });

        const insertPages = {};

        function previewInsertPDF(file) {
            const reader = new FileReader();
            reader.onload = function () {
                const typedarray = new Uint8Array(reader.result);
                pdfjsLib.getDocument(typedarray).promise.then(function (pdf) {
                    const container = document.getElementById('insert-preview-container');
                    container.innerHTML = '';
                    document.getElementById('insert-preview-wrapper').classList.remove('hidden');
                    container.className = 'grid grid-cols-3 gap-4 justify-items-center';

                    const scale = 0.5;

                    for (let i = 1; i <= pdf.numPages; i++) {
                        pdf.getPage(i).then(function (page) {
                            const viewport = page.getViewport({ scale });
                            const canvas = document.createElement('canvas');
                            const context = canvas.getContext('2d');
                            canvas.height = viewport.height;
                            canvas.width = viewport.width;
                            page.render({ canvasContext: context, viewport });

                            // Ukladáme objekt s canvas + scale
                            insertPages[i] = {
                                canvas: canvas,
                                scale: scale
                            };

                            const wrapper = document.createElement('div');
                            wrapper.className = 'relative border rounded shadow p-2 bg-white';
                            wrapper.setAttribute('draggable', 'true');
                            wrapper.dataset.page = i;

                            wrapper.addEventListener('dragstart', (e) => {
                                e.dataTransfer.setData('application/pdf-insert-page', i);
                            });

                            const pageNumber = document.createElement('div');
                            pageNumber.textContent = `Strana ${i}`;
                            pageNumber.className = 'absolute top-2 left-2 bg-white text-xs text-gray-600 px-2 py-1 rounded shadow';

                            canvas.className = 'rounded w-full h-auto';
                            wrapper.appendChild(pageNumber);
                            wrapper.appendChild(canvas);
                            container.appendChild(wrapper);
                        });
                    }
                });
            };
            reader.readAsArrayBuffer(file);
        }



        const dropBase = document.getElementById('dropBase');
        const baseInput = document.getElementById('base_file');
        const baseFileName = document.getElementById('base-file-name');


        dropBase.addEventListener('click', () => baseInput.click());
        dropBase.addEventListener('dragover', (e) => { e.preventDefault(); dropBase.classList.add('bg-gray-200'); });
        dropBase.addEventListener('dragleave', () => dropBase.classList.remove('bg-gray-200'));
        dropBase.addEventListener('drop', (e) => {
            e.preventDefault(); dropBase.classList.remove('bg-gray-200');
            if (e.dataTransfer.files.length === 1) {
                baseInput.files = e.dataTransfer.files;
                baseFileName.innerText = e.dataTransfer.files[0].name;
                previewBasePDF(e.dataTransfer.files[0]);
            }
        });

        baseInput.addEventListener('change', () => {
            const file = baseInput.files[0];
            baseFileName.innerText = file?.name || "";
            if (file) previewBasePDF(file);
        });

function previewBasePDF(file) {
    const reader = new FileReader();
    reader.onload = function () {
        const typedarray = new Uint8Array(reader.result);
        pdfjsLib.getDocument(typedarray).promise.then(function (pdf) {
            const container = document.getElementById('base-preview-container');
            container.innerHTML = '';
            document.getElementById('base-preview-wrapper').classList.remove('hidden');

            container.className = 'flex flex-col gap-2';
            const positionInput = document.getElementById('position');

            function createDropZone(position) {
                const dropZone = document.createElement('div');
                dropZone.className = 'text-sm text-gray-500 text-center py-2 border-2 border-dashed border-transparent hover:border-purple-400 hover:bg-purple-50 transition rounded';
                dropZone.innerText = `⬇️ Pusti sem pre vloženie na pozíciu ${position}`;
                dropZone.dataset.position = position;

                dropZone.addEventListener('dragover', (e) => {
                    e.preventDefault();
                    dropZone.classList.add('border-purple-500', 'bg-purple-50');
                });

                dropZone.addEventListener('dragleave', () => {
                    dropZone.classList.remove('border-purple-500', 'bg-purple-50');
                });

                dropZone.addEventListener('drop', (e) => {
                    e.preventDefault();
                    const pageToInsert = e.dataTransfer.getData('application/pdf-insert-page');
                    if (!insertPages[pageToInsert]) return;

                    positionInput.value = position;
                    document.querySelectorAll('[data-position]').forEach(z => z.classList.remove('bg-purple-100'));
                    dropZone.classList.add('bg-purple-100');

                    const original = insertPages[pageToInsert];
                    const originalCanvas = original.canvas;

                    // Klonujeme canvas bez zväčšenia
                    const clonedCanvas = document.createElement('canvas');
                    clonedCanvas.width = originalCanvas.width;
                    clonedCanvas.height = originalCanvas.height;
                    clonedCanvas.classList.add('rounded', 'w-full', 'h-auto');

                    const ctx = clonedCanvas.getContext('2d');
                    ctx.drawImage(originalCanvas, 0, 0);

                    const wrapper = document.createElement('div');
                    wrapper.className = 'relative border-2 border-dashed border-purple-300 rounded shadow p-2 bg-purple-50';

                    const pageNumber = document.createElement('div');
                    pageNumber.textContent = `+ Strana ${pageToInsert} (vložená)`;
                    pageNumber.className = 'absolute top-2 left-2 bg-purple-100 text-xs text-purple-800 px-2 py-1 rounded shadow';

                    wrapper.appendChild(pageNumber);
                    wrapper.appendChild(clonedCanvas);

                    dropZone.insertAdjacentElement('afterend', wrapper);
                });

                return dropZone;
            }

            container.appendChild(createDropZone(0));

            const scale = 0.5;

            for (let i = 1; i <= pdf.numPages; i++) {
                pdf.getPage(i).then(function (page) {
                    const viewport = page.getViewport({ scale });
                    const canvas = document.createElement('canvas');
                    const context = canvas.getContext('2d');
                    canvas.height = viewport.height;
                    canvas.width = viewport.width;
                    page.render({ canvasContext: context, viewport });

                    const wrapper = document.createElement('div');
                    wrapper.className = 'relative border rounded shadow p-2 bg-white mx-auto w-fit';

                    const pageNumber = document.createElement('div');
                    pageNumber.textContent = `Strana ${i}`;
                    pageNumber.className = 'absolute top-2 left-2 bg-white text-xs text-gray-600 px-2 py-1 rounded shadow';

                    canvas.className = 'rounded w-full h-auto';

                    wrapper.appendChild(pageNumber);
                    wrapper.appendChild(canvas);
                    container.appendChild(wrapper);

                    container.appendChild(createDropZone(i));
                });
            }
        });
    };
    reader.readAsArrayBuffer(file);
}



        const form = document.getElementById('add-page-form');
        const resultBox = document.getElementById('result');
        const errorBox = document.getElementById('error');
        const link = document.getElementById('download-link');
        const errorMsg = document.getElementById('error-msg');

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            resultBox.classList.add('hidden');
            errorBox.classList.add('hidden');

            const formData = new FormData(form);

            fetch("{{ route('pdf.add-page.upload') }}", {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.url) {
                    link.href = data.url;
                    resultBox.classList.remove('hidden');
                } else {
                    errorBox.classList.remove('hidden');
                }
            })
            .catch(err => {
                errorBox.classList.remove('hidden');
                errorMsg.innerText = "Nastala chyba: " + err.message;
            });
        });

        </script>
</x-app-layout>