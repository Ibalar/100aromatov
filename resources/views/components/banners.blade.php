@props(['banners' => []])

@if($banners->isNotEmpty())
    <div class="themesFlat">
        <div class="container">
            <div dir="ltr" class="swiper tf-swiper" data-preview="3" data-tablet="2" data-mobile-sm="2"
                 data-mobile="1" data-space-lg="30" data-space-md="20" data-space="10" data-pagination="1"
                 data-pagination-sm="2" data-pagination-md="2" data-pagination-lg="3">
                <div class="swiper-wrapper">
                    @foreach($banners as $banner)
                        <div class="swiper-slide">
                            <div class="box-image_v02 hover-img wow fadeInLeft" data-wow-delay="{{ $loop->index * 0.1 }}s">
                                <div class="box-image_img img-style">
                                    <img loading="lazy" width="450" height="280"
                                         src="{{ $banner->image_url }}" alt="{{ $banner->title ?? 'Banner' }}">
                                </div>
                                <div class="box-image_content">
                                    @php
                                        $isWhite = strtolower($banner->text_color) === '#ffffff';
                                        $textClass = $isWhite ? 'text-white link-underline-white' : 'link-underline-text';
                                    @endphp
                                    <div class="title h4 fw-medium {{ $textClass }}">
                                        {{ $banner->title }}
                                    </div>
                                </div>
                                <a href="{{ $banner->button_link ?? '#' }}" class="stretched-link" aria-label="{{ $banner->title }}"></a>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="sw-dot-default tf-sw-pagination"></div>
            </div>
        </div>
    </div>
@endif
