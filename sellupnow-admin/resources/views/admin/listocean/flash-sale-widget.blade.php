@extends('layouts.app')

@section('header-title', __('Flash Sale Widget'))
@section('header-subtitle', __('Choose where the flash sale widget appears on the Listocean customer web'))

@section('content')
    <div class="page-title">
        <div class="d-flex gap-2 align-items-center">
            <i class="fa-solid fa-bolt"></i> {{ __('Flash Sale Widget') }}
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 col-xl-7">
            <div class="card mt-3">
                <div class="card-body">
                    <form action="{{ route('admin.flashSaleWidget.update') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <div class="form-check">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    name="enabled"
                                    id="enabled"
                                    value="1"
                                    @checked(!empty($enabled))
                                />
                                <label class="form-check-label" for="enabled">
                                    {{ __('Enable Flash Sale Widget on Listocean') }}
                                </label>
                            </div>
                            <div class="small text-muted">
                                {{ __('Widget pulls running flash sale + products from the SellUpNow API and displays it on selected pages/slots.') }}
                            </div>
                        </div>

                        <hr class="my-4" />

                        <h5 class="mb-2">{{ __('Placements') }}</h5>
                        <div class="small text-muted mb-3">{{ __('Select where to show the widget for each page type.') }}</div>

                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th>{{ __('Page') }}</th>
                                        @foreach($slots as $slotKey => $slotLabel)
                                            <th class="text-center">{{ __($slotLabel) }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pages as $pageKey => $pageLabel)
                                        <tr>
                                            <td>
                                                <div class="fw-semibold">{{ __($pageLabel) }}</div>
                                                <div class="small text-muted">{{ $pageKey }}</div>
                                            </td>
                                            @foreach($slots as $slotKey => $slotLabel)
                                                @php
                                                    $checked = in_array($slotKey, (array)($placements[$pageKey] ?? []), true);
                                                @endphp
                                                <td class="text-center">
                                                    <input
                                                        class="form-check-input"
                                                        type="checkbox"
                                                        name="placements[{{ $pageKey }}][]"
                                                        value="{{ $slotKey }}"
                                                        @checked($checked)
                                                    />
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-end mt-3">
                            <button type="submit" class="btn btn-primary py-2 px-4">
                                {{ __('Save') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
