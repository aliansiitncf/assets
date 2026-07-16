@if ($paginator->hasPages())
    <div class="flex flex-col sm:flex-row items-center justify-between gap-3 mt-4">

        {{-- Info jumlah data --}}
        <p class="text-sm text-base-content/60 order-2 sm:order-1">
            Menampilkan
            <span class="font-medium text-base-content">{{ $paginator->firstItem() }}</span>
            -
            <span class="font-medium text-base-content">{{ $paginator->lastItem() }}</span>
            dari
            <span class="font-medium text-base-content">{{ $paginator->total() }}</span>
            data
        </p>

        {{-- Pagination --}}
        <div class="join shadow-sm order-1 sm:order-2">
            {{-- Previous --}}
            @if ($paginator->onFirstPage())
                <button disabled class="join-item btn btn-sm btn-ghost opacity-40 cursor-not-allowed">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="size-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                    </svg>
                </button>
            @else
                <button wire:click="previousPage" wire:loading.attr="disabled"
                    class="join-item btn btn-sm btn-ghost hover:btn-info transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="size-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                    </svg>
                </button>
            @endif

            {{-- Page Numbers --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <button disabled class="join-item btn btn-sm btn-ghost cursor-default">{{ $element }}</button>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <button class="join-item btn btn-sm btn-info text-white font-semibold pointer-events-none">
                                {{ $page }}
                            </button>
                        @else
                            <button wire:click="gotoPage({{ $page }})" wire:loading.attr="disabled"
                                class="join-item btn btn-sm btn-ghost hover:btn-info hover:text-white transition-colors">
                                {{ $page }}
                            </button>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next --}}
            @if ($paginator->hasMorePages())
                <button wire:click="nextPage" wire:loading.attr="disabled"
                    class="join-item btn btn-sm btn-ghost hover:btn-info transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="size-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                    </svg>
                </button>
            @else
                <button disabled class="join-item btn btn-sm btn-ghost opacity-40 cursor-not-allowed">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="size-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                    </svg>
                </button>
            @endif
        </div>
    </div>
@endif
