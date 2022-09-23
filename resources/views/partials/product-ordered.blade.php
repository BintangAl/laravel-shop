<div class="bg-white shadow-sm p-3 mb-3">
    <div class="text-main mb-3"><i class="bi bi-cart-check-fill"></i> Produk Dipesan</div>
    @foreach ($products as $item)
        <div class="bg-light p-2 mb-2 position-relative pointer"
            onclick="window.location='{{ route('product', [$item->product_detail->id, strtolower(str_replace([' ', '/'], '_', $item->product_detail->product_name))]) }}'">
            <div class="d-flex">
                <img src="{{ $item->product_detail->image[0]->image }}" width="50px" class="me-2">
                <div class="ms-2 text-truncate">
                    <span class="sans text-uppercase">{{ $item->product_detail->product_name }}
                        {{ $item->product_size ? '( ' . $item->product_size . ' )' : '' }}
                        {{ $item->product_color ? ' ( ' . $item->product_color . ' )' : '' }}
                    </span>
                    <div class="text-dark">Rp
                        {{ number_format($item->price) }} <span class="text-gray fs-small">x
                            {{ $item->quantity }}</span></div>

                    <div class="text-main position-absolute bottom-0 end-0 pe-3 pb-2">
                        Rp {{ number_format($item->price * $item->quantity) }}
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
