<div id="errorModal" class="fixed z-50 inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
    <div class="bg-white rounded-lg shadow-lg max-w-md w-full p-6">
        <h2 class="text-xl font-semibold mb-4">Error</h2>
        <p id="errorMessage" class="text-gray-700 mb-4">Something went wrong.</p>
        <div class="text-right">
            <button id="closeModalBtn" class="px-4 py-2 bg-amber-600 text-white rounded hover:bg-amber-800">Close</button>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('closeModalBtn')?.addEventListener('click', () => {
            document.getElementById('errorModal')?.classList.add('hidden');
        });
    });

    function showModal(message) {
        const modal = document.getElementById('errorModal');
        const errorText = document.getElementById('errorMessage');
        if (modal && errorText) {
            errorText.innerText = message;
            modal.classList.remove('hidden');
        }
    }
</script>