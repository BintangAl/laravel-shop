@extends('layouts.main')

@section('container')
<div class="position-relative">
    @include('partials.nav')

    <div id="app-field">
        <div class="container mt-4 overpass">
            @include('partials.alert-success', [$message = 'Produk telah ditambahkan ke keranjang belanja'])
            @include('partials.alert-error', [$message = 'Terjadi Kesalahan!'])
    
            <div class="bg-white p-3 mb-4">
                <div class="row">
                    <div class="col-lg-4 col-12 mb-3">
                        <img src="{{ $product->product_image }}" class="img-fluid border">
                    </div>
                    <div class="col-lg-8 col-12">
                        <div class="mb-3">
                            <div class="sans fs-4 fw-bold mb-2">{{ $product->product_name }}</div>
                            <div class="text-main fs-3 fw-bold">Rp {{ number_format($product->product_price) }}</div>
                        </div>
    
                        <form action="{{ route('checkout', [$product->id, strtolower(str_replace([' ', '/'],'_', $product->product_name)), '0']) }}" method="get">
                            <div class="d-flex mb-3 align-items-center">
                                <div class="fs-small text-gray me-3">Kuantitas</div>
                                <div class="input-group input-group-sm w-101 me-3">
                                    <span class="input-group-text pointer" onclick="minQuantity()">-</span>
                                    <input type="number" class="form-control text-center" name="quantity" id="quantity" value="1">
                                    <span class="input-group-text pointer" onclick="addQuantity()">+</span>
                                </div>
                                <div class="fs-small text-gray">tersisa <span class="stok">{{ $product->product_stok }}</span> buah</div>
                            </div>
            
                            <div class="d-flex">
                                <div class="btn btn-sm fw-bold bg-main-50 border-main text-main me-3 rounded-1 p-2 px-3" id="add-cart" onclick="addCart('{{ $product->id }}')"><i class="fa-solid fa-cart-plus"></i> Masukan Keranjang</div>
                                <button type="submit" class="btn btn-sm fw-bold bg-main text-light rounded-1 p-2 px-3" onclick="load()">Beli Sekarang</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="bg-white p-3 text-main mb-2">DETAIL PRODUK</div>
            <div class="bg-white p-3">{!! $product->product_detail !!}</div>
        </div>
    </div>
    
    <div id="seacrh-field" style="display: none">
        <div class="container mt-4 overpass">
            <div class="bg-white p-3 text-main border-bottom mb-2 text-uppercase text-truncate">Search : <span id="value">Baju</span></div>
            <div class="row" id="product-search">
            </div>
        </div>
    </div>
</div>

@include('partials.footer')
@endsection