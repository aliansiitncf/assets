<div
    class="min-h-screen flex items-center justify-center bg-gradient-to-br from-orange-300 via-amber-400 to-yellow-200 relative overflow-hidden p-6">

    <div class="relative w-full max-w-md">
            {{-- Error --}}
    @if (session()->has('error'))
    <div class="alert alert-error shadow-lg mb-4"> <svg xmlns="http://www.w3.org/2000/svg" fill="none"
            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
        </svg>
        <div> <span>{{ session('error') }}</span> </div>
    </div>
    @endif
        <div class="backdrop-blur-xl bg-white/70 border border-white/40 shadow-2xl rounded-3xl p-8">

            {{-- Logo --}}
            <div class="text-center mb-6">
                <img src="{{ asset('img/logo.png') }}" class="w-20 md:w-30 mx-auto" alt="Logo">

                <h1 class="mt-4 text-3xl font-bold text-gray-800">
                    Welcome Back
                </h1>

                <p class="text-sm text-gray-600 mt-1">
                    Sign in to your account
                </p>
            </div>
            {{-- Form --}}
            <form wire:submit.prevent="login" class="space-y-5">
                {{-- Email --}}
                <div>
                    <label class="text-sm text-gray-700">Username</label>
                    <input type="text" wire:model="name" placeholder="your username" autocomplete="name"
                        class="w-full mt-1 px-4 py-3 text-black rounded-xl border border-gray-300 bg-white focus:ring-2 focus:ring-orange-400 focus:outline-none transition duration-300" />
                    @error('name')
                    <span class="text-red-500 text-xs">
                        {{ $message }}
                    </span>
                    @enderror
                </div>

                {{-- Password --}}
                <div x-data="{ show: false }" class="relative">

                    <label class="text-sm text-gray-700">Password</label>

                    <input :type="show ? 'text' : 'password'" wire:model="password" placeholder="••••••••" autocomplete="current-password"
                        class="w-full mt-1 px-4 py-3 text-gray-700 rounded-xl border border-gray-300 bg-white focus:ring-2 focus:ring-orange-400 focus:outline-none transition duration-300" />

                    {{-- Toggle Button --}}
                    <button type="button" @click="show = !show"
                        class="absolute right-3 top-[42px] text-gray-500 hover:text-orange-500">

                        {{-- Eye Icon --}}
                        <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.75-7.5 9.75-7.5S21.75 12 21.75 12
                     18 19.5 12 19.5 2.25 12 2.25 12Z" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 15.75A3.75 3.75 0 1 0 12 8.25a3.75 3.75 0 0 0 0 7.5Z" />
                        </svg>

                        {{-- Eye Off --}}
                        <svg x-show="show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 3l18 18M10.584 10.587a3.75 3.75 0 1 0 5.303 5.303" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.88 5.073A9.953 9.953 0 0 1 12 4.5
                     c6 0 9.75 7.5 9.75 7.5
                     a15.66 15.66 0 0 1-4.293 5.774
                     M6.223 6.223A15.91 15.91 0 0 0 2.25 12
                     s3.75 7.5 9.75 7.5
                     a9.953 9.953 0 0 0 3.727-.707" />
                        </svg>

                    </button>

                    @error('password')
                    <span class="text-red-500 text-xs">
                        {{ $message }}
                    </span>
                    @enderror
                </div>

                {{-- Remember --}}
                <div class="flex justify-between items-center text-sm">
                    <label class="flex items-center gap-2 cursor-pointer text-gray-600">
                        <input type="checkbox" wire:model="remember"
                            class="rounded text-orange-500 focus:ring-orange-400">
                        Remember me
                    </label>
                </div>

                {{-- Button --}}
                <button type="submit" wire:loading.attr="disabled" class="w-full py-3 rounded-xl
                               bg-gradient-to-r from-orange-500 to-amber-500
                               text-white font-semibold
                               hover:scale-[1.02]
                               transition duration-300  
                               shadow-lg">

                    <span wire:loading.remove>Sign In</span>

                    <span wire:loading wire:target="login" class="loading loading-spinner loading-md">
                    </span>
                </button>

            </form>

        </div>

        {{-- Footer --}}
        <p class="text-center text-xs text-gray-600 mt-6">
            © {{ date('Y') }} Assets Management System
        </p>

    </div>
</div>