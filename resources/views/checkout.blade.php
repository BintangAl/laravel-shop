@extends('layouts.main')

@section('container')
    @include('partials.alert-error', ['message' => 'Alamat tidak ditemukan!'])
    @include('partials.nav')

    <div id="app-field">
        <div class="bg-white p-3 text-main border-bottom mb-2">
            <div class="container">Checkout</div>
        </div>

        <div class="container mt-3 overpass">
            <form action="{{ count($address) ? route('transaction') : '' }}" method="post">
                @csrf
                @if ($cart_id != 0)
                    <input type="hidden" name="cart_id" value="{{ $cart_id }}">
                @endif
                @include('partials.address-option')
                @include('partials.product-ordered', [
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'product_image' => $product->image[0]->image,
                    'product_name' => $product->product_name,
                    'product_price' => $product->product_price,
                ])

                @include('partials.delivery-option')

                <div class="bg-white shadow-sm mb-3">
                    @include('partials.payment-method')
                    @include('partials.subtotal')
                    <div class="p-3 d-flex justify-content-end">
                        <button type="{{ count($address) ? 'submit' : 'button' }}" class="btn bg-main px-4 text-light"
                            onclick="{{ count($address) ? 'load()' : 'alertError()' }}">Buat Pesanan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @include('partials.search')

    @include('partials.footer')
@endsection
