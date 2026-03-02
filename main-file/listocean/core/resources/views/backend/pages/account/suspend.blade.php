@extends('backend.admin-master')
@section('site-title')
    {{__('Suspend Account')}}
@endsection
@section('content')
    <div class="row g-4 mt-0">
            <div class="col-xxl-8 col-lg-12 mt-0">
                <div class="topic bg-white padding-20 radius-10">
                    <div class="topic__inner">
                        <div class="topic_single">
                            <div class="topic_single__header flex-between">
                              <h4 class="topic_single__title">{{ __('Choose whom account you want to suspend.') }}</h4>
                            </div>
                            <div class="proposal_wrapper mt-4">
                                <div class="single_proposal">
                                    <div class="single_proposal__item">
                                        <div class="single_proposal__item__left">
                                            <div class="single_proposal__item__left">
                                                <h4 class="single_proposal__item__title mb-3">{{ __('User Details') }}</h4>
                                                <p class="single_proposal__item__para mt-3"><strong>{{ __('Wallet Balance:') }}</strong> {{ float_amount_with_currency_symbol($user_wallet_balance->balance) }}</p>
                                                <p class="single_proposal__item__para mt-3"><strong>{{ __('Name:') }}</strong> {{ $user->fullname }}</p>
                                                <p class="single_proposal__item__para mt-3"><strong>{{ __('Email:') }}</strong> {{ $user->email }}</p>
                                                <p class="single_proposal__item__para mt-3"><strong>{{ __('Phone:') }}</strong> {{ $user->phone }}</p>
                                                <p class="single_proposal__item__para mt-3"><strong>{{ __('Country:') }}</strong> {{ $user->country?->country }}</p>
                                                <p class="single_proposal__item__para mt-3"><strong>{{ __('State:') }}</strong> {{ $user->state?->state }}</p>
                                                <p class="single_proposal__item__para mt-3"><strong>{{ __('City:') }}</strong> {{ $user->city?->city }}</p>
                                            </div>
                                        </div>
                                        <div class="single_proposal__item__action">
                                            <div class="single_proposal__item__action__flex">
                                                <x-status.table.status-change
                                                    :title="__('Suspend')"
                                                    :class="'btn-profile btn-bg-cancel suspend_user_account'"
                                                    :url="route('admin.account.suspend',$user->id)"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </div>
@endsection
@section('scripts')
    <x-sweet-alert.sweet-alert2-js/>
    @include('backend.pages.account.account-js')
@endsection
