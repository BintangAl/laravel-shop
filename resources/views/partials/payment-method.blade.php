<div class="p-3 border-bottom">
    <div class="d-flex justify-content-between mb-3">
        <div class="text-main"><i class="fa-solid fa-money-bill-1-wave"></i> Metode Pembayaran</div>
        <div class="text-primary pointer {{ (count($channel) ?: 'd-none') }}" data-bs-toggle="collapse" href="#collapsePayment" role="button" aria-expanded="false" aria-controls="collapsePayment">Ubah</div>
    </div>
    <div class="mb-1">
        @if (count($channel))
        <div class="d-flex justify-content-between">
            <img src="{{ $channel[2]->icon_url }}" id="channel_icon" width="100px">
            <div class="text-main text-end"><span class="text-gray fs-small me-2">fee:</span> Rp <span id="channel_fee">{{ number_format($channel[2]->total_fee->flat) }}</span></div>
        </div>
        <div class="fw-bold sans text-uppercase" id="channel_name">{{ $channel[2]->name }}</div>
        <div class="text-gray fs-small">Pembayaran dengan cara transfer ke nomor rekening virtual dari bank <span id="channel_bank">{{ explode(' ', $channel[2]->name)[0] }}</span></div>
        @endif
    </div>
    <div class="row collapse" id="collapsePayment">
        @if (count($channel))
            @foreach ($channel as $item)
            @if ($item->active)
                @if ($item->group == 'Virtual Account')
                <div class="col-lg-4 col-12 mb-2">
                    <label class="bg-light shadow-sm pointer p-3 w-100" for="{{ $item->code }}">
                        <div class="form-check align-items-center">
                            <input class="form-check-input" type="radio" name="payment" id="{{ $item->code }}" value="{{ $item->code }}#{{ $item->name }}#{{ $item->total_fee->flat }}#{{ $item->icon_url }}" {{ ($item->code == $channel[2]->code) ? 'checked' : '' }}>
                            <div class="d-flex justify-content-between">
                                <img src="{{ $item->icon_url }}" width="60px" >
                                <div class="text-gray fs-xsmall">fee : <span>Rp {{ number_format($item->total_fee->flat) }}</span></div>
                            </div>
                        </div>
                    </label>
                </div>
                @endif
            @endif
            @endforeach
        @endif
    </div>
</div>