<div class="seller-phone text-center">
    <p>{{ __('Phone') }}</p>
    <span type="text" id="default_phone_number_show_for_responsive" class="number">{{ __('+880 XXX XXX XX') }}</span>
    @if($listing->phone_hidden === 0)
        <div class="number" id="phoneNumberForResponsive">{{ $listing->phone }}</div>
        <a href="#" class="show-number callPhoneNumberBtn" id="userPhoneNumberBtnForResponsive">{{ __('Show Number') }}</a>
    @endif
</div>
