<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>AI Form Builder — Build Smarter Forms</title>

    <meta name="description"
          content="Create powerful forms manually, import them from Word and Excel, and generate forms faster with AI.">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-950 text-white antialiased">

    <!-- ========================================= -->
    <!-- NAVBAR -->
    <!-- ========================================= -->

    <header class="absolute inset-x-0 top-0 z-50">
        <nav class="mx-auto flex max-w-7xl items-center justify-between px-6 py-6 lg:px-8">

            <!-- Logo -->
            <a href="/" class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl
                            bg-gradient-to-br from-indigo-500 to-violet-600
                            shadow-lg shadow-indigo-500/20">
                    <svg class="h-6 w-6 text-white"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor"
                         stroke-width="1.8">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/>
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M9 5a3 3 0 016 0v1H9V5z"/>
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M9 12h6M9 16h4"/>
                    </svg>
                </div>

                <span class="text-lg font-bold tracking-tight">
                    AI Form Builder
                </span>
            </a>

            <!-- Desktop Navigation -->
            <div class="hidden items-center gap-8 md:flex">

                <a href="#features"
                   class="text-sm text-slate-300 transition hover:text-white">
                    Features
                </a>

                <a href="#how-it-works"
                   class="text-sm text-slate-300 transition hover:text-white">
                    How it works
                </a>

                <a href="#ai"
                   class="text-sm text-slate-300 transition hover:text-white">
                    AI Forms
                </a>

            </div>

            <!-- Auth -->
            <div class="flex items-center gap-3">

                @auth

                    <a href="{{ url('/dashboard') }}"
                       class="rounded-lg border border-white/10 px-4 py-2 text-sm
                              font-medium text-slate-200 transition hover:bg-white/10">
                        Dashboard
                    </a>

                @else

                    <a href="{{ route('login') }}"
                       class="hidden text-sm font-medium text-slate-300 transition hover:text-white sm:block">
                        Sign in
                    </a>

                    @if (Route::has('register'))
                        <a href="{{ route('register') }}"
                           class="rounded-lg bg-white px-4 py-2 text-sm font-semibold
                                  text-slate-950 transition hover:bg-slate-200">
                            Get started
                        </a>
                    @endif

                @endauth

            </div>

        </nav>
    </header>


    <!-- ========================================= -->
    <!-- HERO -->
    <!-- ========================================= -->

    <main>

        <section class="relative isolate overflow-hidden">

            <!-- Background glow -->
            <div class="absolute inset-x-0 top-0 -z-10 h-[700px] overflow-hidden">
                <div class="absolute left-1/2 top-[-200px]
                            h-[600px] w-[900px]
                            -translate-x-1/2
                            rounded-full
                            bg-indigo-600/20
                            blur-3xl">
                </div>

                <div class="absolute right-[-150px] top-[250px]
                            h-[400px] w-[400px]
                            rounded-full
                            bg-violet-600/10
                            blur-3xl">
                </div>
            </div>


            <div class="mx-auto max-w-7xl px-6 pb-24 pt-40 lg:px-8 lg:pb-32">

                <div class="grid items-center gap-16 lg:grid-cols-2">

                    <!-- Hero content -->
                    <div>

                        <div class="mb-6 inline-flex items-center gap-2 rounded-full
                                    border border-indigo-400/20
                                    bg-indigo-500/10
                                    px-4 py-2 text-sm text-indigo-300">

                            <span class="h-2 w-2 rounded-full bg-indigo-400"></span>

                            AI-powered form creation

                        </div>


                        <h1 class="max-w-3xl text-5xl font-bold leading-tight
                                   tracking-tight sm:text-6xl lg:text-7xl">

                            Build smarter forms
                            <span class="bg-gradient-to-r from-indigo-400
                                         via-violet-400 to-fuchsia-400
                                         bg-clip-text text-transparent">
                                with AI.
                            </span>

                        </h1>


                        <p class="mt-6 max-w-xl text-lg leading-8 text-slate-300">

                            Create powerful, editable forms in minutes.
                            Build from scratch, import Word or Excel files,
                            or turn a simple description into a complete form.

                        </p>


                        <!-- CTA -->
                        <div class="mt-10 flex flex-col gap-4 sm:flex-row">

                            @auth

                                <a href="{{ url('/forms/create') }}"
                                   class="group inline-flex items-center justify-center gap-2
                                          rounded-xl bg-indigo-500 px-6 py-3.5
                                          font-semibold text-white
                                          shadow-xl shadow-indigo-500/20
                                          transition hover:bg-indigo-400">

                                    Create a form

                                    <svg class="h-5 w-5 transition group-hover:translate-x-1"
                                         fill="none"
                                         viewBox="0 0 24 24"
                                         stroke="currentColor">
                                        <path stroke-linecap="round"
                                              stroke-linejoin="round"
                                              stroke-width="2"
                                              d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                    </svg>

                                </a>

                            @else

                                <a href="{{ route('login') }}"
                                   class="group inline-flex items-center justify-center gap-2
                                          rounded-xl bg-indigo-500 px-6 py-3.5
                                          font-semibold text-white
                                          shadow-xl shadow-indigo-500/20
                                          transition hover:bg-indigo-400">

                                    Start building

                                    <svg class="h-5 w-5 transition group-hover:translate-x-1"
                                         fill="none"
                                         viewBox="0 0 24 24"
                                         stroke="currentColor">
                                        <path stroke-linecap="round"
                                              stroke-linejoin="round"
                                              stroke-width="2"
                                              d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                    </svg>

                                </a>

                            @endauth


                            @auth

                                <a href="{{ url('/imports/create') }}"
                                   class="inline-flex items-center justify-center gap-2
                                          rounded-xl border border-white/10
                                          bg-white/5 px-6 py-3.5
                                          font-semibold text-white
                                          transition hover:bg-white/10">

                                    Import Word / Excel

                                </a>

                            @else

                                <a href="{{ route('login') }}"
                                   class="inline-flex items-center justify-center gap-2
                                          rounded-xl border border-white/10
                                          bg-white/5 px-6 py-3.5
                                          font-semibold text-white
                                          transition hover:bg-white/10">

                                    Import a document

                                </a>

                            @endauth

                        </div>


                        <!-- Trust text -->
                        <div class="mt-8 flex flex-wrap gap-x-6 gap-y-2 text-sm text-slate-400">

                            <span class="flex items-center gap-2">
                                <span class="text-emerald-400">✓</span>
                                Fully editable
                            </span>

                            <span class="flex items-center gap-2">
                                <span class="text-emerald-400">✓</span>
                                Schema-based
                            </span>

                            <span class="flex items-center gap-2">
                                <span class="text-emerald-400">✓</span>
                                Server-side validation
                            </span>

                        </div>

                    </div>


                    <!-- Product preview -->
                    <div class="relative">

                        <div class="absolute -inset-4 rounded-3xl
                                    bg-gradient-to-r from-indigo-500/20
                                    to-violet-500/20 blur-2xl">
                        </div>


                        <div class="relative overflow-hidden rounded-2xl
                                    border border-white/10
                                    bg-slate-900
                                    shadow-2xl shadow-black/40">

                            <!-- Browser bar -->
                            <div class="flex items-center gap-2 border-b
                                        border-white/10 bg-slate-900/80 px-5 py-4">

                                <span class="h-3 w-3 rounded-full bg-red-400/70"></span>
                                <span class="h-3 w-3 rounded-full bg-yellow-400/70"></span>
                                <span class="h-3 w-3 rounded-full bg-green-400/70"></span>

                                <div class="ml-4 h-7 flex-1 rounded-md bg-white/5"></div>

                            </div>


                            <div class="grid min-h-[440px] grid-cols-12">

                                <!-- Sidebar -->
                                <div class="col-span-3 border-r border-white/10
                                            bg-slate-950/60 p-4">

                                    <div class="mb-6 h-3 w-20 rounded bg-white/10"></div>

                                    <div class="space-y-3">

                                        <div class="rounded-lg bg-indigo-500/20 px-3 py-3">
                                            <div class="h-2 w-16 rounded bg-indigo-300/50"></div>
                                        </div>

                                        <div class="px-3 py-3">
                                            <div class="h-2 w-20 rounded bg-white/10"></div>
                                        </div>

                                        <div class="px-3 py-3">
                                            <div class="h-2 w-14 rounded bg-white/10"></div>
                                        </div>

                                    </div>

                                </div>


                                <!-- Form -->
                                <div class="col-span-9 p-6">

                                    <div class="mb-7 flex items-center justify-between">

                                        <div>
                                            <div class="h-4 w-40 rounded bg-white/20"></div>
                                            <div class="mt-2 h-2 w-28 rounded bg-white/10"></div>
                                        </div>

                                        <div class="rounded-lg bg-indigo-500 px-3 py-2">
                                            <div class="h-2 w-12 rounded bg-white/70"></div>
                                        </div>

                                    </div>


                                    <!-- Field -->
                                    <div class="mb-5">

                                        <div class="mb-2 h-2 w-20 rounded bg-white/20"></div>

                                        <div class="h-10 rounded-lg border border-white/10
                                                    bg-white/5">
                                        </div>

                                    </div>


                                    <!-- Field -->
                                    <div class="mb-5">

                                        <div class="mb-2 h-2 w-24 rounded bg-white/20"></div>

                                        <div class="h-10 rounded-lg border border-white/10
                                                    bg-white/5">
                                        </div>

                                    </div>


                                    <!-- Two fields -->
                                    <div class="grid grid-cols-2 gap-4">

                                        <div>
                                            <div class="mb-2 h-2 w-16 rounded bg-white/20"></div>
                                            <div class="h-10 rounded-lg border border-white/10
                                                        bg-white/5">
                                            </div>
                                        </div>

                                        <div>
                                            <div class="mb-2 h-2 w-20 rounded bg-white/20"></div>
                                            <div class="h-10 rounded-lg border border-white/10
                                                        bg-white/5">
                                            </div>
                                        </div>

                                    </div>


                                    <div class="mt-6 h-10 w-28 rounded-lg bg-indigo-500"></div>

                                </div>

                            </div>

                        </div>


                        <!-- Floating AI card -->
                        <div class="absolute -bottom-8 -left-8 hidden w-64
                                    rounded-2xl border border-white/10
                                    bg-slate-900/95 p-4 shadow-2xl
                                    backdrop-blur md:block">

                            <div class="flex items-center gap-3">

                                <div class="flex h-9 w-9 items-center justify-center
                                            rounded-lg bg-violet-500/20 text-violet-300">

                                    ✦

                                </div>

                                <div>
                                    <p class="text-xs font-semibold text-white">
                                        AI Form Generation
                                    </p>

                                    <p class="mt-1 text-[11px] text-slate-400">
                                        Creating your form...
                                    </p>
                                </div>

                            </div>

                            <div class="mt-4 h-1.5 overflow-hidden rounded-full bg-white/10">
                                <div class="h-full w-3/4 rounded-full bg-indigo-500"></div>
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </section>


        <!-- ========================================= -->
        <!-- FEATURES -->
        <!-- ========================================= -->

        <section id="features" class="border-y border-white/5 bg-slate-900/50">

            <div class="mx-auto max-w-7xl px-6 py-24 lg:px-8">

                <div class="mx-auto max-w-2xl text-center">

                    <p class="text-sm font-semibold uppercase tracking-widest text-indigo-400">
                        Everything you need
                    </p>

                    <h2 class="mt-3 text-3xl font-bold tracking-tight sm:text-4xl">
                        A modern form builder
                    </h2>

                    <p class="mt-4 text-slate-400">
                        Build, import, validate and manage forms from one place.
                    </p>

                </div>


                <div class="mt-16 grid gap-6 md:grid-cols-2 lg:grid-cols-3">


                    <!-- Feature -->
                    <div class="rounded-2xl border border-white/10
                                bg-white/[0.03] p-7 transition
                                hover:-translate-y-1 hover:border-indigo-400/30">

                        <div class="flex h-12 w-12 items-center justify-center
                                    rounded-xl bg-indigo-500/10 text-indigo-400">

                            ✦

                        </div>

                        <h3 class="mt-5 text-lg font-semibold">
                            AI-powered forms
                        </h3>

                        <p class="mt-3 text-sm leading-6 text-slate-400">
                            Turn a natural-language description into a complete
                            form with sensible fields and validations.
                        </p>

                    </div>


                    <!-- Feature -->
                    <div class="rounded-2xl border border-white/10
                                bg-white/[0.03] p-7 transition
                                hover:-translate-y-1 hover:border-indigo-400/30">

                        <div class="flex h-12 w-12 items-center justify-center
                                    rounded-xl bg-violet-500/10 text-violet-400">

                            ◈

                        </div>

                        <h3 class="mt-5 text-lg font-semibold">
                            Flexible form builder
                        </h3>

                        <p class="mt-3 text-sm leading-6 text-slate-400">
                            Add and configure different field types while
                            keeping your form structure fully editable.
                        </p>

                    </div>


                    <!-- Feature -->
                    <div class="rounded-2xl border border-white/10
                                bg-white/[0.03] p-7 transition
                                hover:-translate-y-1 hover:border-indigo-400/30">

                        <div class="flex h-12 w-12 items-center justify-center
                                    rounded-xl bg-emerald-500/10 text-emerald-400">

                            ↑

                        </div>

                        <h3 class="mt-5 text-lg font-semibold">
                            Word & Excel import
                        </h3>

                        <p class="mt-3 text-sm leading-6 text-slate-400">
                            Convert existing documents into editable forms
                            instead of rebuilding everything manually.
                        </p>

                    </div>


                    <!-- Feature -->
                    <div class="rounded-2xl border border-white/10
                                bg-white/[0.03] p-7 transition
                                hover:-translate-y-1 hover:border-indigo-400/30">

                        <div class="flex h-12 w-12 items-center justify-center
                                    rounded-xl bg-cyan-500/10 text-cyan-400">

                            ✓

                        </div>

                        <h3 class="mt-5 text-lg font-semibold">
                            Schema-based validation
                        </h3>

                        <p class="mt-3 text-sm leading-6 text-slate-400">
                            Keep your form structure consistent with a
                            single schema and server-side validation.
                        </p>

                    </div>


                    <!-- Feature -->
                    <div class="rounded-2xl border border-white/10
                                bg-white/[0.03] p-7 transition
                                hover:-translate-y-1 hover:border-indigo-400/30">

                        <div class="flex h-12 w-12 items-center justify-center
                                    rounded-xl bg-orange-500/10 text-orange-400">

                            ↗

                        </div>

                        <h3 class="mt-5 text-lg font-semibold">
                            File uploads
                        </h3>

                        <p class="mt-3 text-sm leading-6 text-slate-400">
                            Support file-based fields and document imports
                            with controlled upload handling.
                        </p>

                    </div>


                    <!-- Feature -->
                    <div class="rounded-2xl border border-white/10
                                bg-white/[0.03] p-7 transition
                                hover:-translate-y-1 hover:border-indigo-400/30">

                        <div class="flex h-12 w-12 items-center justify-center
                                    rounded-xl bg-pink-500/10 text-pink-400">

                            ≡

                        </div>

                        <h3 class="mt-5 text-lg font-semibold">
                            Submission management
                        </h3>

                        <p class="mt-3 text-sm leading-6 text-slate-400">
                            Store form responses in a structured way and
                            build workflows around collected data.
                        </p>

                    </div>

                </div>

            </div>

        </section>


        <!-- ========================================= -->
        <!-- AI SECTION -->
        <!-- ========================================= -->

        <section id="ai" class="relative overflow-hidden">

            <div class="mx-auto max-w-7xl px-6 py-24 lg:px-8">

                <div class="grid items-center gap-16 lg:grid-cols-2">


                    <div>

                        <p class="text-sm font-semibold uppercase tracking-widest text-indigo-400">
                            AI-powered workflow
                        </p>

                        <h2 class="mt-4 text-3xl font-bold tracking-tight sm:text-4xl">
                            Describe your form.
                            <br>
                            Let AI do the structure.
                        </h2>

                        <p class="mt-6 leading-7 text-slate-400">
                            Instead of starting with a blank canvas, describe
                            what you need in plain language. The form structure
                            can then be generated and edited like a normal form.
                        </p>


                        <div class="mt-8 space-y-5">

                            <div class="flex gap-4">

                                <div class="mt-1 flex h-6 w-6 shrink-0 items-center
                                            justify-center rounded-full bg-indigo-500/20
                                            text-xs text-indigo-300">
                                    1
                                </div>

                                <div>
                                    <h3 class="font-semibold">
                                        Describe your requirement
                                    </h3>

                                    <p class="mt-1 text-sm text-slate-400">
                                        Explain the form you want using normal language.
                                    </p>
                                </div>

                            </div>


                            <div class="flex gap-4">

                                <div class="mt-1 flex h-6 w-6 shrink-0 items-center
                                            justify-center rounded-full bg-indigo-500/20
                                            text-xs text-indigo-300">
                                    2
                                </div>

                                <div>
                                    <h3 class="font-semibold">
                                        Generate the structure
                                    </h3>

                                    <p class="mt-1 text-sm text-slate-400">
                                        Fields, labels, options and validations can
                                        be generated from the description.
                                    </p>
                                </div>

                            </div>


                            <div class="flex gap-4">

                                <div class="mt-1 flex h-6 w-6 shrink-0 items-center
                                            justify-center rounded-full bg-indigo-500/20
                                            text-xs text-indigo-300">
                                    3
                                </div>

                                <div>
                                    <h3 class="font-semibold">
                                        Edit and publish
                                    </h3>

                                    <p class="mt-1 text-sm text-slate-400">
                                        Review the generated form and customize it
                                        before publishing.
                                    </p>
                                </div>

                            </div>

                        </div>

                    </div>


                    <!-- AI prompt card -->
                    <div class="rounded-2xl border border-white/10
                                bg-slate-900 p-6 shadow-2xl">

                        <div class="flex items-center justify-between">

                            <div>
                                <p class="text-sm font-semibold">
                                    Generate with AI
                                </p>

                                <p class="mt-1 text-xs text-slate-500">
                                    Describe the form you need
                                </p>
                            </div>

                            <div class="flex h-9 w-9 items-center justify-center
                                        rounded-lg bg-indigo-500/10 text-indigo-400">
                                ✦
                            </div>

                        </div>


                        <div class="mt-6 rounded-xl border border-white/10
                                    bg-slate-950 p-4">

                            <p class="text-sm leading-6 text-slate-300">
                                Create an internship application form with
                                personal information, education history,
                                skills, experience and resume upload.
                            </p>

                        </div>


                        <button type="button"
                                class="mt-4 flex w-full items-center justify-center
                                       gap-2 rounded-xl bg-indigo-500 py-3
                                       text-sm font-semibold transition
                                       hover:bg-indigo-400">

                            <span>Generate form</span>
                            <span>✦</span>

                        </button>


                        <div class="mt-6 space-y-3">

                            <div class="flex items-center justify-between
                                        rounded-lg bg-white/[0.03] px-4 py-3">

                                <span class="text-sm text-slate-300">
                                    Personal Information
                                </span>

                                <span class="text-xs text-emerald-400">
                                    Ready
                                </span>

                            </div>

                            <div class="flex items-center justify-between
                                        rounded-lg bg-white/[0.03] px-4 py-3">

                                <span class="text-sm text-slate-300">
                                    Education
                                </span>

                                <span class="text-xs text-emerald-400">
                                    Ready
                                </span>

                            </div>

                            <div class="flex items-center justify-between
                                        rounded-lg bg-white/[0.03] px-4 py-3">

                                <span class="text-sm text-slate-300">
                                    Skills & Experience
                                </span>

                                <span class="text-xs text-emerald-400">
                                    Ready
                                </span>

                            </div>

                            <div class="flex items-center justify-between
                                        rounded-lg bg-white/[0.03] px-4 py-3">

                                <span class="text-sm text-slate-300">
                                    Resume Upload
                                </span>

                                <span class="text-xs text-emerald-400">
                                    Ready
                                </span>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </section>


        <!-- ========================================= -->
        <!-- HOW IT WORKS -->
        <!-- ========================================= -->

        <section id="how-it-works" class="border-y border-white/5 bg-slate-900/50">

            <div class="mx-auto max-w-7xl px-6 py-24 lg:px-8">

                <div class="mx-auto max-w-2xl text-center">

                    <p class="text-sm font-semibold uppercase tracking-widest text-indigo-400">
                        Simple workflow
                    </p>

                    <h2 class="mt-3 text-3xl font-bold sm:text-4xl">
                        From idea to form in three steps
                    </h2>

                </div>


                <div class="mt-16 grid gap-8 md:grid-cols-3">


                    <div class="relative">

                        <div class="mb-6 flex h-14 w-14 items-center justify-center
                                    rounded-2xl bg-indigo-500/10 text-xl font-bold
                                    text-indigo-400">
                            01
                        </div>

                        <h3 class="text-xl font-semibold">
                            Create or import
                        </h3>

                        <p class="mt-3 text-sm leading-6 text-slate-400">
                            Start from a blank form or convert an existing
                            Word or Excel document.
                        </p>

                    </div>


                    <div class="relative">

                        <div class="mb-6 flex h-14 w-14 items-center justify-center
                                    rounded-2xl bg-violet-500/10 text-xl font-bold
                                    text-violet-400">
                            02
                        </div>

                        <h3 class="text-xl font-semibold">
                            Customize
                        </h3>

                        <p class="mt-3 text-sm leading-6 text-slate-400">
                            Configure fields, labels, options, required
                            states and validation rules.
                        </p>

                    </div>


                    <div class="relative">

                        <div class="mb-6 flex h-14 w-14 items-center justify-center
                                    rounded-2xl bg-emerald-500/10 text-xl font-bold
                                    text-emerald-400">
                            03
                        </div>

                        <h3 class="text-xl font-semibold">
                            Publish & collect
                        </h3>

                        <p class="mt-3 text-sm leading-6 text-slate-400">
                            Publish your form and start collecting structured
                            submissions.
                        </p>

                    </div>

                </div>

            </div>

        </section>


        <!-- ========================================= -->
        <!-- CTA -->
        <!-- ========================================= -->

        <section>

            <div class="mx-auto max-w-7xl px-6 py-24 lg:px-8">

                <div class="relative overflow-hidden rounded-3xl
                            border border-indigo-400/20
                            bg-gradient-to-br from-indigo-600/20
                            via-violet-600/10
                            to-slate-900
                            px-6 py-16 text-center sm:px-16">

                    <div class="absolute left-1/2 top-0
                                h-48 w-96 -translate-x-1/2
                                rounded-full bg-indigo-500/20 blur-3xl">
                    </div>

                    <div class="relative">

                        <h2 class="text-3xl font-bold sm:text-4xl">
                            Ready to build your next form?
                        </h2>

                        <p class="mx-auto mt-4 max-w-xl text-slate-400">
                            Create a form from scratch or turn your existing
                            documents into editable forms.
                        </p>


                        <div class="mt-8">

                            @auth

                                <a href="{{ url('/forms/create') }}"
                                   class="inline-flex items-center rounded-xl
                                          bg-white px-6 py-3.5
                                          font-semibold text-slate-950
                                          transition hover:bg-slate-200">

                                    Create your first form

                                </a>

                            @else

                                <a href="{{ route('login') }}"
                                   class="inline-flex items-center rounded-xl
                                          bg-white px-6 py-3.5
                                          font-semibold text-slate-950
                                          transition hover:bg-slate-200">

                                    Get started

                                </a>

                            @endauth

                        </div>

                    </div>

                </div>

            </div>

        </section>

    </main>


    <!-- ========================================= -->
    <!-- FOOTER -->
    <!-- ========================================= -->

    <footer class="border-t border-white/5">

        <div class="mx-auto flex max-w-7xl flex-col gap-4
                    px-6 py-8 sm:flex-row sm:items-center
                    sm:justify-between lg:px-8">

            <div class="flex items-center gap-2">

                <div class="flex h-8 w-8 items-center justify-center
                            rounded-lg bg-indigo-500/20 text-indigo-400">
                    ✦
                </div>

                <span class="text-sm font-semibold">
                    AI Form Builder
                </span>

            </div>


            <p class="text-sm text-slate-500">
                Built with Laravel, Livewire & MySQL.
            </p>


            <p class="text-sm text-slate-500">
                © {{ date('Y') }} AI Form Builder
            </p>

        </div>

    </footer>

</body>
</html>