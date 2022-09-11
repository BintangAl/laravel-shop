function arrowScroll(element) {
    $('.arrow-left').click(function(event) {
        event.preventDefault();
        $(element).animate({
          scrollLeft: "-=370px"
        }, "slow");
    });
    
    $('.arrow-right').click(function(event) {
        event.preventDefault();
        $(element).animate({
        scrollLeft: "+=370px"
        }, "slow");
    });
}
function checkOver(element) {
    if (document.getElementById(element).offsetHeight < document.getElementById(element).scrollHeight ||
        document.getElementById(element).offsetWidth < document.getElementById(element).scrollWidth) {
        console.log('over');
    } else {
        console.log('not over');
    }
}
function alertSuccess() {
    $('.success').fadeIn();
    $('.success').delay(2000).fadeOut();
}
function alertError() {
    $('.error').fadeIn();
    $('.error').delay(2000).fadeOut();
}
function addCart(id){
    $('#load').show();
    
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        type: "post",
        url: window.location.origin + "/add-cart",
        data: {
            'product_id' : id, 
            'quantity' : $('#quantity').val(),
            'size' : $('input[name=size]:checked').val()
        },
        success: function (response) {
            $('#load').hide();
            alertSuccess();
            
            if (response == 'added') {
                if ($("#cartAlertBadge").length) {
                    $('#cartAlertBadge').text(parseInt($('#cartAlertBadge').text()) + 1);
                } else {
                    $('#cartAlert').append(`
                      <span
                      class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-light text-main border-main"
                      style="font-size: 8px"
                      id="cartAlertBadge">1</span>
                    `);
                }   
            }
        }, error: function (error) {
            if(error.status == 401){
                window.location = window.location.origin + '/login';
            } else {
                alertError();
            }
        }
    });
}
function minQuantity() {
    if($('#quantity').val() != 1){
        $('#quantity').val(parseInt($('#quantity').val()) - 1);
    }
}
function addQuantity() {
    if($('#quantity').val() != $('.stok').text()){
        $('#quantity').val(parseInt($('#quantity').val()) + 1);
    }

}
function minQuantityUpdate(id) {
    var id = id.replace("min", "")
    if($('#quantity'+id).val() != 0){
        $('#quantity'+id).val(parseInt($('#quantity'+id).val()) - 1);
    }
    
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    if($('#quantity'+id).val() == 0){
        $('#confirm_delete_cart').fadeIn('fast');

        $('.confirm-cancel').click(function () { 
            $('#confirm_delete_cart').fadeOut('fast');
            $('#quantity'+id).val(1);
        });

        $('.is-confirm').click(function () { 
            $('#load').show();
            $.ajax({
                type: "post",
                url: window.location.origin + "/delete-cart",
                data: {'id' : id,},
                success: function (response) {
                    $('#cart'+id).empty();
                    
                    if ($('#cartAlertBadge').text() == 1) {
                        $('#cartAlertBadge').empty();
                        $('#cartList').append(`
                            <div class="text-center text-gray fw-bold"><i class="bi bi-cart-x-fill"></i> Keranjang Kosong</div>
                            <a href="/" class="text-center fs-small text-main fw-bold">Belanja Sekarang.</a>
                        `);
                    } else {
                        $('#cartAlertBadge').text(parseInt($('#cartAlertBadge').text()) - 1)
                    }

                    $('#load').hide();
                    $('#confirm_delete_cart').fadeOut('fast');
                }
            });
        });
    } else {
        $.ajax({
            type: "post",
            url: window.location.origin + "/update-quantity",
            data: {
                'id' : id,
                'action' : 'min', 
                'quantity' : $('#quantity'+id).val(),
            },
            success: function (response) {
            }
        });
    }
}
function addQuantityUpdate(id) {
    var id = id.replace("add", "")
    if($('#quantity'+id).val() != 0){
        $('#quantity'+id).val(parseInt($('#quantity'+id).val()) + 1);
    }
    
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        type: "post",
        url: window.location.origin + "/update-quantity",
        data: {
            'id' : id,
            'action' : 'add', 
            'quantity' : $('#quantity'+id).val(),
        },
        success: function (response) {
        }
    });
}
function formatNumber(num) {
    return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')
}
function load() {
    $('#load').show();
}
function deliveryOption() {
    $("input:radio[name='delivery']").change(function () { 
        const value = $(this).val().split('#');

        $('#delivery_service').text('JNE ' + value[0] + ' (' + value[1] + ')');
        $('#delivery_ongkir').text(formatNumber(value[2]));
        $('#delivery_etd').text(value[3]);

        $('#total_ongkir').text(formatNumber(value[2]));
        $('input[name=total_ongkir]').val(value[2]);
        total();
    });
}
function totalaVal(name) {
    return parseInt($('input[name='+ name +']').val())
}
function total() {
    var total = totalaVal('sub_total') + totalaVal('total_ongkir') + totalaVal('payment_fee');
    $('#total').text(formatNumber(total));
    $('input[name=total]').val(totalaVal('sub_total') + totalaVal('total_ongkir'));
}
function copyToClipboard(element) {
    var $temp = $("<input>");
    $("body").append($temp);
    $temp.val($(element).text()).select();
    document.execCommand("copy");
    $temp.remove();
    alertSuccess();
}
function purchaseStatusMenu() {
    if(window.location.href.split('/')[5] == 'unpaid'){
        $('#status_purchase').animate({scrollLeft:$('#unpaid').offset().left});
    } else if(window.location.href.split('/')[5] == 'packed'){
        $('#status_purchase').animate({scrollLeft:$('#packed').offset().left});
    } else if(window.location.href.split('/')[5] == 'sent'){
        $('#status_purchase').animate({scrollLeft:$('#sent').offset().left});
    } else if(window.location.href.split('/')[5] == 'done'){
        $('#status_purchase').animate({scrollLeft:$('#done').offset().left});
    } else if(window.location.href.split('/')[5] == 'cancel'){
        $('#status_purchase').animate({scrollLeft:$('#cancel').offset().left});
    } else if(window.location.href.split('/')[5] == 'failed'){
        $('#status_purchase').animate({scrollLeft:$('#failed').offset().left});
    }
}
function showPassword(id, hide, show) {
    $(id).attr('type', 'text');
    $(show).addClass('d-none');
    $(hide).removeClass('d-none');
}
function hidePassword(id, hide, show) {
    $(id).attr('type', 'password');
    $(hide).addClass('d-none');
    $(show).removeClass('d-none');
}


