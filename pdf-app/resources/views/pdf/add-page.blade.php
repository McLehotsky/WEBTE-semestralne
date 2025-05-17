<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Add Page to PDF
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-100 min-h-screen">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-8 rounded-lg shadow-md border border-gray-300">
                <form id="add-page-form" enctype="multipart/form-data" class="text-center">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Base PDF</label>
                            <div id="drop-base" class="cursor-pointer p-4 border-2 border-dashed border-gray-400 rounded-lg bg-gray-50 hover:bg-gray-100 transition">
                                <p class="font-semibold text-gray-600">Drop or choose base PDF</p>
                                <input type="file" name="base" id="base" accept="application/pdf" class="hidden" required>
                                <p id="base-name" class="mt-2 text-sm text-gray-500"></p>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Insert PDF (1 page)</label>
                            <div id="drop-insert" class="cursor-pointer p-4 border-2 border-dashed border-gray-400 rounded-lg bg-gray-50 hover:bg-gray-100 transition">
                                <p class="font-semibold text-gray-600">Drop or choose insert PDF</p>
                                <input type="file" name="insert" id="insert" accept="application/pdf" class="hidden" required>
                                <p id="insert-name" class="mt-2 text-sm text-gray-500"></p>
                            </div>
                        </div>
                    </div>

                    <div id="base-preview" class="mt-6 flex flex-wrap gap-4 border rounded p-4 bg-white hidden items-start"></div>
                    <div id="insert-preview" class="mt-6 flex justify-center border rounded p-4 bg-white hidden"></div>
                    
                    <input type="hidden" name="position" id="position">

                    <button type="button" id="add-page-btn"
                            class="mt-6 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded transition">
                        Add Page
                    </button>

                    <div id="result" class="mt-6 hidden">
                        <div class="flex items-center bg-white border border-gray-300 rounded-md px-4 py-3 shadow-sm justify-center">
                            <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414L8.414 15l-4.121-4.121a1 1 0 011.414-1.414L8.414 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-sm text-gray-800">
                                Page was successfully added.
                                <a id="download-link" href="#" download="merged.pdf" class="text-blue-600 font-medium underline ml-1">Download PDF</a>
                            </span>
                        </div>
                    </div>

                    <div id="errorModal" class="fixed z-50 inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
                        <div class="bg-white rounded-lg shadow-lg max-w-md w-full p-6">
                            <h2 class="text-xl font-semibold mb-4">Error</h2>
                            <p id="errorMessage" class="text-gray-700 mb-4">Something went wrong.</p>
                            <div class="text-right">
                                <button id="closeModalBtn" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">Close</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>
    <script>
        const dropBase = document.getElementById('drop-base');
        const dropInsert = document.getElementById('drop-insert');
        const inputBase = document.getElementById('base');
        const inputInsert = document.getElementById('insert');
        const baseName = document.getElementById('base-name');
        const insertName = document.getElementById('insert-name');
        const basePreview = document.getElementById('base-preview');
        const insertPreview = document.getElementById('insert-preview');
        const positionInput = document.getElementById('position');

        dropBase.addEventListener('click', () => inputBase.click());
        dropInsert.addEventListener('click', () => inputInsert.click());

        inputBase.addEventListener('change', () => {
            if (inputBase.files.length > 0) {
                baseName.innerText = inputBase.files[0].name;
                renderPDF(inputBase.files[0], basePreview, true);
            }
        });

        inputInsert.addEventListener('change', () => {
            if (inputInsert.files.length > 0) {
                insertName.innerText = inputInsert.files[0].name;
                renderPDF(inputInsert.files[0], insertPreview, false);
            }
        });

        function renderPDF(file, container, isBase) {
            container.innerHTML = '';
            const reader = new FileReader();

            reader.onload = function () {
                const typedarray = new Uint8Array(reader.result);
            
                pdfjsLib.getDocument(typedarray).promise.then(function (pdf) {
                    container.classList.remove('hidden');
                    const numPages = isBase ? pdf.numPages : 1;
                
                    for (let i = 1; i <= numPages; i++) {
                        pdf.getPage(i).then(function (page) {
                            const scale = 0.33;
                            const viewport = page.getViewport({ scale });
                            const canvas = document.createElement('canvas');
                            canvas.height = viewport.height;
                            canvas.width = viewport.width;
                        
                            const context = canvas.getContext('2d');
                            page.render({ canvasContext: context, viewport });
                        
                            const wrapper = document.createElement('div');
                            wrapper.className = 'relative border rounded shadow hover:shadow-lg p-1';
                            wrapper.setAttribute('data-index', i - 1);
                            canvas.className = 'rounded w-full h-auto';
                        
                            if (isBase) {
                                // DROP ZÓNA PRED STRÁNKOU
                                const dropZone = document.createElement('div');
                                dropZone.className = 'w-full h-4 my-1 bg-transparent transition-all drop-indicator';
                                dropZone.dataset.index = i - 1;
                            
                                dropZone.ondragover = e => {
                                    e.preventDefault();
                                    e.dataTransfer.dropEffect = 'move';
                                    dropZone.classList.add('bg-blue-400');
                                };
                                dropZone.ondragleave = () => dropZone.classList.remove('bg-blue-400');
                                dropZone.ondrop = e => {
                                    e.preventDefault();
                                    positionInput.value = dropZone.dataset.index;
                                    document.querySelectorAll('.drop-indicator').forEach(el => el.classList.remove('bg-blue-400'));
                                    dropZone.classList.add('bg-blue-400');
                                };
                            
                                container.appendChild(dropZone);
                            } else {
                                canvas.setAttribute('draggable', true);
                                canvas.addEventListener('dragstart', e => {
                                    e.dataTransfer.setData('text/html', canvas.outerHTML);
                                    e.dataTransfer.effectAllowed = 'move';
                                });
                            }
                        
                            wrapper.appendChild(canvas);
                            container.appendChild(wrapper);
                        
                            // ZÁVEREČNÁ DROP ZÓNA
                            if (isBase && i === pdf.numPages) {
                                const endDropZone = document.createElement('div');
                                endDropZone.className = 'w-2 bg-transparent transition-all drop-indicator';
                                endDropZone.dataset.index = pdf.numPages;
                            
                                endDropZone.ondragover = e => {
                                    e.preventDefault();
                                    e.dataTransfer.dropEffect = 'move';
                                    endDropZone.classList.add('bg-blue-400');
                                };
                                endDropZone.ondragleave = () => endDropZone.classList.remove('bg-blue-400');
                                dropZone.ondrop = e => {
                                    e.preventDefault();
                                    const insertHTML = e.dataTransfer.getData('text/html');
                                    if (insertHTML) {
                                        // Vloženie canvasu medzi stránky base
                                        const insertWrapper = document.createElement('div');
                                        insertWrapper.className = 'relative border border-dashed p-1 bg-yellow-100 rounded';
                                        insertWrapper.innerHTML = insertHTML;
                                    
                                        // Zisti pozíciu kde to vložiť
                                        const insertIndex = parseInt(dropZone.dataset.index);
                                        const allWrappers = [...basePreview.querySelectorAll('.relative')];
                                    
                                        if (insertIndex >= allWrappers.length) {
                                            basePreview.appendChild(insertWrapper);
                                        } else {
                                            basePreview.insertBefore(insertWrapper, allWrappers[insertIndex]);
                                        }
                                    
                                        // Nastav pozíciu pre backend
                                        positionInput.value = insertIndex;
                                    }
                                
                                    document.querySelectorAll('.drop-indicator').forEach(el => el.classList.remove('bg-blue-400'));
                                };

                            
                                container.appendChild(endDropZone);
                            }
                        });
                    }
                });
            };
        
            reader.readAsArrayBuffer(file);
        }


        document.getElementById('add-page-btn').addEventListener('click', () => {
            const base = inputBase.files[0];
            const insert = inputInsert.files[0];
            const position = positionInput.value;

            if (!base || !insert || position === '') {
                showModal("Please drop both PDFs and select drop position by dragging.");
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
                    showModal("Something went wrong.");
                }
            })
            .catch(() => showModal("Error while adding page."));
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

<!-- TOTO DAM PREC A UROBIM TO CELE INAK LEBO TATO PICOVINA PROSTE NEJDE ESTE ZE CHCEM MAT NIECO PEKNE V TOMTO ZIVOTE -->