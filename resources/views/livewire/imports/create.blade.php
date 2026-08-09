<div class="max-w-3xl mx-auto p-6">

    <div class="mb-6">
        <h1 class="text-2xl font-bold">
            Import Form
        </h1>

        <p class="text-gray-600 mt-1">
            Create a form from a Word or Excel file.
        </p>
    </div>

    @if (session('success'))
        <div class="mb-6 rounded-lg bg-green-50 border border-green-200
                    text-green-700 px-4 py-3">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-xl shadow border p-6">

        <form class="space-y-6">

            <div>
                <label class="block font-medium mb-2">
                    Upload File
                </label>

                <input type="file" wire:model="file" accept=".docx,.xlsx" class="w-full border rounded-lg px-3 py-2">

                <div wire:loading wire:target="file" class="mt-2 text-sm text-gray-500">
                    Uploading file...
                </div>

                @error('file')
                    <p class="mt-2 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div class="text-sm text-gray-500">
                Supported formats:
                <strong>.docx</strong> and
                <strong>.xlsx</strong>
            </div>

            <button type="button" wire:click="analyzeImport" wire:loading.attr="disabled" wire:target="analyzeImport"
                class="px-5 py-2.5 bg-black text-white rounded-lg disabled:opacity-50">
                <span wire:loading.remove wire:target="analyzeImport">
                    Upload & Analyze
                </span>

                <span wire:loading wire:target="analyzeImport">
                    Uploading...
                </span>
            </button>

        </form>

    </div>

</div>
