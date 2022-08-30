@extends('layouts.main')

@section('container')

@include('partials.alert-success', ['message' => 'No. Resi berhasil disalin'])

@include('partials.nav')

<div class="container overpass mt-3">
  <div class="bg-white shadow-sm p-3 mb-3">
    <div class="d-flex">
      <img src="{{ url('/asset/img/jne.png') }}" width="100px">
      <div class="ms-2">
        <div class="fs-6">Estimasi diterima sebelum</div>
        <div class="text-main">{{ $estimation }}</div>
        <div class="text-gray fs-small">
          Dikirim dengan {{ $transaction->delivery_service }}
        </div>
      </div>
    </div>
  </div>
  
  <div class="bg-white shadow-sm mb-3">
    <div class="border-bottom p-3">
      <div class="d-flex justify-content-between">
        <div class="text-gray">No. Resi</div>
        <div>
          <span id="no_resi">{{ $transaction->no_resi }}</span>
          <span class="text-primary pointer" onclick="copyToClipboard('#no_resi')"> SALIN</span>
         </div>
      </div>
    </div>
    
    <div class="p-3">
      <div id="tracking">
      @if (count($tracking))
        @foreach ($tracking as $key => $value)
        <div class="d-flex mb-3">
          <div class="fs-xsmall text-gray col-2">{{ date_format(date_create($value->date), 'd M H:i') }}</div>
          <i class="fa-solid fa-circle {{ ($key == 0) ? 'text-success' : 'text-gray' }} mx-2 mt-1" style="font-size:9px"></i>
          <div class="fs-small {{ ($key == 0) ? 'text-success' : 'text-gray' }}">
            {{ $value->desc }}
          </div>
        </div>
        @endforeach
      @else
      <div class="text-center text-gray fs-small">Data tidak ada.</div>
      @endif
      </div>
    </div>
  </div>
  
  <button class="btn bg-gray border-0 w-100 mb-5 rounded-0" onclick="window.location='{{ route('detail-transaction', [$transaction->invoice]) }}'; load()">Kembali</button>
</div>

@endsection