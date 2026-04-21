@props(['slides' => []])

@if($slides->isNotEmpty())
    <div class="tf-slideshow tf-btn-swiper-main hover-sw-nav">
        <div dir="ltr" class="swiper tf-swiper sw-slide-show slider_effect_fade" data-loop="true" data-auto="true" data-speed="1000" data-effect="fade" data-delay="5000">
            <div class="swiper-wrapper">
                @foreach($slides as $slide)
                    <div class="swiper-slide">
                        <div class="slideshow-wrap">
                            <div class="sld_image">
                                <img loading="lazy"
                                     width="1920"
                                     height="730"
                                     src="{{ $slide->image_url }}"
                                     alt="{{ $slide->title ?? 'Slide image' }}">
                            </div>
                            <div class="sld_content">
                                <div class="container">
                                    <div class="content-sld_wrap">
                                        @if($slide->subtitle)
                                            <div class="heading">
                                                <p class="sub-text_sld text-body-1 fade-item fade-item-1 mb-15"
                                                   style="color: {{ $slide->text_color }};">
                                                    {!! nl2br($slide->subtitle) !!}
                                                </p>
                                                @if($slide->title)
                                                    <p class="title_sld text-display fw-medium fade-item fade-item-2"
                                                       style="color: {{ $slide->text_color }};">
                                                        {!! nl2br($slide->title) !!}
                                                    </p>
                                                @endif
                                            </div>
                                        @elseif($slide->title)
                                            <div class="heading">
                                                <p class="title_sld text-display fw-medium fade-item fade-item-2"
                                                   style="color: {{ $slide->text_color }};">
                                                    {!! nl2br($slide->title) !!}
                                                </p>
                                            </div>
                                        @endif

                                        @if($slide->hasButton())
                                            <div class="fade-item fade-item-3">
                                                <a href="{{ $slide->button_link }}" class="tf-btn btn-white">
                                                    Подробнее
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="sw-line-default tf-sw-pagination"></div>
        </div>
        <div class="group-nav-action">
            <div class="container-full">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="tf-sw-nav text-white link nav-prev-swiper">
                        <i class="icon icon-ArrowLongLeft"></i>
                    </div>
                    <div class="tf-sw-nav text-white link nav-next-swiper">
                        <i class="icon icon-ArrowLongRight"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
