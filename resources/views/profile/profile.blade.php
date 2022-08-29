<div class="bg-white p-3">
    <div class="fw-bold">Profil Saya</div>
<div class="fs-small text-dark-gray">Kelola informasi profil Anda untuk mengontrol, melindungi dan mengamankan akun</div>
<hr class="my-2">
<form action="{{ route('profile-update') }}" method="post" class="mt-3">
    @csrf
    <div class="row align-items-center mb-3">
        <div class="col-auto text-end text-dark-gray fs-small">
            <label for="name">Nama</label>
        </div>
        <div class="col-auto">
            <input type="text" id="name" name="name" class="form-control form-control-sm" value="{{ auth()->user()->name }}">
        </div>
    </div>
    <div class="row align-items-center mb-3">
        <div class="col-auto text-end text-dark-gray fs-small">
            <label for="email">Email</label>
        </div>
        <div class="col-auto">
            <div class="fs-sm">{{ auth()->user()->email }}</div>
        </div>
    </div>
    <button class="btn btn-sm bg-main px-3 text-light">Simpan</button>
</form>

</div>