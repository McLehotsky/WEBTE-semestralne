<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Split PDF into Chunks
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-100 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-8 rounded-lg shadow-md border-2 border-dashed border-gray-300 text-center">
                <form id="split-form" enctype="multipart/form-data">
                    @csrf
                    <input type="file" name="file" id="file" class="hidden" accept="application/pdf" required>

                    <div id="drop-area"
                         class="cursor-pointer p-8 border-2 border-dashed border-gray-400 rounded-lg bg-gray-50 hover:bg-gray-100 transition">
                        <p class="text-xl font-semibold text-gray-600 mb-4">Choose a PDF</p>
                        <p class="text-gray-400">... or drop a file here</p>
                    </div>

                    <div id="file-name" class="mt-4 text-sm text-gray-600"></div>

                    <div class="mt-6">
                        <label for="chunk_size" class="block font-medium text-sm text-gray-700 mb-1">
                            Number of pages per chunk:
                        </label>
                        <input type="number" name="chunk_size" id="chunk_size"
                               class="border border-gray-300 rounded-md p-2 w-full" min="1" required>
                    </div>

                    <div id="preview-scroll-wrapper"
                         class="mt-6 max-h-[600px] overflow-y-auto border border-gray-300 rounded-md p-4 shadow-inner bg-white hidden">
                        <div id="preview-container" class="grid grid-cols-3 gap-4 justify-items-center"></div>
                    </div>

                    <div class="text-center mt-6">
                        <button type="button" id="split-btn"
                                class="bg-amber-600 hover:bg-amber-800 text-white font-bold py-2 px-6 rounded transition">
                            Split PDF
                        </button>
                    </div>

                    <div id="result" class="mt-6 hidden">
                        <div class="flex items-center bg-white border border-gray-300 rounded-md px-4 py-3 shadow-sm justify-center">
                            <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414L8.414 15l-4.121-4.121a1 1 0 011.414-1.414L8.414 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-sm text-gray-800">
                                PDF was successfully split.
                                <a id="download-link" href="#" download="split.pdf" class="text-amber-600 font-medium underline ml-1" target="_blank">Download ZIP</a>
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
        const dropArea = document.getElementById('drop-area');
        const fileInput = document.getElementById('file');
        const fileName = document.getElementById('file-name');
        const chunkInput = document.getElementById('chunk_size');
        const previewContainer = document.getElementById('preview-container');
        let globalPageWrappers = [];

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

        chunkInput.addEventListener('input', () => {
            const size = parseInt(chunkInput.value);
            if (!size || size <= 0) return;

            globalPageWrappers.forEach((el, i) => {
                const group = Math.floor(i / size);
                el.style.borderLeft = `6px solid hsl(${group * 45 % 360}, 70%, 60%)`;
                el.setAttribute('data-chunk', group);
            });
        });

        function loadPDF(file) {
            const reader = new FileReader();
            reader.onload = function () {
                const typedarray = new Uint8Array(reader.result);
                pdfjsLib.getDocument(typedarray).promise.then(function (pdf) {
                    previewContainer.innerHTML = '';
                    globalPageWrappers = [];
                    document.getElementById('preview-scroll-wrapper').classList.remove('hidden');

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
                            wrapper.className = 'relative border rounded shadow hover:shadow-lg p-1';
                            wrapper.setAttribute('data-index', i);
                            canvas.className = 'rounded w-full h-auto';
                            wrapper.appendChild(canvas);
                            previewContainer.appendChild(wrapper);

                            globalPageWrappers.push(wrapper);
                        });
                    }
                });
            };
            reader.readAsArrayBuffer(file);
        }

        document.getElementById('split-btn').addEventListener('click', () => {
            const file = fileInput.files[0];
            const chunkSize = chunkInput.value;

            if (!file || !chunkSize) {
                showModal("Please upload a file and specify chunk size.");
                return;
            }

            const formData = new FormData();
            formData.append('file', file);
            formData.append('chunk_size', chunkSize);

            fetch("{{ route('pdf.split.upload') }}", {
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
            .catch(() => showModal("Something went wrong during split."));
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