@extends('layouts.main')

@section('container')

    @if ($transaction->status == 'Belum Bayar')
        <form action="{{ route('cancel', [$transaction->invoice]) }}" method="POST">
            @csrf
            @include('partials.alert-confirm', [
                'confirm_id' => 'confirm_order_cancel',
                'message' => 'Anda yakin ingin membatalkan pesanan?',
                'is_confirm' => 'load()',
                'confirm_btn' => 'Batalkan',
                'type' => 'submit',
            ])
        </form>
    @endif

    @if ($transaction->status == 'Dikirim')
        <form action="{{ route('done', [$transaction->invoice]) }}" method="POST">
            @csrf
            @include('partials.alert-confirm', [
                'confirm_id' => 'confirm_order_accepted',
                'message' => 'Anda yakin ingin mengkonfirmasi pesanan?',
                'is_confirm' => 'load()',
                'confirm_btn' => 'Konfirmasi',
                'type' => 'submit',
            ])
        </form>
    @endif

    @include('partials.nav')

    <div id="app-field">
        <div class="bg-white p-3 text-main border-bottom mb-2">
            <div class="container d-flex justify-content-between align-items-center">
                <a href="{{ route('purchase', ['all']) }}" class="text-main">Transaction</a>
                <span class="pointer text-end" onclick="window.location.reload()">
                    <div class="fw-bold">{{ $transaction->invoice }}</div>
                    <div class="fs-xsmall text-dark"
                        title="Pesanan dibuat : {{ date('d F Y H:m', strtotime($transaction->expire)) }}">
                        {{ date('d F Y H:m', strtotime($transaction->expire)) }}</div>
                </span>
            </div>
        </div>

        <div class="container mt-3 overpass">
            @if ($transaction->status == 'Belum Bayar')
                <div class="alert alert-danger py-2 alert-dismissible fade show pointer fs-small"
                    onclick="window.location='{{ route('payment', [$transaction->invoice]) }}'; load()" role="alert">
                    Segera lakukan pembayaran sebelum <strong>{{ date('d F Y H:m', strtotime($transaction->expire)) }}
                        WIB</strong>
                </div>
            @endif

            @if ($transaction->status == 'Dikemas' || $transaction->status == 'Dikirim' || $transaction->status == 'Selesai')
                <div class="bg-white border-bottom p-3 {{ $transaction->status == 'Dikirim' || $transaction->status == 'Selesai' ? 'pointer' : '' }}"
                    @if ($transaction->status == 'Dikirim' || $transaction->status == 'Selesai') onclick="window.location='{{ route('delivery', [$transaction->invoice]) }}'; load()" @endif>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="text-main"><i class="fa-solid fa-truck"></i> Info Pengiriman</div>
                        @if ($transaction->status == 'Dikirim')
                            <div class="text-main fs-small">DETAIL</div>
                        @endif
                    </div>

                    <div class="fw-bold text-uppercase" id="delivery_service">{{ $transaction->delivery_service }}
                    </div>
                    @if ($transaction->status == 'Dikemas')
                        <div class="text-warning">
                            Barang Sedang Dikemas
                            <i class="bi bi-box-seam-fill"></i>
                        </div>
                    @endif
                    @if ($transaction->status == 'Dikirim')
                        <div class="text-gray fs-small">No. Resi {{ $transaction->no_resi }}</div>
                        <div class="text-gray fs-small">
                            Estimasi diterima sebelum {{ $estimation }}
                        </div>
                    @endif
                    @if ($transaction->status == 'Selesai')
                        <div class="text-success fs-small">Paket telah diterima <i class="bi bi-check-circle-fill"></i>
                        </div>
                    @endif
                </div>
            @endif

            <div class="bg-white shadow-sm p-3 mb-3">
                <div class="text-main mb-3"><i class="fa-solid fa-location-dot"></i> Alamat Pengiriman</div>
                <div class="fw-bold" id="recipient_name">{{ $customer->nama_penerima }} {{ $customer->no_tlp }}</div>
                <div id="recipient_address">{{ $customer->alamat }}, {{ explode('#', $customer->kota)[0] }},
                    {{ explode('#', $customer->provinsi)[0] }}, ID {{ $customer->kodepos }}</div>
            </div>
            @include('partials.product-ordered')

            @if ($transaction->notes)
                <div class="bg-white shadow-sm p-3 mb-3">
                    <div class="text-main mb-3"><i class="bi bi-sticky-fill"></i> Catatan
                    </div>
                    <p>{{ $transaction->notes }}</p>
                </div>
            @endif

            @if ($transaction->status == 'Belum Bayar')
                <div class="bg-white shadow-sm p-3 mb-3">
                    <div class="text-main mb-3"><i class="fa-solid fa-truck"></i> Opsi Pengiriman</div>

                    <div class="d-flex justify-content-between">
                        <div class="fw-bold text-uppercase" id="delivery_service">{{ $transaction->delivery_service }}
                        </div>
                        <div class="text-main text-end"><span class="text-gray fs-small me-2">ongkir:</span> Rp <span
                                id="delivery_ongkir">{{ number_format($transaction->delivery_ongkir) }}</span></div>
                    </div>
                    <div>Estimasi <span id="delivery_etd">{{ $transaction->delivery_estimation }}</span> hari kerja.</div>
                </div>
            @endif

            <div class="bg-white shadow-sm mb-3">
                <div class="p-3 border-bottom">
                    <div class="text-main mb-3"><i class="fa-solid fa-money-bill-1-wave"></i> Metode Pembayaran</div>

                    <div class="mb-1">
                        @if ($payment != [])
                            <div class="d-flex justify-content-between">
                                <img src="{{ $payment->icon_url }}" id="channel_icon" width="100px">
                                <div class="text-main text-end"><span class="text-gray fs-small me-2">fee:</span> Rp
                                    {{ number_format($payment->total_fee->flat) }}</div>
                            </div>
                            <div class="fw-bold sans text-uppercase" id="channel_name">{{ $payment->name }}</div>
                            <div class="text-gray fs-small">Pembayaran dengan cara transfer ke nomor rekening virtual dari
                                bank
                                <span id="channel_bank">{{ explode(' ', $payment->name)[0] }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="p-3 border-gray-bottom-dash">
                    <div class="d-flex justify-content-between">
                        <div class="fs-small">Subtotal untuk Produk</div>
                        <div class="fs-small fw-bold">Rp
                            {{ number_format($subtotal) }}
                        </div>
                    </div>
                    <div class="d-flex justify-content-between">
                        <div class="fs-small">Total Ongkos Kirim</div>
                        <div class="fs-small fw-bold">Rp {{ number_format($transaction->delivery_ongkir) }}</div>
                    </div>
                    <div class="d-flex justify-content-between">
                        <div class="fs-small">Total Fee Pemabayaran</div>
                        <div class="fs-small fw-bold">
                            @if ($payment != [])
                                Rp {{ number_format($payment->total_fee->flat) }}
                            @else
                                <span class="text-danger pointer"
                                    onclick="window.location.reload(); load()">error-500</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="px-3 py-2 border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="sans fw-bold fs-5">Total</div>
                        <div class="fs-4 text-main fw-bold sans">
                            @if ($payment != [])
                                Rp {{ number_format($transaction->amount) }}
                            @else
                                <span class="text-danger pointer"
                                    onclick="window.location.reload(); load()">error-500</span>
                            @endif
                        </div>
                        <input type="hidden" name="total" value="">
                    </div>
                </div>
            </div>

            @if ($transaction->status == 'Belum Bayar')
                <button class="btn bg-main text-light w-100 mb-3 rounded-0"
                    onclick="window.location='{{ route('payment', [$transaction->invoice]) }}'; load()">Menunggu
                    Pembayaran</button>
                <button class="btn bg-gray w-100 mb-3 rounded-0"
                    onclick="$('#confirm_order_cancel').fadeIn('fast');">Batalkan
                    Pesanan</button>
            @endif

            @if ($transaction->status == 'Dikemas')
                <button class="btn bg-main text-light w-100 mb-3 rounded-0">Barang Sedang Dikemas</button>
            @endif

            @if ($transaction->status == 'Dikirim')
                <button class="btn bg-main text-light w-100 mb-3 rounded-0"
                    onclick="$('#confirm_order_accepted').fadeIn('fast');">Konfirmasi
                    Pesanan
                    Diterima</button>
            @endif

            <button class="btn bg-gray border-0 w-100 mb-3 rounded-0"
                onclick="window.location='{{ route('purchase', [$transaction->status == 'Selesai' ? 'done' : 'all']) }}'">Kembali</button>
        </div>
    </div>

    @include('partials.search')

    @include('partials.footer')
@endsection

@section('js')
@endsection
