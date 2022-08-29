@extends('layouts.main')

@section('container')
    @include('partials.nav')

    <div id="app-field">
        @include('partials.corousel')
        @include('partials.category')
        @include('partials.recomendation')
    
        {{-- @foreach ($category as $item)
            @include('partials.category-view', ['category' => $item->category])
        @endforeach --}}
    </div>

    <div id="seacrh-field" style="display: none">
        <div class="container mt-4 overpass">
            <div class="bg-white p-3 text-main border-bottom mb-2 text-uppercase text-truncate">Search : <span id="value">Baju</span></div>
            <div class="row" id="product-search">
            </div>
        </div>
    </div>

    <div class="mb-5"></div>
    @include('partials.footer')
@endsection