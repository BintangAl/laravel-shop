<div class="container mt-4 overpass select-none">
    <div class="bg-white p-3 text-main border-bottom">KATEGORI</div>
    <div class="col-12 mb-3 position-relative">
        <div class="arrow arrow-left bg-white rounded-circle text-center p-1 d-block d-xl-none"><i class="fa-solid fa-angle-left"></i></div>
        <div class="arrow arrow-right bg-white rounded-circle text-center p-1 d-block d-xl-none"><i class="fa-solid fa-angle-right"></i></div>

        <div class="bg-white rounded mb-3">
            <div class="d-flex overpass" id="category" style="overflow: auto">
                @php $numItems = count($category); $i = 0; @endphp
                @foreach ($category as $item)
                <div class="w-125 {{ (++$i === $numItems) ? '' : 'border-end' }}">
                    <a href="{{ route('category', [$item->id, strtolower(str_replace(' ', '_', $item->category))]) }}" class="box-category">
                        <div class="category w-125 p-2 text-center">
                            <img src="{{ url($item->image) }}" width="70px" class="mb-2" alt="">
                            <div class="text-dark fs-small w-100 px-2">{{ $item->category }}</div>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>