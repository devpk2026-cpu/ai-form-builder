<div class="max-w-3xl mx-auto p-6">

    @if ($submitted)

        <div class="bg-white rounded-xl shadow border p-10 text-center">

            <div class="text-green-600 text-5xl mb-4">
                ✓
            </div>

            <h1 class="text-2xl font-bold">
                Thank you!
            </h1>

            <p class="text-gray-600 mt-2">
                Your response has been submitted successfully.
            </p>

        </div>
    @else
        <div class="bg-white rounded-xl shadow border p-6">

            <h1 class="text-3xl font-bold">
                {{ $form->title }}
            </h1>

            @if ($form->description)
                <p class="text-gray-600 mt-2">
                    {{ $form->description }}
                </p>
            @endif

            <form wire:submit="submit" class="mt-8 space-y-8">

                @foreach ($form->schema_json['sections'] ?? [] as $section)
                    <section>

                        <h2 class="text-xl font-semibold">
                            {{ $section['title'] }}
                        </h2>

                        <div class="mt-5 space-y-5">

                            @foreach ($section['fields'] ?? [] as $field)
                                @if ($field['type'] === 'section')
                                    <div class="pt-4">
                                        <h3 class="text-lg font-semibold">
                                            {{ $field['label'] }}
                                        </h3>
                                    </div>

                                    @continue
                                @endif

                                <div>

                                    <label class="block font-medium mb-1">

                                        {{ $field['label'] }}

                                        @if ($field['required'] ?? false)
                                            <span class="text-red-500">*</span>
                                        @endif

                                    </label>


                                    {{-- TEXTAREA --}}
                                    @if ($field['type'] === 'textarea')
                                        <textarea wire:model="data.{{ $field['key'] }}" placeholder="{{ $field['placeholder'] ?? '' }}"
                                            class="w-full border rounded-lg px-3 py-2" rows="4"></textarea>


                                        {{-- DROPDOWN --}}
                                    @elseif ($field['type'] === 'dropdown')
                                        <select wire:model="data.{{ $field['key'] }}"
                                            class="w-full border rounded-lg px-3 py-2">

                                            <option value="">
                                                Select an option
                                            </option>

                                            @foreach ($field['options'] ?? [] as $option)
                                                <option value="{{ $option['value'] }}">
                                                    {{ $option['label'] }}
                                                </option>
                                            @endforeach

                                        </select>


                                        {{-- RADIO --}}
                                    @elseif ($field['type'] === 'radio')
                                        <div class="space-y-2">

                                            @foreach ($field['options'] ?? [] as $option)
                                                <label class="flex gap-2 items-center">

                                                    <input type="radio" wire:model="data.{{ $field['key'] }}"
                                                        value="{{ $option['value'] }}">

                                                    {{ $option['label'] }}

                                                </label>
                                            @endforeach

                                        </div>


                                        {{-- CHECKBOX --}}
                                    @elseif ($field['type'] === 'checkbox')
                                        <div class="space-y-2">

                                            @foreach ($field['options'] ?? [] as $optionIndex => $option)
                                                <label class="flex gap-2 items-center">

                                                    <input type="checkbox" wire:model="data.{{ $field['key'] }}"
                                                        value="{{ $option['value'] }}">

                                                    {{ $option['label'] }}

                                                </label>
                                            @endforeach

                                        </div>


                                        {{-- FILE --}}
                                    @elseif ($field['type'] === 'file')
                                        <input type="file" wire:model="data.{{ $field['key'] }}"
                                            class="w-full border rounded-lg px-3 py-2">


                                        {{-- RATING --}}
                                    @elseif ($field['type'] === 'rating')
                                        @php
                                            $maxRating = (int) ($field['validation']['max'] ?? 5);
                                            $currentRating = (int) ($data[$field['key']] ?? 0);
                                        @endphp

                                        <div class="flex gap-2">

                                            @for ($rating = 1; $rating <= $maxRating; $rating++)
                                                <label class="cursor-pointer">

                                                    <input type="radio" class="sr-only"
                                                        wire:model.live="data.{{ $field['key'] }}"
                                                        value="{{ $rating }}">

                                                    <span
                                                        class="text-3xl transition
                        {{ $rating <= $currentRating ? 'text-yellow-400' : 'text-gray-300' }}">
                                                        ★
                                                    </span>

                                                </label>
                                            @endfor

                                        </div>


                                        {{-- NORMAL INPUT --}}
                                    @else
                                        <input
                                            type="{{ match ($field['type']) {
                                                'email' => 'email',
                                                'number' => 'number',
                                                'date' => 'date',
                                                'phone' => 'tel',
                                                default => 'text',
                                            } }}"
                                            wire:model="data.{{ $field['key'] }}"
                                            value="{{ $field['default'] ?? '' }}"
                                            placeholder="{{ $field['placeholder'] ?? '' }}"
                                            class="w-full border rounded-lg px-3 py-2">
                                    @endif


                                    @if (!empty($field['help_text']))
                                        <p class="text-xs text-gray-500 mt-1">
                                            {{ $field['help_text'] }}
                                        </p>
                                    @endif

                                    @error($field['key'])
                                        <p class="mt-1 text-sm text-red-600">
                                            {{ $message }}
                                        </p>
                                    @enderror

                                </div>
                            @endforeach

                        </div>

                    </section>
                @endforeach


                <div class="pt-4 border-t">


                    <button type="submit" wire:loading.attr="disabled" wire:target="submit"
                        class="px-6 py-3 bg-black text-white rounded-lg disabled:opacity-50">
                        <span wire:loading.remove wire:target="submit">
                            Submit
                        </span>

                        <span wire:loading wire:target="submit">
                            Submitting...
                        </span>
                    </button>

                </div>

            </form>

        </div>

    @endif

</div>
