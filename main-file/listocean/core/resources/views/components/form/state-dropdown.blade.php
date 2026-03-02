<div class="single-input">
    <label class="label-title">{{ $title }} @if($required) <span class="text-danger">*</span> @endif </label>
    <select name="{{ $name ?? '' }}" id="{{ $id ?? '' }}" class="select2_activation get_country_state state_select2">
        <option value="">{{ __('Select State') }}</option>
        @foreach($all_states = \Modules\CountryManage\app\Models\State::all_states() as $state)
            <option value="{{ $state->id }}" @if(Auth::guard('web')->check() && $state->id == Auth::guard('web')->user()->state_id) selected @endif>{{ $state->state }}</option>
        @endforeach
    </select>
    <span class="state_info"></span>
</div>
