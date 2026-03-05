<section class="section-page-title text-center flat-spacing-2 pb-0">
    <div class="container">
        <div class="main-page-title">

            <div class="breadcrumbs">

                <a href="{{ url('/') }}" class="text-caption-01 cl-text-3 link">
                    Главная
                </a>

                @foreach($items as $item)

                    <i class="icon icon-CaretRightThin cl-text-3"></i>

                    @if(!empty($item['url']))
                        <a href="{{ $item['url'] }}" class="text-caption-01 cl-text-3 link">
                            {{ $item['title'] }}
                        </a>
                    @else
                        <p class="text-caption-01">
                            {{ $item['title'] }}
                        </p>
                    @endif

                @endforeach

            </div>

            <h3>{{ $title }}</h3>

        </div>
    </div>
</section>
