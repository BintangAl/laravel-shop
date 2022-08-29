@include('partials.alert-success', ['message' => 'Password Berhasil Diubah'])
<div class="bg-white p-3">
    <div class="fw-bold">Ubah Password</div>
<hr class="my-2">
<form action="{{ route('change-password') }}" method="post" class="mt-3">
    @csrf
    <div class="row align-items-center mb-3">
        <div class="col-lg-3 col-12 text-dark-gray fs-small">
            <label for="old_password">Password Lama</label>
        </div>
        <div class="col-lg-9 col-12">
            <div class="input-group">
                <input id="old_password" name="old_password" type="password" class="form-control shadow-none border-end-0 @error('old_password') is-invalid @enderror @if(session()->has('checkFailed')) is-invalid @endif">
                <span class="input-group-text bg-white border border-start-0 pointer text-main d-none" onclick="hidePassword('#old_password', '#hide_old_password', '#show_old_password')" id="hide_old_password"><i class="bi bi-eye-slash-fill"></i></span>
                <span class="input-group-text bg-white border border-start-0 pointer text-main" onclick="showPassword('#old_password', '#hide_old_password', '#show_old_password')" id="show_old_password"><i class="bi bi-eye-fill"></i></span>
            </div>
            @error('old_password')
                <div class="text-danger fs-xsmall">{{ $message }}</div>
            @enderror
            @if (session()->has('checkFailed'))
                <div class="text-danger fs-xsmall">{{ session()->get('checkFailed'); }}</div>
            @endif
        </div>
    </div>
    <div class="row align-items-center mb-3">
        <div class="col-lg-3 col-12 text-dark-gray fs-small">
            <label for="password">Password Baru</label>
        </div>
        <div class="col-lg-9 col-12">
            <div class="input-group">
                <input id="password" name="password" type="password" class="form-control border-end-0 shadow-none @error('password') is-invalid @enderror">
                <span class="input-group-text bg-white border border-start-0 pointer text-main d-none" onclick="hidePassword('#password', '#hide_password', '#show_password')" id="hide_password"><i class="bi bi-eye-slash-fill"></i></span>
                <span class="input-group-text bg-white border border-start-0 pointer text-main" onclick="showPassword('#password', '#hide_password', '#show_password')" id="show_password"><i class="bi bi-eye-fill"></i></span>
            </div>
            @error('password')
                <div class="text-danger fs-xsmall">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="row align-items-center mb-3">
        <div class="col-lg-3 col-12 text-dark-gray fs-small">
            <label for="password_confirmation">Konfirmasi Password Baru</label>
        </div>
        <div class="col-lg-9 col-12">
            <div class="input-group">
                <input id="password_confirmation" name="password_confirmation" type="password" class="form-control border-end-0 shadow-none @error('password') is-invalid @enderror">
                <span class="input-group-text bg-white border border-start-0 pointer text-main d-none" onclick="hidePassword('#password_confirmation', '#hide_password_confirmation', '#show_password_confirmation')" id="hide_password_confirmation"><i class="bi bi-eye-slash-fill"></i></span>
                <span class="input-group-text bg-white border border-start-0 pointer text-main" onclick="showPassword('#password_confirmation', '#hide_password_confirmation', '#show_password_confirmation')" id="show_password_confirmation"><i class="bi bi-eye-fill"></i></span>
            </div>
            @error('password')
                <div class="text-danger fs-xsmall">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <button class="btn btn-sm bg-main px-3 text-light" onclick="load()">Ubah Password</button>
</form>

</div>