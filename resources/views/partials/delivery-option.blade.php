<div class="bg-white shadow-sm p-3 mb-3">
    <div class="d-flex justify-content-between mb-3">
        <div class="text-main"><i class="fa-solid fa-truck"></i> Opsi Pengiriman</div>
        <div class="text-primary pointer {{ count($delivery) ?: 'd-none' }}" data-bs-toggle="modal"
            data-bs-target="#deliveryOption">Ubah</div>
    </div>
    @if (count($delivery))
        <div class="d-flex justify-content-between">
            <div class="fw-bold text-uppercase" id="delivery_service">JNE {{ $delivery[0]->costs[0]->service }}
                ({{ $delivery[0]->costs[0]->description }})</div>
            <div class="text-main text-end"><span class="text-gray fs-small me-2">ongkir:</span> Rp <span
                    id="delivery_ongkir">{{ number_format($delivery[0]->costs[0]->cost[0]->value) }}</span></div>
        </div>
        <div>Estimasi <span id="delivery_etd">{{ $delivery[0]->costs[0]->cost[0]->etd }}</span> hari kerja.</div>
    @else
        <a href="{{ route('address') . "?link=$_SERVER[REQUEST_URI]" }}" class="text-danger fs-small">Alamat tidak
            ditemukan!</a>
    @endif
</div>


<!-- Modal -->
<div class="modal fade" id="deliveryOption" tabindex="-1" aria-labelledby="deliveryOptionLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-body">
                <div class="fw-bold mb-3 text-center">Pilih Opsi Pengiriman</div>
                <button type="button" class="btn-close position-absolute p-3 shadow-none top-0 end-0"
                    data-bs-dismiss="modal"></button>

                <div class="delivery-option">
                    @if (count($delivery))
                        @foreach ($delivery[0]->costs as $item)
                            <label for="delivery#{{ $item->service }}"
                                class="bg-light w-100 pointer shadow-sm sans align-items-center mb-3 px-2 py-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="delivery"
                                        id="delivery#{{ $item->service }}"
                                        value="{{ $item->service }}#{{ $item->description }}#{{ $item->cost[0]->value }}#{{ $item->cost[0]->etd }}"
                                        {{ $item->service == $delivery[0]->costs[0]->service ? 'checked' : '' }}>
                                    <div class="mb-1">
                                        <div class="fw-bold"><span
                                                class="text-uppercase">{{ $item->service }}</span><span
                                                class="ms-2 text-main">Rp
                                                {{ number_format($item->cost[0]->value) }}</span></div>
                                        <div class="text-gray fs-small">estimasi {{ $item->cost[0]->etd }} hari kerja.
                                        </div>
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
