<div class="p-6">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold">
                My Forms
            </h1>

            <p class="text-gray-600 mt-1">
                Create and manage your forms.
            </p>
        </div>

        <a
            href="{{ route('forms.create') }}"
            class="px-4 py-2 bg-black text-white rounded-lg"
        >
            + Create Form
        </a>
    </div>

    @if (session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow overflow-hidden">

        <table class="w-full">

            <thead class="bg-gray-100">
                <tr>
                    <th class="text-left p-4">Title</th>
                    <th class="text-left p-4">Status</th>
                    <th class="text-left p-4">Created</th>
                    <th class="text-right p-4">Actions</th>
                </tr>
            </thead>

            <tbody>

                @forelse ($forms as $form)

                    <tr class="border-t">

                        <td class="p-4">
                            <div class="font-medium">
                                {{ $form->title }}
                            </div>

                            @if ($form->description)
                                <div class="text-sm text-gray-500">
                                    {{ $form->description }}
                                </div>
                            @endif
                        </td>

                        <td class="p-4">
                            <span class="px-2 py-1 text-xs rounded bg-gray-100">
                                {{ ucfirst($form->status) }}
                            </span>
                        </td>

                        <td class="p-4">
                            {{ $form->created_at->format('d M Y') }}
                        </td>

                        <td class="p-4 text-right space-x-2">

                            <a
                                href="{{ route('forms.edit', $form) }}"
                                class="text-blue-600"
                            >
                                Edit
                            </a>

                            <button
                                wire:click="delete({{ $form->id }})"
                                wire:confirm="Are you sure you want to delete this form?"
                                class="text-red-600"
                            >
                                Delete
                            </button>

                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="4" class="p-8 text-center text-gray-500">
                            No forms created yet.
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

    <div class="mt-4">
        {{ $forms->links() }}
    </div>

</div>