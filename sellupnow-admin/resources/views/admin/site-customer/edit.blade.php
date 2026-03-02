@extends('layouts.app')

@section('content')
    <div class="container-fluid my-md-0 my-4">
        <form action="{{ route('admin.siteCustomer.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row h-100vh">
                <div class="col-12 m-auto">
                    <div class="card rounded-12 border-0 shadow-md">
                        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2 py-3">
                            <h3 class="m-0">{{ __('Edit User (Customer Web)') }}</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-7">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mt-3">
                                                <x-input label="First Name" name="first_name" type="text" placeholder="Enter First Name" required="true" :value="old('first_name', $user->first_name)" />
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mt-3">
                                                <x-input label="Last Name" name="last_name" type="text" placeholder="Enter Last Name" required="true" :value="old('last_name', $user->last_name)" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mt-3">
                                                <x-input label="Username" name="username" type="text" placeholder="Enter Username" required="true" :value="old('username', $user->username)" />
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mt-3">
                                                <x-input type="email" name="email" label="Email" placeholder="Enter Email Address" required="true" :value="old('email', $user->email)" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-3">
                                        <x-input label="Phone Number" name="phone" type="text" placeholder="Enter phone number" :value="old('phone', $user->phone)" />
                                    </div>

                                    <div class="mt-3" style="max-width: 260px;">
                                        <x-select label="Status" name="status">
                                            <option value="1" {{ (string) old('status', (string) ($user->status ?? 1)) === '1' ? 'selected' : '' }}>{{ __('Active') }}</option>
                                            <option value="0" {{ (string) old('status', (string) ($user->status ?? 1)) === '0' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                                        </x-select>
                                    </div>

                                    <div class="text-muted mt-3" style="font-size: 12px;">
                                        {{ __('Profile photo upload is managed on the customer web.') }}
                                    </div>
                                </div>
                                <div class="col-lg-5">
                                    <div>
                                        <h5>{{ __('Profile') }}</h5>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="{{ $user->thumbnail ?? asset('assets/icons/user.svg') }}" alt="user" width="50" height="50" class="rounded-circle" style="object-fit: cover;" />
                                        <div>
                                            <div class="fw-medium">{{ $user->fullName ?? __('User') }}</div>
                                            <div class="text-muted" style="font-size: 12px;">#{{ $user->id }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <a href="{{ route('admin.siteCustomer.show', $user->id) }}" class="btn btn-lg btn-outline-secondary">
                                {{ __('Cancel') }}
                            </a>
                            <button type="submit" class="btn btn-lg btn-primary">
                                {{ __('Update') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
