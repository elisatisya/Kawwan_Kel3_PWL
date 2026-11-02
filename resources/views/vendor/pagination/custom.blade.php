@if ($paginator->hasPages())
    <nav class="admin-pagination" role="navigation" aria-label="Navigasi halaman">
        {{-- Previous --}}
        @if ($paginator->onFirstPage())
            <span class="admin-page-btn is-disabled" aria-disabled="true">Sebelumnya</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="admin-page-btn" rel="prev" aria-label="Halaman sebelumnya">Sebelumnya</a>
        @endif

        {{-- Page Numbers --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="admin-page-btn is-dots">{{ $element }}</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="admin-page-btn is-active" aria-current="page">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="admin-page-btn" aria-label="Halaman {{ $page }}">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="admin-page-btn" rel="next" aria-label="Halaman berikutnya">Selanjutnya</a>
        @else
            <span class="admin-page-btn is-disabled" aria-disabled="true">Selanjutnya</span>
        @endif
    </nav>
@endif
