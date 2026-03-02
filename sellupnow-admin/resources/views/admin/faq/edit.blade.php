@extends('layouts.app')
@section('header-title', __('Edit FAQ'))
@section('content')
<div class="container-fluid my-4">

    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3 px-1">
        <h4 class="m-0">{{ __('Edit FAQ') }}</h4>
        <a href="{{ route('admin.faq.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i>{{ __('Back to FAQs') }}
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-xl-8 col-lg-10">
            <div class="card">
                <div class="card-header py-3">
                    <h5 class="card-title m-0">{{ __('FAQ Details') }}</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.faq.update', $faq) }}">
                        @csrf @method('PUT')
                        @include('admin.faq._form', ['faq' => $faq, 'nextOrder' => $faq->sort_order])
                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fa-solid fa-floppy-disk me-1"></i>{{ __('Update FAQ') }}
                            </button>
                            <a href="{{ route('admin.faq.index') }}" class="btn btn-outline-secondary px-4">
                                {{ __('Cancel') }}
                            </a>
                            @hasPermission('admin.faq.destroy')
                                <form method="POST" action="{{ route('admin.faq.destroy', $faq) }}"
                                      class="ms-auto"
                                      onsubmit="return confirm('{{ __('Delete this FAQ permanently?') }}')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger px-4">
                                        <i class="fa-solid fa-trash me-1"></i>{{ __('Delete') }}
                                    </button>
                                </form>
                            @endhasPermission
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
