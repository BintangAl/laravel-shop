@extends('layouts.main')

@section('container')
    @include('partials.alert-confirm', [
        'confirm_id' => 'confirm_delete_cart',
        'message' => 'Hapus product dari keranjang',
        'confirm_btn' => 'Delete',
    ])
    @include('partials.nav')

    <div id="app-field">
        <div class="bg-white p-3 text-main border-bottom mb-2">
            <div class="container">Keranjang Belanja</div>
        </div>

        <div class="container mt-3 overpass">
            <div class="row" id="cartList">
                @if (count($cart))
                    <form action="{{ route('checkout') }}" method="get" class="mb-5 pb-5">
                        @foreach ($cart as $item)
                            <div class="col-12 mb-3" id="cart{{ $item->id }}">
                                <div class="cart-item bg-white shadow-sm p-2 d-flex w-100 position-relative pointer">
                                    <div class="d-flex align-items-center me-2">
                                        <input type="checkbox" name="cart[]"
                                            class="cart custom-control-input pointer shadow-none border-main"
                                            id="cartCheck{{ $item->id }}" value="{{ $item->id }}"
                                            onchange="btnCheckoutReady()"
                                            @if (session('buyNow')) @if ($loop->iteration == 1)
                                                    checked @endif
                                            @endif>
                                    </div>
                                    <a
                                        href="{{ route('product', [$item->product_id, strtolower(str_replace([' ', '/'], '_', $item->product_detail->product_name))]) }}">
                                        <div class="w-101 h-101 bg-gray bg-image border"
                                            style="background-image: url({{ $item->product_detail->image[0]->image }})">
                                        </div>
                                    </a>
                                    <div class="ms-2 text-truncate position-relative">
                                        <a href="{{ route('product', [$item->product_id, strtolower(str_replace([' ', '/'], '_', $item->product_detail->product_name))]) }}"
                                            class="fw-bold text-dark">
                                            {{ $item->product_detail->product_name }}
                                        </a>
                                        <div class="text-main">Rp <span
                                                id="productPrice">{{ number_format($item->price) }}</span>
                                        </div>

                                        <div class="d-flex text-gray fs-small">
                                            @if (isset($item->product_size))
                                                <div class="me-2">Ukuran : {{ $item->product_size }}</div>
                                            @endif
                                            @if (isset($item->product_color))
                                                <div class="me-2">Warna : <span
                                                        class="text-uppercase">{{ $item->product_color }}</span></div>
                                            @endif
                                        </div>

                                        <div class="input-group input-group-sm w-101 position-absolute bottom-0 start-0">
                                            <span class="input-group-text pointer" id="min{{ $item->id }}"
                                                onclick="minQuantityUpdate(this.id, '#totalPriceCart{{ $item->id }}', {{ $item->price }})">-</span>
                                            <input type="number" class="form-control text-center shadow-none"
                                                id="quantity{{ $item->id }}" value="{{ $item->quantity }}" readonly>
                                            <span class="input-group-text pointer" id="add{{ $item->id }}"
                                                onclick="addQuantityUpdate(this.id, '#totalPriceCart{{ $item->id }}', {{ $item->product_detail->product_stok }})">+</span>
                                        </div>
                                    </div>
                                    <div class="text-main fw-bold position-absolute bottom-0 end-0 m-2">
                                        <span class="text-gray small fw-normal d-none d-md-inline-block">Total :</span> Rp
                                        <span
                                            class="total-price-cart @if (session('buyNow')) @if ($loop->iteration == 1)
                                            active @endif
                                    @endif"
                                            id="totalPriceCart{{ $item->id }}">{{ number_format($item->price * $item->quantity) }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        @if (count($cart))
                            <div class="position-fixed bottom-0 start-0 w-100" id="cartDetail">
                                <div class="bg-white p-3 border-top">
                                    <div class="container">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="d-flex alight-items-center">
                                                <input type="checkbox"
                                                    class="custom-control-input pointer shadow-none border-main me-2 d-none d-md-inline-block"
                                                    id="checkAll">
                                                <label class="pointer d-none d-md-inline-block" for="checkAll">Pilih Semua
                                                    ({{ count($cart) }})</label>
                                            </div>
                                            <div class="d-flex">
                                                <div class="me-3 text-end">
                                                    <div class="fs-6">
                                                        <i class="fa-solid fa-cart-shopping text-main"></i> <span
                                                            id="countCartCheck">0</span> Produk
                                                    </div>
                                                    <div class="fs-5 text-main">
                                                        Rp <span id="subTotalCart">
                                                            @if (session('buyNow'))
                                                                {{ number_format(session('buyNow')) }}
                                                            @else
                                                                0
                                                            @endif
                                                        </span>
                                                    </div>
                                                </div>

                                                <button type="button" disabled id="btnCheckout"
                                                    class="btn btn-lg px-3 bg-main text-light"
                                                    onclick="load()">Checkout</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </form>
                @else
                    <div class="text-center text-gray fw-bold"><i class="bi bi-cart-x-fill"></i> Keranjang Kosong</div>
                    <a href="{{ url('/') }}" class="text-center fs-small text-main fw-bold">Belanja Sekarang.</a>
                @endif
            </div>
        </div>
    </div>

    @include('partials.search')
@endsection
