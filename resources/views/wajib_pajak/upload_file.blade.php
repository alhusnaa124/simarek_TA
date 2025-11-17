<!-- Modal -->
<div id="upload-modal" tabindex="-1" aria-hidden="true"
    class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto h-[calc(100%-1rem)] max-h-full bg-black bg-opacity-50">
    <div class="relative w-full max-w-md max-h-full m-auto">
        <!-- Modal content -->
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <!-- Header -->
            <div class="flex items-center justify-between p-4 border-b rounded-t dark:border-gray-600">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Upload File
                </h3>
                <button type="button"
                    class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center"
                    data-modal-hide="upload-modal">
                    <svg aria-hidden="true" class="w-5 h-5" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Form -->
            <form id="upload-form" action="{{ route('wajib_pajak.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="p-6">
                    <!-- Drag & Drop Zone -->
                    <label for="file-input" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">Select Excel File</label>

                    <div
                        class="flex flex-col items-center justify-center w-full h-40 border-2 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 dark:bg-gray-700 dark:border-gray-600 dark:hover:bg-gray-600 transition"
                        onclick="document.getElementById('file-input').click();">
                        <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v9m-5 0H5a1 1 0 0 0-1 1v4a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-4a1 1 0 0 0-1-1h-2M8 9l4-5 4 5m1 8h.01"/>
                        </svg>

                        <p class="text-sm text-gray-500 dark:text-gray-300">
                            Drag & drop a file here or <span class="text-blue-600 hover:underline">browse</span>
                        </p>
                        <p class="text-xs text-gray-400 mt-1">Supported formats: .xlsx, .xls, .csv</p>
                    </div>

                    <!-- Hidden File Input -->
                    <input type="file" name="excel_file" id="file-input" accept=".xlsx,.xls,.csv"
                        class="hidden" onchange="handleFileSelection()" required>

                    <!-- File Selected Display -->
                    <div id="selected-file-name"
                        class="mt-4 text-sm text-gray-700 dark:text-gray-300 hidden">
                        <div class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span></span>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="mt-6 flex justify-end gap-2">
                        <button type="button" data-modal-hide="upload-modal"
                            class="text-gray-700 bg-gray-200 hover:bg-gray-300 font-medium rounded-lg text-sm px-4 py-2">
                            Cancel
                        </button>
                        <button type="submit" id="modal-select-button"
                            class="text-white bg-blue-600 hover:bg-blue-700 font-medium rounded-lg text-sm px-4 py-2 disabled:opacity-50"
                            disabled>
                            Upload
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JS -->
<script>
    function handleFileSelection() {
        const fileInput = document.getElementById('file-input');
        const fileName = document.querySelector('#selected-file-name span');
        const fileNameContainer = document.getElementById('selected-file-name');
        const submitBtn = document.getElementById('modal-select-button');

        if (fileInput.files.length > 0) {
            fileName.textContent = fileInput.files[0].name;
            fileNameContainer.classList.remove('hidden');
            submitBtn.disabled = false;
        } else {
            fileNameContainer.classList.add('hidden');
            fileName.textContent = '';
            submitBtn.disabled = true;
        }
    }
</script>
