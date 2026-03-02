@extends('layouts.app')

@section('content')
    <div class="d-flex align-items-center flex-wrap gap-3 justify-content-between px-3">
        <div>
            <h4 class="mb-0">{{ __('User Details (Customer Web)') }}</h4>
            <div class="text-muted" style="font-size: 12px;">#{{ $user->id }}</div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.customer.index') }}" class="btn btn-outline-secondary">{{ __('Back') }}</a>
            <a href="{{ route('admin.siteCustomer.edit', $user->id) }}" class="btn btn-primary">{{ __('Edit') }}</a>
        </div>
    </div>

    <div class="container-fluid mt-3">
        <div class="row g-3">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <img src="{{ $user->thumbnail ?? asset('assets/icons/user.svg') }}" alt="user" width="50" height="50" class="rounded-circle" style="object-fit: cover;" />
                            <div>
                                <div class="fw-medium">{{ $user->fullName ?? __('User') }}</div>
                                <div class="text-muted" style="font-size: 12px;">{{ $user->email ?? '--' }}</div>
                            </div>
                        </div>

                        <div class="mb-2"><strong>{{ __('First Name') }}:</strong> {{ $user->first_name ?? '--' }}</div>
                        <div class="mb-2"><strong>{{ __('Last Name') }}:</strong> {{ $user->last_name ?? '--' }}</div>
                        <div class="mb-2"><strong>{{ __('Username') }}:</strong> {{ $user->username ?? '--' }}</div>
                        <div class="mb-2"><strong>{{ __('Phone') }}:</strong> {{ $user->phone ?? '--' }}</div>
                        <div class="mb-2"><strong>{{ __('Status') }}:</strong>
                            @if((int) ($user->status ?? 1) === 1)
                                <span class="badge bg-success">{{ __('Active') }}</span>
                            @else
                                <span class="badge bg-secondary">{{ __('Inactive') }}</span>
                            @endif
                        </div>
                        <div class="mb-2"><strong>{{ __('Verified') }}:</strong>
                            @if((int) ($user->verified_status ?? 0) === 1)
                                <span class="badge bg-success">{{ __('Yes') }}</span>
                            @else
                                <span class="badge bg-secondary">{{ __('No') }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="mb-3">{{ __('Reset Password') }}</h5>
                        <form method="POST" action="{{ route('admin.siteCustomer.reset-password', $user->id) }}">
                            @csrf
                            <div class="mb-3">
                                <label for="password" class="form-label">{{ __('Password') }}</label>
                                <input type="password" name="password" id="password" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">{{ __('Confirm Password') }}</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-outline-primary">{{ __('Update Password') }}</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="mb-3">{{ __('Membership / Subscription') }}</h5>

                        @php
                            $sub = $user->activeSubscription ?? null;
                        @endphp

                        <div class="row g-3">
                            <div class="col-lg-6">
                                <div class="p-3 border rounded">
                                    <div class="fw-medium mb-2">{{ __('Current Active Subscription') }}</div>
                                    @if($sub)
                                        <div class="mb-1"><strong>{{ __('Plan') }}:</strong> {{ $sub->subscription_name ?? '--' }}</div>
                                        <div class="mb-1"><strong>{{ __('Start') }}:</strong> {{ $sub->start_date ?? '--' }}</div>
                                        <div class="mb-1"><strong>{{ __('Expire') }}:</strong> {{ $sub->expire_date ?? '--' }}</div>
                                        <div class="mb-1"><strong>{{ __('Status') }}:</strong> {{ $sub->status ?? '--' }}</div>
                                    @else
                                        <div class="text-muted">{{ __('No active subscription found.') }}</div>
                                    @endif
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <form method="POST" action="{{ route('admin.siteCustomer.subscription.update', $user->id) }}">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('Assign From Membership Plan') }}</label>
                                        <select name="membership_plan_id" class="form-select">
                                            <option value="">{{ __('-- Select plan (optional) --') }}</option>
                                            @foreach(($membershipPlans ?? collect()) as $plan)
                                                <option value="{{ $plan->id }}" {{ (string) old('membership_plan_id') === (string) $plan->id ? 'selected' : '' }}>
                                                    {{ $plan->name }}
                                                    @if((int) ($plan->duration_days ?? 0) > 0)
                                                        ({{ (int) $plan->duration_days }} {{ __('days') }})
                                                    @else
                                                        ({{ __('no expiry') }})
                                                    @endif
                                                    @if((int) ($plan->is_active ?? 1) !== 1)
                                                        - {{ __('inactive') }}
                                                    @endif
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="text-muted mt-1" style="font-size: 12px;">
                                            {{ __('If you select a plan, the subscription name is set automatically. Leave Expire Date empty to auto-calculate using the plan duration.') }}
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">{{ __('Subscription Name') }}</label>
                                        <input type="text" name="subscription_name" class="form-control"
                                            value="{{ old('subscription_name', $sub->subscription_name ?? '') }}">
                                        <div class="text-muted mt-1" style="font-size: 12px;">
                                            {{ __('Required if you do not select a plan above.') }}
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">{{ __('Start Date') }}</label>
                                            <input type="date" name="start_date" class="form-control"
                                                value="{{ old('start_date', $sub->start_date ?? '') }}">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">{{ __('Expire Date') }}</label>
                                            <input type="date" name="expire_date" class="form-control"
                                                value="{{ old('expire_date', $sub->expire_date ?? '') }}">
                                        </div>
                                    </div>

                                    <div class="mb-3" style="max-width: 240px;">
                                        <label class="form-label">{{ __('Status') }}</label>
                                        <select name="status" class="form-select" required>
                                            @php $statusOld = old('status', $sub->status ?? 'active'); @endphp
                                            <option value="active" {{ $statusOld === 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                                            <option value="inactive" {{ $statusOld === 'inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                                            <option value="canceled" {{ $statusOld === 'canceled' ? 'selected' : '' }}>{{ __('Canceled') }}</option>
                                        </select>
                                    </div>

                                    <button type="submit" class="btn btn-outline-primary">{{ __('Save Subscription') }}</button>
                                </form>
                                @error('subscription')
                                    <div class="text-danger mt-2">{{ $message }}</div>
                                @enderror
                                @error('membership_plan_id')
                                    <div class="text-danger mt-2">{{ $message }}</div>
                                @enderror
                                @error('subscription_name')
                                    <div class="text-danger mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
