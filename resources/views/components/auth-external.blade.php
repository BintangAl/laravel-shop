<div class="text-center text-lg-start">
    <div class="text-dark-gray fs-xsmall mb-2">Or you can join with</div>
    <div class="d-flex justify-content-center justify-content-lg-start">
        <div class="col-auto me-3">
            <a href="{{ route('google.login') }}" onclick="$('#load').show();" class="btn bg-white border-0 rounded-circle shadow-sm" style="width: 40px; height:40px">
                <img src="{{ url('/asset/img/google.png') }}" class="img-fluid" alt="google">
            </a>
        </div>
        <div class="col-auto">
            <div class="btn border-0 rounded-circle shadow-sm" style="width: 40px; height:40px; background-color:#3B599A;">
                <img src="{{ url('/asset/img/facebook.png') }}" class="img-fluid" alt="facebook" style="filter: brightness(0) invert(1)">
            </div>
        </div>
    </div>
</div>