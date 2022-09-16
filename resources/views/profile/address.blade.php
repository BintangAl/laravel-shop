<div class="bg-white p-3">
    <div class="py-2 d-flex justify-content-between align-items-center">
        <div class="fw-bold fs-5">Alamat Saya</div>
        <div class="btn rounded-0 bg-main px-3 text-light" data-bs-toggle="modal" data-bs-target="#addAddress"><i
                class="fa-solid fa-plus"></i> Tambah Alamat Baru</div>
    </div>
    @if (count($address))
        <hr class="my-2 row">
        @foreach ($address as $item)
            @if ($item->status == 'true')
                <div class="row bg-light sans align-items-center mb-3 px-2 py-3">
                    <div class="col-lg-7 col-12 mb-2 mb-lg-0">
                        <div class="d-block d-lg-flex mb-1">
                            <div class="fw-bold">{{ $item->nama_penerima }}</div>
                            <div class="vr mx-2 d-none d-lg-block"></div>
                            <div class="text-dark-gray">{{ $item->no_tlp }}</div>
                        </div>
                        <div class="text-dark-gray fs-small">
                            <span>{{ $item->alamat }}</span>
                            <span class="text-uppercase">{{ explode('#', $item->kota)[0] }},
                                {{ explode('#', $item->provinsi)[0] }}, ID {{ $item->kodepos }}</span>
                        </div>
                    </div>
                    <div class="col-lg-5 col-12 d-flex justify-content-start justify-content-lg-end">
                        <div class="btn btn-sm rounded-0 bg-main text-light"><i class="bi bi-check-circle-fill"></i>
                            Alamat utama</div>
                    </div>
                </div>
            @endif
        @endforeach
        @foreach ($address as $item)
            @if ($item->status != 'true')
                <div class="row bg-light sans align-items-center mb-3 px-2 py-3">
                    <div class="col-lg-7 col-12 mb-2 mb-lg-0">
                        <div class="d-block d-lg-flex mb-1">
                            <div class="fw-bold">{{ $item->nama_penerima }}</div>
                            <div class="vr mx-2 d-none d-lg-block"></div>
                            <div class="text-dark-gray">{{ $item->no_tlp }}</div>
                        </div>
                        <div class="text-dark-gray fs-small">
                            <span>{{ $item->alamat }}</span>
                            <span class="text-uppercase">{{ explode('#', $item->kota)[0] }},
                                {{ explode('#', $item->provinsi)[0] }}, ID {{ $item->kodepos }}</span>
                        </div>
                    </div>
                    <div class="col-lg-5 col-12 d-flex justify-content-start justify-content-lg-end">
                        <form action="{{ route('main-address') }}" method="post">
                            @csrf
                            <button type="submit" class="btn btn-sm rounded-0 border-main" name="main_address"
                                value="{{ $item->id }}" onclick="$('#load').show()">Atur sebagai utama</button>
                        </form>
                    </div>
                </div>
            @endif
        @endforeach
    @endif

    <!-- Modal -->
    <div class="modal fade" id="addAddress" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="addAddressLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body p-4">
                    <h5 class="modal-title mb-3" id="addAddressLabel">Alamat Baru</h5>

                    <form action="{{ route('add-address') . '?link=' . request()->get('link') }}" method="post">
                        @csrf
                        <div class="d-flex mb-4">
                            <input type="text" name="nama_penerima" class="form-control shadow-none me-4"
                                placeholder="Nama Lengkap" onkeyup="valid()" required>
                            <input type="text" name="no_tlp" class="form-control shadow-none"
                                placeholder="Nomor Telepon" onkeyup="valid()" required>
                        </div>
                        <textarea class="form-control mb-4 shadow-none" cols="1" rows="1" placeholder="Alamat" name="alamat"
                            onkeyup="valid()" required></textarea>

                        <select class="mb-3" name="provinsi" placeholder="Provinsi" onchange="valid()" required>
                            <option value="">Provinsi</option>
                            @foreach ($province as $item)
                                <option value="{{ $item->province . '#' . $item->province_id }}">{{ $item->province }}
                                </option>
                            @endforeach
                        </select>
                        <select class="mb-3" name="kota" placeholder="Kota/Kabupaten" onchange="valid()" required>
                            <option value="">Kota/Kabupaten</option>
                            @foreach ($city as $item)
                                <option value="{{ $item->city_name . '#' . $item->city_id }}">{{ $item->city_name }}
                                </option>
                            @endforeach
                        </select>

                        <input type="number" name="kodepos" class="form-control shadow-none mb-4"
                            placeholder="Kode Pos" onkeyup="valid()" required>

                        <div class="d-flex justify-content-end">
                            <div class="btn bg-white me-2 border-0 bg-gray-hover rounded-0 px-4 text-dark-gray"
                                data-bs-dismiss="modal">Nanti Saja</div>
                            <button class="btn bg-main rounded-0 text-light px-5" disabled
                                id="btnAddAddress">OK</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
