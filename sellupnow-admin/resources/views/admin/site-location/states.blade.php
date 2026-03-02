@extends('layouts.app')
@section('header-title', __('Listing Locations - Cities'))

@section('content')
    <div class="d-flex align-items-center flex-wrap gap-3 justify-content-between px-3">
        <h4 class="m-0">{{ __('Cities') }}</h4>
    </div>

    <div class="container-fluid mt-3">
        <div class="mb-3 card">
            <div class="card-body">
                <form action="" class="d-flex align-items-center justify-content-between gap-3 mb-3">
                    <div class="d-flex gap-2 flex-wrap" style="max-width: 720px">
                        <div class="input-group" style="max-width: 420px">
                            <input type="text" name="search" class="form-control" placeholder="{{ __('Search city/country') }}"
                                value="{{ request('search') }}">
                            <button type="submit" class="input-group-text btn btn-primary">
                                <i class="fa fa-search"></i> {{ __('Search') }}
                            </button>
                        </div>

                        <select name="country_id" class="form-control" style="max-width: 260px"
                            onchange="this.form.submit()">
                            <option value="">{{ __('All Countries') }}</option>
                            @foreach($countries as $c)
                                <option value="{{ $c->id }}" @selected((string)request('country_id') === (string)$c->id)>
                                    {{ $c->country }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    @hasPermission('admin.siteState.store')
                        <button type="button" data-bs-toggle="modal" data-bs-target="#createState" class="btn py-2 btn-primary">
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
                                <th>{{ __('City') }}</th>
                                <th>{{ __('Country') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th class="text-center">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($states as $key => $state)
                                @php $serial = $states->firstItem() + $key; @endphp
                                <tr>
                                    <td class="text-center">{{ $serial }}</td>
                                    <td>{{ $state->state }}</td>
                                    <td>{{ $state->country_name ?? '--' }}</td>
                                    <td>
                                        <span class="badge {{ (int)$state->status === 1 ? 'bg-success' : 'bg-secondary' }}">
                                            {{ (int)$state->status === 1 ? __('Active') : __('Inactive') }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex gap-2 justify-content-center">
                                            @hasPermission('admin.siteState.update')
                                                <button type="button" class="btn btn-outline-primary circleIcon btn-sm"
                                                    onclick='openUpdateModal(@json($state))'>
                                                    <img src="{{ asset('assets/icons-admin/edit.svg') }}" alt="edit" loading="lazy" />
                                                </button>
                                            @endhasPermission

                                            @hasPermission('admin.siteState.destroy')
                                                <a href="{{ route('admin.siteState.destroy', $state->id) }}"
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
            {{ $states->links('pagination::bootstrap-5') }}
        </div>
    </div>

    <form action="{{ route('admin.siteState.store') }}" method="POST">
        @csrf
        <div class="modal fade" id="createState" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('Add New City') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">{{ __('Country') }} *</label>
                            <select name="country_id" class="form-control" required>
                                <option value="">{{ __('Select Country') }}</option>
                                @foreach($countries as $c)
                                    <option value="{{ $c->id }}">{{ $c->country }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('City') }} *</label>
                            <input type="text" name="state" class="form-control" required>
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
        <div class="modal fade" id="updateStateModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('Update City') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">{{ __('Country') }} *</label>
                            <select id="u_country_id" name="country_id" class="form-control" required>
                                <option value="">{{ __('Select Country') }}</option>
                                @foreach($countries as $c)
                                    <option value="{{ $c->id }}">{{ $c->country }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('City') }} *</label>
                            <input type="text" id="u_state" name="state" class="form-control" required>
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
            document.getElementById('u_state').value = row.state ?? '';
            document.getElementById('u_country_id').value = String(row.country_id ?? '');
            document.getElementById('u_status').value = String(row.status ?? 1);

            const actionTemplate = @json(route('admin.siteState.update', ['id' => '__ID__']));
            document.getElementById('updateForm').setAttribute('action', actionTemplate.replace('__ID__', String(row.id)));

            const modalEl = document.getElementById('updateStateModal');
            if (modalEl && window.bootstrap?.Modal) {
                window.bootstrap.Modal.getOrCreateInstance(modalEl).show();
            }
        }
    </script>
@endpush
