<div class="bg-white shadow-sm p-3 mb-3">
    <div class="text-main mb-3"><i class="bi bi-cart-check-fill"></i> Produk Dipesan</div>
    <div class="bg-light p-2">
        <div class="d-flex">
            <input type="hidden" name="product_id" value="{{ $product_id }}">
            <input type="hidden" name="quantity" value="{{ $quantity }}">
            <img src="{{ $product_image }}" width="50px" class="me-2">
            <div class="ms-2 text-truncate">
                <span class="sans">{{ $product_name }}</span>
                <div class="text-main text-end text-md-start">Rp {{ number_format($product_price) }} <span class="text-gray fs-small">x {{ $quantity }}</span></div>
            </div>
        </div>
    </div>
</div>