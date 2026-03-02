<!-- brand Edit Modal -->
<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal_xl__fixed">
        <div class="popup_contents modal-content">
            <div class="popup_contents__header">
                <div class="popup_contents__header__flex">
                    <div class="popup_contents__header__contents">
                        <h2 class="popup_contents__header__title">{{ __('Add New Brand') }}</h2>
                    </div>
                    <div class="popup_contents__header__close" data-bs-dismiss="modal">
                        <span class="popup_contents__close popup_close"> <i class="fas fa-times"></i> </span>
                    </div>
                </div>
            </div>
            <div class="popup_contents__body">
                <form action="{{route('admin.brand.all')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form__input__single">
                        <x-form.text :title="__('Brand')" :type="__('text')" :name="'title'" :id="'brand'" :placeholder="__('Enter brand name')"/>
                    </div>
                    <div class="form__input__single">
                        <x-form.text :title="__('Url')" :type="__('text')" :name="'url'" :id="'url'" :placeholder="__('Enter url')"/>
                    </div>
                    <div class="form__input__single">
                        <x-form.active-inactive :title="__('Select Status')" :info="__('If you select inactive the not show in frontend')" />
                    </div>
                    <x-backend.image :title="__('')" :name="'image'" :dimentions="__('300x300(optional)')"/>
                    <div class="popup_contents__footer flex_btn justify-content-end profile-border-top">
                        <button type="submit" class="cmnBtn btn_5 btn_bg_blue radius-5">{{__('Submit')}}</button>
                        <a href="javascript:void(0)" class="cmnBtn btn_5 btn_bg_danger radius-5" data-bs-dismiss="modal">{{__('Cancel')}}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
