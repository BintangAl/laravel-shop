<div class="container mt-4">
    <div id="carouselIndicators" class="carousel slide" data-bs-ride="true">
        <div class="carousel-indicators">
            @if (count($carousel))
                @php $i = 0; @endphp
                @foreach ($carousel as $item)
                    <button type="button" data-bs-target="#carouselIndicators" data-bs-slide-to="{{ $i }}"
                        @if ($i == 0) class="active"
                    aria-current="true" @endif
                        aria-label="Slide {{ $loop->iteration }}"></button>

                    @php $i++ @endphp
                    @if ($loop->iteration >= count($carousel))
                        @php break; @endphp
                    @endif
                @endforeach
            @else
                <button type="button" data-bs-target="#carouselIndicators" data-bs-slide-to="0" class="active"
                    aria-current="true" aria-label="Slide 1"></button>
                <button type="button" data-bs-target="#carouselIndicators" data-bs-slide-to="1"
                    aria-label="Slide 2"></button>
            @endif
        </div>
        <div class="carousel-inner">
            @if (count($carousel))
                @foreach ($carousel as $item)
                    <div class="carousel-item {{ $loop->iteration == 1 ? 'active' : '' }}">
                        <img src="{{ url($item->image) }}" class="d-block w-100">
                    </div>
                @endforeach
            @else
                <div class="carousel-item active">
                    <img src="{{ url('/asset/img/header/cor1.png') }}" class="d-block w-100">
                </div>
                <div class="carousel-item">
                    <img src="{{ url('/asset/img/header/cor2.jpeg') }}" class="d-block w-100">
                </div>
            @endif
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselIndicators" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselIndicators" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
</div>
