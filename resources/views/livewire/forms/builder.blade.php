<div
    class="min-h-screen bg-gray-50"
    x-data="{
        draggedSection: null,
        draggedField: null,

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

{{-- ============================================================= --}}

{{-- TOP TOOLBAR --}}
{{-- ============================================================= --}}

<div class="bg-white border-b sticky top-0 z-30">
    <div class="max-w-[1600px] mx-auto px-6 py-4">

        <div class="flex items-center justify-between gap-4">

            {{-- Left --}}
            <div class="min-w-0">

                <a
                    href="{{ route('forms.index') }}"
                    class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-900 transition"
                >
                    <span>←</span>
                    <span>Back to Forms</span>
                </a>

                <div class="flex items-center gap-3 mt-2">

                    <h1 class="text-xl font-bold text-gray-900">
                        {{ $form ? 'Edit Form' : 'Create Form' }}
                    </h1>

                    @if ($isDirty)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-amber-50 text-amber-700 text-xs font-medium">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                            Unsaved changes
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-green-50 text-green-700 text-xs font-medium">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                            Saved
                        </span>
                    @endif

                </div>

            </div>


            {{-- Right --}}
            <div class="flex items-center gap-2 shrink-0">

                {{-- Undo --}}
                <button
                    wire:click="undo"
                    @disabled($historyIndex <= 0)
                    class="inline-flex items-center justify-center w-9 h-9 border border-gray-200 rounded-lg text-gray-600 hover:bg-gray-50 disabled:opacity-30 disabled:cursor-not-allowed transition"
                    title="Undo"
                >
                    ↶
                </button>

                {{-- Redo --}}
                <button
                    wire:click="redo"
                    @disabled($historyIndex >= count($history) - 1)
                    class="inline-flex items-center justify-center w-9 h-9 border border-gray-200 rounded-lg text-gray-600 hover:bg-gray-50 disabled:opacity-30 disabled:cursor-not-allowed transition"
                    title="Redo"
                >
                    ↷
                </button>

                <div class="w-px h-7 bg-gray-200 mx-1"></div>

                <button
                    wire:click="save"
                    wire:loading.attr="disabled"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-900 text-white text-sm font-medium rounded-lg hover:bg-black disabled:opacity-50 transition"
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

    </div>
</div>


{{-- Flash Message --}}
@if (session('success'))
    <div class="max-w-[1600px] mx-auto px-6 pt-5">
        <div class="flex items-center gap-2 p-3.5 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm">
            <span>✓</span>
            <span>{{ session('success') }}</span>
        </div>
    </div>
@endif


{{-- ============================================================= --}}
{{-- MAIN BUILDER AREA --}}
{{-- ============================================================= --}}

