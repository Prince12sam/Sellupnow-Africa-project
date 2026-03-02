@php
    $countries = \Modules\CountryManage\app\Models\Country::all_countries();
    $restrictedCountryCodes = $countries
        ->pluck('country_code')
        ->filter(fn($code) => is_string($code) && preg_match('/^[a-zA-Z]{2}$/', $code))
        ->map(fn($code) => strtolower($code))
        ->values();

    $restrictedCountriesJson = $restrictedCountryCodes->toJson();
@endphp
<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/intlTelInput.min.js"></script>
<script type="text/javascript">
    (function($) {
        "use strict";

        $(document).ready(function() {
            const input = document.querySelector("#phone");
            const hiddenInput = document.querySelector("#country-code");
            const errorMsg = document.querySelector("#error-msg");
            if (!input || !hiddenInput || !errorMsg || typeof window.intlTelInput !== 'function') return;

            const errorMap = ["Invalid number", "Invalid country code", "Too short", "Too long", "Invalid number"];
            const allowedCountryCodes = {!! $restrictedCountriesJson !!};
            const defaultCountry = allowedCountryCodes.length ? allowedCountryCodes[0] : 'us';

            const iti = window.intlTelInput(input, {
                hiddenInput: "full_number",
                nationalMode: false,
                formatOnDisplay: true,
                separateDialCode: true,
                autoHideDialCode: true,
                autoPlaceholder: "aggressive",
                initialCountry: defaultCountry,
                placeholderNumberType: "MOBILE",
                preferredCountries: allowedCountryCodes,
                utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.1.1/js/utils.js",
                allowDropdown: true,
                searchCountryFlag: true
            });

            $('.iti__country').each(function() {
                const countryDataCode = $(this).attr('data-country-code').toLowerCase();
                if (countryDataCode && !allowedCountryCodes.includes(countryDataCode)) {
                    $(this).hide();
                }
            });

            input.addEventListener('input', validatePhoneNumber);

            function validatePhoneNumber() {
                reset();
                const isValid = iti.isValidNumber();
                $(input).toggleClass('form-control is-invalid', !isValid).toggleClass('form-control is-valid', isValid);
                if (!isValid) {
                    const errorCode = iti.getValidationError();
                    errorMsg.innerHTML = errorMap[errorCode];
                    $(errorMsg).toggle(!!errorCode);
                } else {
                    hiddenInput.value = iti.getSelectedCountryData().dialCode;
                }
            }

            function reset() {
                $(input).removeClass('form-control is-invalid is-valid');
                errorMsg.innerHTML = "";
                $(errorMsg).hide();
            }
        });
    })(jQuery);
</script>

<script type="text/javascript">
    (function($) {
        "use strict";

        $(document).on('keyup', '#phone', function() {
            let phone = $(this).val();
            let phoneRegex = /([0-9]{4})|(\([0-9]{3}\)\s+[0-9]{3}\-[0-9]{4})/;
            if (phoneRegex.test(phone) && phone != '') {
                $.ajax({
                    url: "{{ route('user.phone.number.availability') }}",
                    type: 'post',
                    data: {
                        phone: phone
                    },
                    success: function(res) {
                        if (res.status == 'available') {
                            $("#phone_availability").html(
                                "<span style='color: green;'>" + res.msg +
                                "</span>");
                        } else {
                            $("#phone_availability").html(
                                "<span style='color: red;'>" + res.msg +
                                "</span>");
                        }
                    }
                });
            } else if(phone.length > 3) {
                $("#phone_availability").html(
                    "<span style='color: red;'>{{ __('Enter valid phone number') }}</span>"
                );
            }else if(phone.length == 0) {
                $("#phone_availability").html(
                    "<span style='color: red;'></span>"
                );
            }
        });

    })(jQuery);
</script>
