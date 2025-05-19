<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-700 leading-tight flex items-center justify-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="#D97706" class="size-8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
            </svg>
            {{__('dashboard.card.extract-text')}}
        </h2>
    </x-slot>

    <div class="py-12 bg-transparent min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-8 rounded-lg shadow-md border-2 border-dashed border-gray-300 text-center">
                <form id="extract-text-form" enctype="multipart/form-data">
                    @csrf
                    <input type="file" name="file" id="file" class="hidden" accept="application/pdf" required>

                    <div id="drop-area"
                         class="cursor-pointer p-8 border-2 border-dashed border-gray-400 rounded-lg bg-gray-50 hover:bg-gray-100 transition">
                        <p class="text-xl font-semibold text-gray-600 mb-4">Choose a PDF</p>
                        <p class="text-gray-400">... or drop a file here</p>
                    </div>

                    <div id="file-name" class="mt-4 text-sm text-gray-600"></div>

                    <div class="text-center mt-6">
                        <button type="button" id="extract-btn"
                                class="bg-amber-600 hover:bg-amber-800 text-white font-bold py-2 px-6 rounded transition">
                            Extract Text
                        </button>
                    </div>

                    <div id="result" class="mt-6 hidden">
                        <textarea id="preview-text"
                                  class="w-full h-64 border border-gray-300 rounded-md p-3 text-left text-sm font-mono bg-gray-50 mb-4"
                                  readonly></textarea>

                        <div class="flex items-center bg-white border border-gray-300 rounded-md px-4 py-3 shadow-sm justify-center">
                            <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414L8.414 15l-4.121-4.121a1 1 0 011.414-1.414L8.414 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-sm text-gray-800">
                                Text was successfully extracted.
                                <a id="download-link" href="#" download="text_extracted.txt" class="text-amber-600 font-medium underline ml-1">Download TXT</a>
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
            } else {
                showModal("Please select exactly 1 PDF file.");
            }
        });

        fileInput.addEventListener('change', () => {
            const file = fileInput.files[0];
            if (file) {
                fileName.innerText = file.name;
            }
        });

        document.getElementById('extract-btn').addEventListener('click', () => {
            const file = fileInput.files[0];
            if (!file) {
                showModal("Please upload a PDF file.");
                return;
            }

            const formData = new FormData();
            formData.append('file', file);

            fetch("{{ route('pdf.extract-text.upload') }}", {
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
                    const textarea = document.getElementById('preview-text');

                    link.href = data.url.replace(/\\/g, '');
                    document.getElementById('result').classList.remove('hidden');

                    fetch(data.url.replace(/\\/g, ''))
                        .then(r => r.text())
                        .then(text => textarea.value = text)
                        .catch(() => textarea.value = '[Failed to load preview]');
                } else {
                    showModal("Something went wrong.");
                }
            })
            .catch(() => showModal("Something went wrong during extraction."));
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
