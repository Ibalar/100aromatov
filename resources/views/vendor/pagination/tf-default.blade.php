@if ($paginator->hasPages())
    @php
        $lastPage = $paginator->lastPage();
        $currentPage = $paginator->currentPage();
        $allPages = range(1, $lastPage);
        $leadingPages = $lastPage <= 6
            ? range(1, $lastPage)
            : range($currentPage <= 2 ? 1 : $currentPage, min($currentPage <= 2 ? 3 : $currentPage + 2, $lastPage));
        $trailingPages = $lastPage <= 6
            ? []
            : range(max($lastPage - 2, 1), $lastPage);
        $pages = array_values(array_unique(array_merge($leadingPages, $trailingPages)));
        sort($pages);
        $pageItems = [];
        $previousPage = null;
        $choosePageLabel = "\u{0412}\u{044B}\u{0431}\u{0440}\u{0430}\u{0442}\u{044C} \u{0441}\u{0442}\u{0440}\u{0430}\u{043D}\u{0438}\u{0446}\u{0443}";
        $pageListLabel = "\u{0421}\u{043F}\u{0438}\u{0441}\u{043E}\u{043A} \u{0441}\u{0442}\u{0440}\u{0430}\u{043D}\u{0438}\u{0446}";

        foreach ($pages as $page) {
            if ($previousPage !== null && $page - $previousPage > 1) {
                $pageItems[] = '...';
            }

            $pageItems[] = $page;
            $previousPage = $page;
        }
    @endphp

    @once
        <style>
            .tf-page-pagination {
                position: relative;
            }

            .pag-ellipsis {
                position: relative;
            }

            .pag-ellipsis summary {
                list-style: none;
                cursor: pointer;
            }

            .pag-ellipsis summary::-webkit-details-marker {
                display: none;
            }

            .pag-ellipsis-panel {
                position: absolute;
                top: calc(100% + 8px);
                left: 50%;
                transform: translateX(-50%);
                min-width: 220px;
                max-width: min(80vw, 520px);
                padding: 10px;
                border: 1px solid rgba(24, 24, 24, 0.12);
                border-radius: 14px;
                background: #fff;
                box-shadow: 0 18px 40px rgba(24, 24, 24, 0.12);
                z-index: 20;
            }

            .pag-ellipsis-scroll {
                display: flex;
                gap: 8px;
                overflow-x: auto;
                padding-bottom: 4px;
                scrollbar-width: thin;
                cursor: grab;
                user-select: none;
            }

            .pag-ellipsis-scroll .pag-item {
                flex: 0 0 auto;
                cursor: inherit;
            }

            .pag-ellipsis-scroll.is-dragging {
                cursor: grabbing;
            }

            .pag-ellipsis-scroll::-webkit-scrollbar-thumb {
                cursor: grab;
            }
        </style>
    @endonce

    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}">
        <div class="tf-page-pagination">
            @unless ($paginator->onFirstPage())
                <a class="pag-item" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="{{ __('pagination.previous') }}">
                    <i class="icon icon-CaretLeft"></i>
                </a>
            @endunless

            @foreach ($pageItems as $item)
                @if ($item === '...')
                    <details class="pag-ellipsis">
                        <summary class="pag-item" aria-label="{{ $choosePageLabel }}">...</summary>
                        <div class="pag-ellipsis-panel">
                            <div class="pag-ellipsis-scroll" role="list" aria-label="{{ $pageListLabel }}" data-draggable-pagination>
                                @foreach ($allPages as $pageNumber)
                                    @if ($pageNumber === $currentPage)
                                        <span class="pag-item active" aria-current="page">{{ $pageNumber }}</span>
                                    @else
                                        <a class="pag-item" href="{{ $paginator->url($pageNumber) }}">{{ $pageNumber }}</a>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </details>
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

    @once
        <script>
            document.querySelectorAll('[data-draggable-pagination]').forEach((container) => {
                if (container.dataset.dragReady === '1') {
                    return;
                }

                container.dataset.dragReady = '1';

                let isDragging = false;
                let startX = 0;
                let startScrollLeft = 0;
                let moved = false;

                container.addEventListener('mousedown', (event) => {
                    if (event.button !== 0) {
                        return;
                    }

                    isDragging = true;
                    moved = false;
                    startX = event.pageX;
                    startScrollLeft = container.scrollLeft;
                    container.classList.add('is-dragging');
                });

                container.addEventListener('mousemove', (event) => {
                    if (!isDragging) {
                        return;
                    }

                    const distance = event.pageX - startX;

                    if (Math.abs(distance) > 4) {
                        moved = true;
                    }

                    container.scrollLeft = startScrollLeft - distance;
                });

                const stopDragging = () => {
                    isDragging = false;
                    container.classList.remove('is-dragging');
                };

                container.addEventListener('mouseleave', stopDragging);
                container.addEventListener('mouseup', stopDragging);
                window.addEventListener('mouseup', stopDragging);

                container.querySelectorAll('a').forEach((link) => {
                    link.addEventListener('click', (event) => {
                        if (moved) {
                            event.preventDefault();
                        }
                    });
                });
            });
        </script>
    @endonce
@endif
