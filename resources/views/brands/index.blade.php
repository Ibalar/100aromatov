@extends('layouts.app')

@section('content')

    <x-breadcrumbs
        title="Бренды"
        :items="[
        ['title' => 'Бренды']
    ]"
    />

    <!-- Search -->
    <div class="flat-spacing-3 page-search-inner">
        <div class="container">
            <div class="row">
                <div class="col-xl-6 mx-auto">
                    <form method="GET" class="form-search-nav style-2 mb-10">
                        <fieldset>
                            <input
                                type="text"
                                name="search"
                                value="{{ $search }}"
                                placeholder="Введите название бренда..."
                                required>
                        </fieldset>
                        <button type="submit" class="btn-action">
                            <i class="icon icon-MagnifyingGlass"></i>
                        </button>
                    </form>
                    <div class="tf-col-quicklink">
                        <span class="title fw-semibold">Быстрые ссылки:</span>                        &nbsp;
                        <a class="cl-text-2 link" href="/">для женщин</a>,
                        <a class="cl-text-2 link" href="/">для мужчин</a>,
                        <a class="cl-text-2 link" href="/">унисекс</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Search -->

    <div class="container">
        <div class="wd-full">
            <div class="tf-page-pagination justify-content-center flex-wrap">
                @foreach($letters as $letter)
                    <a href="#letter_{{ $letter }}" class="brand-letter-link pag-item">
                        {{ $letter }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>



    <div class="container flat-spacing-2">




        {{-- Список брендов --}}
        @foreach($grouped as $letter => $brands)

            <div class="row brand-letter-block flat-spacing-3">

                {{-- Буква --}}
                <div id="letter_{{ $letter }}" class="col-md-2 letter">
                    {{ $letter }}
                </div>

                {{-- Список брендов --}}
                <div class="col-md-10">
                    <div class="row">

                        @foreach($brands as $brand)

                            <div class="col-xs-6 col-sm-4 col-md-3 brend"
                                 data-h2="{{ $brand->name }}">

                                <a href="{{ route('brand.show', $brand->slug) }}" class="name-product lh-24 fw-medium link-underline-text py-4">
                                    {{ $brand->name }}

                                    @if($brand->products_count ?? false)
                                        <sup>{{ $brand->products_count }}</sup>
                                    @endif

                                </a>

                            </div>

                        @endforeach

                    </div>
                </div>

                <div class="col-md-12 flat-spacing-3 pb-0">
                    <hr class="first">
                </div>

            </div>

        @endforeach

    </div>

@endsection
