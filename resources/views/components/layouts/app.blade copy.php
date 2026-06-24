<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="corporate">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Page Title' }}</title>
    <script src="https://cdn.jsdelivr.net/npm/pikaday/pikaday.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

@livewireStyles

<body class="h-screen overflow-hidden bg-white">

    <div class="drawer lg:drawer-open h-screen overflow-hidden">

        <input id="my-drawer-4" type="checkbox" class="drawer-toggle" />

        <!-- MAIN AREA -->
        <div class="drawer-content flex flex-col h-screen overflow-hidden">

            <!-- NAVBAR (FIXED) -->
            @include('components.layouts.navbar')

            <!-- CONTENT AREA -->
            <div class="flex-1 flex overflow-hidden">

                <!-- CONTENT WRAPPER -->
                <div class="flex-1 overflow-hidden">
                    <div
                        class=" bg-orange-200 border-l-6 border-orange-600 md:rounded-tl-2xl h-full shadow flex flex-col overflow-hidden">

                        <!-- SCROLL AREA (SATU-SATUNYA YANG BOLEH SCROLL) -->
                        <div class="flex-1 overflow-y-auto p-3 no-scrollbar">
                            {{ $slot }}
                        </div>

                    </div>
                </div>
            </div>

        </div>

        <!-- SIDEBAR -->
        @include('components.layouts.sidebar')

    </div>


    @livewireScripts
</body>

</html>