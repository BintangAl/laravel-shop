@extends('layouts.main')

@section('container')
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        @include('partials.alert-confirm', [
            'message' => 'Anda yakin ingin log out?',
            'type' => 'submit',
            'confirm_btn' => 'Log out',
        ])
    </form>

    @include('partials.nav')
    <div id="app-field" style="margin-bottom: 100px">
        <div class="container mt-3 overpass">
            <div class="row">
                <div class="col-lg-3 col-12 mb-3">
                    <div class="bg-white">
                        <div class="d-flex py-2 px-3 align-items-center justify-content-between">
                            <div class="text-main fw-bold sans">{{ $title == 'purchase' ? 'Pesanan Saya' : 'Akun Saya' }}
                            </div>
                            <div class="d-block d-lg-none pointer" data-bs-toggle="collapse" data-bs-target="#setting"><i
                                    class="fa-solid fa-list-ul text-main"></i></div>
                        </div>
                        <div class="collapse d-lg-block" id="setting">
                            <div class="p-3 bg-gray-hover fs-small fw-bold pointer" data-bs-toggle="collapse"
                                data-bs-target="#my-account">
                                <i class="fa-solid fa-user text-main me-2"></i>Akun Saya
                            </div>
                            <ul class="collapse {{ $title == 'profile' || $title == 'address' || $title == 'password' ? 'show' : '' }}"
                                id="my-account">
                                <li class="fs-small pointer text-main-hover {{ $title == 'profile' ? 'text-main' : '' }} p-2"
                                    onclick="window.location='{{ route('profile') }}'">Profil</li>
                                <li class="fs-small pointer text-main-hover {{ $title == 'address' ? 'text-main' : '' }} p-2"
                                    onclick="window.location='{{ route('address') }}'">Alamat</li>
                                <li class="fs-small pointer text-main-hover {{ $title == 'password' ? 'text-main' : '' }} p-2"
                                    onclick="window.location='{{ route('password') }}'">Ubah Password</li>
                            </ul>
                            <div class="p-3 bg-gray-hover fs-small fw-bold pointer"
                                onclick="window.location='{{ route('purchase', ['all']) }}'">
                                <i class="fa-solid fa-clipboard-list text-main me-2"></i>Pesanan Saya
                            </div>
                            <button type="button"
                                class="btn bg-none border-0 rounded-0 w-100 text-start p-3 bg-gray-hover fs-small fw-bold pointer"
                                onclick="$('.confirm').fadeIn('fast');">
                                <i class="fa-solid fa-right-from-bracket text-main me-2"></i>Log out
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-lg-9 col-12">
                    @if ($title == 'profile')
                        @include('profile.profile')
                    @endif
                    @if ($title == 'address')
                        @include('profile.address')
                    @endif
                    @if ($title == 'password')
                        @include('profile.password')
                    @endif
                    @if ($title == 'purchase')
                        @include('profile.purchase')
                    @endif
                </div>
            </div>
        </div>
    </div>

    @include('partials.search')

    <div class="d-none d-lg-block">
        @include('partials.footer')
    </div>
@endsection

@section('js')
    @if (session()->has('successChangePassword'))
        <script>
            $('.success').fadeIn();
            $('.success').delay(2000).fadeOut();
        </script>
    @endif
@endsection
