@if(Breadcrumbs::has())
<div class="breadcrumbs text-sm">
    <ul>
        @foreach (Breadcrumbs::current() as $crumb)
        <li>
            @if ($crumb->url() && !$loop->last)
            <a href="{{ $crumb->url() }}">
                {{ $crumb->title() }}
            </a>
            @else
            <span>{{ $crumb->title() }}</span>
            @endif
        </li>
        @endforeach
    </ul>
</div>
@endif