<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-700 leading-tight flex items-center justify-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="#D97706" class="size-8">
                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
            </svg>
            {{__('dashboard.card.delete')}}
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
                        <p class="text-xl font-semibold text-gray-600 mb-4">{{__('drop-area.choosePDF')}}</p>
                        <p class="text-gray-400">{{__('drop-area.dragPDF')}}</p>
                    </div>

                    <div id="file-name" class="mt-4 text-sm text-gray-600"></div>

                    <h3 id="select-heading" class="text-lg font-semibold mt-8 mb-2 hidden">{{__('delete-page.select-pages')}}</h3>
                    <div id="preview-scroll-wrapper"
                         class="mt-8 max-h-[600px] overflow-y-auto border border-gray-300 rounded-md p-4 shadow-inner bg-white hidden">
                        <div id="preview-container"
                             class="grid grid-cols-3 gap-4 justify-items-center"></div>
                    </div>
                    <div class="text-center mt-6">
                        <button type="button" id="delete-pages-btn"
                                class="bg-amber-600 hover:bg-amber-800 text-white font-bold py-2 px-6 rounded transition hidden">
                            {{__('button.delete')}}
                        </button>
                    </div>

                    <div id="result" class="mt-6 hidden">
                        <div class="flex items-center bg-white border border-gray-300 rounded-md px-4 py-3 shadow-sm justify-center">
                            <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414L8.414 15l-4.121-4.121a1 1 0 011.414-1.414L8.414 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-sm text-gray-800">
                                {{__('delete-page.deleted')}}
                                <a id="download-link" href="#" download="deleted.pdf" class="text-amber-600 font-medium underline ml-1">{{__('downloadPDF')}}</a>
                            </span>
                        </div>
                    </div>

                    <!-- Modal -->
                    <div id="errorModal" class="fixed z-50 inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
                        <div class="bg-white rounded-lg shadow-lg max-w-md w-full p-6">
                            <h2 class="text-xl font-semibold mb-4">{{__('error-modal.title')}}</h2>
                            <p id="errorMessage" class="text-gray-700 mb-4">{{__('error-modal.subtitle.vague')}}</p>
                            <div class="text-right">
                                <button id="closeModalBtn" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">{{__('error.modal.close')}}</button>
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
                showModal("{{__('error-modal.one-file')}}");
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

                        const wrapper = document.createElement('div');
                        wrapper.className = 'relative group transition-transform transform hover:scale-105 cursor-pointer border rounded shadow hover:shadow-lg';
                        wrapper.style.width = '200px';

                        const pageNumber = document.createElement('div');
                        pageNumber.textContent = i + 1;
                        pageNumber.className = 'absolute top-2 right-2 bg-white text-sm text-gray-600 px-2 py-1 rounded shadow';
                        wrapper.appendChild(pageNumber);

                        const trash = document.createElement('div');
                        trash.innerHTML = `
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 p-2 bg-amber-600 text-white rounded-full shadow-lg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                              <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
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
                            wrapper.classList.toggle('ring-amber-400');
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
            showModal("{{__('error-modal.delete.select-pages')}}");
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
                const link = document.getElementById('download-link');
                link.href = data.url.replace(/\\/g, '');
                document.getElementById('result').classList.remove('hidden');
            } else {
                showModal("{{__('error-modal.subtitle.vague')}}");
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
    </script>
</x-app-layout>
