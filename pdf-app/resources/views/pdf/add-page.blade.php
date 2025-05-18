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
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div id="dropBase"
                            class="cursor-pointer p-8 border-2 border-dashed border-gray-400 rounded-lg bg-gray-50 hover:bg-gray-100 transition">
                            <p class="text-xl font-semibold text-gray-600 mb-4">Vyber cieľový PDF súbor, do ktorého sa bude pridávať</p>
                            <p class="text-gray-400">... alebo sem presuň súbor</p>
                        </div>
                        <div id="drop-insert"
                             class="cursor-pointer p-8 border-2 border-dashed border-gray-400 rounded-lg bg-gray-50 hover:bg-gray-100 transition">
                            <p class="text-xl font-semibold text-gray-600 mb-4">Vyber PDF z ktorého sa bude brať</p>
                            <p class="text-gray-400">... alebo sem presuň súbor</p>
                        </div>
                    </div>
                    
                    <div id="insert-file-name" class="mt-4 text-sm text-gray-600"></div>
                    <div id="base-file-name" class="mt-4 text-sm text-gray-600"></div>

                    <div id="base-preview-wrapper" class="mt-8 hidden">
                        <h3 class="text-lg font-semibold mb-2">Náhľad cieľového PDF:</h3>
                        <div id="base-preview-container" class="flex justify-center"></div>
                    </div>

                    <div id="insert-preview-wrapper" class="mt-8 hidden">
                        <h3 class="text-lg font-semibold mb-2">Náhľad PDF, z ktorého berieme stranu:</h3>
                        <div id="insert-preview-container" class="flex justify-center"></div>
                    </div>
                    
                    <input type="file" name="base_file" id="base_file" class="hidden" accept="application/pdf" required> 

                    <div class="mt-6 text-left hidden">
                        <label for="position" class="block font-medium text-gray-700 mb-2">Pozícia vloženia (od 0)</label>
                        <input type="number" name="position" id="position" min="0" required
                               class="w-full border-gray-300 rounded-md shadow-sm">
                    </div>

                    <button type="button" id="add-page-btn"
                            class="mt-6 bg-amber-600 hover:bg-amber-800 text-white font-bold py-2 px-6 rounded transition">
                        Pridať stránku
                    </button>

                    <div id="result" class="mt-6 hidden">
                        <div class="flex items-center bg-white border border-gray-300 rounded-md px-4 py-3 shadow-sm justify-center">
                            <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414L8.414 15l-4.121-4.121a1 1 0 011.414-1.414L8.414 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-sm text-gray-800">
                                Stránka bola úspešne pridaná.
                                <a id="download-link" href="#" download="added.pdf" class="text-amber-600 font-medium underline ml-1">Download PDF</a>
                            </span>
                        </div>
                    </div>

                    <div id="errorModal" class="fixed z-50 inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
                        <div class="bg-white rounded-lg shadow-lg max-w-md w-full p-6">
                            <h2 class="text-xl font-semibold mb-4">Chyba</h2>
                            <p id="errorMessage" class="text-gray-700 mb-4">Niečo sa pokazilo.</p>
                            <div class="text-right">
                                <button id="closeModalBtn" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">Close</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const dropInsert = document.getElementById('drop-insert');
        const insertInput = document.getElementById('insert_file');
        const insertFileName = document.getElementById('insert-file-name');
        const positionInput = document.getElementById('position');

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

                    const scale = 0.33;

                    for (let i = 1; i <= pdf.numPages; i++) {
                        pdf.getPage(i).then(function (page) {
                            const viewport = page.getViewport({ scale });
                            const canvas = document.createElement('canvas');
                            const context = canvas.getContext('2d');
                            canvas.height = viewport.height;
                            canvas.width = viewport.width;
                            page.render({ canvasContext: context, viewport });

                            insertPages[i] = {
                                page: page,
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

                    container.className = 'flex flex-row gap-4 overflow-x-auto';

                    function createDropZone(position) {
                        const dropZone = document.createElement('div');
                        dropZone.className = 'text-sm text-gray-500 text-center py-2 border-2 border-dashed border-transparent hover:border-amber-400 hover:bg-amber-50 transition rounded';
                        dropZone.dataset.position = position;

                        dropZone.addEventListener('dragover', (e) => {
                            e.preventDefault();
                            dropZone.classList.add('border-amber-500', 'bg-amber-50');
                        });

                        dropZone.addEventListener('dragleave', () => {
                            dropZone.classList.remove('border-amber-500', 'bg-amber-50');
                        });

                        dropZone.addEventListener('drop', (e) => {
                            e.preventDefault();
                            const pageToInsert = e.dataTransfer.getData('application/pdf-insert-page');
                            if (!insertPages[pageToInsert]) return;

                            positionInput.value = position;
                            document.querySelectorAll('[data-position]').forEach(z => z.classList.remove('bg-amber-100'));
                            dropZone.classList.add('bg-amber-100');

                            const original = insertPages[pageToInsert];
                            const scale = 0.33;
                            const viewport = original.page.getViewport({ scale });

                            original.page.getViewport({ scale });

                            const canvas = document.createElement('canvas');
                            canvas.width = viewport.width;
                            canvas.height = viewport.height;
                            canvas.className = 'rounded h-[250px] w-auto';

                            const context = canvas.getContext('2d');
                            original.page.render({ canvasContext: context, viewport });


                            const wrapper = document.createElement('div');
                            wrapper.className = 'relative border-2 border-dashed border-amber-300 rounded shadow p-2 bg-amber-50';

                            const pageNumber = document.createElement('div');
                            pageNumber.textContent = `+ Strana ${pageToInsert} (vložená)`;
                            pageNumber.className = 'absolute top-2 left-2 bg-amber-100 text-xs text-amber-800 px-2 py-1 rounded shadow';

                            wrapper.appendChild(pageNumber);
                            wrapper.appendChild(canvas);

                            dropZone.insertAdjacentElement('afterend', wrapper);
                        });

                        return dropZone;
                    }

                    container.appendChild(createDropZone(0));

                    const scale = 0.33;

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

                            canvas.className = 'rounded h-[250px] w-auto';

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

        document.getElementById('add-page-btn').addEventListener('click', () => {
            const base = baseInput.files[0];
            const insert = insertInput.files[0];
            const position = positionInput.value;
    
            if (!base || !insert || position === '') {
                showModal("Prosím vložte oba PDF súbory a vyberte pozíciu presunutím.");
                return;
            }
        
            const formData = new FormData();
            formData.append('base', base);
            formData.append('insert', insert);
            formData.append('position', position);
        
            fetch("{{ route('pdf.add-page.upload') }}", {
                method: "POST",
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.url) {
                    const link = document.getElementById('download-link');
                    link.href = data.url.replace(/\\/g, '');
                    document.getElementById('result').classList.remove('hidden');
                } else {
                    showModal("Niečo sa pokazilo.");
                }
            })
            .catch(() => showModal("Chyba pri pridávaní stránky."));
        });

        function showModal(message) {
            const modal = document.getElementById('errorModal');
            const errorText = document.getElementById('errorMessage');
            errorText.innerText = message;
            modal.classList.remove('hidden');
        }

        document.getElementById('closeModalBtn').addEventListener('click', () => {
            document.getElementById('errorModal').classList.add('hidden');
        });

    </script>
</x-app-layout>