<div class="envelope-line"></div>
<div class="bg-white shadow-sm p-3 mb-3">
    <div class="d-flex justify-content-between mb-3">
        <div class="text-main"><i class="fa-solid fa-location-dot"></i> Alamat Pengiriman</div>
        <div class="text-primary pointer {{ count($address) ?: 'd-none' }}" data-bs-toggle="modal"
            data-bs-target="#addressOption">Ubah</div>
    </div>
    @if (count($address))
        @foreach ($address as $item)
            @if ($item->status == 'true')
                <div id="address-main">
                    <div class="fw-bold">{{ $item->nama_penerima }} {{ $item->no_tlp }}</div>
                    <div>{{ $item->alamat }}, {{ explode('#', $item->kota)[0] }},
                        {{ explode('#', $item->provinsi)[0] }}, ID {{ $item->kodepos }}</div>
                </div>
            @else
                <div class="fw-bold" id="recipient_name"></div>
                <div id="recipient_address"></div>
            @endif
        @endforeach
    @else
        <a href="{{ route('address') . "?link=$_SERVER[REQUEST_URI]" }}"
            class="btn btn-sm rounded-0 bg-main px-3 text-light"><i class="fa-solid fa-plus"></i> Tambah Alamat</a>
    @endif
</div>

<!-- Modal Option -->
<div class="modal fade" id="addressOption" tabindex="-1" aria-labelledby="addressOptionLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-body position-relative">
                <div class="fw-bold mb-3 text-center">Alamat Saya</div>
                <button type="button" class="btn-close position-absolute p-3 shadow-none top-0 end-0"
                    data-bs-dismiss="modal"></button>

                @if (count($address))
                    @foreach ($address as $item)
                        <label for="address{{ $item->id }}"
                            class="bg-light w-100 pointer shadow-sm sans align-items-center mb-3 px-2 py-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="address_id"
                                    id="address{{ $item->id }}" value="address{{ $item->id }}"
                                    {{ $item->status == 'true' ? 'checked' : '' }}>
                                <div class="mb-1">
                                    <div class="fw-bold">{{ $item->nama_penerima }}</div>
                                    <div class="text-dark-gray">{{ $item->no_tlp }}</div>
                                </div>
                                <div class="text-dark-gray fs-small">
                                    <span>{{ $item->alamat }}</span>
                                    <span class="text-uppercase">{{ explode('#', $item->kota)[0] }},
                                        {{ explode('#', $item->provinsi)[0] }}, {{ $item->kodepos }}</span>
                                </div>
                            </div>
                        </label>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>
