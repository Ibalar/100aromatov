@if ($paginator->hasPages())
    @php
        $lastPage = $paginator->lastPage();
        $pageItems = $lastPage <= 6
            ? range(1, $lastPage)
            : [1, 2, 3, '...', $lastPage - 2, $lastPage - 1, $lastPage];
    @endphp

    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}">
        <div class="tf-page-pagination">
            @unless ($paginator->onFirstPage())
                <a class="pag-item" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="{{ __('pagination.previous') }}">
                    <i class="icon icon-CaretLeft"></i>
                </a>
            @endunless

            @foreach ($pageItems as $item)
                @if ($item === '...')
                    <span class="pag-item disabled" aria-disabled="true">...</span>
                @elseif ($item == $paginator->currentPage())
                    <span class="pag-item active" aria-current="page">{{ $item }}</span>
                @else
                    <a class="pag-item" href="{{ $paginator->url($item) }}">{{ $item }}</a>
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a class="pag-item" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="{{ __('pagination.next') }}">
                    <i class="icon icon-CaretRightThin"></i>
                </a>
            @endif
        </div>
    </nav>
@endif
