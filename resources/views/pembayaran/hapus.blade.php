<!-- Flowbite Modal -->
<div id="delete-modal" tabindex="-1"
    class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto inset-0 h-[calc(100%-1rem)] max-h-full bg-gray-900 bg-opacity-50 flex justify-center items-center">
    <div class="relative w-full max-w-md max-h-full">
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <button type="button"
                class="absolute top-3 right-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center"
                onclick="closeModal()">
                <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                        clip-rule="evenodd"></path>
                </svg>
            </button>
            <div class="p-6 text-center">

                <svg class="mx-auto mb-4 text-gray-400 w-12 h-12" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M5 7h14m-9 3v8m4-8v8M10 3h4a1 1 0 0 1 1 1v3H9V4a1 1 0 0 1 1-1ZM6 7h12v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7Z" />
                </svg>

                <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">Apakah anda yakin ingin menghapus
                    data ini?</h3>
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="text-white bg-red-600 hover:bg-red-800 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center mr-2">
                        Ya, Hapus
                    </button>
                    <button type="button" onclick="closeModal()"
                        class="text-gray-500 bg-white hover:bg-gray-100 border border-gray-200 rounded-lg text-sm font-medium px-5 py-2.5">Batal</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function openModal(id) {
        const form = document.getElementById('deleteForm');
        form.action = `/pembayaran/${id}/hapus`;
        document.getElementById('delete-modal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('delete-modal').classList.add('hidden');
    }
</script>
