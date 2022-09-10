<div class="confirm" id="{{ $confirm_id ?? '' }}">
    <div class="bg-black-50 w-100 h-full position-fixed z-999"></div>
    <div class="bg-white p-3 z-999 overpass rounded position-fixed top-50 start-50 translate-middle">
        <div class="text-center mb-1">{{ $message }}</div>
        <div class="d-flex justify-content-center">
            <button type="button" class="btn btn-sm btn-danger me-3 confirm-cancel"
                onclick="{{ $confirm_cancel ?? "$('.confirm').fadeOut('fast');" }}">Cancel</button>
            <button type="{{ $type ?? 'button' }}" class="btn btn-sm btn-success is-confirm"
                onclick="{{ $is_confirm ?? '' }}">{{ $confirm_btn }}</button>
        </div>
    </div>
</div>
