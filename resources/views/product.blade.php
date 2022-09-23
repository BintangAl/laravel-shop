@extends('layouts.main')


@section('jsHead')
    @if (session()->has('action'))
        <script>
            $(document).ready(function() {
                addCart("{{ session('product_id') }}", "{{ session('action') }}", "{{ session('quantity') }}",
                    "{{ session('size') }}", "{{ session('color') }}")
            });
        </script>
    @endif
@endsection

@section('container')
    <div class="position-relative">
        @include('partials.nav')

        <div id="app-field">
            <div class="container mt-4 overpass">
                @include('partials.alert-success', [
                    ($message = 'Produk telah ditambahkan ke keranjang belanja'),
                ])
                @include('partials.alert-error', [($message = 'Terjadi Kesalahan!')])

                <div class="bg-white p-3 mb-4">
                    <div class="row">
                        <div class="col-lg-4 col-12 mb-3 text-center">
                            @if (count($image) > 1)
                                @include('partials.product-image')
                            @else
                                <img src="{{ $image[0]->image }}" class="img-fluid border">
                            @endif
                        </div>
                        <div class="col-lg-8 col-12">
                            <div class="mb-3">
                                <div class="sans fs-4 fw-bold mb-2">{{ $product->product_name }}</div>
                                <div class="text-main fs-3 fw-bold" id="productPrice">Rp
                                    {{ number_format($product->product_price) }}</div>
                            </div>

                            <form action="" method="get">
                                @if (count($product->size))
                                    @php $sizes = json_decode($product->size); @endphp
                                    <div class="{{ count($sizes) > 5 ? 'd-block' : 'd-flex align-items-center' }} mb-3">
                                        <div class="fs-small text-gray me-3">Ukuran</div>
                                        <div class="{{ count($sizes) > 5 ? 'row' : 'd-flex align-items-center' }}">
                                            <input type="hidden" id="sizePrice" value="{{ $product->product_price }}">
                                            @foreach ($sizes as $item)
                                                <div class="{{ count($sizes) > 5 ? 'col-auto' : 'me-2' }}">
                                                    <input type="radio" class="btn-check" name="size"
                                                        onchange="productSize({{ $item->price }})"
                                                        id="size{{ $item->size }}" value="{{ $item->id }}"
                                                        {{ $loop->iteration == 1 ? 'checked' : '' }}>
                                                    <label class="btn btn-sm border-main shadow-none"
                                                        for="size{{ $item->size }}"
                                                        onclick="$('#sizePrice').val('{{ $item->price }}')">{{ $item->size }}</label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                @if (count($product->color))
                                    @php $colors = json_decode($product->color); @endphp
                                    <div class="{{ count($colors) > 4 ? 'd-block' : 'd-flex align-items-center' }} mb-3">
                                        <div class="fs-small text-gray me-3">Warna</div>
                                        <div class="{{ count($colors) > 4 ? 'row' : 'd-flex align-items-center' }}">
                                            <input type="hidden" id="colorPrice" value="{{ $product->product_price }}">
                                            @foreach ($colors as $item)
                                                <div class="{{ count($colors) > 4 ? 'col-auto mb-2 mb-md-0' : 'me-2' }}">
                                                    <input type="radio" class="btn-check" name="color"
                                                        id="color{{ $item->color }}" value="{{ $item->id }}"
                                                        {{ $loop->iteration == 1 ? 'checked' : '' }}
                                                        onchange="productColor({{ $item->price }})">
                                                    <label class="btn btn-sm border-main shadow-none p-1"
                                                        for="color{{ $item->color }}"
                                                        onclick="$('#colorPrice').val('{{ $item->price }}'); @if ($item->image) productImage('{{ $item->id }}') @endif">
                                                        @if ($item->image)
                                                            <img src="{{ url($item->image) }}" width="25px">
                                                        @endif
                                                        <span class="text-uppercase me-1">{{ $item->color }}</span>
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                <div class="d-flex mb-3 align-items-center">
                                    <div class="fs-small text-gray me-3">Kuantitas</div>
                                    <div class="input-group input-group-sm w-101 me-3">
                                        <span class="input-group-text pointer" onclick="minQuantity()">-</span>
                                        <input type="number" class="form-control text-center" name="quantity"
                                            id="quantity" value="1">
                                        <span class="input-group-text pointer" onclick="addQuantity()">+</span>
                                    </div>
                                    <div class="fs-small text-gray">tersisa <span
                                            class="stok">{{ $product->product_stok }}</span> buah</div>
                                </div>

                                <div class="d-flex mb-3">
                                    <div class="btn btn-sm fw-bold bg-main-50 border-main text-main me-3 rounded-1 p-2 px-3"
                                        id="add-cart" onclick="addCart('{{ $product->id }}')"><i
                                            class="fa-solid fa-cart-plus"></i> Masukan Keranjang</div>
                                    <button type="button" onclick="addCart('{{ $product->id }}', 'buyNow')"
                                        class="btn btn-sm fw-bold bg-main text-light rounded-1 p-2 px-3"
                                        onclick="load()">Beli Sekarang</button>
                                </div>

                                @if ($product->product_sold > 0)
                                    <div class="fs-small text-gray">{{ $product->product_sold }} Terjual</div>
                                @endif
                            </form>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-3 text-main mb-2">DETAIL PRODUK</div>
                <div class="bg-white p-3 mb-3">
                    @foreach ($detail as $key => $val)
                        {{ $val }}
                        @if ($key != 0)
                            <br>
                        @endif
                    @endforeach
                </div>

                <div class="bg-white p-3 text-main mb-2">REKOMENDASI</div>
                <div class="row mb-3">
                    @foreach ($recomendation as $item)
                        @if ($item->id != $product->id)
                            <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-4">
                                <div class="product position-relative"
                                    onclick="window.location='{{ route('product', [$item->id, strtolower(str_replace([' ', '/'], '_', $item->product_name))]) }}'">
                                    <div
                                        class="box-product bg-white shadow-sm h-225 position-relative pointer placeholder-glow">
                                        <div class="product-image bg-white h-150 w-100 bg-image"
                                            style="background-image: url({{ url($item->image[0]->image) }})"></div>
                                        <div class="fs-xsmall p-2 sans">
                                            {{ substr($item->product_name, 0, 27) . (strlen($item->product_name) > 27 ? '...' : '') }}
                                        </div>
                                        <div class="product-price position-absolute bottom-0 start-0 ps-2 text-main mb-1">Rp
                                            {{ number_format($item->product_price) }}</div>
                                    </div>
                                    <div
                                        class="product-btn bg-main text-light fs-small text-center p-2 position-absolute w-100 pointer">
                                        BELI SEKARANG</div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>

        @include('partials.search')
    </div>

    @include('partials.footer')
@endsection
