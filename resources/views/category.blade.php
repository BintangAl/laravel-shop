@extends('layouts.main')

@section('container')
@include('partials.nav')
<div id="app-field">
    <div class="container mt-4 overpass">
        <div class="bg-white p-3 text-main border-bottom mb-2 text-uppercase">Kategori : {{ $category }}</div>
        <div class="row">
            @foreach ($product as $item)
            <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-4">
                <div class="product position-relative" onclick="window.location='{{ route('product', [$item->id, strtolower(str_replace([' ', '/'],'_', $item->product_name))]) }}'">
                    <div class="box-product bg-white shadow-sm h-225 position-relative pointer">
                        <div class="product-image bg-primary h-150 w-100 bg-image" style="background-image: url({{ url($item->product_image) }})"></div>
                        <div class="product-name fs-xsmall p-2 sans" id="product-name{{ $item->id }}">{{ $item->product_name }}</div>
                        <div class="product-price position-absolute bottom-0 start-0 ps-2 text-main mb-1">{{ $item->product_price }}</div>
                    </div>
                    <div class="product-btn bg-main text-light fs-small text-center p-2 position-absolute w-100 pointer">BELI SEKARANG</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<div id="seacrh-field" style="display: none">
    <div class="container mt-4 overpass">
        <div class="bg-white p-3 text-main border-bottom mb-2 text-uppercase text-truncate">Search : <span id="value">Baju</span></div>
        <div class="row" id="product-search">
        </div>
    </div>
</div>

@include('partials.footer')
@endsection