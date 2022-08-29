@extends('layouts.main')

@section('container')

@include('partials.alert-error', ['message' => 'Alamat tidak ditemukan!'])
@include('partials.nav')

<div id="app-field">
    <div class="bg-white p-3 text-main border-bottom mb-2"><div class="container">Checkout</div></div>

    <div class="container mt-3 overpass">
        <form action="{{ (count($address)) ? route('transaction') : '' }}" method="post">
            @csrf
            @if ($cart_id != 0) <input type="hidden" name="cart_id" value="{{ $cart_id }}"> @endif
            @include('partials.address-option')
            @include('partials.product-ordered', [
                'product_id' => $product->id, 
                'quantity' => $quantity, 
                'product_image' => $product->product_image,
                'product_name' => $product->product_name,
                'product_price' => $product->product_price
            ])
    
            @include('partials.delivery-option')
        
            <div class="bg-white shadow-sm mb-3">
                @include('partials.payment-method')
                @include('partials.subtotal')
                <div class="p-3 d-flex justify-content-end">
                    <button type="{{ (count($address)) ? 'submit' : 'button' }}" class="btn bg-main px-4 text-light" onclick="{{ (count($address)) ? 'load()' : 'alertError()' }}">Buat Pesanan</button>
                </div>
            </div>
        </form>
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