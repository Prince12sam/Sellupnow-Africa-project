@extends('layouts.app')
@section('header-title', __('Listing Locations - Countries'))

@section('content')
    <div class="d-flex align-items-center flex-wrap gap-3 justify-content-between px-3">
        <h4 class="m-0">{{ __('Countries') }}</h4>
    </div>

    <div class="container-fluid mt-3">
        <div class="mb-3 card">
            <div class="card-body">
                <form action="" class="d-flex align-items-center justify-content-between gap-3 mb-3">
                    <div class="input-group" style="max-width: 520px">
                        <input type="text" name="search" class="form-control"
                            placeholder="{{ __('Search by country/code/dial') }}" value="{{ request('search') }}">
                        <button type="submit" class="input-group-text btn btn-primary">
                            <i class="fa fa-search"></i> {{ __('Search') }}
                        </button>
                    </div>

                    @hasPermission('admin.siteCountry.store')
                        <button type="button" data-bs-toggle="modal" data-bs-target="#createCountry"
                            class="btn py-2 btn-primary">
                            <i class="fa fa-plus-circle"></i>
                            {{ __('Create New') }}
                        </button>
                    @endhasPermission
                </form>

                <div class="table-responsive">
                    <table class="table border table-responsive-lg">
                        <thead>
                            <tr>
                                <th class="text-center">{{ __('SL') }}</th>
                                <th>{{ __('Country') }}</th>
                                <th>{{ __('Code') }}</th>
                                <th>{{ __('Dial Code') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th class="text-center">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($countries as $key => $country)
                                @php $serial = $countries->firstItem() + $key; @endphp
                                <tr>
                                    <td class="text-center">{{ $serial }}</td>
                                    <td>{{ $country->country }}</td>
                                    <td>{{ $country->country_code ?? '--' }}</td>
                                    <td>{{ $country->dial_code ?? '--' }}</td>
                                    <td>
                                        <span class="badge {{ (int)$country->status === 1 ? 'bg-success' : 'bg-secondary' }}">
                                            {{ (int)$country->status === 1 ? __('Active') : __('Inactive') }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex gap-2 justify-content-center">
                                            @hasPermission('admin.siteCountry.update')
                                                <button type="button" class="btn btn-outline-primary circleIcon btn-sm"
                                                    onclick='openUpdateModal(@json($country))'>
                                                    <img src="{{ asset('assets/icons-admin/edit.svg') }}" alt="edit" loading="lazy" />
                                                </button>
                                            @endhasPermission

                                            @hasPermission('admin.siteCountry.destroy')
                                                <a href="{{ route('admin.siteCountry.destroy', $country->id) }}"
                                                    class="circleIcon btn btn-outline-danger btn-sm deleteConfirm">
                                                    <img src="{{ asset('assets/icons-admin/trash.svg') }}" alt="delete" loading="lazy" />
                                                </a>
                                            @endhasPermission
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
            {{ $countries->links('pagination::bootstrap-5') }}
        </div>
    </div>

    <form action="{{ route('admin.siteCountry.store') }}" method="POST">
        @csrf
        <div class="modal fade" id="createCountry">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('Add New Country') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">{{ __('Country') }} *</label>
                            <input type="text" name="country" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('Country Code (ISO2)') }}</label>
                            <input type="text" name="country_code" class="form-control" placeholder="e.g. GH" maxlength="2">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('Dial Code') }}</label>
                            <input type="text" name="dial_code" class="form-control" placeholder="e.g. 233">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('Status') }}</label>
                            <select name="status" class="form-control" required>
                                <option value="1" selected>{{ __('Active') }}</option>
                                <option value="0">{{ __('Inactive') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <form action="" id="updateForm" method="POST">
        @csrf
        @method('PUT')
        <div class="modal fade" id="updateCountryModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('Update Country') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">{{ __('Country') }} *</label>
                            <input type="text" id="u_country" name="country" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('Country Code (ISO2)') }}</label>
                            <input type="text" id="u_country_code" name="country_code" class="form-control" maxlength="2">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('Dial Code') }}</label>
                            <input type="text" id="u_dial_code" name="dial_code" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('Status') }}</label>
                            <select id="u_status" name="status" class="form-control" required>
                                <option value="1">{{ __('Active') }}</option>
                                <option value="0">{{ __('Inactive') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        const openUpdateModal = (row) => {
            document.getElementById('u_country').value = row.country ?? '';
            document.getElementById('u_country_code').value = row.country_code ?? '';
            document.getElementById('u_dial_code').value = row.dial_code ?? '';
            document.getElementById('u_status').value = String(row.status ?? 1);

            const actionTemplate = @json(route('admin.siteCountry.update', ['id' => '__ID__']));
            document.getElementById('updateForm').setAttribute('action', actionTemplate.replace('__ID__', String(row.id)));

            const modalEl = document.getElementById('updateCountryModal');
            if (modalEl && window.bootstrap?.Modal) {
                window.bootstrap.Modal.getOrCreateInstance(modalEl).show();
            }
        }
    </script>
@endpush
