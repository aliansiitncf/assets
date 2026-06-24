<x-layouts.guest>
    <x-slot name="title">403 Forbidden</x-slot>
    <div
        class="min-h-screen flex flex-col justify-center items-center bg-gradient-to-br from-blue-50 via-white to-gray-100">
        <div
            class="bg-white/80 backdrop-blur-sm p-10 rounded-2xl shadow-xl text-center border border-gray-200 max-w-md w-full">

            <div class="flex justify-center mb-6">
                <div class="w-20 h-20 flex items-center justify-center bg-red-100 rounded-full shadow-inner">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-red-600" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M18.364 5.636l-12.728 12.728m0-12.728l12.728 12.728" />
                    </svg>
                </div>
            </div>

            <h1 class="text-7xl font-extrabold text-red-600 mb-4 drop-shadow-sm">403</h1>
            <h2 class="text-2xl font-semibold text-gray-800 mb-2">Akses Dilarang</h2>
            <p class="text-gray-600 mb-8">
                @if(isset($exception) && $exception->getMessage())
                {{ $exception->getMessage() }}
                @else
                Maaf, Anda tidak memiliki izin untuk mengakses halaman ini.
                @endif</p>
            <div class="flex justify-center gap-3">
                <a href="{{ route('dashboard') }}"
                    class="px-6 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition">
                    Ke Beranda
                </a>
            </div>
        </div>

        <footer class="mt-10 text-sm text-gray-500">
            &copy; {{ date('Y') }} Sistem Manajemen Aset
        </footer>
    </div>
</x-layouts.guest>