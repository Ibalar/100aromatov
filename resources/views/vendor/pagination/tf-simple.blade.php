@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}">
        <div class="tf-page-pagination">
            @unless ($paginator->onFirstPage())
                <a class="pag-item" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="{{ __('pagination.previous') }}">
                    <i class="icon icon-CaretLeft"></i>
                </a>
            @endunless

            <span class="pag-item active">{{ $paginator->currentPage() }}</span>

            @if ($paginator->hasMorePages())
                <a class="pag-item" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="{{ __('pagination.next') }}">
                    <i class="icon icon-CaretRightThin"></i>
                </a>
            @endif
        </div>
    </nav>
@endif
