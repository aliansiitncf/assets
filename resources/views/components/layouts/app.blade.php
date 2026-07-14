<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Page Title' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.6.2/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.6.2/dist/js/tom-select.complete.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/pikaday/pikaday.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body>
    <div class="drawer lg:drawer-open bg-base-200">
        <input id="my-drawer-4" type="checkbox" class="drawer-toggle" checked />
        <div class="drawer-content h-screen lg:py-2 lg:pr-2 overflow-hidden ">
            <div class="flex w-full flex-col bg-base-100 h-full rounded-xl overflow-hidden">
                <div class="flex items-center gap-4 p-2 shrink-0">
                    <label for="my-drawer-4" aria-label="open sidebar" class="btn btn-square btn-ghost">
                        <!-- Sidebar toggle icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-linejoin="round"
                            stroke-linecap="round" stroke-width="2" fill="none" stroke="currentColor"
                            class="my-1.5 inline-block size-5">
                            <path d="M4 4m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z">
                            </path>
                            <path d="M9 4v16"></path>
                            <path d="M14 10l2 2l-2 2"></path>
                        </svg>
                    </label>
                    <x-breadcrumbs />

                    <div class="flex items-center gap-2 ml-auto">
                        <livewire:asset.asset-scanner />
                        <x-theme-toggle />
                    </div>
                </div>
                <hr class="border-slate-300 shrink-0">
                <!-- Page content here -->
                <div class="flex-1 p-4 overflow-y-auto no-scrollbar">
                    {{ $slot }}
                </div>
            </div>

        </div>
        @include('components.layouts.sidebar')
    </div>
    @livewireScripts
</body>

</html>
