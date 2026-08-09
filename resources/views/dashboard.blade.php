<x-app-layout>

    <x-slot name="header">

        <div class="flex items-center justify-between">

            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Dashboard
                </h2>

                <p class="text-sm text-gray-500 mt-1">
                    Manage your forms and responses.
                </p>
            </div>

            <a
                href="{{ route('forms.create') }}"
                wire:navigate
                class="hidden sm:inline-flex items-center px-4 py-2 bg-black text-white rounded-lg hover:bg-gray-800 transition"
            >
                + Create Form
            </a>

        </div>

    </x-slot>


    @php

        $user = auth()->user();

        $forms = $user
            ->forms()
            ->withCount('submissions')
            ->latest()
            ->get();

        $totalForms = $forms->count();

        $publishedForms = $forms
            ->where('status', 'published')
            ->count();

        $draftForms = $forms
            ->where('status', 'draft')
            ->count();

        $totalSubmissions = $forms
            ->sum('submissions_count');

        $recentForms = $forms->take(5);

    @endphp


    <div class="py-8">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">


            {{-- Welcome --}}
            <div class="mb-8">

                <h1 class="text-2xl font-bold text-gray-900">
                    Welcome back, {{ $user->name }} 👋
                </h1>

                <p class="text-gray-600 mt-1">
                    Here's what's happening with your forms.
                </p>

            </div>


            {{-- Mobile Create Button --}}
            <div class="mb-6 sm:hidden">

                <a
                    href="{{ route('forms.create') }}"
                    wire:navigate
                    class="w-full inline-flex justify-center items-center px-4 py-3 bg-black text-white rounded-lg"
                >
                    + Create New Form
                </a>

            </div>


            {{-- Statistics --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">


                {{-- Total Forms --}}
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">

                    <div class="flex items-center justify-between">

                        <div>

                            <p class="text-sm text-gray-500">
                                Total Forms
                            </p>

                            <p class="text-3xl font-bold text-gray-900 mt-2">
                                {{ $totalForms }}
                            </p>

                        </div>

                        <div class="w-11 h-11 rounded-lg bg-gray-100 flex items-center justify-center">

                            <svg
                                class="w-6 h-6 text-gray-700"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 12h6m-6 4h6M5 4h14a2 2 0 012 2v12a2 2 0 01-2 2H5a2 2 0 01-2-2V6a2 2 0 012-2z"
                                />
                            </svg>

                        </div>

                    </div>

                </div>


                {{-- Published --}}
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">

                    <div class="flex items-center justify-between">

                        <div>

                            <p class="text-sm text-gray-500">
                                Published
                            </p>

                            <p class="text-3xl font-bold text-gray-900 mt-2">
                                {{ $publishedForms }}
                            </p>

                        </div>

                        <div class="w-11 h-11 rounded-lg bg-green-50 flex items-center justify-center">

                            <svg
                                class="w-6 h-6 text-green-600"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M5 13l4 4L19 7"
                                />
                            </svg>

                        </div>

                    </div>

                </div>


                {{-- Drafts --}}
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">

                    <div class="flex items-center justify-between">

                        <div>

                            <p class="text-sm text-gray-500">
                                Drafts
                            </p>

                            <p class="text-3xl font-bold text-gray-900 mt-2">
                                {{ $draftForms }}
                            </p>

                        </div>

                        <div class="w-11 h-11 rounded-lg bg-yellow-50 flex items-center justify-center">

                            <svg
                                class="w-6 h-6 text-yellow-600"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 8v4l3 3"
                                />
                            </svg>

                        </div>

                    </div>

                </div>


                {{-- Submissions --}}
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">

                    <div class="flex items-center justify-between">

                        <div>

                            <p class="text-sm text-gray-500">
                                Total Responses
                            </p>

                            <p class="text-3xl font-bold text-gray-900 mt-2">
                                {{ $totalSubmissions }}
                            </p>

                        </div>

                        <div class="w-11 h-11 rounded-lg bg-blue-50 flex items-center justify-center">

                            <svg
                                class="w-6 h-6 text-blue-600"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M17 20h5V4H2v16h5m10 0v-4H7v4m10 0H7"
                                />
                            </svg>

                        </div>

                    </div>

                </div>

            </div>


            {{-- Recent Forms --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm">

                <div class="px-6 py-5 border-b border-gray-200">

                    <div class="flex items-center justify-between">

                        <div>

                            <h2 class="text-lg font-semibold text-gray-900">
                                Recent Forms
                            </h2>

                            <p class="text-sm text-gray-500 mt-1">
                                Your recently created forms.
                            </p>

                        </div>

                        <a
                            href="{{ route('forms.index') }}"
                            wire:navigate
                            class="text-sm text-blue-600 hover:underline"
                        >
                            View all
                        </a>

                    </div>

                </div>


                @if ($recentForms->count())

                    <div class="overflow-x-auto">

                        <table class="w-full">

                            <thead class="bg-gray-50">

                                <tr>

                                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">
                                        Form
                                    </th>

                                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">
                                        Status
                                    </th>

                                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">
                                        Responses
                                    </th>

                                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">
                                        Created
                                    </th>

                                    <th class="text-right px-6 py-3 text-xs font-semibold text-gray-500 uppercase">
                                        Action
                                    </th>

                                </tr>

                            </thead>


                            <tbody>

                                @foreach ($recentForms as $form)

                                    <tr class="border-t border-gray-100 hover:bg-gray-50">

                                        <td class="px-6 py-4">

                                            <div class="font-medium text-gray-900">
                                                {{ $form->title }}
                                            </div>

                                            @if ($form->description)

                                                <div class="text-sm text-gray-500 mt-1 max-w-md truncate">
                                                    {{ $form->description }}
                                                </div>

                                            @endif

                                        </td>


                                        <td class="px-6 py-4">

                                            @if ($form->status === 'published')

                                                <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700">
                                                    Published
                                                </span>

                                            @else

                                                <span class="inline-flex px-2.5 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-700">
                                                    Draft
                                                </span>

                                            @endif

                                        </td>


                                        <td class="px-6 py-4 text-sm text-gray-700">
                                            {{ $form->submissions_count }}
                                        </td>


                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            {{ $form->created_at->format('d M Y') }}
                                        </td>


                                        <td class="px-6 py-4 text-right">

                                            <a
                                                href="{{ route('forms.edit', $form) }}"
                                                wire:navigate
                                                class="text-blue-600 hover:underline text-sm font-medium"
                                            >
                                                Manage
                                            </a>

                                        </td>

                                    </tr>

                                @endforeach

                            </tbody>

                        </table>

                    </div>

                @else

                    {{-- Empty State --}}
                    <div class="p-12 text-center">

                        <div class="w-14 h-14 mx-auto bg-gray-100 rounded-full flex items-center justify-center">

                            <svg
                                class="w-7 h-7 text-gray-500"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 12h6m-6 4h6M5 4h14a2 2 0 012 2v12a2 2 0 01-2 2H5a2 2 0 01-2-2V6a2 2 0 012-2z"
                                />
                            </svg>

                        </div>

                        <h3 class="mt-4 text-lg font-semibold text-gray-900">
                            No forms yet
                        </h3>

                        <p class="mt-1 text-sm text-gray-500">
                            Create your first form to start collecting responses.
                        </p>

                        <a
                            href="{{ route('forms.create') }}"
                            wire:navigate
                            class="inline-flex mt-5 px-4 py-2 bg-black text-white rounded-lg hover:bg-gray-800"
                        >
                            Create Your First Form
                        </a>

                    </div>

                @endif

            </div>

        </div>

    </div>

</x-app-layout>

