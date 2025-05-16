<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Delete Pages from PDF
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-100 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-8 rounded-lg shadow-md border-2 border-dashed border-gray-300 text-center">
                <form id="delete-form" enctype="multipart/form-data" >
                    @csrf

                    <input type="file" name="file" id="file" class="hidden" accept="application/pdf" required>

                    <div id="drop-area"
                         class="cursor-pointer p-8 border-2 border-dashed border-gray-400 rounded-lg bg-gray-50 hover:bg-gray-100 transition">
                        <p class="text-xl font-semibold text-gray-600 mb-4">Choose a PDF</p>
                        <p class="text-gray-400">... or drop a file here</p>
                    </div>

                    <div id="file-name" class="mt-4 text-sm text-gray-600"></div>

                    <h3 id="select-heading" class="text-lg font-semibold mt-8 mb-2 hidden">Select pages to delete:</h3>
                    <div id="preview-scroll-wrapper"
                         class="mt-8 max-h-[600px] overflow-y-auto border border-gray-300 rounded-md p-4 shadow-inner bg-white hidden">
                        <div id="preview-container"
                             class="grid grid-cols-3 gap-4 justify-items-center"></div>
                    </div>
                    <div class="text-center mt-6">
                        <button type="button" id="delete-pages-btn"
                                class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-6 rounded transition hidden">
                            Delete Selected Pages
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const dropArea = document.getElementById('drop-area');
        const fileInput = document.getElementById('file');
        const fileName = document.getElementById('file-name');

        dropArea.addEventListener('click', () => {
            fileInput.click();
        });

        dropArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropArea.classList.add('bg-gray-200');
        });

        dropArea.addEventListener('dragleave', () => {
            dropArea.classList.remove('bg-gray-200');
        });

        dropArea.addEventListener('drop', (e) => {
            e.preventDefault();
            dropArea.classList.remove('bg-gray-200');

            if (e.dataTransfer.files.length === 1) {
                fileInput.files = e.dataTransfer.files;
                fileName.innerText = e.dataTransfer.files[0].name;
            } else {
                alert("Please select exactly 1 PDF file.");
            }
        });

        fileInput.addEventListener('change', () => {
        const file = fileInput.files[0];
        fileName.innerText = file?.name || "";

        if (!file) return;

        const reader = new FileReader();
        reader.onload = function () {
            const typedarray = new Uint8Array(reader.result);

            pdfjsLib.getDocument(typedarray).promise.then(function (pdf) {
                const previewContainer = document.getElementById('preview-container');
                previewContainer.innerHTML = '';

                document.getElementById('select-heading').classList.remove('hidden');
                document.getElementById('preview-scroll-wrapper').classList.remove('hidden');
                document.getElementById('delete-pages-btn').classList.remove('hidden');

                for (let i = 0; i < pdf.numPages; i++) {
                    pdf.getPage(i + 1).then(function (page) {
                        const scale = 0.5;
                        const viewport = page.getViewport({ scale });
                        const canvas = document.createElement('canvas');
                        const context = canvas.getContext('2d');
                        canvas.height = viewport.height;
                        canvas.width = viewport.width;

                        page.render({ canvasContext: context, viewport });

                        // === 👇 Všetko ako predtým (wrapper, trash ikonka atď.) ===
                        const wrapper = document.createElement('div');
                        wrapper.className = 'relative group transition-transform transform hover:scale-105 cursor-pointer border rounded shadow hover:shadow-lg';
                        wrapper.style.width = '200px';

                        const pageNumber = document.createElement('div');
                        pageNumber.textContent = i + 1;
                        pageNumber.className = 'absolute top-2 right-2 bg-white text-sm text-gray-600 px-2 py-1 rounded shadow';
                        wrapper.appendChild(pageNumber);

                        const trash = document.createElement('div');
                        trash.innerHTML = `
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 p-2 bg-red-500 text-white rounded-full shadow-lg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                              <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7h6m2 0a1 1 0 001-1V5a1 1 0 00-1-1h-2.5a1 1 0 01-.707-.293l-.5-.5A1 1 0 0012.5 3h-1a1 1 0 00-.707.293l-.5.5A1 1 0 019.5 4H7a1 1 0 00-1 1v1a1 1 0 001 1h10z" />
                            </svg>`;
                        trash.className = 'absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition';
                        wrapper.appendChild(trash);

                        canvas.className = 'rounded w-full h-auto';
                        wrapper.appendChild(canvas);

                        const checkbox = document.createElement('input');
                        checkbox.type = 'checkbox';
                        checkbox.value = i;
                        checkbox.name = 'pages[]';
                        checkbox.style.display = 'none';
                        wrapper.appendChild(checkbox);

                        wrapper.addEventListener('click', () => {
                            checkbox.checked = !checkbox.checked;
                            wrapper.classList.toggle('ring-4');
                            wrapper.classList.toggle('ring-red-400');
                        });

                        previewContainer.appendChild(wrapper);
                    });
                }
                document.getElementById('delete-pages-btn').classList.remove('hidden');
            });
        };

        reader.readAsArrayBuffer(file);
    });

    document.getElementById('delete-pages-btn').addEventListener('click', () => {
        const file = fileInput.files[0];
        const selected = [...document.querySelectorAll('input[name="pages[]"]:checked')]
            .map(cb => cb.value)
            .join(',');

        if (!selected) {
            alert("Please select at least one page to delete.");
            return;
        }

        const formData = new FormData();
        formData.append('file', file);
        formData.append('pages', selected);

        fetch("{{ route('pdf.delete.upload') }}", {
            method: "POST",
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.url) {
                // Môžeš aj otvoriť nové okno:
                window.open(data.url, '_blank');

                // Alebo zobraziť odkaz:
                const resultBox = document.createElement('div');
                resultBox.innerHTML = `<a href="${data.url}" class="text-blue-600 underline font-medium mt-4 block" target="_blank">📥 Download cleaned PDF</a>`;
                document.getElementById('preview-container').appendChild(resultBox);
            } else {
                alert("Something went wrong.");
            }
        });
    });
    </script>
</x-app-layout>
