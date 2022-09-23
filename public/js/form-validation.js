// Example starter JavaScript for disabling form submissions if there are invalid fields
(() => {
  'use strict'

  // Fetch all the forms we want to apply custom Bootstrap validation styles to
  const forms = document.querySelectorAll('.needs-validation');
  
  $('select[name=provinsi]').change(function (e) { 
    e.preventDefault();        
    $('.city-options-box > .selectize-control > .selectize-input').removeClass('border-success')
    $('.city-options-box > .selectize-control > .selectize-input').addClass('border-danger')

    if ($('.province-options-box > .selectize-control > .selectize-input').hasClass('border-danger')) {    
      $('.province-options-box > .selectize-control > .selectize-input').removeClass('border-danger')
      $('.province-options-box > .selectize-control > .selectize-input').addClass('border-success')
    }
  });

  $('select[name=kota]').change(function (e) { 
    e.preventDefault();
    if ($('.city-options-box > .selectize-control > .selectize-input').hasClass('border-danger')) {
      if ($('select[name=kota]').val() != '') {
        $('.city-options-box > .selectize-control > .selectize-input').removeClass('border-danger')
        $('.city-options-box > .selectize-control > .selectize-input').addClass('border-success') 
      }
    }
  });

  // Loop over them and prevent submission
  Array.from(forms).forEach(form => {
    form.addEventListener('submit', event => {
      if (!form.checkValidity()) {
        event.preventDefault()
        event.stopPropagation()
      }

      form.classList.add('was-validated')
      if ($('select[name=provinsi]').val() == '') {
        $('.province-options-box > .selectize-control > .selectize-input').addClass('border border-danger')
      } else {
        $('.province-options-box > .selectize-control > .selectize-input').addClass('border border-success')
      }

      if($('select[name=kota]').val() == ''){
        $('.city-options-box > .selectize-control > .selectize-input').addClass('border border-danger')
      } else {
        $('.city-options-box > .selectize-control > .selectize-input').addClass('border border-success')
      }
    }, false)
  })
})()