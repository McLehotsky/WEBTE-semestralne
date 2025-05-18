<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-700 leading-tight flex items-center justify-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="#D97706" class="size-8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m13.35-.622 1.757-1.757a4.5 4.5 0 0 0-6.364-6.364l-4.5 4.5a4.5 4.5 0 0 0 1.242 7.244" />
            </svg>
            {{__('dashboard.card.merge')}}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-100 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-8 rounded-lg shadow-md border-2 border-dashed border-gray-300 text-center">
                <form id="upload-form" action="{{ route('pdf.merge.upload') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <input type="file" id="file-selector" class="hidden" accept="application/pdf" multiple>

                    <input type="file" name="file1" id="file1" class="hidden" accept="application/pdf">
                    <input type="file" name="file2" id="file2" class="hidden" accept="application/pdf">

                    <div id="drop-area"
                        class="cursor-pointer p-8 border-2 border-dashed border-gray-400 rounded-lg bg-gray-50 hover:bg-gray-100 transition">
                        <p class="text-xl font-semibold text-gray-600 mb-4">Choose files</p>
                        <p class="text-gray-400">... or drop files here</p>
                    </div>

                    <div id="file-names" class="mt-4 text-sm text-gray-600"></div>

                    <button type="submit"
                        class="mt-6 bg-amber-600 hover:bg-amber-800 text-white font-bold py-2 px-6 rounded transition">
                        Merge PDF
                    </button>
                </form>

                <div id="result" class="mt-6 hidden">
                    <div class="flex items-center bg-white border border-gray-300 rounded-md px-4 py-3 shadow-sm">
                        <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414L8.414 15l-4.121-4.121a1 1 0 011.414-1.414L8.414 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-sm text-gray-800">
                            PDF bol úspešne zlúčený. 
                            <a id="download-link" href="#" class="text-amber-600 font-medium underline ml-1" target="_blank">Stiahnuť PDF</a>
                        </span>
                    </div>
                </div>

                <!-- Modal -->
                <div id="errorModal" class="fixed z-50 inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
                    <div class="bg-white rounded-lg shadow-lg max-w-md w-full p-6">
                        <h2 class="text-xl font-semibold mb-4">Chyba</h2>
                        <p id="errorMessage" class="text-gray-700 mb-4">Niečo sa pokazilo.</p>
                        <div class="text-right">
                            <button id="closeModalBtn" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">Zavrieť</button>
                        </div>
                    </div>
                </div>

                <p class="mt-6 text-xs text-gray-400">By using this function, you accept our <a href="#" class="underline">terms of service</a>.</p>
            </div>
        </div>
    </div>

    <script>
        const dropArea = document.getElementById('drop-area');
        const fileSelector = document.getElementById('file-selector');
        const file1 = document.getElementById('file1');
        const file2 = document.getElementById('file2');
        const fileNames = document.getElementById('file-names');

        dropArea.addEventListener('click', () => {
            fileSelector.click();
        });

        fileSelector.addEventListener('change', () => {
            assignFiles(fileSelector.files);
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
            assignFiles(e.dataTransfer.files);
        });

        function assignFiles(files) {
            if (files.length !== 2) {
                showModal("Vyber prosím presne 2 PDF súbory.");
                return;
            }
        
            const dt1 = new DataTransfer();
            dt1.items.add(files[0]);
            file1.files = dt1.files;
        
            const dt2 = new DataTransfer();
            dt2.items.add(files[1]);
            file2.files = dt2.files;
        
            fileNames.innerText = `${files[0].name}\n${files[1].name}`;
        }


    document.getElementById('upload-form').addEventListener('submit', async function(e) {
        e.preventDefault();
            
        const form = e.target;
        const formData = new FormData(form);
            
        const response = await fetch("{{ route('pdf.merge.upload') }}", {
            method: "POST",
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        });
    
        const data = await response.json();
    
        if (data.url) {
            const resultBox = document.getElementById('result');
            const link = document.getElementById('download-link');
        
            // Odstránenie spätných lomítok, keby náhodou boli
            const cleanUrl = data.url.replace(/\\/g, '');
        
            link.href = cleanUrl;
            resultBox.classList.remove('hidden');
        } else {
            showModal("Zlúčenie PDF zlyhalo.");
        }
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
