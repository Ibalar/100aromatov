@extends('layouts.app')

@section('content')

    <x-breadcrumbs
        title="Бренды"
        :items="[
        ['title' => 'Бренды']
    ]"
    />

    <!-- Search -->
    <div class="flat-spacing page-search-inner">
        <div class="container">
            <div class="row">
                <div class="col-xl-6 mx-auto">
                    <form action="search-result.html" class="form-search-nav style-2 mb-10">
                        <fieldset>
                            <input type="text" placeholder="Searching..." required>
                        </fieldset>
                        <button type="submit" class="btn-action">
                            <i class="icon icon-MagnifyingGlass"></i>
                        </button>
                    </form>
                    <div class="tf-col-quicklink">
                        <span class="title fw-semibold">Quick link:</span>
                        &nbsp;
                        <a class="cl-text-2 link" href="shop-default.html">Fashion</a>,
                        <a class="cl-text-2 link" href="shop-default.html">Men</a>,
                        <a class="cl-text-2 link" href="shop-default.html">Women</a>,
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
                    <a href="#letter-{{ $letter }}" class="pag-item">{{ $letter }}</a>
                @endforeach
            </div>
        </div>
    </div>

    <div class="container">

        <h1>Бренды</h1>

        {{-- Поиск --}}
        <form method="GET" class="brand-search">
            <input
                type="text"
                name="search"
                value="{{ $search }}"
                placeholder="Поиск бренда..."
            >
            <button type="submit">Найти</button>
        </form>

        {{-- Быстрый переход по буквам --}}


        {{-- Список брендов --}}
        <div class="brand-list">

            @foreach($grouped as $letter => $brands)
                <div class="brand-letter-block" id="letter-{{ $letter }}">

                    <h2>{{ $letter }}</h2>

                    <ul>
                        @foreach($brands as $brand)
                            <li>
                                <a href="{{ route('brand.show', $brand->slug) }}">
                                    {{ $brand->name }}
                                </a>
                            </li>
                        @endforeach
                    </ul>

                </div>
            @endforeach

        </div>

    </div>

@endsection
