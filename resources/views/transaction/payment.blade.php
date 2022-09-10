@extends('layouts.main')

@section('container')
    @include('partials.alert-success', ['message' => 'No. Rekening berhasil disalin'])
    @include('partials.nav')
    <div class="bg-white p-3 text-main border-bottom mb-2">
        <div class="container">Pembayaran</div>
    </div>

    <div id="app-field">
        <div class="container overpass">
            <div class="bg-white p-3 shadow-sm mb-3">
                <div class="d-flex justify-content-between">
                    <div class="fs-small fw-bold">Total Pembayaran</div>
                    <div class="fs-small fw-bold sans">Rp {{ number_format($data->amount) }}</div>
                    <input type="hidden" name="payment_fee" value="">
                </div>
            </div>

            <div class="bg-white p-3 shadow-sm mb-3">
                <div class="d-flex justify-content-between border-bottom align-items-center pb-2 mb-3">
                    <div class="fw-bold">{{ $payment->name }}</div>
                    <img src="{{ $payment->icon_url }}" width="60px">
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <div class="fs-small">No. Rekening:</div>
                        <div class="fs-5 text-main sans"><span id="va_number">{{ $data->pay_code }}</span> <span
                                class="text-primary pointer fs-xsmall d-none d-lg-inline-block ms-2"
                                onclick="copyToClipboard('#va_number')"><i class="fa-solid fa-copy"></i> SALIN</span></div>
                    </div>
                    <div class="text-primary pointer d-block d-lg-none" onclick="copyToClipboard('#va_number')"><i
                            class="fa-solid fa-copy"></i> SALIN</div>
                </div>
                <div class="fs-xsmall text-dark-gray">Lakukan pembayaran sebelum <span
                        class="fw-bold">{{ date('d F Y H:m', strtotime(date('Y-m-d H:i:s', $data->expired_time))) }}
                        WIB</span></div>
            </div>

            <div class="shadow-sm mb-3">
                <div class="bg-white p-3 fw-bold border-bottom">Petunjuk Pembayaran</div>
                <div class="accordion accordion-flush" id="instruction">
                    @foreach ($data->instructions as $item)
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="flush-heading{{ $loop->iteration }}">
                                <button class="accordion-button collapsed shadow-none" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#flush-collapse{{ $loop->iteration }}"
                                    aria-expanded="false" aria-controls="flush-collapse{{ $loop->iteration }}">
                                    {{ $item->title }}
                                </button>
                            </h2>
                            <div id="flush-collapse{{ $loop->iteration }}" class="accordion-collapse collapse"
                                aria-labelledby="flush-heading{{ $loop->iteration }}" data-bs-parent="#instruction">
                                <div class="accordion-body">
                                    <ol type="1">
                                        @foreach ($item->steps as $step)
                                            <li class="mt-1">{!! $step !!}</li>
                                        @endforeach
                                    </ol>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <button class="btn bg-main text-light w-100 mb-3 rounded-0"
                onclick="window.location='{{ route('detail-transaction', [$data->merchant_ref]) }}'; load()">OKE</button>
        </div>
    </div>

    @include('partials.search')
@endsection