$(document).ready(function () {
    arrowScroll('#category');
    purchaseStatusMenu();

    $('.search').keyup(function (e) { 
        if($(this).val() != ''){
            $('#app-field').hide();
            $('#seacrh-field').show();
            $('#value').text($(this).val());
    
            $.ajax({
                type: "get",
                url: window.location.origin + "/search",
                data: {'search' : $(this).val()},
                success: function (response) {
                    if (response != '') {
                        $('#product-search').html('');
                        $('#product-search').append(response);
                        var idArray = [];
                        $('.product-name').each(function () {
                            idArray.push(this.id);
                            var text = $('#'+this.id).text();
                            if(text.length > 30){
                                $('#'+this.id).text(text.substring(0, 28)+'...');
                            }
                        });
                    } else {
                        $('#product-search').html('');
                        $('#product-search').append('<div class="text-center mt-3 text-gray fw-bold"><i class="fa-solid fa-magnifying-glass"></i> Produk tidak ditemukan</div>');
                    }
                }
            });
        } else {
            $('#app-field').show();
            $('#seacrh-field').hide();
        }
    });

    $('select').selectize({
        sortField: 'text'
    });

    $('#quantity').change(function () { 
        if($('#quantity').val() > $('.stok').text()){
            $('#quantity').val($('.stok').text());
        }
    });

    if(window.location.href.split('/')[3] == 'checkout'){
        deliveryOption();

        $('input[name=payment]').change(function () { 
            const channel = $(this).val().split('#');

            $('#channel_icon').attr('src', channel[3]);
            $('#channel_fee').text(formatNumber(channel[2]));
            $('#channel_name').text(channel[1]);
            $('#channel_bank').text(channel[1].split(' ')[0]);

            $('#payment_fee').text(formatNumber(channel[2]));
            $('input[name=payment_fee]').val(channel[2]);

            total();
        });

        $('input[name=address_id]').change(function () { 
            $('#load').show();
            var settings = {
                "url": window.location.origin + "/api/select-address/620c137d81d7a05a57d2aa9ebb299b5b/"+$(this).val().replace('address', ''),
                "method": "POST",
                "timeout": 0,
            };
            
            $.ajax(settings).done(function (response) {
                $('#address-main').html('');
                $('#recipient_name').text(response.name + ' ' + response.phone);
                $('#recipient_address').text(response.address + ', ' + response.city.toUpperCase() + ', ' + response.province.toUpperCase() + ', ' + 'ID ' + response.kodepos);

                var settings = {
                    "url": window.location.origin + "/api/delivery-option/620c137d81d7a05a57d2aa9ebb299b5b/"+response.city_id,
                    "method": "POST",
                    "timeout": 0,
                    "headers": {
                      "Content-Type": "application/x-www-form-urlencoded"
                    },
                  };
                  
                  $.ajax(settings).done(function (response) {
                    $('#load').hide();
                    $('.delivery-option').html('');

                    $.each(response.costs, function (key, value) { 
                        $('.delivery-option').append(`
                        <label for="delivery#`+ value.service +`" class="bg-light w-100 pointer shadow-sm sans align-items-center mb-3 px-2 py-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="delivery" id="delivery#`+ value.service +`" value="`+ value.service +`#`+ value.description +`#`+ value.cost[0].value +`#`+ value.cost[0].etd +`">
                                <div class="mb-1">
                                    <div class="fw-bold"><span class="text-uppercase">`+ value.service +`</span><span class="ms-2 text-main">Rp `+ formatNumber(value.cost[0].value) +`</span></div>
                                    <div class="text-gray fs-small">estimasi `+ value.cost[0].etd +` hari kerja.</div>
                                </div>
                            </div>
                        </label>
                        `);
                    });
                    
                    $("input:radio[name='delivery']").filter(function(){
                        return $(this).val() === response.costs[0].service +`#`+ response.costs[0].description +`#`+ response.costs[0].cost[0].value +`#`+ response.costs[0].cost[0].etd
                     }).prop( "checked", true);

                    $('#delivery_service').text('JNE ' + response.costs[0].service + ' (' + response.costs[0].description + ')');
                    $('#delivery_ongkir').text(formatNumber(response.costs[0].cost[0].value));
                    $('#delivery_etd').text(response.costs[0].cost[0].etd);

                    $('#total_ongkir').text(formatNumber(response.costs[0].cost[0].value));
                    $('input[name=total_ongkir]').val(response.costs[0].cost[0].value);

                    deliveryOption();
                    total();
                  });
            });
        });
        
        total();
    }
});