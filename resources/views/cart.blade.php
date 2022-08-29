@extends('layouts.main')

@section('container')

@include('partials.alert-confirm', [ 'message' => 'Hapus product dari keranjang', 'confirm_btn' => 'Delete'])
@include('partials.nav')

<div id="app-field">
    <div class="bg-white p-3 text-main border-bottom mb-2"><div class="container">Keranjang Belanja</div></div>

    <div class="container mt-3 overpass">
        <div class="row">
            @foreach ($cart as $item)
            <div class="col-lg-4 col-md-6 col-12 mb-3" id="cart{{ $item->product_id }}">
                <form action="{{ route('checkout', [$item->product_id, strtolower(str_replace([' ', '/'],'_', $item->product_name)), $item->id]) }}" method="get">
                    <div class="bg-white shadow-sm p-2 d-flex w-100 position-relative pointer">
                        <a href="{{ route('product', [$item->product_id, strtolower(str_replace([' ', '/'],'_', $item->product_name))]) }}">
                            <div class="w-101 h-101 bg-image border" style="background-image: url({{ $item->product_image }})"></div>
                        </a>
                        <div class="ms-2 text-truncate position-relative">
                            <a href="{{ route('product', [$item->product_id, strtolower(str_replace([' ', '/'],'_', $item->product_name))]) }}" class="fw-bold text-dark">
                                {{ $item->product_name }}
                            </a>
                            <div class="text-main mb-3">Rp {{ number_format($item->product_price) }}</div>
        
                            <div class="input-group input-group-sm w-101 position-absolute bottom-0 start-0">
                                <span class="input-group-text pointer" id="min{{ $item->product_id  }}" onclick="minQuantityUpdate(this.id)">-</span>
                                <input type="number" class="form-control text-center shadow-none" name="quantity" id="quantity{{ $item->product_id  }}" value="{{ $item->product_quantity }}" readonly>
                                <span class="input-group-text pointer" id="add{{ $item->product_id  }}" onclick="addQuantityUpdate(this.id)">+</span>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-sm bg-main text-light position-absolute bottom-0 end-0 m-2" onclick="load()">Checkout</button>
                    </div>
                </form>
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
@endsection