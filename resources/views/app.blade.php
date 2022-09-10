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

    @include('partials.search')

    <div class="mb-5"></div>
    @include('partials.footer')
@endsection
