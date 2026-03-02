@extends('layouts.app')

@section('content')
    <div class="d-flex align-items-center flex-wrap gap-3 justify-content-between px-3">
        <div>
            <h4 class="mb-0">{{ __('Identity Verification Details') }}</h4>
            <div class="text-muted" style="font-size: 12px;">{{ __('Request') }} #{{ $row->id }} • {{ __('User') }} #{{ $row->user_id }}</div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.identityVerification.index') }}" class="btn btn-outline-secondary">
                {{ __('Back') }}
            </a>
        </div>
    </div>

    <div class="container-fluid mt-3">
        <div class="row g-3">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="mb-3">{{ __('User Info') }}</h5>
                        <div class="mb-2"><strong>{{ __('Name') }}:</strong> {{ $row->fullName }}</div>
                        <div class="mb-2"><strong>{{ __('Email') }}:</strong> {{ $row->email ?? '--' }}</div>
                        <div class="mb-2"><strong>{{ __('Phone') }}:</strong> {{ $row->phone ?? '--' }}</div>
                        <div class="mb-2"><strong>{{ __('Account Verified') }}:</strong>
                            @if((int) $row->user_verified_status === 1)
                                <span class="badge bg-success">{{ __('Yes') }}</span>
                            @else
                                <span class="badge bg-secondary">{{ __('No') }}</span>
                            @endif
                        </div>
                        <div class="mb-2"><strong>{{ __('Request Status') }}:</strong>
                            @if((int) $row->status === 0)
                                <span class="badge bg-warning">{{ __('Pending') }}</span>
                            @elseif((int) $row->status === 1)
                                <span class="badge bg-success">{{ __('Approved') }}</span>
                            @elseif((int) $row->status === 2)
                                <span class="badge bg-danger">{{ __('Declined') }}</span>
                            @else
                                <span class="badge bg-secondary">{{ __('Unknown') }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-body">
                        <h5 class="mb-3">{{ __('Submitted Details') }}</h5>
                        <div class="mb-2"><strong>{{ __('Identification Type') }}:</strong> {{ $row->identification_type ?? '--' }}</div>
                        <div class="mb-2"><strong>{{ __('Identification Number') }}:</strong> {{ $row->identification_number ?? '--' }}</div>
                        <div class="mb-2"><strong>{{ __('Zip Code') }}:</strong> {{ $row->zip_code ?? '--' }}</div>
                        <div class="mb-2"><strong>{{ __('Address') }}:</strong> {{ $row->address ?? '--' }}</div>
                        <div class="mb-2"><strong>{{ __('Verified By') }}:</strong> {{ $row->verify_by ?? '--' }}</div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-body">
                        <h5 class="mb-3">{{ __('Decision') }}</h5>

                        {{-- Decline with reason --}}
                        <form method="POST" action="{{ route('admin.identityVerification.decline', $row->id) }}" id="declineForm">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-medium">{{ __('Decline Reason') }} <span class="text-muted fw-normal">({{ __('required when declining') }})</span></label>
                                <textarea name="decline_reason" class="form-control" rows="2"
                                    placeholder="{{ __('e.g. Document image is blurry or ID is expired') }}">{{ old('decline_reason') }}</textarea>
                            </div>
                            <button type="button" class="btn btn-danger"
                                onclick="confirmAction('declineForm', '{{ __('Decline this verification?') }}', '{{ __('The user will be notified with your reason.') }}')">
                                {{ __('Decline') }}
                            </button>
                        </form>

                        {{-- Approve --}}
                        <form method="POST" action="{{ route('admin.identityVerification.approve', $row->id) }}" id="approveForm" class="mt-3">
                            @csrf
                            <button type="button" class="btn btn-success"
                                onclick="confirmAction('approveForm', '{{ __('Approve and verify this user?') }}', '{{ __('The verified badge will be added to their profile.') }}')">
                                {{ __('Approve & Grant Verified Badge') }}
                            </button>
                        </form>
                    </div>
                </div>
                @if(!empty($audits) && $audits->count() > 0)
                <div class="card mt-3">
                    <div class="card-body">
                        <h5 class="mb-3">{{ __('Audit Trail') }}</h5>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>{{ __('Date') }}</th>
                                        <th>{{ __('Action') }}</th>
                                        <th>{{ __('Admin') }}</th>
                                        <th>{{ __('Reason') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($audits as $a)
                                        <tr>
                                            <td>{{ $a->created_at?->format('d M, Y H:i') ?? '-' }}</td>
                                            <td>{{ ucfirst($a->action ?? '-') }}</td>
                                            <td>{{ $a->admin_name ?? ($a->admin_id ? '#'.$a->admin_id : '-') }}</td>
                                            <td>{{ $a->reason ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="mb-3">{{ __('Documents') }}</h5>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="border rounded p-2">
                                    <div class="fw-medium mb-2">{{ __('Front Document') }}</div>
                                    @if($row->frontDocumentUrl)
                                        <a href="{{ $row->frontDocumentUrl }}" target="_blank" rel="noopener">{{ __('Open') }}</a>
                                    @else
                                        <div class="text-muted">--</div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="border rounded p-2">
                                    <div class="fw-medium mb-2">{{ __('Back Document') }}</div>
                                    @if($row->backDocumentUrl)
                                        <a href="{{ $row->backDocumentUrl }}" target="_blank" rel="noopener">{{ __('Open') }}</a>
                                    @else
                                        <div class="text-muted">--</div>
                                    @endif
                                </div>
                            </div>
                            @if($row->selfiePhotoUrl)
                            <div class="col-12">
                                <div class="border rounded p-2">
                                    <div class="fw-medium mb-2">{{ __('Selfie with ID') }}</div>
                                    <a href="{{ $row->selfiePhotoUrl }}" target="_blank" rel="noopener">
                                        <img src="{{ $row->selfiePhotoUrl }}" alt="{{ __('Selfie') }}"
                                             style="max-height: 160px; border-radius: 8px; object-fit: cover;">
                                    </a>
                                </div>
                            </div>
                            @endif
                        </div>

                        <div class="text-muted mt-3" style="font-size: 12px;">
                            {{ __('Documents are hosted on the customer web.') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function confirmAction(formId, title, text) {
            const root = document.documentElement;
            const themeColor = getComputedStyle(root).getPropertyValue("--theme-color").trim() || '#3085d6';

            Swal.fire({
                title: title,
                text: text,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: themeColor,
                cancelButtonColor: '#d33',
                confirmButtonText: "{{ __('Yes, proceed') }}",
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(formId).submit();
                }
            });
        }
    </script>
@endpush
