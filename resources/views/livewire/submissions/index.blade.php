<div class="max-w-7xl mx-auto p-6">

    <div class="flex items-center justify-between mb-6">

        <div>
            <h1 class="text-2xl font-bold">
                {{ $form->title }} - Submissions
            </h1>

            <p class="text-gray-600 mt-1">
                View and manage responses submitted to this form.
            </p>
        </div>

        <a
            href="{{ route('forms.edit', $form) }}"
            class="px-4 py-2 border rounded-lg"
        >
            Back to Form
        </a>

    </div>

    @if (session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow border overflow-hidden">

        @if ($submissions->count())

            <table class="w-full">

                <thead class="bg-gray-100">
                    <tr>
                        <th class="text-left p-4">
                            #
                        </th>

                        <th class="text-left p-4">
                            Submitted At
                        </th>

                        <th class="text-left p-4">
                            Response
                        </th>

                        <th class="text-right p-4">
                            Actions
                        </th>
                    </tr>
                </thead>

                <tbody>

                    @foreach ($submissions as $submission)

                        <tr class="border-t">

                            <td class="p-4">
                                {{ $submission->id }}
                            </td>

                            <td class="p-4">
                                {{ $submission->submitted_at?->format('d M Y, h:i A') }}
                            </td>

                            <td class="p-4">

                                @php
                                    $data = $submission->data_json ?? [];
                                    $preview = collect($data)
                                        ->reject(fn ($value) => is_array($value))
                                        ->map(fn ($value, $key) => ucfirst(str_replace('_', ' ', $key)) . ': ' . ($value ?? '-'))
                                        ->take(2);
                                @endphp

                                @foreach ($preview as $item)
                                    <div class="text-sm text-gray-700">
                                        {{ $item }}
                                    </div>
                                @endforeach

                            </td>

                            <td class="p-4 text-right space-x-3">

                                <a
                                    href="{{ route('forms.submissions.show', [
                                        'form' => $form,
                                        'submission' => $submission,
                                    ]) }}"
                                    class="text-blue-600"
                                >
                                    View
                                </a>

                                <button
                                    type="button"
                                    wire:click="delete({{ $submission->id }})"
                                    wire:confirm="Are you sure you want to delete this submission?"
                                    class="text-red-600"
                                >
                                    Delete
                                </button>

                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        @else

            <div class="p-10 text-center text-gray-500">
                No submissions yet.
            </div>

        @endif

    </div>

    <div class="mt-4">
        {{ $submissions->links() }}
    </div>

</div>