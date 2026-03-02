@extends('layouts.app')
@section('content')
    @php
        $isRootUser = false;
        if (auth()->check()) {
            try {
                $isRootUser = auth()->user()->getRoleNames()->contains('root');
            } catch (\Throwable $th) {
                $isRootUser = false;
            }
        }

        $isCustomerWeb = !empty($issiteCustomers);
        $createUrl = $isCustomerWeb ? route('admin.siteCustomer.create') : route('admin.customer.create');
        $resetPasswordActionTemplate = $isCustomerWeb
            ? route('admin.siteCustomer.reset-password', ['id' => '__ID__'])
            : route('admin.customer.reset-password', ['customer' => '__ID__']);
    @endphp

    <div class="d-flex align-items-center flex-wrap gap-3 justify-content-between px-3">
        <h4>{{ __('All Customers') }}</h4>

        @if($isRootUser)
            <a href="{{ $createUrl }}" class="btn py-2 btn-primary">
                <i class="bi bi-patch-plus"></i>
                {{ __('Create New') }}
            </a>
        @else
            @hasPermission('admin.customer.create')
                <a href="{{ $createUrl }}" class="btn py-2 btn-primary">
                    <i class="bi bi-patch-plus"></i>
                    {{ __('Create New') }}
                </a>
            @endhasPermission
        @endif
    </div>

    <div class="container-fluid mt-3">

        <div class="mb-3 card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table border table-responsive-lg">
                        <thead>
                            <tr>
                                <th class="text-center">{{ __('SL') }}.</th>
                                <th>{{ __('Profile') }}</th>
                                <th style="min-width: 150px">{{ __('Name') }}</th>
                                <th style="min-width: 100px">{{ __('Phone') }}</th>
                                <th>{{ __('Email') }}</th>
                                <th class="text-center">{{ __('Gender') }}</th>
                                <th class="text-center">{{ __('Date of Birth') }}</th>
                                <th class="text-center">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        @forelse($customers as $key => $customer)
                            <tr>
                                <td class="text-center">{{ ++$key }}</td>

                                <td>
                                    <img src="{{ $customer->thumbnail ?? asset('assets/icons/user.svg') }}" width="50">
                                </td>

                                <td>{{ Str::limit($customer->fullName, 50, '...') }}</td>

                                <td>
                                    {{ $customer->phone ?? '--' }}
                                </td>

                                <td>
                                    {{ $customer->email ?? '--' }}
                                </td>

                                <td class="text-center">
                                    {{ $customer->gender ?? '--' }}
                                </td>

                                <td class="text-center">
                                    {{ $customer->date_of_birth ?? '--' }}
                                </td>

                                <td class="text-center">
                                    <div class="d-flex gap-2 justify-content-center">
                                        @if($isCustomerWeb)
                                            <a href="{{ route('admin.siteCustomer.show', $customer->id) }}"
                                                class="btn btn-outline-secondary circleIcon" data-bs-toggle="tooltip"
                                                data-bs-placement="left" data-bs-title="{{ __('View') }}">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.siteCustomer.edit', $customer->id) }}"
                                                class="btn btn-outline-primary circleIcon" data-bs-toggle="tooltip"
                                                data-bs-placement="left" data-bs-title="{{ __('Edit') }}">
                                                <img src="{{ asset('assets/icons-admin/edit.svg') }}" alt="edit" loading="lazy" />
                                            </a>
                                            <button type="button" class="btn btn-outline-info circleIcon"
                                                data-bs-toggle="tooltip" data-bs-placement="left"
                                                data-bs-title="{{ __('Reset Password') }}"
                                                onclick="openResetPasswordModal('{{ $customer->id }}','{{ $customer->fullName }}')">
                                                <img src="{{ asset('assets/icons-admin/role-permission.svg') }}" alt="key" loading="lazy" />
                                            </button>
                                        @else
                                            @hasPermission('admin.customer.edit')
                                                <a href="{{ route('admin.customer.edit', $customer->id) }}"
                                                    class="btn btn-outline-primary circleIcon" data-bs-toggle="tooltip"
                                                    data-bs-placement="left" data-bs-title="{{ __('Edit') }}">
                                                    <img src="{{ asset('assets/icons-admin/edit.svg') }}" alt="edit" loading="lazy" />
                                                </a>
                                            @endhasPermission

                                            @hasPermission('admin.customer.destroy')
                                                <a href="{{ route('admin.customer.destroy', $customer->id) }}"
                                                    class="btn btn-outline-danger circleIcon deleteConfirm" data-bs-toggle="tooltip"
                                                    data-bs-placement="left" data-bs-title="{{ __('Delete') }}">
                                                    <img src="{{ asset('assets/icons-admin/trash.svg') }}" alt="delete" loading="lazy" />
                                                </a>
                                            @endhasPermission

                                            @hasPermission('admin.customer.reset-password')
                                                <button type="button" class="btn btn-outline-info circleIcon"
                                                    data-bs-toggle="tooltip" data-bs-placement="left"
                                                    data-bs-title="{{ __('Reset Password') }}"
                                                    onclick="openResetPasswordModal('{{ $customer->id }}','{{ $customer->fullName }}')">
                                                    <img src="{{ asset('assets/icons-admin/role-permission.svg') }}" alt="key" loading="lazy" />
                                                </button>
                                            @endhasPermission
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="text-center" colspan="100%">{{ __('No Data Found') }}</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="my-3">
            {{ $customers->withQueryString()->links() }}
        </div>

        <form action="" method="POST" id="resetPasswordForm">
            @csrf
            <div class="modal fade" id="resetPasswordModal" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title fs-5">{{ __('Reset Password') }} <span id="userName"></span></h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="password1" class="form-label">
                                    {{ __('Password') }}
                                </label>
                                <div class="position-relative passwordInput">
                                    <input type="password" name="password" id="password1" class="form-control"
                                        required="true" placeholder="Enter Password">
                                    <span class="eye" onclick="showHidePassword(1)">
                                        <i class="fa fa-eye-slash" id="togglePassword1"></i>
                                    </span>
                                </div>
                                @error('password')
                                    <p class="text text-danger m-0">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password2" class="form-label">
                                    {{ __('Confirm Password') }}
                                </label>
                                <div class="position-relative passwordInput">
                                    <input type="password" name="password_confirmation" id="password2" class="form-control"
                                        required="true" placeholder="Enter Password again">
                                    <span class="eye" onclick="showHidePassword(2)">
                                        <i class="fa fa-eye-slash" id="togglePassword2"></i>
                                    </span>
                                </div>
                                <span id="passwordMatch" class="text text-danger d-none"></span>
                                @error('password_confirmation')
                                    <p class="text text-danger m-0">{{ $message }}</p>
                                @enderror
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                {{ __('Close') }}
                            </button>
                            <button type="submit" id="submit" class="btn btn-primary">
                                {{ __('Save changes') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

    </div>
@endsection
@push('scripts')
    <script>
        function openResetPasswordModal(userId, userName) {
            const modalEl = document.getElementById('resetPasswordModal');
            const nameEl = document.getElementById('userName');
            const formEl = document.getElementById('resetPasswordForm');

            if (nameEl) {
                nameEl.textContent = userName ? `(${userName})` : '';
            }

            if (formEl) {
                const actionTemplate = @json($resetPasswordActionTemplate);
                formEl.setAttribute('action', actionTemplate.replace('__ID__', String(userId)));
            }

            if (modalEl && window.bootstrap?.Modal) {
                window.bootstrap.Modal.getOrCreateInstance(modalEl).show();
            }
        }

        function showHidePassword(num) {
            const toggle = document.getElementById('togglePassword' + num);
            const password = document.getElementById('password' + num);
            if (!toggle || !password) return;

            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);

            toggle.classList.toggle('fa-eye');
            toggle.classList.toggle('fa-eye-slash');
        }

        (function wirePasswordMatch() {
            const password2El = document.getElementById('password2');
            if (!password2El) return;

            password2El.addEventListener('keyup', function () {
                const password1 = (document.getElementById('password1')?.value ?? '');
                const password2 = (document.getElementById('password2')?.value ?? '');
                const messageEl = document.getElementById('passwordMatch');
                const submitEl = document.getElementById('submit');

                if (password1 === password2) {
                    password2El.classList.remove('is-invalid');
                    if (messageEl) messageEl.classList.add('d-none');
                    if (submitEl) submitEl.disabled = false;
                } else {
                    password2El.classList.add('is-invalid');
                    if (messageEl) {
                        messageEl.classList.remove('d-none');
                        messageEl.textContent = "Password doesn't match";
                    }
                    if (submitEl) submitEl.disabled = true;
                }
            });
        })();
    </script>
@endpush
