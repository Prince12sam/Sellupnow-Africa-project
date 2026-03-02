@props([
    'selector' => '#telephone',
    'submitButtonId' => 'register_button',
    'key' => 1
])

<link rel="stylesheet" href="{{asset('assets/common/css/intlTelInput.min.css')}}">
<style>
    #telephone.error{
        border-color: var(--main-color-one);
    }
    #telephone.success{
        border-color: var(--main-color-three);
    }
    .single-input .iti {
        width: 100%;
    }
</style>

<script src="{{asset('assets/common/js/intlTelInput.js')}}"></script>
<script>
    eval('let input' + `{{$key}}` + '= undefined;');
    eval('let iti' + `{{$key}}` + '= undefined;');

    let input{{$key}} = document.querySelector(`{{$selector}}`);

    let iti{{$key}} = window.intlTelInput(input{{$key}}, {
        autoPlaceholder: "aggressive",
        // formatOnDisplay: false,
        // initialCountry: "auto",
        // localizedCountries: { 'de': 'Deutschland' },
        excludeCountries: ["il"],
        separateDialCode: true,
        utilsScript: `{{asset("assets/common/js/utils.js")}}`
    });

    // When user select a country and input another country phone number then again select the correct country then auto validate the full number
    $(document).on('keyup', `{{$selector}}`, function () {
        let el = $(this);
        let inputNumbers = el.val();

        let phoneNumbers = inputNumbers.replace(/[^0-9+]/g, '');
        el.val(phoneNumbers);

        $('.error-text').remove();

        let isValid = iti{{$key}}.isValidNumber();
        if (!isValid) {
            el.addClass('error');
            el.parent().after(`<p class="text-end text-danger error-text"><small>{{__('The number is not valid.')}}</small></p>`);
            document.getElementById(`{{$submitButtonId}}`).disabled = true;
        } else {
            el.val(iti{{$key}}.getNumber());

            el.removeClass('error');
            el.addClass('success');
            el.parent().after(`<p class="text-end text-success error-text"><small>{{__('The number is perfect.')}}</small></p>`);
            setTimeout(function () {
                el.removeClass('success');
                $('.error-text').remove();
            }, 5000);
            document.getElementById(`{{$submitButtonId}}`).disabled = false;
        }
    });
</script>
