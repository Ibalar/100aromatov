@extends('layouts.app')

@section('content')

    <x-breadcrumbs
        :title="$brand->name"
        :items="[
        ['title' => 'Бренды', 'url' => route('brands.index')],
        ['title' => $brand->name]
    ]"
    />

@endsection
