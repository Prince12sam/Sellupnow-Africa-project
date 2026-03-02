@extends('layouts.app')

@section('header-title', __('Report Reasons'))

@section('content')
    <div class="d-flex align-items-center flex-wrap gap-3 justify-content-between px-3">
        <h4 class="m-0">{{ __('Report Reasons') }}</h4>
        @hasPermission('admin.reportReason.store')
            <button type="button" data-bs-toggle="modal" data-bs-target="#createReason" class="btn py-2.5 btn-primary">
                <i class="fa fa-plus-circle"></i>
                {{ __('Create New') }}
            </button>
        @endhasPermission
    </div>

    <div class="container-fluid mt-3">
        <div class="card mb-3">
            <div class="card-header py-3">
                <h5 class="card-title m-0">{{ __('Reason List') }}</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table border-left-right table-responsive-md">
                        <thead>
                            <tr>
                                <th class="text-center">{{ __('SL') }}</th>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th class="text-center">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reportReasons as $key => $reason)
                                @php $serial = $reportReasons->firstItem() + $key; @endphp
                                <tr>
                                    <td class="text-center">{{ $serial }}</td>
                                    <td>{{ $reason->name }}</td>
                                    <td>
                                        @hasPermission('admin.reportReason.toggle')
                                            <label class="switch mb-0">
                                                <a href="{{ route('admin.reportReason.toggle', $reason->id) }}">
                                                    <input type="checkbox" {{ $reason->is_active ? 'checked' : '' }}>
                                                    <span class="slider round"></span>
                                                </a>
                                            </label>
                                        @else
                                            <span class="badge {{ $reason->is_active ? 'bg-success' : 'bg-secondary' }}">
                                                {{ $reason->is_active ? __('Active') : __('Inactive') }}
                                            </span>
                                        @endhasPermission
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex gap-2 justify-content-center">
                                            @hasPermission('admin.reportReason.update')
                                                <button type="button" class="btn btn-outline-info btn-sm circleIcon"
                                                    onclick='openEditModal(@json($reason))'>
                                                    <img src="{{ asset('assets/icons-admin/edit.svg') }}" alt="icon" loading="lazy" />
                                                </button>
                                            @endhasPermission
                                            @hasPermission('admin.reportReason.delete')
                                                <a href="{{ route('admin.reportReason.delete', $reason->id) }}"
                                                    class="btn btn-outline-danger btn-sm deleteConfirmAlert circleIcon">
                                                    <img src="{{ asset('assets/icons-admin/trash.svg') }}" alt="icon" loading="lazy" />
                                                </a>
                                            @endhasPermission
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="100%" class="text-center">{{ __('No Data Found') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="my-3">
            {{ $reportReasons->withQueryString()->links() }}
        </div>
    </div>

    <form action="{{ route('admin.reportReason.store') }}" method="POST">
        @csrf
        <div class="modal fade" id="createReason" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('Create Report Reason') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">{{ __('Name') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" maxlength="255" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Save Reason') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <form action="" id="editReasonForm" method="POST">
        @csrf
        @method('PUT')
        <div class="modal fade" id="editReason" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('Edit Report Reason') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">{{ __('Name') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="editReasonName" name="name" maxlength="255" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Update Reason') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        const openEditModal = (reason) => {
            document.getElementById('editReasonName').value = reason.name;
            document.getElementById('editReasonForm').setAttribute(
                'action',
                `{{ route('admin.reportReason.update', ':id') }}`.replace(':id', reason.id)
            );
            $('#editReason').modal('show');
        }

        document.querySelectorAll('.deleteConfirmAlert').forEach((element) => {
            element.addEventListener('click', function(e) {
                e.preventDefault();
                const url = this.getAttribute('href');
                Swal.fire({
                    title: "{{ __('Are you sure?') }}",
                    text: "{{ __('You will not be able to revert this!') }}",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "{{ __('Yes, delete it!') }}",
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = url;
                    }
                });
            });
        });
    </script>
@endpush
