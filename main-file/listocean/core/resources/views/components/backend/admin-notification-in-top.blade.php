@if($notification->type =='Create Listing' || $notification->type =='Edit Listing')
    <a href="{{ route('admin.listings.details', $notification->identity) }}" class="dashboard__notification__list__item click-notification">
        <li class="dashboard__header__notification__wrap__list__item">
            <div class="dashboard__header__notification__wrap__list__flex">
                <div class="dashboard__header__notification__wrap__list__icon">
                    <i class="las la-bell"></i>
                </div>
                <div class="dashboard__header__notification__wrap__list__contents">
                    {{ $notification->message ?? '' }}  <strong>#{{ $notification->identity }}</strong>
                    <span class="dashboard__header__notification__wrap__list__contents__sub">
                        {{ $notification->created_at->toFormattedDateString() }}
                    </span>
                </div>
            </div>
        </li>
    </a>
@endif

@if($notification->type =='Ticket')
    <a href="{{ route('admin.ticket.details', $notification->identity) }}" class="dashboard__notification__list__item click-notification">
        <li class="dashboard__header__notification__wrap__list__item">
            <div class="dashboard__header__notification__wrap__list__flex">
                <div class="dashboard__header__notification__wrap__list__icon">
                    <i class="las la-bell"></i>
                </div>
                <div class="dashboard__header__notification__wrap__list__contents">
                    {{ $notification->message ?? '' }}  <strong>#{{ $notification->identity }}</strong>
                    <span class="dashboard__header__notification__wrap__list__contents__sub">
                        {{ $notification->created_at->toFormattedDateString() }}
                    </span>
                </div>
            </div>
        </li>
    </a>
@endif

@if(moduleExists('Membership'))
    @if(membershipModuleExistsAndEnable('Membership'))
        @if($notification->type =='Buy Membership')
            <a href="{{ route('admin.user.membership.read.unread', $notification->identity) }}" class="dashboard__notification__list__item click-notification">
                <li class="dashboard__header__notification__wrap__list__item">
                    <div class="dashboard__header__notification__wrap__list__flex">
                            <div class="dashboard__header__notification__wrap__list__icon">
                                <i class="las la-bell"></i>
                            </div>
                            <div class="dashboard__notification__list__content">
                                <span class="dashboard__notification__list__content__title">{{ $notification->message ?? '' }}  <strong>#{{ $notification->identity }}</strong></span> <br>
                                <span class="dashboard__notification__list__content__time">{{ $notification->created_at->toFormattedDateString() }}</span>
                            </div>
                     </div>
                </li>
            </a>
        @endif
        @if($notification->type == 'Renew Membership')
            <a href="{{ route('admin.user.membership.read.unread', $notification->identity) }}" class="dashboard__notification__list__item click-notification">
                <li class="dashboard__header__notification__wrap__list__item">
                    <div class="dashboard__header__notification__wrap__list__flex">
                        <div class="dashboard__header__notification__wrap__list__icon">
                            <i class="las la-bell"></i>
                        </div>
                        <div class="dashboard__notification__list__content">
                            <span class="dashboard__notification__list__content__title">{{ $notification->message ?? '' }}  <strong>#{{ $notification->identity }}</strong></span> <br>
                            <span class="dashboard__notification__list__content__time">{{ $notification->created_at->toFormattedDateString() }}</span>
                        </div>
                    </div>
                </li>
            </a>
        @endif
        @if($notification->type == 'Upgrade Membership')
            <a href="{{ route('admin.user.membership.read.unread', $notification->identity) }}" class="dashboard__notification__list__item click-notification">
                <li class="dashboard__header__notification__wrap__list__item">
                    <div class="dashboard__header__notification__wrap__list__flex">
                        <div class="dashboard__header__notification__wrap__list__icon">
                            <i class="las la-bell"></i>
                        </div>
                        <div class="dashboard__notification__list__content">
                            <span class="dashboard__notification__list__content__title">{{ $notification->message ?? '' }}  <strong>#{{ $notification->identity }}</strong></span> <br>
                            <span class="dashboard__notification__list__content__time">{{ $notification->created_at->toFormattedDateString() }}</span>
                        </div>
                    </div>
                </li>
            </a>
        @endif
    @endif
@endif
