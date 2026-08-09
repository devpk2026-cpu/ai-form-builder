<div
    class="p-6"
    x-data="{
        draggedSection: null,
        draggedField: null,
        draggedFieldIndex: null,

        startDrag(sectionIndex, fieldIndex) {
            this.draggedSection = sectionIndex;
            this.draggedField = fieldIndex;
        },

        dropField(sectionIndex, fieldIndex) {
            if (this.draggedSection === null) {
                return;
            }

            $wire.moveField(
                this.draggedSection,
                this.draggedField,
                sectionIndex,
                fieldIndex
            );

            this.draggedSection = null;
            this.draggedField = null;
        }
    }"
>
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">

        <div>
            <a
                href="{{ route('forms.index') }}"
                class="text-sm text-gray-500"
            >
                ← Back to Forms
            </a>

            <div class="flex items-center gap-3 mt-2">

                <h1 class="text-2xl font-bold">
                    {{ $form ? 'Edit Form' : 'Create Form' }}
                </h1>

                @if ($isDirty)
                    <span class="text-xs text-orange-600">
                        Unsaved changes
                    </span>
                @else
                    <span class="text-xs text-green-600">
                        Saved
                    </span>
                @endif

            </div>
        </div>

        <div class="flex items-center gap-2">

            {{-- Undo --}}
            <button
                wire:click="undo"
                @disabled($historyIndex <= 0)
                class="px-3 py-2 border rounded-lg disabled:opacity-40"
                title="Undo"
            >
                ↶
            </button>

            {{-- Redo --}}
            <button
                wire:click="redo"
                @disabled($historyIndex >= count($history) - 1)
                class="px-3 py-2 border rounded-lg disabled:opacity-40"
                title="Redo"
            >
                ↷
            </button>

            <button
                wire:click="save"
                wire:loading.attr="disabled"
                class="px-5 py-2 bg-black text-white rounded-lg"
            >
                <span wire:loading.remove>
                    Save Form
                </span>

                <span wire:loading>
                    Saving...
                </span>
            </button>

        </div>

    </div>

    @if (session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-12 gap-5">

        {{-- ========================================================= --}}
        {{-- LEFT: BUILDER CANVAS --}}
        {{-- ========================================================= --}}

        <div class="col-span-7">

            {{-- Form settings --}}
            <div class="bg-white rounded-xl shadow-sm border p-5 mb-5">

                <h2 class="font-semibold text-lg mb-4">
                    Form Settings
                </h2>

                <div class="grid grid-cols-2 gap-4">

                    <div class="col-span-2">

                        <label class="block text-sm font-medium mb-1">
                            Form Title
                        </label>

                        <input
                            type="text"
                            wire:model.live.debounce.500ms="title"
                            class="w-full border rounded-lg px-3 py-2"
                            placeholder="Enter form title"
                        >

                    </div>

                    <div class="col-span-2">

                        <label class="block text-sm font-medium mb-1">
                            Description
                        </label>

                        <textarea
                            wire:model.live.debounce.500ms="description"
                            rows="3"
                            class="w-full border rounded-lg px-3 py-2"
                            placeholder="Describe this form"
                        ></textarea>

                    </div>

                    <div>

                        <label class="block text-sm font-medium mb-1">
                            Status
                        </label>

                        <select
                            wire:model.live="status"
                            class="w-full border rounded-lg px-3 py-2"
                        >
                            <option value="draft">
                                Draft
                            </option>

                            <option value="published">
                                Published
                            </option>
                        </select>

                    </div>

                </div>

            </div>

            {{-- Sections --}}
            @foreach ($sections as $sectionIndex => $section)

                <div
                    wire:key="section-{{ $section['id'] }}"
                    class="bg-white rounded-xl shadow-sm border p-5 mb-5"
                >

                    <div class="flex items-center justify-between mb-5">

                        <input
                            type="text"
                            wire:model.live.debounce.500ms="sections.{{ $sectionIndex }}.title"
                            class="text-lg font-semibold border-0 border-b focus:ring-0"
                        >

                        <button
                            wire:click="removeSection({{ $sectionIndex }})"
                            class="text-sm text-red-600"
                        >
                            Remove section
                        </button>

                    </div>

                    {{-- Fields --}}
                    <div class="space-y-3">

                        @forelse ($section['fields'] as $fieldIndex => $field)

                            <div
                                wire:key="field-{{ $field['id'] }}"
                                draggable="true"

                                @dragstart="
                                    startDrag(
                                        {{ $sectionIndex }},
                                        {{ $fieldIndex }}
                                    )
                                "

                                @dragover.prevent

                                @drop="
                                    dropField(
                                        {{ $sectionIndex }},
                                        {{ $fieldIndex }}
                                    )
                                "

                                wire:click="selectField('{{ $field['id'] }}')"

                                class="
                                    border rounded-xl p-4 cursor-pointer
                                    transition
                                    {{ $selectedFieldId === $field['id']
                                        ? 'border-black ring-2 ring-gray-200'
                                        : 'border-gray-200' }}
                                "
                            >

                                <div class="flex items-start gap-3">

                                    {{-- Drag handle --}}
                                    <div class="text-gray-400 cursor-grab pt-1">
                                        ⋮⋮
                                    </div>

                                    <div class="flex-1">

                                        <div class="flex items-center justify-between">

                                            <div>

                                                <span class="text-xs text-gray-400 uppercase">
                                                    {{ $field['type'] }}
                                                </span>

                                                <div class="font-medium mt-1">
                                                    {{ $field['label'] }}
                                                </div>

                                            </div>

                                            <div class="flex items-center gap-3">

                                                @if ($field['required'])
                                                    <span class="text-xs text-red-500">
                                                        Required
                                                    </span>
                                                @endif

                                                <button
                                                    wire:click.stop="
                                                        removeField(
                                                            {{ $sectionIndex }},
                                                            {{ $fieldIndex }}
                                                        )
                                                    "
                                                    class="text-red-500 text-sm"
                                                >
                                                    Delete
                                                </button>

                                            </div>

                                        </div>

                                        <div class="text-xs text-gray-400 mt-2">
                                            {{ $field['key'] }}
                                        </div>

                                    </div>

                                </div>

                            </div>

                        @empty

                            <div class="border-2 border-dashed rounded-xl p-8 text-center text-gray-400">
                                Drag fields here or add one below.
                            </div>

                        @endforelse

                    </div>

                    {{-- Add Field --}}
                    <div class="mt-5">

                        <div class="text-xs font-semibold uppercase text-gray-400 mb-2">
                            Add Field
                        </div>

                        <div class="flex flex-wrap gap-2">

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
                                    class="px-3 py-2 text-xs border rounded-lg hover:bg-gray-50"
                                >
                                    + {{ $label }}
                                </button>

                            @endforeach

                        </div>

                    </div>

                </div>

            @endforeach

            <button
                wire:click="addSection"
                class="w-full border-2 border-dashed rounded-xl p-4 text-gray-500 hover:bg-white"
            >
                + Add Section
            </button>

        </div>


        {{-- ========================================================= --}}
        {{-- MIDDLE: LIVE PREVIEW --}}
        {{-- ========================================================= --}}

        <div class="col-span-3">

            <div class="bg-white rounded-xl shadow-sm border p-5 sticky top-5">

                <div class="flex items-center justify-between mb-5">

                    <h2 class="font-semibold">
                        Live Preview
                    </h2>

                    <span class="text-xs text-gray-400">
                        Preview
                    </span>

                </div>

                <h3 class="text-xl font-bold">
                    {{ $title ?: 'Untitled Form' }}
                </h3>

                @if ($description)

                    <p class="text-sm text-gray-500 mt-2">
                        {{ $description }}
                    </p>

                @endif

                <div class="mt-6 space-y-6">

                    @foreach ($sections as $section)

                        <div>

                            <h4 class="font-semibold">
                                {{ $section['title'] }}
                            </h4>

                            <div class="space-y-4 mt-4">

                                @foreach ($section['fields'] as $field)

                                    <div>

                                        @if ($field['type'] === 'section')

                                            <div class="font-semibold">
                                                {{ $field['label'] }}
                                            </div>

                                        @else

                                            <label class="block text-sm font-medium mb-1">

                                                {{ $field['label'] }}

                                                @if ($field['required'])
                                                    <span class="text-red-500">*</span>
                                                @endif

                                            </label>

                                            @if ($field['type'] === 'textarea')

                                                <textarea
                                                    class="w-full border rounded-lg px-3 py-2"
                                                    placeholder="{{ $field['placeholder'] }}"
                                                ></textarea>

                                            @elseif ($field['type'] === 'dropdown')

                                                <select class="w-full border rounded-lg px-3 py-2">

                                                    <option>
                                                        Select an option
                                                    </option>

                                                    @foreach ($field['options'] ?? [] as $option)
                                                        <option value="{{ $option['value'] }}">
                                                            {{ $option['label'] }}
                                                        </option>
                                                    @endforeach

                                                </select>

                                            @elseif ($field['type'] === 'radio')

                                                <div class="space-y-2">

                                                    @foreach ($field['options'] ?? [] as $option)

                                                        <label class="flex items-center gap-2">

                                                            <input
                                                                type="radio"
                                                                name="preview_{{ $field['id'] }}"
                                                            >

                                                            {{ $option['label'] }}

                                                        </label>

                                                    @endforeach

                                                </div>

                                            @elseif ($field['type'] === 'checkbox')

                                                <div class="space-y-2">

                                                    @foreach ($field['options'] ?? [] as $option)

                                                        <label class="flex items-center gap-2">

                                                            <input type="checkbox">

                                                            {{ $option['label'] }}

                                                        </label>

                                                    @endforeach

                                                </div>

                                            @elseif ($field['type'] === 'file')

                                                <input
                                                    type="file"
                                                    class="w-full text-sm"
                                                >

                                            @elseif ($field['type'] === 'rating')

                                                <div class="flex gap-1 text-xl">

                                                    @for (
                                                        $rating = 1;
                                                        $rating <= ($field['validation']['max'] ?? 5);
                                                        $rating++
                                                    )

                                                        <span>
                                                            ★
                                                        </span>

                                                    @endfor

                                                </div>

                                            @else

                                                <input
                                                    type="{{ match ($field['type']) {
                                                        'number' => 'number',
                                                        'date' => 'date',
                                                        'email' => 'email',
                                                        'phone' => 'tel',
                                                        default => 'text'
                                                    } }}"

                                                    value="{{ $field['default'] ?? '' }}"

                                                    placeholder="{{ $field['placeholder'] ?? '' }}"

                                                    class="w-full border rounded-lg px-3 py-2"
                                                >

                                            @endif

                                            @if (! empty($field['help_text']))

                                                <p class="text-xs text-gray-500 mt-1">
                                                    {{ $field['help_text'] }}
                                                </p>

                                            @endif

                                        @endif

                                    </div>

                                @endforeach

                            </div>

                        </div>

                    @endforeach

                </div>

                <button
                    class="w-full mt-6 bg-black text-white rounded-lg py-2"
                >
                    Submit
                </button>

            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- RIGHT: CONFIGURATION --}}
        {{-- ========================================================= --}}

        <div class="col-span-2">

            <div class="bg-white rounded-xl shadow-sm border p-5 sticky top-5">

                <h2 class="font-semibold mb-5">
                    Field Settings
                </h2>

                @if ($selectedFieldId)

                    @foreach ($sections as $sectionIndex => $section)

                        @foreach ($section['fields'] as $fieldIndex => $field)

                            @if ($field['id'] === $selectedFieldId)

                                {{-- Basic --}}
                                <div class="space-y-4">

                                    <div>

                                        <label class="block text-xs font-medium mb-1">
                                            Type
                                        </label>

                                        <div class="px-3 py-2 bg-gray-100 rounded-lg text-sm">
                                            {{ ucfirst($field['type']) }}
                                        </div>

                                    </div>

                                    <div>

                                        <label class="block text-xs font-medium mb-1">
                                            Label
                                        </label>

                                        <input
                                            type="text"
                                            wire:model.live.debounce.500ms="sections.{{ $sectionIndex }}.fields.{{ $fieldIndex }}.label"
                                            class="w-full border rounded-lg px-3 py-2 text-sm"
                                        >

                                    </div>

                                    <div>

                                        <label class="block text-xs font-medium mb-1">
                                            Field Key
                                        </label>

                                        <input
                                            type="text"
                                            wire:model.live.debounce.500ms="sections.{{ $sectionIndex }}.fields.{{ $fieldIndex }}.key"
                                            class="w-full border rounded-lg px-3 py-2 text-sm"
                                        >

                                    </div>

                                    <div>

                                        <label class="block text-xs font-medium mb-1">
                                            Placeholder
                                        </label>

                                        <input
                                            type="text"
                                            wire:model.live.debounce.500ms="sections.{{ $sectionIndex }}.fields.{{ $fieldIndex }}.placeholder"
                                            class="w-full border rounded-lg px-3 py-2 text-sm"
                                        >

                                    </div>

                                    <div>

                                        <label class="block text-xs font-medium mb-1">
                                            Help Text
                                        </label>

                                        <textarea
                                            wire:model.live.debounce.500ms="sections.{{ $sectionIndex }}.fields.{{ $fieldIndex }}.help_text"
                                            rows="2"
                                            class="w-full border rounded-lg px-3 py-2 text-sm"
                                        ></textarea>

                                    </div>

                                    <div>

                                        <label class="block text-xs font-medium mb-1">
                                            Default Value
                                        </label>

                                        <input
                                            type="text"
                                            wire:model.live.debounce.500ms="sections.{{ $sectionIndex }}.fields.{{ $fieldIndex }}.default"
                                            class="w-full border rounded-lg px-3 py-2 text-sm"
                                        >

                                    </div>

                                    <label class="flex items-center gap-2 text-sm">

                                        <input
                                            type="checkbox"
                                            wire:model.live="sections.{{ $sectionIndex }}.fields.{{ $fieldIndex }}.required"
                                        >

                                        Required

                                    </label>


                                    {{-- Text / Number validation --}}
                                    @if (in_array($field['type'], [
                                        'text',
                                        'textarea',
                                        'number'
                                    ], true))

                                        <div class="border-t pt-4">

                                            <div class="font-medium text-sm mb-3">
                                                Validation
                                            </div>

                                            <div class="grid grid-cols-2 gap-2">

                                                <input
                                                    type="number"
                                                    placeholder="Min"
                                                    wire:model.live="sections.{{ $sectionIndex }}.fields.{{ $fieldIndex }}.validation.min"
                                                    class="border rounded-lg px-2 py-2 text-sm"
                                                >

                                                <input
                                                    type="number"
                                                    placeholder="Max"
                                                    wire:model.live="sections.{{ $sectionIndex }}.fields.{{ $fieldIndex }}.validation.max"
                                                    class="border rounded-lg px-2 py-2 text-sm"
                                                >

                                            </div>

                                        </div>

                                    @endif


                                    {{-- String length --}}
                                    @if (in_array($field['type'], [
                                        'text',
                                        'textarea'
                                    ], true))

                                        <div class="grid grid-cols-2 gap-2">

                                            <input
                                                type="number"
                                                placeholder="Min length"
                                                wire:model.live="sections.{{ $sectionIndex }}.fields.{{ $fieldIndex }}.validation.min_length"
                                                class="border rounded-lg px-2 py-2 text-sm"
                                            >

                                            <input
                                                type="number"
                                                placeholder="Max length"
                                                wire:model.live="sections.{{ $sectionIndex }}.fields.{{ $fieldIndex }}.validation.max_length"
                                                class="border rounded-lg px-2 py-2 text-sm"
                                            >

                                        </div>

                                    @endif


                                    {{-- Regex --}}
                                    @if (in_array($field['type'], [
                                        'text',
                                        'textarea',
                                        'phone'
                                    ], true))

                                        <div>

                                            <label class="block text-xs font-medium mb-1">
                                                Regex
                                            </label>

                                            <input
                                                type="text"
                                                placeholder="/^[0-9]+$/"
                                                wire:model.live.debounce.500ms="sections.{{ $sectionIndex }}.fields.{{ $fieldIndex }}.validation.regex"
                                                class="w-full border rounded-lg px-2 py-2 text-sm"
                                            >

                                        </div>

                                    @endif


                                    {{-- Options --}}
                                    @if (in_array($field['type'], [
                                        'dropdown',
                                        'radio',
                                        'checkbox'
                                    ], true))

                                        <div class="border-t pt-4">

                                            <div class="flex items-center justify-between mb-3">

                                                <div class="font-medium text-sm">
                                                    Options
                                                </div>

                                                <button
                                                    wire:click="addOption(
                                                        {{ $sectionIndex }},
                                                        {{ $fieldIndex }}
                                                    )"
                                                    class="text-xs text-blue-600"
                                                >
                                                    + Add
                                                </button>

                                            </div>

                                            <div class="space-y-2">

                                                @foreach ($field['options'] ?? [] as $optionIndex => $option)

                                                    <div class="flex gap-2">

                                                        <input
                                                            type="text"
                                                            wire:model.live.debounce.500ms="sections.{{ $sectionIndex }}.fields.{{ $fieldIndex }}.options.{{ $optionIndex }}.label"
                                                            placeholder="Label"
                                                            class="w-full border rounded px-2 py-1 text-xs"
                                                        >

                                                        <input
                                                            type="text"
                                                            wire:model.live.debounce.500ms="sections.{{ $sectionIndex }}.fields.{{ $fieldIndex }}.options.{{ $optionIndex }}.value"
                                                            placeholder="Value"
                                                            class="w-full border rounded px-2 py-1 text-xs"
                                                        >

                                                        <button
                                                            wire:click="removeOption(
                                                                {{ $sectionIndex }},
                                                                {{ $fieldIndex }},
                                                                {{ $optionIndex }}
                                                            )"
                                                            class="text-red-500"
                                                        >
                                                            ×
                                                        </button>

                                                    </div>

                                                @endforeach

                                            </div>

                                        </div>

                                    @endif


                                    {{-- File --}}
                                    @if ($field['type'] === 'file')

                                        <div class="border-t pt-4">

                                            <div class="font-medium text-sm mb-3">
                                                File Validation
                                            </div>

                                            <input
                                                type="text"
                                                placeholder="pdf,doc,docx,jpg,png"
                                                wire:model.live.debounce.500ms="sections.{{ $sectionIndex }}.fields.{{ $fieldIndex }}.validation.file_types"
                                                class="w-full border rounded-lg px-2 py-2 text-sm"
                                            >

                                            <input
                                                type="number"
                                                placeholder="Max size (KB)"
                                                wire:model.live="sections.{{ $sectionIndex }}.fields.{{ $fieldIndex }}.validation.max_size"
                                                class="w-full border rounded-lg px-2 py-2 text-sm mt-2"
                                            >

                                        </div>

                                    @endif


                                    {{-- Rating --}}
                                    @if ($field['type'] === 'rating')

                                        <div class="border-t pt-4">

                                            <div class="font-medium text-sm mb-3">
                                                Rating Settings
                                            </div>

                                            <div class="grid grid-cols-2 gap-2">

                                                <input
                                                    type="number"
                                                    min="1"
                                                    wire:model.live="sections.{{ $sectionIndex }}.fields.{{ $fieldIndex }}.validation.min"
                                                    placeholder="Min"
                                                    class="border rounded-lg px-2 py-2 text-sm"
                                                >

                                                <input
                                                    type="number"
                                                    min="1"
                                                    wire:model.live="sections.{{ $sectionIndex }}.fields.{{ $fieldIndex }}.validation.max"
                                                    placeholder="Max"
                                                    class="border rounded-lg px-2 py-2 text-sm"
                                                >

                                            </div>

                                        </div>

                                    @endif

                                </div>

                            @endif

                        @endforeach

                    @endforeach

                @else

                    <div class="text-sm text-gray-500">
                        Select a field from the builder to configure it.
                    </div>

                @endif

            </div>

        </div>

    </div>

    {{-- ============================================================= --}}
    {{-- AUTOSAVE --}}
    {{-- ============================================================= --}}

    @if ($form)

        <div
            wire:poll.15s="autosave"
            class="fixed bottom-5 right-5"
        >

            @if ($isDirty)

                <div class="px-4 py-2 bg-white border shadow rounded-lg text-xs">
                    Autosave pending...
                </div>

            @else

                <div class="px-4 py-2 bg-white border shadow rounded-lg text-xs text-green-600">
                    All changes saved
                </div>

            @endif

        </div>

    @endif

</div>