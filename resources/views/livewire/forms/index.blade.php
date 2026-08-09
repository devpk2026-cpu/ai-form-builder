<div class="min-h-screen bg-gray-50">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">

            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    My Forms
                </h1>

                <p class="mt-1 text-sm text-gray-500">
                    Create, manage and monitor your forms.
                </p>
            </div>

            <a
                href="{{ route('forms.create') }}"
                wire:navigate
                class="inline-flex items-center justify-center px-4 py-2.5
                       bg-gray-900 text-white text-sm font-medium
                       rounded-lg hover:bg-gray-800 transition"
            >
                <span class="mr-2 text-lg leading-none">+</span>
                Create Form
            </a>

        </div>


        {{-- Success Message --}}
        @if (session('success'))

            <div class="mb-6 flex items-center gap-3 p-4
                        bg-green-50 border border-green-200
                        text-green-800 rounded-lg">

                <div class="flex-shrink-0">
                    ✓
                </div>

                <div class="text-sm font-medium">
                    {{ session('success') }}
                </div>

            </div>

        @endif


        {{-- Forms Table --}}
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">

            @if ($forms->count())

                <div class="overflow-x-auto">

                    <table class="w-full">

                        <thead class="bg-gray-50 border-b border-gray-200">

                            <tr>

                                <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Form
                                </th>

                                <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>

                                <th class="text-center px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Submissions
                                </th>

                                <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Created
                                </th>

                                <th class="text-right px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>

                            </tr>

                        </thead>


                        <tbody class="divide-y divide-gray-100">

                            @foreach ($forms as $form)

                                <tr class="hover:bg-gray-50 transition">

                                    {{-- Form --}}
                                    <td class="px-6 py-5">

                                        <div class="font-semibold text-gray-900">
                                            {{ $form->title }}
                                        </div>

                                        @if ($form->description)

                                            <div class="mt-1 text-sm text-gray-500 max-w-md truncate">
                                                {{ $form->description }}
                                            </div>

                                        @endif

                                        <div class="mt-1 text-xs text-gray-400">
                                            /forms/{{ $form->slug }}
                                        </div>

                                    </td>


                                    {{-- Status --}}
                                    <td class="px-6 py-5">

                                        @if ($form->status === 'published')

                                            <span class="inline-flex items-center gap-1.5
                                                         px-2.5 py-1 rounded-full
                                                         text-xs font-medium
                                                         bg-green-100 text-green-700">

                                                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>

                                                Published

                                            </span>

                                        @elseif ($form->status === 'draft')

                                            <span class="inline-flex items-center gap-1.5
                                                         px-2.5 py-1 rounded-full
                                                         text-xs font-medium
                                                         bg-yellow-100 text-yellow-700">

                                                <span class="w-1.5 h-1.5 rounded-full bg-yellow-500"></span>

                                                Draft

                                            </span>

                                        @else

                                            <span class="inline-flex items-center
                                                         px-2.5 py-1 rounded-full
                                                         text-xs font-medium
                                                         bg-gray-100 text-gray-700">

                                                {{ ucfirst($form->status) }}

                                            </span>

                                        @endif

                                    </td>


                                    {{-- Submissions --}}
                                    <td class="px-6 py-5 text-center">

                                        <a
                                            href="{{ route('forms.submissions.index', $form) }}"
                                            wire:navigate
                                            class="inline-flex items-center justify-center
                                                   min-w-10 px-3 py-1.5
                                                   rounded-lg
                                                   bg-blue-50 text-blue-700
                                                   hover:bg-blue-100
                                                   font-semibold text-sm
                                                   transition"
                                        >
                                            {{ $form->submissions_count }}
                                        </a>

                                    </td>


                                    {{-- Created --}}
                                    <td class="px-6 py-5">

                                        <div class="text-sm text-gray-700">
                                            {{ $form->created_at->format('d M Y') }}
                                        </div>

                                        <div class="text-xs text-gray-400 mt-1">
                                            {{ $form->created_at->format('h:i A') }}
                                        </div>

                                    </td>


                                    {{-- Actions --}}
                                    <td class="px-6 py-5">

                                        <div class="flex items-center justify-end gap-4">

                                            {{-- Edit --}}
                                            <a
                                                href="{{ route('forms.edit', $form) }}"
                                                wire:navigate
                                                class="text-sm font-medium text-blue-600
                                                       hover:text-blue-800"
                                            >
                                                Edit
                                            </a>


                                            {{-- Public Form --}}
                                            @if ($form->status === 'published')

                                                <a
                                                    href="{{ route('forms.public', $form->slug) }}"
                                                    target="_blank"
                                                    class="text-sm font-medium text-green-600
                                                           hover:text-green-800"
                                                >
                                                    View
                                                </a>

                                            @endif


                                            {{-- Delete --}}
                                            <button
                                                wire:click="delete({{ $form->id }})"
                                                wire:confirm="Are you sure you want to delete this form?"
                                                class="text-sm font-medium text-red-600
                                                       hover:text-red-800"
                                            >
                                                Delete
                                            </button>

                                        </div>

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>


                {{-- Pagination --}}
                @if ($forms->hasPages())

                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $forms->links() }}
                    </div>

                @endif


            @else

                {{-- Empty State --}}
                <div class="py-20 px-6 text-center">

                    <div class="mx-auto w-14 h-14 flex items-center justify-center
                                rounded-full bg-gray-100 text-gray-500 text-2xl">
                        +
                    </div>

                    <h3 class="mt-4 text-lg font-semibold text-gray-900">
                        No forms yet
                    </h3>

                    <p class="mt-2 text-sm text-gray-500">
                        Create your first form and start collecting submissions.
                    </p>

                    <div class="mt-6">

                        <a
                            href="{{ route('forms.create') }}"
                            wire:navigate
                            class="inline-flex items-center px-4 py-2.5
                                   bg-gray-900 text-white text-sm font-medium
                                   rounded-lg hover:bg-gray-800 transition"
                        >
                            + Create your first form
                        </a>

                    </div>

                </div>

            @endif

        </div>

    </div>

</div>