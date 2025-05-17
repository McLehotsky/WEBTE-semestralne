<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Otočiť stránky PDF
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-100 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-8 rounded-lg shadow-md border-2 border-dashed border-gray-300 text-center">
                <form id="rotate-form" enctype="multipart/form-data">
                    @csrf

                    <input type="file" name="file" id="file" class="hidden" accept="application/pdf" required>

                    <div id="drop-area"
                         class="cursor-pointer p-8 border-2 border-dashed border-gray-400 rounded-lg bg-gray-50 hover:bg-gray-100 transition">
                        <p class="text-xl font-semibold text-gray-600 mb-4">Vyber PDF</p>
                        <p class="text-gray-400">... alebo sem presuň súbor</p>
                    </div>

                    <div id="file-name" class="mt-4 text-sm text-gray-600"></div>

                    <h3 class="text-lg font-semibold mt-8 mb-2 hidden" id="select-heading">Vyber stránky na otočenie:</h3>
                    <div id="preview-scroll-wrapper"
                         class="mt-8 max-h-[600px] overflow-y-auto border border-gray-300 rounded-md p-4 shadow-inner bg-white hidden">
                        <div id="preview-container"
                             class="grid grid-cols-3 gap-4 justify-items-center"></div>
                    </div>


                    <div class="text-center mt-6">
                        <button type="button" id="rotate-pages-btn"
                                class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-6 rounded transition hidden">
                            Otočiť vybrané stránky
                        </button>
                    </div>

                    <div id="result" class="mt-6 hidden">
                        <div class="flex items-center bg-white border border-gray-300 rounded-md px-4 py-3 shadow-sm">
                            <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414L8.414 15l-4.121-4.121a1 1 0 011.414-1.414L8.414 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-sm text-gray-800">
                                PDF bolo úspešne otočené. <a id="download-link" href="#" class="text-blue-600 underline font-medium ml-1" target="_blank">Stiahnuť PDF</a>
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
                </form>
            </div>
        </div>
    </div>

