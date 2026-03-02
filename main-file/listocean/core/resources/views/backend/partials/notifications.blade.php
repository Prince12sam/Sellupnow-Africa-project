<div class="dashboard__header__right__item">
    <div class="dashboard__header__notification">
        <a href="javascript:void(0)" class="dashboard__header__notification__icon"> <i class="material-symbols-outlined">{{ __('Notifications') }}</i> </a>
        <span class="dashboard__header__notification__number">{{ \App\Models\Backend\AdminNotification::unread_notification()->count() }}</span>

        <div class="dashboard__header__notification__wrap">
            <h6 class="dashboard__header__notification__wrap__title"> {{ __('Notifications') }} </h6>
            <ul class="dashboard__header__notification__wrap__list">
                <!-- Display notification details -->
                @foreach(\App\Models\Backend\AdminNotification::unread_notification() as $notification)
                    <x-backend.admin-notification-in-top :notification="$notification"/>
                @endforeach
            </ul>
            <a href="{{ route('admin.notification.all') }}" class="dashboard__header__notification__wrap__btn"> {{ __('See All Notification') }} </a>
        </div>
    </div>
</div>
