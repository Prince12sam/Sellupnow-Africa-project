<div class="single-input">
    <label class="label-title">{{ $title }} @if($required) <span class="text-danger">*</span> @endif </label>
    <select name="{{ $name ?? '' }}" id="{{ $id ?? '' }}" class="select2_activation get_state_city city_select2">
        <option value="">{{ __('Select City') }}</option>
        @foreach($all_cities = \Modules\CountryManage\app\Models\City::all_cities() as $city)
            <option value="{{ $city->id }}" @if($city->id == Auth::guard('web')->user()->city_id) selected @endif>{{ $city->city }}</option>
        @endforeach
    </select>
    <span class="city_info"></span>
</div>
