<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-700 leading-tight flex items-center justify-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="#D97706" class="size-8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21 3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5" />
            </svg>
            {{__('dashboard.card.reorder')}}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-100 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-8 rounded-lg shadow-md border-2 border-dashed border-gray-300 text-center">
                <form id="reorder-form" enctype="multipart/form-data">
                    @csrf
                    <input type="file" name="file" id="file" class="hidden" accept="application/pdf" required>

                    <div id="drop-area"
                         class="cursor-pointer p-8 border-2 border-dashed border-gray-400 rounded-lg bg-gray-50 hover:bg-gray-100 transition">
                        <p class="text-xl font-semibold text-gray-600 mb-4">Choose a PDF</p>
                        <p class="text-gray-400">... or drop a file here</p>
                    </div>

                    <div id="file-name" class="mt-4 text-sm text-gray-600"></div>

                    <h3 id="select-heading" class="text-lg font-semibold mt-8 mb-2 hidden">Reorder pages:</h3>
                    <div id="preview-scroll-wrapper"
                         class="mt-6 max-h-[600px] overflow-y-auto border border-gray-300 rounded-md p-4 shadow-inner bg-white hidden">
                        <div id="preview-container" class="grid grid-cols-3 gap-4 justify-items-center"></div>
                    </div>

                    <div class="text-center mt-6">
                        <button type="button" id="reorder-btn"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded transition hidden">
                            Reorder Pages
                        </button>
                    </div>

                    <div id="result" class="mt-6 hidden">
                        <div class="flex items-center bg-white border border-gray-300 rounded-md px-4 py-3 shadow-sm">
                            <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414L8.414 15l-4.121-4.121a1 1 0 011.414-1.414L8.414 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-sm text-gray-800">
                                PDF was successfully reordered.
                                <a id="download-link" href="#" class="text-blue-600 font-medium underline ml-1" target="_blank">Download PDF</a>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
    <script>
        const dropArea = document.getElementById('drop-area');
        const fileInput = document.getElementById('file');
        const fileName = document.getElementById('file-name');

        dropArea.addEventListener('click', () => fileInput.click());

        dropArea.addEventListener('dragover', e => {
            e.preventDefault();
            dropArea.classList.add('bg-gray-200');
        });

        dropArea.addEventListener('dragleave', () => dropArea.classList.remove('bg-gray-200'));

        dropArea.addEventListener('drop', e => {
            e.preventDefault();
            dropArea.classList.remove('bg-gray-200');

            if (e.dataTransfer.files.length === 1) {
                fileInput.files = e.dataTransfer.files;
                fileName.innerText = e.dataTransfer.files[0].name;
                loadPDF(fileInput.files[0]);
            } else {
                showModal("Please select exactly 1 PDF file.");
            }
        });

        fileInput.addEventListener('change', () => {
            const file = fileInput.files[0];
            if (file) {
                fileName.innerText = file.name;
                loadPDF(file);
            }
        });

        function loadPDF(file) {
            const reader = new FileReader();
            reader.onload = function () {
                const typedarray = new Uint8Array(reader.result);
                pdfjsLib.getDocument(typedarray).promise.then(function (pdf) {
                    const previewContainer = document.getElementById('preview-container');
                    previewContainer.innerHTML = '';

                    document.getElementById('select-heading').classList.remove('hidden');
                    document.getElementById('preview-scroll-wrapper').classList.remove('hidden');
                    document.getElementById('reorder-btn').classList.remove('hidden');

                    for (let i = 0; i < pdf.numPages; i++) {
                        pdf.getPage(i + 1).then(function (page) {
                            const scale = 0.5;
                            const viewport = page.getViewport({ scale });
                            const canvas = document.createElement('canvas');
                            const context = canvas.getContext('2d');
                            canvas.height = viewport.height;
                            canvas.width = viewport.width;
                            page.render({ canvasContext: context, viewport });

                            const wrapper = document.createElement('div');
                            wrapper.className = 'relative border rounded shadow hover:shadow-lg';
                            wrapper.setAttribute('data-index', i);
                            canvas.className = 'rounded w-full h-auto';
                            wrapper.appendChild(canvas);
                            previewContainer.appendChild(wrapper);
                        });
                    }

                    Sortable.create(previewContainer, {
                        animation: 150
                    });
                });
            };
            reader.readAsArrayBuffer(file);
        }

        document.getElementById('reorder-btn').addEventListener('click', () => {
            const file = fileInput.files[0];
            const order = [...document.querySelectorAll('#preview-container > div')]
                .map(div => div.dataset.index)
                .join(',');

            if (!file || !order) {
                showModal("Please upload a file and reorder the pages.");
                return;
            }

            const formData = new FormData();
            formData.append('file', file);
            formData.append('order', order);

            fetch("{{ route('pdf.reorder.upload') }}", {
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
            .catch(() => showModal("Something went wrong during reorder."));
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
