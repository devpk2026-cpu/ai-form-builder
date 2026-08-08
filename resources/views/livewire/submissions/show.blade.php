<div class="max-w-4xl mx-auto p-6">

    <div class="flex items-center justify-between mb-6">

        <div>
            <h1 class="text-2xl font-bold">
                Submission #{{ $submission->id }}
            </h1>

            <p class="text-gray-600 mt-1">
                {{ $form->title }}
            </p>
        </div>

        <a href="{{ route('forms.submissions.index', $form) }}" class="px-4 py-2 border rounded-lg">
            Back to Submissions
        </a>

    </div>

    <div class="bg-white rounded-lg shadow border p-6">

        <div class="mb-6 pb-4 border-b">

            <p class="text-sm text-gray-500">
                Submitted At
            </p>

            <p class="font-medium">
                {{ $submission->submitted_at?->format('d M Y, h:i A') }}
            </p>

        </div>

        <div class="space-y-6">

            @foreach ($form->schema_json['sections'] ?? [] as $section)
                @foreach ($section['fields'] ?? [] as $field)
                    @if ($field['type'] === 'section')
                        @continue
                    @endif

                    @php
                        $key = $field['key'];
                        $value = $submission->data_json[$key] ?? null;
                    @endphp

                    <div>

                        <p class="text-sm text-gray-500">
                            {{ $field['label'] }}
                        </p>

                        @if (is_array($value))
                            <div class="mt-1">
                                @foreach ($value as $item)
                                    <span class="inline-block bg-gray-100 px-2 py-1 rounded mr-1">
                                        {{ $item }}
                                    </span>
                                @endforeach
                            </div>
                        @else
                            @if ($field['type'] === 'file' && $value)
                                <button type="button" wire:click="download('{{ $key }}')"
                                    class="text-blue-600 hover:underline font-medium">
                                    Download File
                                </button>
                            @elseif ($field['type'] === 'rating' && $value)
                                <div class="mt-1 text-yellow-400 text-xl">
                                    @for ($rating = 1; $rating <= (int) ($field['validation']['max'] ?? 5); $rating++)
                                        {{ $rating <= (int) $value ? '★' : '☆' }}
                                    @endfor
                                </div>
                            @else
                                <p class="font-medium mt-1">
                                    {{ $value ?: '-' }}
                                </p>
                            @endif
                        @endif

                    </div>
                @endforeach
            @endforeach

        </div>

    </div>

</div>