<div class="max-w-[1600px] mx-auto px-6 py-6">

    <div class="grid grid-cols-12 gap-6 items-start">


        {{-- ===================================================== --}}
        {{-- LEFT: FORM BUILDER --}}
        {{-- ===================================================== --}}

        <main class="col-span-12 xl:col-span-6">

            {{-- Form Settings --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 mb-6">

                <div class="flex items-center justify-between mb-5">

                    <div>
                        <h2 class="text-base font-semibold text-gray-900">
                            Form Settings
                        </h2>

                        <p class="text-xs text-gray-500 mt-1">
                            Configure the basic information for your form.
                        </p>
                    </div>

                    <div class="px-2.5 py-1 rounded-md bg-gray-100 text-xs text-gray-500">
                        {{ ucfirst($status) }}
                    </div>

                </div>


                <div class="space-y-5">

                    {{-- Title --}}
                    <div>

                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Form Title
                        </label>

                        <input
                            type="text"
                            wire:model.live.debounce.500ms="title"
                            class="w-full border-gray-300 rounded-lg px-3.5 py-2.5 text-sm focus:border-gray-900 focus:ring-gray-900"
                            placeholder="Enter form title"
                        >

                    </div>


                    {{-- Description --}}
                    <div>

                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Description
                        </label>

                        <textarea
                            wire:model.live.debounce.500ms="description"
                            rows="3"
                            class="w-full border-gray-300 rounded-lg px-3.5 py-2.5 text-sm focus:border-gray-900 focus:ring-gray-900 resize-none"
                            placeholder="Describe this form"
                        ></textarea>

                    </div>


                    {{-- Status --}}
                    <div>

                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Status
                        </label>

                        <select
                            wire:model.live="status"
                            class="w-full border-gray-300 rounded-lg px-3.5 py-2.5 text-sm focus:border-gray-900 focus:ring-gray-900"
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
                    class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 mb-6"
                >

                    {{-- Section Header --}}
                    <div class="flex items-center justify-between gap-4 mb-5">

                        <div class="flex-1">

                            <div class="text-[11px] font-semibold uppercase tracking-wider text-gray-400 mb-1">
                                Section {{ $sectionIndex + 1 }}
                            </div>

                            <input
                                type="text"
                                wire:model.live.debounce.500ms="sections.{{ $sectionIndex }}.title"
                                class="w-full text-lg font-semibold text-gray-900 border-0 border-b border-gray-200 focus:border-gray-900 focus:ring-0 px-0 py-1"
                            >

                        </div>

                        <button
                            wire:click="removeSection({{ $sectionIndex }})"
                            class="shrink-0 text-xs font-medium text-red-500 hover:text-red-700"
                        >
                            Remove
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
                                    group relative border rounded-xl p-4 cursor-pointer
                                    transition-all duration-150
                                    {{ $selectedFieldId === $field['id']
                                        ? 'border-gray-900 bg-gray-50 ring-2 ring-gray-200'
                                        : 'border-gray-200 hover:border-gray-300 hover:shadow-sm bg-white' }}
                                "
                            >

                                <div class="flex items-start gap-3">

                                    {{-- Drag Handle --}}
                                    <div class="pt-1.5 text-gray-300 group-hover:text-gray-500 cursor-grab select-none text-lg leading-none">
                                        ⋮⋮
                                    </div>


                                    <div class="flex-1 min-w-0">

                                        <div class="flex items-start justify-between gap-3">

                                            <div class="min-w-0">

                                                <div class="flex items-center gap-2">

                                                    <span class="inline-flex px-2 py-0.5 rounded-md bg-gray-100 text-[10px] font-semibold uppercase tracking-wide text-gray-500">
                                                        {{ $field['type'] }}
                                                    </span>

                                                    @if ($field['required'])
                                                        <span class="text-[10px] font-medium text-red-500">
                                                            Required
                                                        </span>
                                                    @endif

                                                </div>

                                                <div class="font-medium text-sm text-gray-900 mt-2 truncate">
                                                    {{ $field['label'] }}
                                                </div>

                                            </div>


                                            <button
                                                wire:click.stop="
                                                    removeField(
                                                        {{ $sectionIndex }},
                                                        {{ $fieldIndex }}
                                                    )
                                                "
                                                class="opacity-0 group-hover:opacity-100 text-xs text-red-500 hover:text-red-700 transition"
                                            >
                                                Delete
                                            </button>

                                        </div>


                                        <div class="text-xs text-gray-400 mt-1">
                                            {{ $field['key'] }}
                                        </div>

                                    </div>

                                </div>

                            </div>

                        @empty

                            <div class="border-2 border-dashed border-gray-200 rounded-xl p-10 text-center">

                                <div class="text-2xl mb-2 text-gray-300">
                                    +
                                </div>

                                <p class="text-sm font-medium text-gray-500">
                                    No fields yet
                                </p>

                                <p class="text-xs text-gray-400 mt-1">
                                    Add a field using the options below.
                                </p>

                            </div>

                        @endforelse

                    </div>


                    {{-- Add Field --}}
                    <div class="mt-6 pt-5 border-t border-gray-100">

                        <div class="flex items-center justify-between mb-3">

                            <div>
                                <div class="text-sm font-semibold text-gray-800">
                                    Add Field
                                </div>

                                <div class="text-xs text-gray-400 mt-0.5">
                                    Select a field type to add.
                                </div>
                            </div>

                        </div>


                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">

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
                                    class="flex items-center gap-2 px-3 py-2.5 text-xs font-medium text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 hover:border-gray-300 hover:text-gray-900 transition"
                                >
                                    <span class="text-gray-400">
                                        +
                                    </span>

                                    {{ $label }}
                                </button>

                            @endforeach

                        </div>

                    </div>

                </div>

            @endforeach


            {{-- Add Section --}}
            <button
                wire:click="addSection"
                class="w-full border-2 border-dashed border-gray-300 rounded-2xl p-5 text-sm font-medium text-gray-500 hover:bg-white hover:border-gray-400 transition"
            >
                + Add Section
            </button>

        </main>


        {{-- ===================================================== --}}
        {{-- MIDDLE: LIVE PREVIEW --}}
        {{-- ===================================================== --}}

        <aside class="col-span-12 lg:col-span-6 xl:col-span-3">

            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden sticky top-24">

                {{-- Preview Header --}}
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">

                    <div>

                        <h2 class="text-sm font-semibold text-gray-900">
                            Live Preview
                        </h2>

                        <p class="text-[11px] text-gray-400 mt-0.5">
                            How your form will appear
                        </p>

                    </div>

                    <span class="px-2 py-1 bg-gray-100 rounded-md text-[10px] text-gray-500">
                        Preview
                    </span>

                </div>


                {{-- Preview Content --}}
                <div class="p-5 max-h-[calc(100vh-180px)] overflow-y-auto">

                    <h3 class="text-xl font-bold text-gray-900">
                        {{ $title ?: 'Untitled Form' }}
                    </h3>

                    @if ($description)

                        <p class="text-sm text-gray-500 mt-2 leading-relaxed">
                            {{ $description }}
                        </p>

                    @endif


                    <div class="mt-6 space-y-7">

                        @foreach ($sections as $section)

                            <div>

                                <h4 class="text-sm font-semibold text-gray-800">
                                    {{ $section['title'] }}
                                </h4>

                                <div class="space-y-5 mt-4">

                                    @foreach ($section['fields'] as $field)

                                        <div>

                                            @if ($field['type'] === 'section')

                                                <div class="font-semibold text-sm text-gray-800">
                                                    {{ $field['label'] }}
                                                </div>

                                            @else

                                                <label class="block text-xs font-medium text-gray-700 mb-1.5">

                                                    {{ $field['label'] }}

                                                    @if ($field['required'])
                                                        <span class="text-red-500">*</span>
                                                    @endif

                                                </label>


                                                @if ($field['type'] === 'textarea')

                                                    <textarea
                                                        class="w-full border-gray-300 rounded-lg px-3 py-2 text-xs"
                                                        placeholder="{{ $field['placeholder'] }}"
                                                        rows="3"
                                                    ></textarea>


                                                @elseif ($field['type'] === 'dropdown')

                                                    <select class="w-full border-gray-300 rounded-lg px-3 py-2 text-xs">

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

                                                            <label class="flex items-center gap-2 text-xs text-gray-700">

                                                                <input
                                                                    type="radio"
                                                                    name="preview_{{ $field['id'] }}"
                                                                    class="border-gray-300"
                                                                >

                                                                {{ $option['label'] }}

                                                            </label>

                                                        @endforeach

                                                    </div>


                                                @elseif ($field['type'] === 'checkbox')

                                                    <div class="space-y-2">

                                                        @foreach ($field['options'] ?? [] as $option)

                                                            <label class="flex items-center gap-2 text-xs text-gray-700">

                                                                <input
                                                                    type="checkbox"
                                                                    class="border-gray-300 rounded"
                                                                >

                                                                {{ $option['label'] }}

                                                            </label>

                                                        @endforeach

                                                    </div>


                                                @elseif ($field['type'] === 'file')

                                                    <input
                                                        type="file"
                                                        class="w-full text-xs border border-gray-300 rounded-lg px-2 py-2"
                                                    >


                                                @elseif ($field['type'] === 'rating')

                                                    <div class="flex gap-1 text-lg text-gray-400">

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
                                                        class="w-full border-gray-300 rounded-lg px-3 py-2 text-xs"
                                                    >

                                                @endif


                                                @if (! empty($field['help_text']))

                                                    <p class="text-[11px] text-gray-400 mt-1">
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
                        type="button"
                        class="w-full mt-7 bg-gray-900 text-white rounded-lg py-2.5 text-xs font-medium"
                    >
                        Submit
                    </button>

                </div>

            </div>

        </aside>


        {{-- ===================================================== --}}
        {{-- RIGHT: FIELD CONFIGURATION --}}
        {{-- ===================================================== --}}

        <aside class="col-span-12 lg:col-span-6 xl:col-span-3">

            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm sticky top-24">

                <div class="px-5 py-4 border-b border-gray-100">

                    <h2 class="text-sm font-semibold text-gray-900">
                        Field Settings
                    </h2>

                    <p class="text-[11px] text-gray-400 mt-0.5">
                        Configure the selected field
                    </p>

                </div>


                <div class="p-5 max-h-[calc(100vh-180px)] overflow-y-auto">

                    @if ($selectedFieldId)

                        @foreach ($sections as $sectionIndex => $section)

                            @foreach ($section['fields'] as $fieldIndex => $field)

                                @if ($field['id'] === $selectedFieldId)

                                    <div class="space-y-5">


                                        {{-- Type --}}
                                        <div>

                                            <label class="block text-xs font-medium text-gray-600 mb-1.5">
                                                Field Type
                                            </label>

                                            <div class="px-3 py-2.5 bg-gray-100 rounded-lg text-sm text-gray-700">
                                                {{ ucfirst($field['type']) }}
                                            </div>

                                        </div>


                                        {{-- Label --}}
                                        <div>

                                            <label class="block text-xs font-medium text-gray-600 mb-1.5">
                                                Label
                                            </label>

                                            <input
                                                type="text"
                                                wire:model.live.debounce.500ms="sections.{{ $sectionIndex }}.fields.{{ $fieldIndex }}.label"
                                                class="w-full border-gray-300 rounded-lg px-3 py-2 text-sm focus:border-gray-900 focus:ring-gray-900"
                                            >

                                        </div>


                                        {{-- Key --}}
                                        <div>

                                            <label class="block text-xs font-medium text-gray-600 mb-1.5">
                                                Field Key
                                            </label>

                                            <input
                                                type="text"
                                                wire:model.live.debounce.500ms="sections.{{ $sectionIndex }}.fields.{{ $fieldIndex }}.key"
                                                class="w-full border-gray-300 rounded-lg px-3 py-2 text-sm focus:border-gray-900 focus:ring-gray-900"
                                            >

                                        </div>


                                        {{-- Placeholder --}}
                                        <div>

                                            <label class="block text-xs font-medium text-gray-600 mb-1.5">
                                                Placeholder
                                            </label>

                                            <input
                                                type="text"
                                                wire:model.live.debounce.500ms="sections.{{ $sectionIndex }}.fields.{{ $fieldIndex }}.placeholder"
                                                class="w-full border-gray-300 rounded-lg px-3 py-2 text-sm"
                                            >

                                        </div>


                                        {{-- Help Text --}}
                                        <div>

                                            <label class="block text-xs font-medium text-gray-600 mb-1.5">
                                                Help Text
                                            </label>

                                            <textarea
                                                wire:model.live.debounce.500ms="sections.{{ $sectionIndex }}.fields.{{ $fieldIndex }}.help_text"
                                                rows="2"
                                                class="w-full border-gray-300 rounded-lg px-3 py-2 text-sm resize-none"
                                            ></textarea>

                                        </div>


                                        {{-- Default --}}
                                        <div>

                                            <label class="block text-xs font-medium text-gray-600 mb-1.5">
                                                Default Value
                                            </label>

                                            <input
                                                type="text"
                                                wire:model.live.debounce.500ms="sections.{{ $sectionIndex }}.fields.{{ $fieldIndex }}.default"
                                                class="w-full border-gray-300 rounded-lg px-3 py-2 text-sm"
                                            >

                                        </div>


                                        {{-- Required --}}
                                        <label class="flex items-center gap-3 text-sm text-gray-700 cursor-pointer">

                                            <input
                                                type="checkbox"
                                                wire:model.live="sections.{{ $sectionIndex }}.fields.{{ $fieldIndex }}.required"
                                                class="rounded border-gray-300"
                                            >

                                            <span>
                                                Required field
                                            </span>

                                        </label>


                                        {{-- Text / Number Validation --}}
                                        @if (in_array($field['type'], [
                                            'text',
                                            'textarea',
                                            'number'
                                        ], true))

                                            <div class="pt-5 border-t border-gray-100">

                                                <div class="text-xs font-semibold text-gray-700 mb-3">
                                                    Validation
                                                </div>

                                                <div class="grid grid-cols-2 gap-2">

                                                    <input
                                                        type="number"
                                                        placeholder="Min"
                                                        wire:model.live="sections.{{ $sectionIndex }}.fields.{{ $fieldIndex }}.validation.min"
                                                        class="border-gray-300 rounded-lg px-2.5 py-2 text-sm"
                                                    >

                                                    <input
                                                        type="number"
                                                        placeholder="Max"
                                                        wire:model.live="sections.{{ $sectionIndex }}.fields.{{ $fieldIndex }}.validation.max"
                                                        class="border-gray-300 rounded-lg px-2.5 py-2 text-sm"
                                                    >

                                                </div>

                                            </div>

                                        @endif


                                        {{-- String Length --}}
                                        @if (in_array($field['type'], [
                                            'text',
                                            'textarea'
                                        ], true))

                                            <div>

                                                <div class="grid grid-cols-2 gap-2">

                                                    <input
                                                        type="number"
                                                        placeholder="Min length"
                                                        wire:model.live="sections.{{ $sectionIndex }}.fields.{{ $fieldIndex }}.validation.min_length"
                                                        class="border-gray-300 rounded-lg px-2.5 py-2 text-sm"
                                                    >

                                                    <input
                                                        type="number"
                                                        placeholder="Max length"
                                                        wire:model.live="sections.{{ $sectionIndex }}.fields.{{ $fieldIndex }}.validation.max_length"
                                                        class="border-gray-300 rounded-lg px-2.5 py-2 text-sm"
                                                    >

                                                </div>

                                            </div>

                                        @endif


                                        {{-- Regex --}}
                                        @if (in_array($field['type'], [
                                            'text',
                                            'textarea',
                                            'phone'
                                        ], true))

                                            <div>

                                                <label class="block text-xs font-medium text-gray-600 mb-1.5">
                                                    Regex
                                                </label>

                                                <input
                                                    type="text"
                                                    placeholder="/^[0-9]+$/"
                                                    wire:model.live.debounce.500ms="sections.{{ $sectionIndex }}.fields.{{ $fieldIndex }}.validation.regex"
                                                    class="w-full border-gray-300 rounded-lg px-2.5 py-2 text-sm"
                                                >

                                            </div>

                                        @endif


                                        {{-- Options --}}
                                        @if (in_array($field['type'], [
                                            'dropdown',
                                            'radio',
                                            'checkbox'
                                        ], true))

                                            <div class="pt-5 border-t border-gray-100">

                                                <div class="flex items-center justify-between mb-3">

                                                    <div class="text-xs font-semibold text-gray-700">
                                                        Options
                                                    </div>

                                                    <button
                                                        wire:click="addOption(
                                                            {{ $sectionIndex }},
                                                            {{ $fieldIndex }}
                                                        )"
                                                        class="text-xs font-medium text-blue-600 hover:text-blue-800"
                                                    >
                                                        + Add option
                                                    </button>

                                                </div>


                                                <div class="space-y-2">

                                                    @foreach ($field['options'] ?? [] as $optionIndex => $option)

                                                        <div class="flex gap-2">

                                                            <input
                                                                type="text"
                                                                wire:model.live.debounce.500ms="sections.{{ $sectionIndex }}.fields.{{ $fieldIndex }}.options.{{ $optionIndex }}.label"
                                                                placeholder="Label"
                                                                class="w-full border-gray-300 rounded-lg px-2 py-1.5 text-xs"
                                                            >

                                                            <input
                                                                type="text"
                                                                wire:model.live.debounce.500ms="sections.{{ $sectionIndex }}.fields.{{ $fieldIndex }}.options.{{ $optionIndex }}.value"
                                                                placeholder="Value"
                                                                class="w-full border-gray-300 rounded-lg px-2 py-1.5 text-xs"
                                                            >

                                                            <button
                                                                wire:click="removeOption(
                                                                    {{ $sectionIndex }},
                                                                    {{ $fieldIndex }},
                                                                    {{ $optionIndex }}
                                                                )"
                                                                class="px-2 text-red-500 hover:text-red-700"
                                                            >
                                                                ×
                                                            </button>

                                                        </div>

                                                    @endforeach

                                                </div>

                                            </div>

                                        @endif


                                        {{-- File Validation --}}
                                        @if ($field['type'] === 'file')

                                            <div class="pt-5 border-t border-gray-100">

                                                <div class="text-xs font-semibold text-gray-700 mb-3">
                                                    File Validation
                                                </div>

                                                <input
                                                    type="text"
                                                    placeholder="pdf,doc,docx,jpg,png"
                                                    wire:model.live.debounce.500ms="sections.{{ $sectionIndex }}.fields.{{ $fieldIndex }}.validation.file_types"
                                                    class="w-full border-gray-300 rounded-lg px-2.5 py-2 text-sm"
                                                >

                                                <input
                                                    type="number"
                                                    placeholder="Max size (KB)"
                                                    wire:model.live="sections.{{ $sectionIndex }}.fields.{{ $fieldIndex }}.validation.max_size"
                                                    class="w-full border-gray-300 rounded-lg px-2.5 py-2 text-sm mt-2"
                                                >

                                            </div>

                                        @endif


                                        {{-- Rating --}}
                                        @if ($field['type'] === 'rating')

                                            <div class="pt-5 border-t border-gray-100">

                                                <div class="text-xs font-semibold text-gray-700 mb-3">
                                                    Rating Settings
                                                </div>

                                                <div class="grid grid-cols-2 gap-2">

                                                    <input
                                                        type="number"
                                                        min="1"
                                                        wire:model.live="sections.{{ $sectionIndex }}.fields.{{ $fieldIndex }}.validation.min"
                                                        placeholder="Min"
                                                        class="border-gray-300 rounded-lg px-2.5 py-2 text-sm"
                                                    >

                                                    <input
                                                        type="number"
                                                        min="1"
                                                        wire:model.live="sections.{{ $sectionIndex }}.fields.{{ $fieldIndex }}.validation.max"
                                                        placeholder="Max"
                                                        class="border-gray-300 rounded-lg px-2.5 py-2 text-sm"
                                                    >

                                                </div>

                                            </div>

                                        @endif

                                    </div>

                                @endif

                            @endforeach

                        @endforeach

                    @else

                        <div class="py-12 text-center">

                            <div class="w-12 h-12 mx-auto rounded-full bg-gray-100 flex items-center justify-center text-gray-400 text-xl">
                                ⚙
                            </div>

                            <p class="text-sm font-medium text-gray-600 mt-4">
                                No field selected
                            </p>

                            <p class="text-xs text-gray-400 mt-1 leading-relaxed">
                                Select a field from the builder to configure its properties.
                            </p>

                        </div>

                    @endif

                </div>

            </div>

        </aside>

    </div>

</div>


{{-- ============================================================= --}}
{{-- AUTOSAVE STATUS --}}
{{-- ============================================================= --}}

@if ($form)

    <div
        wire:poll.15s="autosave"
        class="fixed bottom-5 right-5 z-40"
    >

        @if ($isDirty)

            <div class="flex items-center gap-2 px-4 py-2.5 bg-white border border-amber-200 shadow-lg rounded-xl text-xs text-amber-700">

                <span class="w-2 h-2 rounded-full bg-amber-500"></span>

                Autosave pending...

            </div>

        @else

            <div class="flex items-center gap-2 px-4 py-2.5 bg-white border border-green-200 shadow-lg rounded-xl text-xs text-green-600">

                <span class="w-2 h-2 rounded-full bg-green-500"></span>

                All changes saved

            </div>

        @endif

    </div>

@endif


</div>
