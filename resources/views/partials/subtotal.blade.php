<div class="p-3 border-gray-bottom-dash">
    <div class="d-flex justify-content-between">
        <div class="fs-small">Subtotal untuk Produk</div>
        <div class="fs-small fw-bold">Rp {{ number_format($subtotal) }}</div>
        <input type="hidden" name="sub_total" value="{{ $subtotal }}">
    </div>
    <div class="d-flex justify-content-between">
        <div class="fs-small">Total Ongkos Kirim</div>
        <div class="fs-small fw-bold">
            @if (count($delivery))
                Rp
                <span id="total_ongkir">{{ number_format($delivery[0]->costs[0]->cost[0]->value) }}</span>
            @else
                <span class="text-danger fw-normal">alamat tidak ditemukan!</span>
            @endif
        </div>
        <input type="hidden" name="total_ongkir"
            value="{{ count($delivery) ? $delivery[0]->costs[0]->cost[0]->value : '' }}">
    </div>
    <div class="d-flex justify-content-between">
        <div class="fs-small">Total Fee Pemabayaran</div>
        <div class="fs-small fw-bold">
            @if (count($channel))
                Rp
                <span id="payment_fee">{{ number_format($channel[2]->total_fee->flat) }}</span>
            @else
                <span class="text-danger fw-normal">error-500</span>
            @endif
        </div>
        <input type="hidden" name="payment_fee" value="{{ count($channel) ? $channel[2]->total_fee->flat : '' }}">
    </div>
</div>
<div class="px-3 py-2 border-bottom">
    <div class="d-flex justify-content-between align-items-center">
        <div class="sans fw-bold fs-5">Total</div>
        <div class="fs-4 text-main fw-bold sans">Rp <span id="total"></span></div>
        <input type="hidden" name="total" value="">
    </div>
</div>
