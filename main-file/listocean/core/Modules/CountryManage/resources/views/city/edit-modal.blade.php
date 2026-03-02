<!-- State Edit Modal -->
<div class="modal fade" id="editCityModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">{{ __('Edit City') }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{route('admin.city.edit')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="city_id" id="city_id" value="">

                <div class="modal-body">
                    <x-form.text :title="__('City')" :type="__('text')" :name="'city'" :id="'city_name'" :placeholder="__('Enter city name')"/>
                    <div class="single-input">
                        <label class="form__input__single__label">{{ __('Select Country') }}</label>
                        <select name="country" id="country_id" class="form__control radius-5 select2_activation select22-country">
                            <option value="">{{ __('Select Country') }}</option>
                            @foreach($all_countries as $data)
                                <option value="{{ $data->id }}">{{ $data->country }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="single-input mb-3">
                        <label class="form__input__single__label mt-3">{{ __('Select State') }}</label>
                        <select name="state" id="state_id" class="form__control radius-5 select2_activation select22-state">
                            <option value="">{{ __('Select State') }}</option>
                            @foreach($all_states as $data)
                                <option value="{{$data->id}}">{{ $data->state }}</option>
                            @endforeach
                        </select>
                        <span class="info_msg"></span>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="cmnBtn btn_5 btn_bg_blue radius-5" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="cmnBtn btn_5 btn_bg_blue radius-5 edit_city">{{__('Update')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>
