<div class="p-6">

    <div class="flex items-center justify-between mb-6">

        <div>
            <a
                href="{{ route('forms.index') }}"
                class="text-sm text-gray-500"
            >
                ← Back to Forms
            </a>

            <h1 class="text-2xl font-bold mt-2">
                {{ $form ? 'Edit Form' : 'Create Form' }}
            </h1>
        </div>

        <button
            wire:click="save"
            wire:loading.attr="disabled"
            class="px-5 py-2 bg-black text-white rounded-lg"
        >
            <span wire:loading.remove>Save Form</span>
            <span wire:loading>Saving...</span>
        </button>

    </div>

    @if (session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-12 gap-6">

        {{-- Left: Builder --}}
        <div class="col-span-8 space-y-6">

            <div class="bg-white rounded-lg shadow p-6">

                <label class="block font-medium mb-2">
                    Form Title
                </label>

                <input
                    type="text"
                    wire:model="title"
                    class="w-full border rounded-lg px-3 py-2"
                    placeholder="Enter form title"
                >

                @error('title')
                    <div class="text-red-600 text-sm mt-1">
                        {{ $message }}
                    </div>
                @enderror

                <label class="block font-medium mt-4 mb-2">
                    Description
                </label>

                <textarea
                    wire:model="description"
                    class="w-full border rounded-lg px-3 py-2"
                    rows="3"
                    placeholder="Enter form description"
                ></textarea>

            </div>

            @foreach ($sections as $sectionIndex => $section)

                <div
                    wire:key="section-{{ $section['id'] }}"
                    class="bg-white rounded-lg shadow p-6"
                >

                    <div class="flex items-center justify-between mb-4">

                        <input
                            type="text"
                            wire:model="sections.{{ $sectionIndex }}.title"
                            class="text-lg font-semibold border-0 border-b"
                        >

                        <button
                            wire:click="removeSection({{ $sectionIndex }})"
                            class="text-red-600"
                        >
                            Remove Section
                        </button>

                    </div>

                    <div class="space-y-4">

                        @forelse (
                            $section['fields']
                            as $fieldIndex => $field
                        )

                            <div
                                wire:key="field-{{ $field['id'] }}"
                                class="border rounded-lg p-4"
                            >

                                <div class="flex justify-between">

                                    <div>
                                        <span class="text-xs text-gray-500">
                                            {{ strtoupper($field['type']) }}
                                        </span>

                                        <div class="font-medium">
                                            {{ $field['label'] }}
                                        </div>
                                    </div>

                                    <button
                                        wire:click="removeField(
                                            {{ $sectionIndex }},
                                            {{ $fieldIndex }}
                                        )"
                                        class="text-red-600 text-sm"
                                    >
                                        Remove
                                    </button>

                                </div>

                                <div class="grid grid-cols-2 gap-4 mt-4">

                                    <div>
                                        <label class="text-sm">
                                            Label
                                        </label>

                                        <input
                                            type="text"
                                            wire:model="sections.{{ $sectionIndex }}.fields.{{ $fieldIndex }}.label"
                                            class="w-full border rounded px-3 py-2"
                                        >
                                    </div>

                                    <div>
                                        <label class="text-sm">
                                            Field Key
                                        </label>

                                        <input
                                            type="text"
                                            wire:model="sections.{{ $sectionIndex }}.fields.{{ $fieldIndex }}.key"
                                            class="w-full border rounded px-3 py-2"
                                        >
                                    </div>

                                </div>

                                <div class="mt-4">

                                    <label class="inline-flex items-center">

                                        <input
                                            type="checkbox"
                                            wire:model="sections.{{ $sectionIndex }}.fields.{{ $fieldIndex }}.required"
                                            class="mr-2"
                                        >

                                        Required field

                                    </label>

                                </div>

                            </div>

                        @empty

                            <div class="border border-dashed rounded-lg p-6 text-center text-gray-500">
                                No fields in this section.
                            </div>

                        @endforelse

                    </div>

                    {{-- Add Field --}}
                    <div class="flex flex-wrap gap-2 mt-6">

                        @foreach ([
                            'text' => 'Text',
                            'textarea' => 'Textarea',
                            'number' => 'Number',
                            'email' => 'Email',
                            'phone' => 'Phone',
                            'date' => 'Date',
                            'dropdown' => 'Dropdown',
                            'radio' => 'Radio',
                            'checkbox' => 'Checkbox',
                            'file' => 'File',
                            'rating' => 'Rating',
                            'section' => 'Section Heading',
                        ] as $type => $label)

                            <button
                                wire:click="addField(
                                    {{ $sectionIndex }},
                                    '{{ $type }}'
                                )"
                                class="px-3 py-2 text-sm border rounded-lg hover:bg-gray-50"
                            >
                                + {{ $label }}
                            </button>

                        @endforeach

                    </div>

                </div>

            @endforeach

            <button
                wire:click="addSection"
                class="w-full border-2 border-dashed rounded-lg p-4 text-gray-600"
            >
                + Add Section
            </button>

        </div>

        {{-- Right: Preview --}}
        <div class="col-span-4">

            <div class="bg-white rounded-lg shadow p-6 sticky top-6">

                <h2 class="font-semibold text-lg mb-4">
                    Form Preview
                </h2>

                <h3 class="text-xl font-bold">
                    {{ $title ?: 'Untitled Form' }}
                </h3>

                @if ($description)
                    <p class="text-gray-600 mt-2">
                        {{ $description }}
                    </p>
                @endif

                @foreach ($sections as $section)

                    <div class="mt-6">

                        <h4 class="font-semibold">
                            {{ $section['title'] }}
                        </h4>

                        @foreach ($section['fields'] as $field)

                            <div class="mt-4">

                                <label class="block text-sm font-medium mb-1">
                                    {{ $field['label'] }}

                                    @if ($field['required'])
                                        <span class="text-red-500">*</span>
                                    @endif
                                </label>

                                @if ($field['type'] === 'textarea')

                                    <textarea
                                        class="w-full border rounded px-3 py-2"
                                    ></textarea>

                                @elseif ($field['type'] === 'dropdown')

                                    <select class="w-full border rounded px-3 py-2">
                                        <option>
                                            Select an option
                                        </option>
                                    </select>

                                @elseif ($field['type'] === 'radio')

                                    <div class="text-sm text-gray-500">
                                        Radio options
                                    </div>

                                @elseif ($field['type'] === 'checkbox')

                                    <div class="text-sm text-gray-500">
                                        Checkbox options
                                    </div>

                                @elseif ($field['type'] === 'file')

                                    <input
                                        type="file"
                                        class="w-full"
                                    >

                                @elseif ($field['type'] === 'rating')

                                    <div class="text-yellow-500">
                                        ★ ★ ★ ★ ★
                                    </div>

                                @elseif ($field['type'] === 'section')

                                    <div class="font-semibold">
                                        {{ $field['label'] }}
                                    </div>

                                @else

                                    <input
                                        type="{{ $field['type'] === 'number'
                                            ? 'number'
                                            : ($field['type'] === 'date'
                                                ? 'date'
                                                : 'text') }}"
                                        class="w-full border rounded px-3 py-2"
                                    >

                                @endif

                            </div>

                        @endforeach

                    </div>

                @endforeach

            </div>

        </div>

    </div>

</div>