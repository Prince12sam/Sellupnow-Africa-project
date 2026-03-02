@if($user_business_hour === true)
    @php
        $user_business_hours = \Modules\Membership\app\Models\BusinessHours::where('user_id', $listing->user_id)->first();
         if ($user_business_hours) {
             $business_hours_data = json_decode($user_business_hours->day_of_week, true);
         } else {
             $business_hours_data = null;
         }
    @endphp
    @if($business_hours_data)
        <div class="business-hour box-shadow1">
            <h3 class="head5 business-head d-flex">{{ __('Business Hours') }} </h3>
            <div class="hours-wraper">
                @foreach(['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'] as $day)
                    <div class="days">
                        <div class="name">
                            <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9 8.33333L7 7V3.66667M1 7C1 7.78793 1.15519 8.56815 1.45672 9.2961C1.75825 10.0241 2.20021 10.6855 2.75736 11.2426C3.31451 11.7998 3.97595 12.2417 4.7039 12.5433C5.43185 12.8448 6.21207 13 7 13C7.78793 13 8.56815 12.8448 9.2961 12.5433C10.0241 12.2417 10.6855 11.7998 11.2426 11.2426C11.7998 10.6855 12.2417 10.0241 12.5433 9.2961C12.8448 8.56815 13 7.78793 13 7C13 6.21207 12.8448 5.43185 12.5433 4.7039C12.2417 3.97595 11.7998 3.31451 11.2426 2.75736C10.6855 2.20021 10.0241 1.75825 9.2961 1.45672C8.56815 1.15519 7.78793 1 7 1C6.21207 1 5.43185 1.15519 4.7039 1.45672C3.97595 1.75825 3.31451 2.20021 2.75736 2.75736C2.20021 3.31451 1.75825 3.97595 1.45672 4.7039C1.15519 5.43185 1 6.21207 1 7Z" stroke="#1E293B" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg> {{ $day }}
                        </div>
                        <div class="time">
                            @if($business_hours_data && isset($business_hours_data['opening_times'][strtolower($day)]) && isset($business_hours_data['closing_times'][strtolower($day)]))
                                {{ $business_hours_data['opening_times'][strtolower($day)] }} - {{ $business_hours_data['closing_times'][strtolower($day)] }}
                            @else
                                {{ __('Closed') }}
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
@endif
