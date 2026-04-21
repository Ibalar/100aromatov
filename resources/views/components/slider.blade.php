@props(['slides' => []])

@if($slides->isNotEmpty())
    <div class="tf-slideshow tf-btn-swiper-main hover-sw-nav">
        <div dir="ltr" class="swiper tf-swiper sw-slide-show slider_effect_fade" data-loop="true" data-effect="fade" data-delay="3000">
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

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Инициализация Swiper для слайдера
                const sliders = document.querySelectorAll('.tf-swiper.sw-slide-show');

                sliders.forEach(slider => {
                    const swiper = new Swiper(slider, {
                        loop: slider.dataset.loop === 'true',
                        effect: slider.dataset.effect || 'slide',
                        autoplay: {
                            delay: parseInt(slider.dataset.delay) || 3000,
                            disableOnInteraction: false,
                        },
                        pagination: {
                            el: slider.querySelector('.tf-sw-pagination'),
                            type: 'bullets',
                            clickable: true,
                        },
                        navigation: {
                            nextEl: slider.closest('.tf-slideshow').querySelector('.nav-next-swiper'),
                            prevEl: slider.closest('.tf-slideshow').querySelector('.nav-prev-swiper'),
                        },
                        fadeEffect: {
                            crossFade: true
                        }
                    });
                });
            });
        </script>
    @endpush

    @push('styles')
        <style>
            /* Дополнительные стили для анимации fade элементов */
            .fade-item {
                opacity: 0;
                transform: translateY(30px);
                animation: fadeInUp 0.6s ease forwards;
            }

            .fade-item-1 { animation-delay: 0.2s; }
            .fade-item-2 { animation-delay: 0.4s; }
            .fade-item-3 { animation-delay: 0.6s; }

            @keyframes fadeInUp {
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            /* Адаптивность для мобильных */
            @media (max-width: 768px) {
                .title_sld {
                    font-size: 32px !important;
                    line-height: 1.2 !important;
                }

                .sub-text_sld {
                    font-size: 14px !important;
                }

                .sld_image img {
                    height: 500px;
                    object-fit: cover;
                }
            }
        </style>
    @endpush
@endif
