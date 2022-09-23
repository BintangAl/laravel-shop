@extends('layouts.main')

@section('container')
    @include('partials.alert-error', ['message' => 'Alamat tidak ditemukan!'])
    @include('partials.nav')

    <div id="app-field">
        <div class="bg-white p-3 text-main border-bottom mb-2">
            <div class="container">Checkout</div>
        </div>

        <div class="container mt-3 overpass">
            <form
                action="{{ count($address) ? route('transaction') . str_replace('/checkout', '', "$_SERVER[REQUEST_URI]") : '' }}"
                method="post">
                @csrf
                @include('partials.address-option')

                @include('partials.product-ordered')

                <div class="bg-white shadow-sm p-3 mb-3">
                    <div class="text-main pointer" data-bs-toggle="collapse" data-bs-target=".notes-active"
                        aria-expanded="false" aria-controls="notes"><i class="fa-solid fa-pencil"></i> Tambahkan Catatan
                    </div>
                    <div class="notes-active collapse" id="notes">
                        <textarea name="notes" class="form-control shadow-none" placeholder="Tulisan pesan..."
                            onkeyup="notesChange(this.value)"></textarea>
                    </div>
                </div>

                @include('partials.delivery-option')

                <div class="bg-white
                            shadow-sm mb-3">
                    @include('partials.payment-method')
                    @include('partials.subtotal')
                    <div class="p-3 d-flex justify-content-end">
                        <button type="{{ count($address) ? 'submit' : 'button' }}" class="btn bg-main px-4 text-light"
                            onclick="{{ count($address) ? 'load()' : 'alertError()' }}">Buat Pesanan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @include('partials.search')

    @include('partials.footer')
@endsection
