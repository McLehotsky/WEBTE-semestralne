<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-700 leading-tight flex items-center justify-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="#D97706" class="size-8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5V6.75a4.5 4.5 0 1 1 9 0v3.75M3.75 21.75h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H3.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
            </svg>
            {{__('dashboard.card.decrypt')}}
        </h2>
    </x-slot>

    <div class="py-12 bg-transparent min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-8 rounded-lg shadow-md border-2 border-dashed border-gray-300 text-center">
                <form id="decrypt-form" enctype="multipart/form-data">
                    @csrf

                    <input type="file" name="file" id="file" class="hidden" accept="application/pdf" required>

                    <div id="drop-area"
                        class="cursor-pointer p-8 border-2 border-dashed border-gray-400 rounded-lg bg-gray-50 hover:bg-gray-100 transition">
                        <p class="text-xl font-semibold text-gray-600 mb-4">Choose encrypted PDF</p>
                        <p class="text-gray-400">... or drop a file here</p>
                    </div>

                    <div id="file-name" class="mt-4 text-sm text-gray-600"></div>

                    <div class="relative mt-4">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M5 8V6a5 5 0 1110 0v2h1a1 1 0 011 1v9a1 1 0 01-1 1H4a1 1 0 01-1-1V9a1 1 0 011-1h1zm2-2a3 3 0 016 0v2H7V6z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input
                            type="password"
                            name="password"
                            id="password"
                            placeholder="Enter decryption password"
                            class="block w-full pl-12 pr-4 py-2 border border-gray-300 rounded-md focus:ring-amber-500 focus:border-amber-500"
                            required>
                    </div>

                    <button type="submit"
                        class="mt-6 bg-amber-600 hover:bg-amber-800 text-white font-bold py-2 px-6 rounded transition">
                        Decrypt PDF
                    </button>
                </form>

                <!-- Modal -->
                <div id="errorModal" class="fixed z-50 inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
                    <div class="bg-white rounded-lg shadow-lg max-w-md w-full p-6">
                        <h2 class="text-xl font-semibold mb-4">Error</h2>
                        <p id="errorMessage" class="text-gray-700 mb-4">Something went wrong.</p>
                        <div class="text-right">
                            <button id="closeModalBtn" class="px-4 py-2 bg-amber-600 text-white rounded hover:bg-amber-800">Close</button>
                        </div>
                    </div>
                </div>

                <div id="result" class="mt-6 hidden">
                    <div class="flex items-center bg-white border border-gray-300 rounded-md px-4 py-3 shadow-sm justify-center">
                        <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M16.707 5.293a1 1 0 010 1.414L8.414 15l-4.121-4.121a1 1 0 011.414-1.414L8.414 12.586l7.293-7.293a1 1 0 011.414 0z"
                                clip-rule="evenodd" />
                        </svg>
                        <span class="text-sm text-gray-800">
                            PDF has been decrypted.
                            <a id="download-link" href="#" download="decrypted.pdf" class="text-amber-600 font-medium underline ml-1" target="_blank">Download PDF</a>
                        </span>
                    </div>
                </div>
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
                showModal("Please upload exactly 1 encrypted PDF.");
            }
        });

        fileInput.addEventListener('change', () => {
            fileName.innerText = fileInput.files[0]?.name || "";
        });

        document.getElementById('decrypt-form').addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData();
            formData.append('file', fileInput.files[0]);
            formData.append('password', document.getElementById('password').value);

            const response = await fetch("{{ route('pdf.decrypt.upload') }}", {
                method: "POST",
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            });

            const data = await response.json();
            if (data.url) {
                const link = document.getElementById('download-link');
                link.href = data.url.replace(/\\/g, '');
                document.getElementById('result').classList.remove('hidden');
            } else {
                showModal("Decryption failed.");
            }
        });

        function showModal(message) {
            const modal = document.getElementById('errorModal');
            const errorText = document.getElementById('errorMessage');
            errorText.innerText = message;
            modal.classList.remove('hidden');
        }
    </script>
</x-app-layout>
