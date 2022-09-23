@section('css')
    <style>
        .carousel-control-prev-icon {
            background-image: url("data:image/svg+xml;charset=utf8,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='%236435b1' viewBox='0 0 8 8'%3E%3Cpath d='M5.25 0l-4 4 4 4 1.5-1.5-2.5-2.5 2.5-2.5-1.5-1.5z'/%3E%3C/svg%3E") !important;
        }

        .carousel-control-next-icon {
            background-image: url("data:image/svg+xml;charset=utf8,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='%236435b1' viewBox='0 0 8 8'%3E%3Cpath d='M2.75 0l-1.5 1.5 2.5 2.5-2.5 2.5 1.5 1.5 4-4-4-4z'/%3E%3C/svg%3E") !important;
        }
    </style>
@endsection
<div id="carouselIndicators" class="carousel slide border" data-bs-ride="true">
    <div class="carousel-indicators">
        <div class="carousel-indicators">
            @if (count($image))
                @php $i = 0; @endphp
                @foreach ($image as $key => $item)
                    <button type="button" data-bs-target="#carouselIndicators" data-bs-slide-to="{{ $i }}"
                        class="{{ $i == 0 ? 'active' : '' }} bg-main {{ isset($item->color) ? 'is-color color' . $item->id : 'is-product' }}"
                        @if ($i == 0) aria-current="true" @endif
                        aria-label="Slide {{ $loop->iteration }}"></button>

                    @php $i++ @endphp
                    @if ($loop->iteration >= count($image))
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
    </div>
    <div class="carousel-inner">
        @foreach ($image as $item)
            <div class="carousel-item {{ $loop->iteration == 1 ? 'active' : '' }} {{ isset($item->color) ? 'is-color color' . $item->id : 'is-product' }}"
                data-bs-interval='false'>
                <img src="{{ $item->image }}" class="d-block w-100">
            </div>
        @endforeach
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
