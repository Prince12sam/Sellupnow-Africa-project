@if($user_enquiry_form === true)
    <div class="business-hour enquiry-hour box-shadow1 mt-4">
        <h3 class="head5 enquiry-head d-flex">{{ __('Enquiry Form') }} </h3>
        <div class="enquiry-wraper">
            <div class="enquiry_form_submit">
                <form action="{{route('visitor.enquiry.form.submit')}}" method="post">
                    @csrf

                    <input type="hidden" name="listing_id" id="listing_id" value="{{ $listing->id }}">
                    <input type="hidden" name="user_id" id="user_id" value="{{ $listing->user_id }}">

                    <div class="input-wraper mt-3">
                        <label for="name">{{ __('Name') }}</label>
                        <input class="form-control"  type="text" name="name" id="name" placeholder="{{ __('Name') }}">
                    </div>
                    <div class="input-wraper mt-3">
                        <label for="email">{{ __('Email') }}</label>
                        <input  class="form-control" type="email" name="email" id="email" placeholder="{{ __('Email') }}">
                    </div>
                    <div class="input-wraper mt-3">
                        <label for="Phone">{{ __('Phone') }}</label>
                        <input  class="form-control" type="number" name="phone" id="phone" placeholder="{{ __('Phone') }}">
                    </div>
                    <div class="input-wraper mt-3">
                        <label for="#message">{{ __('Message') }}</label>
                        <textarea  class="form-control" type="text" name="message" id="message" placeholder="{{ __('Message') }}"></textarea>
                    </div>
                    <div class="save-change-btn mt-3 text-start btn-sm">
                        <button type="submit" class="red-btn">{{ __('Submit Enquiry') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif
