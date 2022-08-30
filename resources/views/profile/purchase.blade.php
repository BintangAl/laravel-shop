<div class="bg-white shadow-sm overflow-auto mb-3" id="status_purchase" style="white-space: nowrap">
    <div class="d-inline-block text-center p-3 pointer w-110 {{ ($status == 'all' ) ? 'border-main-bottom text-main' : '' }}" id="all" onclick="window.location='{{ route('purchase', ['all']) }}'">Semua</div>
    <div class="d-inline-block text-center p-3 pointer w-110 {{ ($status == 'unpaid' ) ? 'border-main-bottom text-main' : '' }}" id="unpaid" onclick="window.location='{{ route('purchase', ['unpaid']) }}'">Belum Bayar</div>
    <div class="d-inline-block text-center p-3 pointer w-110 {{ ($status == 'packed' ) ? 'border-main-bottom text-main' : '' }}" id="packed" onclick="window.location='{{ route('purchase', ['packed']) }}'">Dikemas</div>
    <div class="d-inline-block text-center p-3 pointer w-110 {{ ($status == 'sent' ) ? 'border-main-bottom text-main' : '' }}" id="sent" onclick="window.location='{{ route('purchase', ['sent']) }}'">Dikirim</div>
    <div class="d-inline-block text-center p-3 pointer w-110 {{ ($status == 'done' ) ? 'border-main-bottom text-main' : '' }}" id="done" onclick="window.location='{{ route('purchase', ['done']) }}'">Selesai</div>
    <div class="d-inline-block text-center p-3 pointer w-110 {{ ($status == 'cancel' ) ? 'border-main-bottom text-main' : '' }}" id="cancel" onclick="window.location='{{ route('purchase', ['cancel']) }}'">Dibatalkan</div>
    <div class="d-inline-block text-center p-3 pointer w-110 {{ ($status == 'failed' ) ? 'border-main-bottom text-main' : '' }}" id="failed" onclick="window.location='{{ route('purchase', ['failed']) }}'">Gagal</div>
</div>

@foreach ($transaction as $item)
<div class="bg-white shadow-sm mb-3 pointer" onclick="window.location='{{ route('detail-transaction', [$item->invoice]) }}'; load()">
    <div class="p-3 border-bottom">
        <div class="d-flex justify-content-between border-bottom pb-2 mb-3">
            <div class="fw-bold">{{ $item->invoice }}</div>
            <div class="text-main text-uppercase">{{ $item->status }}</div>
        </div>
    
        <div class="d-flex">
            <img src="{{ $item->product->product_image }}" width="50px" class="me-2">
            <div class="w-100 text-truncate">
                <span>{{ $item->product->product_name }}</span>
                <div class="d-flex justify-content-between">
                    <div>x{{ $item->quantity }}</div>
                    <div>Rp {{ number_format($item->product->product_price) }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="p-2">
        <div class="text-end">Total Pesanan: 
            <span class="text-main fs-5 ms-4">
                Rp {{ number_format($item->amount) }}
            </span>
        </div>
    </div>
</div>
@endforeach
