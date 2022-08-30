<div class="container mt-4 overpass">
    <div class="bg-white p-3 text-main border-bottom mb-2">REKOMENDASI</div>
    <div class="row">
        @foreach ($recomendation as $item)
        <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-4">
            <div class="product position-relative" onclick="window.location='{{ route('product', [$item->id, strtolower(str_replace([' ', '/'],'_', $item->product_name))]) }}'">
                <div class="box-product bg-white shadow-sm h-225 position-relative pointer placeholder-glow">
                    <div class="product-image bg-white h-150 w-100 bg-image" style="background-image: url({{ url($item->product_image) }})"></div>
                    <div class="product-name fs-xsmall p-2 sans" id="product-name{{ $item->id }}">{{ $item->product_name }}</div>
                    <div class="product-price position-absolute bottom-0 start-0 ps-2 text-main mb-1">Rp {{ number_format($item->product_price) }}</div>
                </div>
                <div class="product-btn bg-main text-light fs-small text-center p-2 position-absolute w-100 pointer">BELI SEKARANG</div>
            </div>
        </div>
        @endforeach
    </div>
</div>