<script>
    const dropArea = document.getElementById('drop-area');
    const fileInput = document.getElementById('file');
    const fileName = document.getElementById('file-name');

    dropArea.addEventListener('click', () => fileInput.click());
    dropArea.addEventListener('dragover', (e) => { e.preventDefault(); dropArea.classList.add('bg-gray-200'); });
    dropArea.addEventListener('dragleave', () => dropArea.classList.remove('bg-gray-200'));
    dropArea.addEventListener('drop', (e) => {
        e.preventDefault(); dropArea.classList.remove('bg-gray-200');
        if (e.dataTransfer.files.length === 1) {
            fileInput.files = e.dataTransfer.files;
            fileName.innerText = e.dataTransfer.files[0].name;
            previewPDF(e.dataTransfer.files[0]);
        } else {
            showModal("Vyber prosím jeden PDF súbor.");
        }
    });

    fileInput.addEventListener('change', () => {
        const file = fileInput.files[0];
        fileName.innerText = file?.name || "";
        if (file) previewPDF(file);
    });

    function previewPDF(file) {
        const reader = new FileReader();
        reader.onload = function () {
            const typedarray = new Uint8Array(reader.result);

            pdfjsLib.getDocument(typedarray).promise.then(function (pdf) {
                const container = document.getElementById('preview-container');
                container.innerHTML = '';
                document.getElementById('select-heading').classList.remove('hidden');
                document.getElementById('preview-scroll-wrapper').classList.remove('hidden');
                document.getElementById('rotate-pages-btn').classList.remove('hidden');

                for (let i = 0; i < pdf.numPages; i++) {
                    pdf.getPage(i + 1).then(function (page) {
                        const scale = 0.5;
                        const viewport = page.getViewport({ scale });
                        const canvas = document.createElement('canvas');
                        const context = canvas.getContext('2d');
                        canvas.height = viewport.height;
                        canvas.width = viewport.width;
                        canvas.style.zIndex = '1';
                        canvas.style.position = 'relative';

                        page.render({ canvasContext: context, viewport });

                        const wrapper = document.createElement('div');
                        wrapper.className = 'relative group hover:scale-105 transition cursor-pointer border rounded shadow';
                        wrapper.style.width = '200px';
                        wrapper.style.overflow = 'visible';




                        const pageNumber = document.createElement('div');
                        pageNumber.textContent = i + 1;
                        pageNumber.className = 'absolute top-2 right-2 bg-white text-sm text-gray-600 px-2 py-1 rounded shadow';
                        wrapper.appendChild(pageNumber);

                        const checkbox = document.createElement('input');
                        checkbox.type = 'checkbox';
                        checkbox.value = i;
                        checkbox.name = 'pages[]';
                        checkbox.className = 'hidden';
                        checkbox.dataset.rotation = "0";
                        wrapper.appendChild(checkbox);

                        const rotateBtn = document.createElement('button');
                        rotateBtn.innerHTML = '↻0';
                        rotateBtn.className = 'absolute bottom-2 left-2 bg-purple-600 text-white rounded-full w-8 h-8 flex items-center justify-center shadow hover:bg-purple-700 text-xs hidden';
                        rotateBtn.style.zIndex = '10';
                        rotateBtn.style.position = 'absolute';
                        rotateBtn.style.pointerEvents = 'auto';
                        rotateBtn.style.transform = 'rotate(0deg)';
                        rotateBtn.title = "Kliknutím otočíte stránku o 90°";


                        rotateBtn.addEventListener('click', (e) => {
                            e.stopPropagation();
                            e.preventDefault();

                            let current = parseInt(checkbox.dataset.rotation || '0');
                            current = (current + 90) % 360;
                            checkbox.dataset.rotation = current;
                            rotateBtn.innerHTML = `↻${current}`;

                             updateRotateButtonVisibility();

                            // 🔁 preview otočenie
                            canvas.style.transform = `rotate(${current}deg)`;
                            canvas.style.transformOrigin = 'center center'; // 🔧 fix
                            canvas.style.display = 'block'; // 🔧 prevent inline overflow
                        });


                        wrapper.appendChild(rotateBtn);

                        canvas.className = 'rounded w-full h-auto';
                        wrapper.appendChild(canvas);


                        wrapper.addEventListener('click', (event) => {
                            checkbox.checked = !checkbox.checked;
                            wrapper.classList.toggle('ring-4');
                            wrapper.classList.toggle('ring-purple-400');
                            rotateBtn.classList.toggle('hidden', !checkbox.checked);
                            updateRotateButtonVisibility();

                        });


                        container.appendChild(wrapper);
                    });
                }
            });
        };
        reader.readAsArrayBuffer(file);
    }

    document.getElementById('rotate-pages-btn').addEventListener('click', () => {
        const file = fileInput.files[0];
        const selectedPages = [...document.querySelectorAll('input[name="pages[]"]:checked')]
            .map(cb => `${cb.value}:${cb.dataset.rotation}`)
            .join(',');

        if (!file || !selectedPages) {
            showModal("Vyber aspoň jednu stránku a nastav jej rotáciu.");
            return;
        }

        const formData = new FormData();
        formData.append('file', file);
        formData.append('rotations', selectedPages);

        fetch("{{ route('pdf.rotate') }}", {
            method: "POST",
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.url) {
                const resultBox = document.getElementById('result');
                const link = document.getElementById('download-link');
                link.href = data.url;
                resultBox.classList.remove('hidden');
            } else {
                showModal("Niečo sa pokazilo.");
            }
        });
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
    function updateRotateButtonVisibility() {
    const anyChecked = document.querySelectorAll('input[name="pages[]"]:checked').length > 0;
    const btn = document.getElementById('rotate-pages-btn');
    btn.classList.toggle('hidden', !anyChecked);
}

</script>

</x-app-layout>
