<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
    <link rel="icon" href="{{ url(env('ICON_APP')) }}">
    <link rel="stylesheet" href="/css/main.css">
    <link rel="stylesheet" href="/css/load.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"
        integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <style>
        input:focus {
            background-color: #EEF2F5 !important;
        }

        a.btn:hover {
            background-color: #EEF2F5 !important;
        }

        ul li {
            list-style-type: inherit !important;
        }
    </style>
</head>

<body style="background-color: #F5F5F5">
    <div class="position-fixed bg-black-50 w-100 h-full" id="load">
        <div class="lds-ripple position-absolute top-50 start-50 translate-middle">
            <div></div>
            <div></div>
        </div>
    </div>

    <div class="container position-relative">
        <div class="position-absolute top-0 start-50 mt-3 translate-middle-x d-block d-lg-none">
            <a href="{{ url('/') }}" class="text-main overpass fs-5" style="font-weight: 500">
                <img src="{{ env('ICON_APP') }}" width="50px" alt="">Shop
            </a>
        </div>
        <div class="row align-items-center" style="height: 100vh">
            <div class="col-lg-7 col-12 d-lg-block d-none">
                <a href="{{ url('/') }}" class="text-main overpass fs-5" style="font-weight: 500">
                    <img src="{{ env('ICON_APP') }}" width="50px" alt="">Shop
                </a>
                <img src="/asset/img/image.png" class="img-fluid" alt="">
            </div>
            <div class="col-lg-5 col-12 px-3 px-lg-5">
                <div class="overpass text-center text-lg-start fs-2 mb-3">Create Account</div>

                <!-- Session Status -->
                <x-auth-session-status class="mb-3 fs-small text-danger" :status="session('status')" />

                <!-- Validation Errors -->
                <x-auth-validation-errors class="mb-3 fs-small text-danger" :errors="$errors" />

                <form action="{{ "$_SERVER[REQUEST_URI]" }}" method="POST">
                    @csrf
                    <div class="border border-1 rounded mb-4">
                        <div class="input-group input-group-lg border-bottom">
                            <span class="input-group-text text-gray bg-white border-0" id="span"
                                style="border-radius: 0; border-top-left-radius: 10px;"><i
                                    class="bi bi-person"></i></span>
                            <div class="form-floating">
                                <input type="text" class="form-control bg-white border-0 shadow-none overpass"
                                    id="name" name="name" value="{{ old('name') }}" placeholder="name">
                                <label for="name" class="text-gray">Name</label>
                            </div>
                        </div>
                        <div class="input-group input-group-lg border-bottom">
                            <span class="input-group-text text-gray bg-white border-0" id="span"
                                style="border-radius: 0; border-top-left-radius: 10px;"><i
                                    class="bi bi-envelope"></i></span>
                            <div class="form-floating">
                                <input type="email" class="form-control bg-white border-0 shadow-none overpass"
                                    id="email" name="email" value="{{ old('email') }}"
                                    placeholder="Email address">
                                <label for="email" class="text-gray">Email address</label>
                            </div>
                        </div>
                        <div class="input-group input-group-lg border-bottom">
                            <span class="input-group-text text-gray bg-white border-0"
                                style="border-radius: 0; border-bottom-left-radius: 10px"><i
                                    class="bi bi-lock"></i></span>
                            <div class="form-floating">
                                <input type="password" class="form-control bg-white border-0 shadow-none overpass"
                                    id="password" name="password" placeholder="Password">
                                <label for="password" class="text-gray">Password</label>
                            </div>
                        </div>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text text-gray bg-white border-0"
                                style="border-radius: 0; border-bottom-left-radius: 10px"><i
                                    class="bi bi-lock-fill"></i></span>
                            <div class="form-floating">
                                <input type="password" class="form-control bg-white border-0 shadow-none overpass"
                                    id="password_confirmation" name="password_confirmation"
                                    placeholder="Password Confirmation">
                                <label for="password_confirmation" class="text-gray">Password Confirmation</label>
                            </div>
                        </div>
                    </div>

                    <div class="d-lg-flex d-block px-2 px-lg-0 mb-4">
                        <button
                            class="col-lg-auto col-12 mb-3 mb-lg-0 btn bg-main shadow-sm text-light px-4 py-2 me-3 rounded-pill fs-small"
                            onclick="$('#load').show();">Create Account</button>
                        <a href="{{ str_replace('register', 'login', "$_SERVER[REQUEST_URI]") }}"
                            class="col-lg-auto col-12 mb-3 mb-lg-0 btn bg-white shadow-sm px-4 py-2 border-0 rounded-pill fs-small">Login</a>
                    </div>
                </form>

                @include('components.auth-external')
            </div>
        </div>
    </div>





    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-A3rJD856KowSb7dwlZdYEkO39Gagi7vIsF0jrRAoQmDKKtQBHUuLZ9AsSv4jD4Xa" crossorigin="anonymous">
    </script>
</body>

</html>
