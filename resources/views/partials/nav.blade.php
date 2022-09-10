<form method="POST" action="{{ route('logout') }}">
    @csrf
    @include('partials.alert-confirm', [
        'message' => 'Anda yakin ingin log out?',
        'type' => 'submit',
        'confirm_btn' => 'Log out',
    ])
</form>

<div class="d-flex w-100 bg-main-gradient py-3 overpass">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <!-- Logo -->
            <a class="navbar-brand text-light me-5 d-none d-md-block" href="{{ route('app') }}">
                <img src="{{ url('/asset/img/icon/icon-white.png') }}" width="40px"> Shop
            </a>

            <!-- Back Button -->
            @if ($title != 'MunnShop')
                <a class="text-light d-block d-md-none" href="{{ route('app') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor"
                        class="bi bi-arrow-left-short" viewBox="0 0 16 16">
                        <path fill-rule="evenodd"
                            d="M12 8a.5.5 0 0 1-.5.5H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5a.5.5 0 0 1 .5.5z" />
                    </svg>
                </a>
            @endif

            <!-- Search -->
            <div class="input-group input-group-sm mb-lg-0">
                <input type="text" class="search form-control rounded-start border-light border-4 shadow-none"
                    placeholder="Search" aria-describedby="search">
                <button type="button"
                    class="input-group-text rounded-end border-light border-4 bg-main text-light px-4"><i
                        class="fa-solid fa-magnifying-glass"></i></button>
            </div>

            <!-- Nav Menu -->
            @auth
                <div class="d-flex ms-4 ms-md-5">
                    <!-- Nav Cart Menu -->
                    <a class="text-light me-4" href="{{ route('cart') }}">
                        <i class="fa-solid fa-cart-shopping position-relative" id="cartAlert">
                            @if (count($cart))
                                <span
                                    class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-light text-main border-main"
                                    style="font-size: 8px" id="cartAlertBadge">{{ count($cart) }}</span>
                            @endif
                        </i>
                    </a>

                    <!-- Nav Notification Menu -->
                    <div class="dropdown me-4">
                        <!-- Icon -->
                        <a class="text-light" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-bell-fill position-relative" id="notifAlert">
                                @if (isset($notif))
                                    @if (count($notif->notification_false))
                                        <span
                                            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-light text-main border-main"
                                            style="font-size: 8px"
                                            id="notifCountBadge">{{ count($notif->notification_false) }}</span>
                                    @endif
                                @endif
                            </i>
                        </a>

                        <!-- Dropdown -->
                        <ul class="dropdown-menu" style="width: 350px;">
                            <!-- Dropdown title -->
                            <li class="text-main fw-bold border-bottom p-2">
                                Notifikasi (<span
                                    id="notifCount">{{ isset($notif) ? count($notif->notification_false) : 0 }}</span>)
                            </li>
                            <!-- Dropdown list -->
                            <div class="overflow-auto" style="max-height:400px">
                                <div id="notification"></div>
                                @if (isset($notif))
                                    @if (count($notif->notification))
                                        @foreach ($notif->notification as $item)
                                            <li class="d-flex {{ $item->status == 'false' ? 'bg-gray-hover' : 'bg-gray' }} pointer justify-content-between p-2 border-bottom"
                                                onclick="notifTrue({{ $item->id }}, '{{ $item->invoice }}')">
                                                <div class="d-flex">
                                                    <!-- Icon Message Notification -->
                                                    <div class="icon bg-main-50 me-2 rounded-circle position-relative"
                                                        style="width: 35px; height: 35px">
                                                        <div class="position-absolute top-50 start-50 translate-middle">
                                                            @if ($item->message == 'Pembayaran Berhasil!')
                                                                <i
                                                                    class="fa-sharp fa-solid fa-check-to-slot text-success"></i>
                                                            @endif
                                                            @if ($item->message == 'Pembayaran Gagal!')
                                                                <i class="fa-solid fa-circle-exclamation text-danger"></i>
                                                            @endif
                                                            @if ($item->message == 'Pesanan Dikirim!')
                                                                <i class="fa-solid fa-truck-fast text-main"></i>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    <!-- Message Notification -->
                                                    <div class="message">
                                                        <div class="fs-small fw-bold">{{ $item->message }}</div>
                                                        <div class="fs-xsmall text-muted">Invoice : {{ $item->invoice }}
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Image Message Notification -->
                                                <div class="bg-image bg-gray rounded"
                                                    style="background-image: url('{{ $item->image }}'); width:35px; height:35px">
                                                </div>
                                            </li>
                                        @endforeach
                                    @else
                                        <div class="text-center fs-small text-gray p-2 fw-bold" id="notifNull"><i
                                                class="bi bi-bell-slash-fill"></i> Tidak ada notifikasi.
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </ul>
                    </div>

                    <!-- Nav Profile Menu -->
                    <div class="dropdown">
                        <!-- Icon -->
                        <a class="text-light" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-solid fa-user"></i>
                        </a>

                        <!-- Dropdown -->
                        <ul class="dropdown-menu">
                            <!-- User -->
                            <li><a class="dropdown-item" href="{{ route('profile') }}"><i
                                        class="fa-solid fa-user text-main me-2"></i> Akun Saya</a></li>

                            <!-- Orders -->
                            <li><a class="dropdown-item" href="{{ route('purchase', ['status' => 'all']) }}"><i
                                        class="fa-solid fa-clipboard-list text-main me-2"></i> Pesanan
                                    Saya</a></li>

                            <!-- Logout -->
                            <li><a class="dropdown-item pointer" onclick="$('.confirm').fadeIn('fast');"><i
                                        class="fa-solid fa-right-from-bracket text-main me-2"></i> Log out</a></li>
                        </ul>
                    </div>
                </div>
            @else
                <div class="d-flex ms-4 ms-md-5">
                    <a class="text-light fs-small" aria-current="page" href="{{ route('register') }}">Daftar</a>
                    <div class="vr bg-light mx-3"></div>
                    <a class="text-light fs-small" aria-current="page" href="{{ route('login') }}">Login</a>
                </div>
            @endauth
        </div>
    </div>
</div>

@section('js')
    <script>
        function notifTrue(id, inv) {
            $('#load').show();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: "post",
                url: "{{ route('notification') }}",
                data: {
                    'id': id
                },
                dataType: "json",
                success: function(response) {
                    window.location.href = "{{ env('APP_URL') }}" + '/transaction/' + inv;
                }
            });
        }
    </script>
@endsection
