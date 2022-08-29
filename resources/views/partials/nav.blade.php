<nav class="navbar navbar-expand-lg navbar-dark bg-main-gradient overpass">
    <div class="container">
      <a class="navbar-brand text-light me-5" href="{{ route('app') }}">
        <img src="{{ url('/asset/img/icon/icon-white.png') }}" width="40px"> Shop
      </a>
      <button class="navbar-toggler shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-tool" aria-controls="navbar-tool" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse align-items-center overpass" id="navbar-tool">
        <div class="d-flex w-100 px-0 px-lg-5 me-5 d-none d-lg-block" role="search">
          @include('partials.search')
        </div>
        <ul class="navbar-nav me-auto">
          @auth
          <li class="nav-item">
            <a class="nav-link" aria-current="page" href="{{ route('cart') }}">
              <i class="fa-solid fa-cart-shopping position-relative">
                @if (count($cart))
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-light text-main border-main" style="font-size: 8px">{{ count($cart) }}</span>
                @endif
              </i> 
              <span class="d-inline-block d-lg-none">Keranjang</span></a>
          </li>
          <div class="vr bg-light d-none d-lg-block mx-2"></div>
          <li class="nav-item">
            <a class="nav-link" aria-current="page" href="{{ route('purchase', ['status' => 'all']) }}"><i class="fa-solid fa-user"></i> <span class="d-inline-block d-lg-none">{{ auth()->user()->name }}</span></a>
          </li>
          @else
          <li class="nav-item">
            <a class="nav-link fs-small" aria-current="page" href="{{ route('register') }}">Daftar</a>
          </li>
          <div class="vr bg-light d-none d-lg-block"></div>
          <li class="nav-item">
            <a class="nav-link fs-small" aria-current="page" href="{{ route('login') }}">Login</a>
          </li>
          @endauth
        </ul>
        <div class="d-flex w-100 px-0 px-lg-5 me-5 d-block d-lg-none" role="search">
          @include('partials.search')
        </div>
      </div>
    </div>
  </nav>