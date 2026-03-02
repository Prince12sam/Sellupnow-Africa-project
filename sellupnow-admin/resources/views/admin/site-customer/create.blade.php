@extends('layouts.app')

@section('content')
    <div class="container-fluid my-md-0 my-4">
        <form action="{{ route('admin.siteCustomer.store') }}" method="POST">
            @csrf
            <div class="row h-100vh">
                <div class="col-12 m-auto">
                    <div class="card rounded-12 border-0 shadow-md">
                        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2 py-3">
                            <h3 class="m-0">{{ __('Add User (Customer Web)') }}</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-7">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mt-3">
                                                <x-input label="First Name" name="first_name" type="text" placeholder="Enter First Name" required="true" :value="old('first_name')" />
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mt-3">
                                                <x-input label="Last Name" name="last_name" type="text" placeholder="Enter Last Name" required="true" :value="old('last_name')" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mt-3">
                                                <x-input label="Username" name="username" type="text" placeholder="Enter Username" required="true" :value="old('username')" />
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mt-3">
                                                <x-input type="email" name="email" label="Email" placeholder="Enter Email Address" required="true" :value="old('email')" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-3">
                                        <x-input label="Phone Number" name="phone" type="text" placeholder="Enter phone number" :value="old('phone')" />
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mt-3">
                                            <x-input type="password" name="password" label="Password" placeholder="Enter Password" required="true" />
                                        </div>

                                        <div class="col-md-6 mt-3">
                                            <x-input type="password" name="password_confirmation" label="Confirm Password" placeholder="Enter Confirm Password" required="true" />
                                        </div>
                                    </div>

                                    <div class="text-muted mt-3" style="font-size: 12px;">
                                        {{ __('This creates a user in the customer web database.') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <a href="{{ route('admin.customer.index') }}" class="btn btn-lg btn-outline-secondary">
                                {{ __('Cancel') }}
                            </a>
                            <button type="submit" class="btn btn-lg btn-primary">
                                {{ __('Submit') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
