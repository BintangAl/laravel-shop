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
function productImage(id) {
    $('.is-color').removeClass('active')
    $('.color'+id).addClass('active')
    $('.is-product').removeClass('active')
}
function productSize(price) {
    var color_price = $('#colorPrice').val();
    if (color_price != undefined) {
        $('#productPrice').text('Rp ' + formatNumber(Math.max.apply(Math, [price, color_price])));
    } else {
        $('#productPrice').text('Rp ' + formatNumber(price));
    }
}
function productColor(price) {
    var size_price = $('#sizePrice').val();
    $('#productPrice').text('Rp ' + formatNumber(Math.max.apply(Math, [price, size_price])));
}
function addCart(id, type, quantity, size, color){
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
            'type' : type ? type : 'add',
            'product_id' : id, 
            'quantity' : quantity ? quantity : $('#quantity').val(),
            'size_id' : size ? size : $('input[name=size]:checked').val(),
            'color_id' : color ? color : $('input[name=color]:checked').val(),
        },
        success: function (response) {
            if (type != 'buyNow') {
                $('#load').hide();
                alertSuccess();   
            } else {
                location.replace('/cart');
            }
            
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
                window.location = window.location.origin + '/login?link=' + location.href + '&action='+ (type ? type : 'cart') +'&product_id=' + id + '&quantity=' + $('#quantity').val() + '&size=' + $('input[name=size]:checked').val() + '&color=' + $('input[name=color]:checked').val();
            } else {
                $('#load').hide();
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
function minQuantityUpdate(id, totalId, productPrice) {
    var id = id.replace("min", "")
    var is_check = false;

    if ($('#cartCheck' + id).is(":checked")) {
        var is_check = true;
    }

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
                    
                    if ($(".cart-item").length == 0) {
                        $('#cartAlertBadge').empty();
                        $('#cartList').append(`
                            <div class="text-center text-gray fw-bold"><i class="bi bi-cart-x-fill"></i> Keranjang Kosong</div>
                            <a href="/" class="text-center fs-small text-main fw-bold">Belanja Sekarang.</a>
                        `);
                        $('#subTotalCart').text(0);
                        $('#cartDetail').empty();
                    } else {
                        $('#cartAlertBadge').text(parseInt($('#cartAlertBadge').text()) - 1)
                    }

                    if (is_check) {
                        totalPriceCart();
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
                $(totalId).text(formatNumber(parseInt(response.price) * parseInt(response.quantity)));

                if (is_check) {
                    if ($('.cart').is(":checked")) {
                        totalPriceCart();
                    }   
                }
            }
        });
    }
}
function addQuantityUpdate(id, totalId, max) {
    var id = id.replace("add", "")
    var is_check = false;

    if ($('#cartCheck' + id).is(":checked")) {
        var is_check = true;
    }
    
    if ($('#quantity'+id).val() < max) {
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
                $(totalId).text(formatNumber(parseInt(response.price) * parseInt(response.quantity)));
    
                if (is_check) {
                    if ($('.cart').is(":checked")) {
                        totalPriceCart();
                    }   
                }
            }
        });
    }
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
function getInteger(string) {
    return string.replace(/[A-Za-z$-,]/g, "");
}
function btnCheckoutReady() {
    if ($('.cart').is(":checked")) {
        $('#btnCheckout').removeAttr('disabled');
        $('#btnCheckout').attr('type', 'submit');
        $('#countCartCheck').text($('.cart:checked').length);
        totalPriceCart();
    } else {
        $('#btnCheckout').attr('disabled', 'true');
        $('#btnCheckout').attr('type', 'button');
        $('#countCartCheck').text(0);
    }
}
function totalPriceCart(){
    var total = 0;
    $('.total-price-cart.active').each(function(){
        total += parseInt(getInteger($(this).text()));
    })
    
    $('#subTotalCart').text(formatNumber(total));
}
function notesChange(note) {
    if (note != '') {
        $('#notes').removeClass('notes-active');
        $('#notes textarea').addClass('border-main');
    } else {
        $('#notes').addClass('notes-active');
        $('#notes textarea').removeClass('border-main');
    }
}
function arrowDown() {
    if ($('.arrow-down').hasClass('is-down')) {
        $('.arrow-down').removeClass('is-down');
        $('.fa-angle-down').css('transform', 'rotate(360deg)');  
    } else {
        $('.arrow-down').addClass('is-down');
        $('.fa-angle-down').css('transform', 'rotate(180deg)');  
    }
}
function getCountry(province) {
    $.getJSON("/asset/json/city.json", function(data) {
        var $select = $('#cityOptions');
        var selectize = $select[0].selectize;
        selectize.clear();
        selectize.clearOptions();
        $.each(data.rajaongkir.results, function(key, value) {
            if (value.province_id == province.split('#')[1]) {
                selectize.addOption({
                    value: value.city_name + '#' + value.city_id,
                    text: value.city_name
                });
            }
        });
    });
}
function validAddress() {
    var name = $('input[name=nama_penerima]').val();
    var phone = $('input[name=no_tlp]').val();
    var address = $('input[name=alamat]').val();
    var province = $('select[name=provinsi]').val();
    var city = $('select[name=kota]').val();
    var code = $('input[name=kodepos]').val();

    if (name != '' && phone != '' && address != '' && province != '' && city != '' && code != '') {
        $('#btnAddAddress').removeAttr('disabled');
        $('#btnAddAddress').click(function() {
            load();
        });
    } else {
        $('#btnAddAddress').attr('disabled', 'true');
    }
}

$(document).ready(function () {
    if (Notification.permission !== 'granted') {
        Notification.requestPermission();
    }
    
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

    if(window.location.href.split('/')[3].split('?')[0] == 'checkout'){
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

    if (window.location.pathname == '/cart') {
        btnCheckoutReady();

        $("#checkAll").click(function() {
            $(".cart").prop("checked", $('#checkAll').prop("checked"));
            $('#countCartCheck').text($('.cart:checked').length);

            if ($(this).prop("checked")) {
                $('#btnCheckout').removeAttr('disabled');
                $('#btnCheckout').attr('type', 'submit');

                $('.total-price-cart').addClass('active');
                totalPriceCart();

            } else {
                $('#btnCheckout').attr('disabled', 'true');
                $('#btnCheckout').attr('type', 'button');
                $('.total-price-cart').removeClass('active');
                $('#subTotalCart').text(0);
            }
        });
        
        $(".cart").click(function() {
            if (!$(this).prop("checked")) {
                $("#checkAll").prop("checked", false);
                $('#countCartCheck').text(0);

                $('#totalPriceCart' + $(this).val()).removeClass('active');
                var total = parseInt(getInteger($('#subTotalCart').text()));
                var uncheck = parseInt(getInteger($('#totalPriceCart' + $(this).val()).text()));

                $('#subTotalCart').text(formatNumber(total - uncheck));
            } else {
                $('#totalPriceCart' + $(this).val()).addClass('active');
                $('#subTotalCart').text(0);

                totalPriceCart();
            }
        });
    }    
});