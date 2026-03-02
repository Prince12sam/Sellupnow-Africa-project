@if(moduleExists("Chat"))
    @if(is_null($listing->admin_id) && $listing->user_id != null && $listing->user_id != 0)
        @if(auth()->check() && Auth::guard('web')->user()->id !== $listing->user_id)
            @if(auth()->check())
                @if($listing->user_id !== Auth::guard('web')->user()->id)
                    <div class="btn-wrapper">
                        <form action="{{ route('user.message.send') }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="member_id" id="member_id" value="{{ $listing->user_id }}">
                            <input type="hidden" name="from_user" id="from_user"  value="{{ Auth::guard('web')->user()->id }}">
                            <input type="hidden" name="listing_id" id="listing_id"  value="{{ $listing->id }}">
                            <div class="send-massage">
                                <button type="submit" class="cmn-btn2 w-100">{{ __('Send a Massage') }}</button>
                            </div>
                        </form>
                    </div>
                @elseif($listing->user_id === Auth::guard('web')->user()->id)
                    <div class="btn-wrapper">
                        <form action="{{ route('member.message.send') }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="user_id" id="user_id" value="{{ $listing->user_id }}">
                            <input type="hidden" name="from_user" id="from_user"  value="{{ Auth::guard('web')->user()->id }}">
                            <input type="hidden" name="listing_id" id="listing_id"  value="{{ $listing->id }}">
                            <div class="send-massage">
                                <button type="submit" class="cmn-btn2 w-100">{{ __('Send a Massage') }}</button>
                            </div>
                        </form>
                    </div>
                @endif
            @else
                <div class="send-massage">
                    <a href="javascript:void(0)" class="cmn-btn2 w-100" data-bs-toggle="modal" data-bs-target="#loginModal">{{ __('Sign in for Massage') }}</a>
                </div>
            @endif
        @endif
    @endif
@endif

@if(empty($listing->admin_id))
    <div class="seller-details box-shadow1">
        <div class="seller-details-wraper">
            @if($listing->user_id === 0)
                <div class="seller-img">
                    {!! userProfileImageView(optional($listing->user)->image) !!}
                </div>
            @else
                <a href="{{ route('about.user.profile', $listing?->user?->username) }}">
                    <div class="seller-img">
                        {!! userProfileImageView(optional($listing->user)->image) !!}
                    </div>
                </a>
            @endif
            <div class="seller-name">
                <div class="name">
                    @if($listing->user_id === 0)
                        <span>{{ $listing->guestListing?->guestfullname }} </span>
                    @else
                        <a href="{{ route('about.user.profile', $listing?->user?->username) }}">
                            <span>{{ optional($listing->user)->fullname }} </span>
                        </a>
                    @endif
                    <x-badge.user-verified-badge :listing="$listing"/>
                </div>

                @if($listing->user_id != null && $listing->user_id != 0)
                    <div class="member-listing">
                        <span class="listing">
                            @if($userTotalListings > 1)
                                {{ $userTotalListings }} {{ __('listings') }}
                            @else
                                {{ $userTotalListings }} {{ __('listing') }}
                            @endif
                        </span>
                        <span class="dot"></span>
                        {{ __('Member since') }}
                        {{ \Carbon\Carbon::parse(optional($listing->user)->created_at)->format('Y') }}
                    </div>
                @endif

            </div>
        </div>
    </div>
@endif
