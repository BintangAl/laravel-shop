<div class="bg-white shadow-sm overflow-auto mb-3" id="status_purchase" style="white-space: nowrap">
    <div class="d-inline-block text-center p-3 pointer w-110 {{ $status == 'all' ? 'border-main-bottom text-main' : '' }}"
        id="all" onclick="window.location='{{ route('purchase', ['all']) }}'">Semua</div>
    <div class="d-inline-block text-center p-3 pointer w-110 {{ $status == 'unpaid' ? 'border-main-bottom text-main' : '' }}"
        id="unpaid" onclick="window.location='{{ route('purchase', ['unpaid']) }}'">Belum Bayar</div>
    <div class="d-inline-block text-center p-3 pointer w-110 {{ $status == 'packed' ? 'border-main-bottom text-main' : '' }}"
        id="packed" onclick="window.location='{{ route('purchase', ['packed']) }}'">Dikemas</div>
    <div class="d-inline-block text-center p-3 pointer w-110 {{ $status == 'sent' ? 'border-main-bottom text-main' : '' }}"
        id="sent" onclick="window.location='{{ route('purchase', ['sent']) }}'">Dikirim</div>
    <div class="d-inline-block text-center p-3 pointer w-110 {{ $status == 'done' ? 'border-main-bottom text-main' : '' }}"
        id="done" onclick="window.location='{{ route('purchase', ['done']) }}'">Selesai</div>
    <div class="d-inline-block text-center p-3 pointer w-110 {{ $status == 'cancel' ? 'border-main-bottom text-main' : '' }}"
        id="cancel" onclick="window.location='{{ route('purchase', ['cancel']) }}'">Dibatalkan</div>
    <div class="d-inline-block text-center p-3 pointer w-110 {{ $status == 'failed' ? 'border-main-bottom text-main' : '' }}"
        id="failed" onclick="window.location='{{ route('purchase', ['failed']) }}'">Gagal</div>
</div>

@if (count($transaction))

    @foreach ($transaction as $item)
        <div class="bg-white shadow-sm mb-3">
            <div class="p-3 border-bottom pointer"
                onclick="window.location='{{ route('detail-transaction', [$item->invoice]) }}'; load()">
                <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                    <div class="fw-bold">{{ $item->invoice }}</div>
                    <div class="text-main text-uppercase">{{ $item->status }}</div>
                </div>

                @foreach ($item->orders as $key => $order)
                    @if ($key != 0)
                        <div class="product collapse">
                    @endif
                    <div
                        class="d-flex {{ count($item->orders) > 1 ? 'border-bottom' : '' }} {{ $key != array_key_last($item->orders) && $key != 0 ? 'mb-3' : '' }} {{ $key == 1 ? 'mt-3' : '' }}">
                        <img src="{{ $order->product_detail->image[0]->image }}" width="50px" class="me-2"
                            style="height: 50px">
                        <div class="w-100 text-truncate">
                            <span>{{ $order->product_detail->product_name }}
                                {{ $order->product_size ? '( ' . $order->product_size . ' )' : '' }}</span>
                            <div class="d-flex justify-content-between">
                                <div class="text-gray">
                                    <span>Rp {{ number_format($order->price) }}</span>
                                    <span class="mx-1">x</span>
                                    <span>{{ $order->quantity }}</span>
                                </div>
                                @if ($item->status != 'Selesai')
                                    <div>Rp
                                        {{ number_format($order->price * $order->quantity) }}
                                    </div>
                                @else
                                    <a href="{{ route('product', [$order->product_detail->id, strtolower(str_replace([' ', '/'], '_', $order->product_detail->product_name))]) }}"
                                        class="btn bg-main fs-small text-light mb-2">Beli Lagi</a>
                                @endif
                            </div>
                        </div>
                    </div>
                    @if ($key != 0)
            </div>
    @endif
@endforeach
</div>
<div class="p-2 position-relative">
    @if (count($item->orders) > 1)
        <div class="bg-gray arrow-down rounded-circle text-center p-1 position-absolute top-0 start-50 translate-middle"
            style="width: 30px; height: 30px;" data-bs-toggle="collapse" data-bs-target=".product" aria-expanded="false"
            aria-controls="product" onclick="arrowDown()">
            <i class="fa-solid fa-angle-down"></i>
        </div>
    @endif

    <div
        class="d-flex {{ $item->status == 'Selesai' ? 'justify-content-between' : 'justify-content-end' }} align-items-center">
        @if ($item->status == 'Selesai')
            <div class="text-gray fs-small">
                <span class="d-block d-md-inline-block">Pesanan diterima :</span>
                <span>{{ date('d F Y H:m', strtotime($item->updated_at)) }}</span>
            </div>
        @endif
        <div class="text-end">
            <span @if ($item->status == 'Selesai') class="d-block d-md-inline-block" @endif>Total Pesanan :</span>
            <span class="text-main fs-5 ms-4">
                Rp {{ number_format($item->amount) }}
            </span>
        </div>
    </div>
</div>
</div>
@endforeach
@else
<div class="text-center">
    <div class="text-gray fw-bold"><i class="fa-solid fa-clipboard-list"></i> Belum ada pesanan</div>
    <a href="{{ url('/') }}" class="fs-small text-main fw-bold">Belanja Sekarang.</a>
</div>
@endif
