<!-- Country Edit Modal -->
<div class="modal fade" id="editCountryModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal_xl__fixed">
        <div class="popup_contents modal-content">
            <div class="popup_contents__header">
                <div class="popup_contents__header__flex">
                    <div class="popup_contents__header__contents">
                        <h2 class="popup_contents__header__title">{{ __('Edit Country') }}</h2>
                    </div>
                    <div class="popup_contents__header__close" data-bs-dismiss="modal">
                        <span class="popup_contents__close popup_close"> <i class="fas fa-times"></i> </span>
                    </div>
                </div>
            </div>
            <div class="popup_contents__body">
                <form action="{{route('admin.country.edit')}}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <input type="hidden" name="country_id" id="country_id" value="">

                    <div class="form__input__single">
                        <label for="title" class="label-title">{{__('Country')}}</label>
                        <input type="text" name="edit_country" id="edit_country" value="{{ old('country') }}" placeholder="{{ __('Enter country') }}" class="form-control" >
                    </div>

                    <div class="popup_contents__footer flex_btn justify-content-end profile-border-top">
                        <button type="submit" class="cmnBtn btn_5 btn_bg_blue radius-5 update_country">{{__('Submit')}}</button>
                        <a href="javascript:void(0)" class="cmnBtn btn_5 btn_bg_danger radius-5" data-bs-dismiss="modal">{{__('Cancel')}}</a>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
