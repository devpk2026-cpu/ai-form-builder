<div class="max-w-5xl mx-auto p-6">

    <div class="mb-6">
        <h1 class="text-2xl font-bold">
            Review Imported Form
        </h1>

        <p class="text-gray-600 mt-1">
            Review and correct the detected fields before creating the form.
        </p>
    </div>

    <div class="bg-white rounded-xl shadow border p-6">

        {{-- FORM TITLE --}}
        <div class="mb-6">
            <label class="block font-medium mb-2">
                Form Title
            </label>

            <input
                type="text"
                wire:model="schema.title"
                class="w-full border rounded-lg px-3 py-2"
            />

            @error('schema.title')
                <p class="text-sm text-red-600 mt-1">
                    {{ $message }}
                </p>
            @enderror
        </div>

        {{-- DESCRIPTION --}}
        <div class="mb-8">
            <label class="block font-medium mb-2">
                Description
            </label>

            <textarea
                wire:model="schema.description"
                rows="3"
                class="w-full border rounded-lg px-3 py-2"
            ></textarea>
        </div>


        {{-- SECTIONS --}}
        @foreach ($schema['sections'] ?? [] as $sectionIndex => $section)

            <div class="border rounded-xl p-5 mb-6">

                <input
                    type="text"
                    wire:model="schema.sections.{{ $sectionIndex }}.title"
                    class="text-xl font-semibold border rounded-lg px-3 py-2 w-full"
                />

                <div class="mt-5 space-y-4">

                    @foreach ($section['fields'] ?? [] as $fieldIndex => $field)

                        <div class="border rounded-lg p-4">

                            <div class="grid md:grid-cols-3 gap-4">

                                {{-- LABEL --}}
                                <div>
                                    <label class="block text-sm font-medium mb-1">
                                        Label
                                    </label>

                                    <input
                                        type="text"
                                        wire:model="schema.sections.{{ $sectionIndex }}.fields.{{ $fieldIndex }}.label"
                                        class="w-full border rounded-lg px-3 py-2"
                                    />
                                </div>

                                {{-- TYPE --}}
                                <div>
                                    <label class="block text-sm font-medium mb-1">
                                        Field Type
                                    </label>

                                    <select
                                        wire:model="schema.sections.{{ $sectionIndex }}.fields.{{ $fieldIndex }}.type"
                                        class="w-full border rounded-lg px-3 py-2"
                                    >
                                        <option value="text">
                                            Text
                                        </option>

                                        <option value="textarea">
                                            Textarea
                                        </option>

                                        <option value="email">
                                            Email
                                        </option>

                                        <option value="number">
                                            Number
                                        </option>

                                        <option value="date">
                                            Date
                                        </option>

                                        <option value="dropdown">
                                            Dropdown
                                        </option>

                                        <option value="radio">
                                            Radio
                                        </option>

                                        <option value="checkbox">
                                            Checkbox
                                        </option>

                                        <option value="file">
                                            File
                                        </option>

                                        <option value="rating">
                                            Rating
                                        </option>
                                    </select>
                                </div>

                                {{-- REQUIRED --}}
                                <div class="flex items-center pt-6">

                                    <label class="flex items-center gap-2">

                                        <input
                                            type="checkbox"
                                            wire:model="schema.sections.{{ $sectionIndex }}.fields.{{ $fieldIndex }}.required"
                                        >

                                        Required
                                    </label>

                                </div>

                            </div>

                            <div class="mt-4 flex justify-between">

                                <div class="text-xs text-gray-500">
                                    Key:
                                    {{ $field['key'] ?? '-' }}
                                </div>

                                <button
                                    type="button"
                                    wire:click="removeField({{ $sectionIndex }}, {{ $fieldIndex }})"
                                    class="text-sm text-red-600"
                                >
                                    Remove
                                </button>

                            </div>

                        </div>

                    @endforeach

                </div>

            </div>

        @endforeach


        <div class="pt-5 border-t flex justify-end">

            <button
                type="button"
                wire:click="createForm"
                wire:loading.attr="disabled"
                wire:target="createForm"
                class="px-6 py-3 bg-black text-white rounded-lg disabled:opacity-50"
            >

                <span wire:loading.remove wire:target="createForm">
                    Create Form
                </span>

                <span wire:loading wire:target="createForm">
                    Creating...
                </span>

            </button>

        </div>

    </div>

</div>