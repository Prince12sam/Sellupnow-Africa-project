@if(moduleExists('Membership'))
    @if(membershipModuleExistsAndEnable('Membership'))

        @if(!empty($user))
            @php
                $expireDate = \Carbon\Carbon::parse($user->membershipUser?->expire_date);
                $isMember = $expireDate->gt(\Carbon\Carbon::now());
            @endphp
            @if($isMember && $user->membershipUser?->membership_badge === 1)
                <div class="membership-badge">
                    <svg width="10" height="8" viewBox="0 0 10 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M0.573587 5.49356L0.00755343 1.86348C-0.029655 1.6253 0.0726682 1.38664 0.268943 1.25394C0.465218 1.12124 0.7187 1.11885 0.9173 1.24726L2.69075 2.39666L4.54931 0.210501C4.6628 0.0768496 4.82745 0 5 0C5.17255 0 5.3372 0.0768496 5.45069 0.210501L7.30925 2.39666L9.0827 1.24726C9.2813 1.11885 9.53478 1.12124 9.73106 1.25394C9.92733 1.38664 10.0297 1.6253 9.99245 1.86348L9.42641 5.49356H0.573587ZM9.31479 6.20955L9.14968 7.26778C9.0841 7.68974 8.72922 8 8.31341 8H1.68659C1.27078 8 0.915905 7.68974 0.850325 7.26778L0.685213 6.20955H9.31479Z" fill="white"/>
                    </svg>
                    <span>{{ __('MEMBER') }}</span>
                </div>
            @endif
        @endif

         @if(!empty($listing) && !empty($listing->user) && !empty($listing->user->membershipUser))
            @php
                $expireDate = \Carbon\Carbon::parse($listing->user?->membershipUser?->expire_date);
                $isMember = $expireDate->gt(\Carbon\Carbon::now());
            @endphp
            @if($isMember && $listing->user?->membershipUser?->membership_badge === 1)
                <div class="membership-badge">
                    <svg width="10" height="8" viewBox="0 0 10 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M0.573587 5.49356L0.00755343 1.86348C-0.029655 1.6253 0.0726682 1.38664 0.268943 1.25394C0.465218 1.12124 0.7187 1.11885 0.9173 1.24726L2.69075 2.39666L4.54931 0.210501C4.6628 0.0768496 4.82745 0 5 0C5.17255 0 5.3372 0.0768496 5.45069 0.210501L7.30925 2.39666L9.0827 1.24726C9.2813 1.11885 9.53478 1.12124 9.73106 1.25394C9.92733 1.38664 10.0297 1.6253 9.99245 1.86348L9.42641 5.49356H0.573587ZM9.31479 6.20955L9.14968 7.26778C9.0841 7.68974 8.72922 8 8.31341 8H1.68659C1.27078 8 0.915905 7.68974 0.850325 7.26778L0.685213 6.20955H9.31479Z" fill="white"/>
                    </svg>
                    <span>{{ __('MEMBER') }}</span>
                </div>
            @endif
        @endif
    @endif
@endif
